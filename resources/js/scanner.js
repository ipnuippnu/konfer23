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
                                hasil => {
                                    
                                    Suwal.fire({
                                        didOpen(){
                                            Suwal.showLoading()
                                            let data = {
                                                'code': hasil.data,
                                                'event': uuid
                                            }
                                            axios({
                                                method: 'post',
                                                url:'',
                                                data: data,
                                                timeout: 2000,
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
                                            }).catch(function(e){

                                                console.log(result, data)

                                                transaction = database.transaction(result.type, "readwrite")
                                                store = transaction.objectStore(result.type)
                                                
                                                let ambil = store.get(data.code)
                                                ambil.onsuccess = function(){
                                                    if(ambil.result){
                                                        queue.push(data)
                                                        Suwal.fire('Sukses!', "(Offline) "+ambil.result.name, 'success')
                                                        window.qr.start()

                                                        transaction = database.transaction(result.type, "readwrite")
                                                        store = transaction.objectStore(result.type)
                                                        store.delete(data.code)
                                                    }
                                                    else
                                                    {
                                                        Suwal.fire('Astaghfirullah', "Data tidak ditemukan", 'error')
                                                    }
                                                }

                                                ambil.onerror = function(){
                                                    Suwal.fire('Astaghfirullah', "Data tidak ditemukan", 'error')
                                                    window.qr.start()
                                                }

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
                                            let data = {
                                                'code': $('#select2').select2('data')[0].id,
                                                'event': uuid
                                            }
                                            window.qr.stop()
                                            return axios({
                                                method: 'post',
                                                data: data,
                                                timeout: 2000
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
                                            }).catch(e => {
                                                transaction = database.transaction(result.type, "readwrite")
                                                store = transaction.objectStore(result.type)
                                                
                                                let ambil = store.get(data.code)
                                                ambil.onsuccess = function(){
                                                    if(ambil.result)
                                                    {
                                                        queue.push(data)
                                                        Suwal.fire('Sukses!', "(Offline) "+ambil.result.name, 'success')
                                                        window.qr.start()

                                                        transaction = database.transaction(result.type, "readwrite")
                                                        store = transaction.objectStore(result.type)
                                                        store.delete(data.code)

                                                    }
                                                    else
                                                    {
                                                        Suwal.fire('Astaghfirullah', "Data tidak ditemukan", 'error')
                                                    }
                                                }

                                                ambil.onerror = function(){
                                                    Suwal.fire('Astaghfirullah', "Data tidak ditemukan", 'error')
                                                    window.qr.start()
                                                }
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

    let busy = false
    setInterval(function(){
        if(!busy && queue.length > 0)
        {
            busy = true
            axios({
                url: '',
                data: queue[0],
                timeout: 1000
            }).then(function(){
                queue.splice(0, 1)
            }).catch(function(){

            }).finally(function(){
                busy = false
            })
        }
    }, 1000)

});