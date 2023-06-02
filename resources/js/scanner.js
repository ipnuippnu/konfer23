import {Eko, axios, Pusher, Suwal} from './bootstrap.js';
import QrScanner from 'qr-scanner';
import $ from 'jquery'
import select2 from 'select2';
import 'select2/dist/css/select2.css';

select2($)


document.addEventListener("DOMContentLoaded", () => {

    let queue = [];

    const indexedDB =
    window.indexedDB ||
    window.mozIndexedDB ||
    window.webkitIndexedDB ||
    window.msIndexedDB ||
    window.shimIndexedDB;

    if (indexedDB) {
    
        document.querySelector('form#gas').addEventListener('submit', function(e){
            e.preventDefault()
            Suwal.fire({
                text: 'Melakukan permintaan...',
                async didOpen(){
                    try {
                        Suwal.showLoading()
                        let uuid = document.querySelector('form#gas select[name="event"]').value;
                        
                        let result = (await axios.get(location.href + '/' + uuid)).data

                        const DB = indexedDB.open(result.type, 1);
        
                        DB.onerror = function()
                        {
                            throw new Error("Database tidak bisa digunakan")
                        }

                        DB.onupgradeneeded = function()
                        {
                            const database = DB.result
                            const store = database.createObjectStore(result.type, {keyPath: "code"})
                            if(result.data[0].name != undefined)
                                store.createIndex('name', ['name'], {unique: false})
                        }

                        DB.onsuccess = function(){
                            const database = DB.result
                            let transaction = database.transaction(result.type, "readwrite")
                            let store = transaction.objectStore(result.type)

                            store.clear()

                            result.data.forEach(val => store.put(val))

                            const video = document.querySelector('video')
                            window.qr = new QrScanner(
                                video,
                                result => {
                                    
                                    Suwal.fire({
                                        didOpen(){
                                            Suwal.showLoading()
                                            axios.post('', {
                                                'code': result.data,
                                                'event': uuid
                                            }).then(e => e.data).then(async e => {
                                                if(e.status === true)
                                                {
                                                    await Suwal.fire('Sukses!', e.message, 'success')
                                                }
                                                else
                                                {
                                                    await Suwal.fire(e.title ?? 'Astaghfirullah', e.message, 'error')
                                                }

                                                window.qr.start()
                                            })
                                        }
                                    })

                                    window.qr.stop()

                                },
                                {
                                    preferredCamera: 'environment',
                                    highlightScanRegion: true
                                }
                            )

                            video.parentNode.insertBefore(window.qr.$canvas, video.nextSibling)
                            window.qr.$canvas.classList.add('w-full', 'h-full')

                            document.querySelector('#badan').classList.remove('hidden')
                            document.querySelector('form#gas').classList.add('hidden')

                            document.querySelector("#pause").addEventListener('click', function(e){
                                if(window.qr._active)
                                {
                                    document.querySelector('#hidden-camera').classList.replace('hidden', 'flex')
                                    e.target.innerHTML = "Mulai Kamera"
                                    window.qr.stop();
                                }
                                else
                                {
                                    document.querySelector('#hidden-camera').classList.replace('flex', 'hidden')
                                    e.target.innerHTML = "Stop Kamera"
                                    window.qr.start();
                                }
                            })
                            document.querySelector("#flash").addEventListener('click', function(){
                                if(window.qr.isFlashOn()){
                                    window.qr.turnFlashOff()
                                }
                                else
                                {
                                    window.qr.turnFlashOn()
                                }
                            })

                            document.querySelector('#manual').addEventListener('click', () => {
                                transaction = database.transaction(result.type, "readwrite")
                                store = transaction.objectStore(result.type)
                                let semua = store.getAll()
                                
                                semua.onsuccess = function(e){

                                    Suwal.fire({
                                        title: 'Pilih Manual',
                                        html: '<div class="py-3"><select id="select2" class="py-3"></select></div>',
                                        willOpen(){
                                            $('#select2').select2({
                                                data: $.map(semua.result, function(obj){
                                                    return {
                                                        id: obj.code,
                                                        text: obj.name
                                                    }
                                                })
                                            })
                                        },
                                        preConfirm: () => {
                                            window.qr.stop()
                                            return axios.post('', {
                                                'code': $('#select2').select2('data')[0].id,
                                                'event': uuid
                                            }).then(e => e.data).then(async e => {
                                                if(e.status === true)
                                                {
                                                    transaction = database.transaction(result.type, "readwrite")
                                                    store = transaction.objectStore(result.type)
                                                    store.delete($('#select2').select2('data')[0].id)
                                                    await Suwal.fire('Sukses!', e.message, 'success')
                                                }
                                                else
                                                {
                                                    await Suwal.fire(e.title ?? 'Astaghfirullah', e.message, 'error')
                                                }

                                                window.qr.start()
                                            })
                                        }
                                    })

                                }
                            })

                            document.querySelector('#ganti-event').addEventListener('click', () => location.href = '')

                            Suwal.close()

                            window.qr.start()
                            
                        }
                        
                    } catch (error) {
                        console.log(error)
                        Suwal.fire('Kesalahan!', 'Mohon hubungi admin.', 'error');
                    }
                }
            })
        })

    }
    else
    {
        Suwal.fire('Tidak Didukung!', 'Perangkat anda tidak mendukung untuk melakukan scan QR Code', 'error')
    }

});