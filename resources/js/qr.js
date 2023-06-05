import {Eko, axios, Pusher, Suwal} from './bootstrap.js';

Eko.channel(`qr_guest`)
    .listen('QrGuest', (e) => {

        let timerInterval
        Suwal.fire({
        icon: 'success',
        title: 'Selamat Datang',
        html: e.name,
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton:false,
        didOpen: () => {
            Swal.showLoading()
            const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() => {
            b.textContent = Swal.getTimerLeft()
            }, 300)
        },
        willClose: () => {
            clearInterval(timerInterval)
        }
        })

    });