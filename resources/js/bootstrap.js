import axios from 'axios';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.validateStatus = function() {
    return true;
};

import 'animate.css'

import Echo from 'laravel-echo';

import Pusher from 'pusher-js';

import Swal from 'sweetalert2'
const Suwal = Swal.mixin({
    allowEscapeKey: () => !Swal.isLoading(),
    allowOutsideClick: () => !Swal.isLoading(),
    customClass: {
        container: 'backdrop-blur-sm'
    }
})

const Eko = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws'],
});

Eko.connector.pusher.connection.bind('connecting', (payload) => {
    console.log('connecting...');
});

Eko.connector.pusher.connection.bind('connected', (payload) => {
    console.log('connected!', payload);
});

Eko.connector.pusher.connection.bind('unavailable', (payload) => {
    console.log('unavailable', payload);
});

Eko.connector.pusher.connection.bind('failed', (payload) => {
    console.log('failed', payload);
});

Eko.connector.pusher.connection.bind('disconnected', (payload) => {
    console.log('disconnected', payload);
});

Eko.connector.pusher.connection.bind('message', (payload) => {
    console.log('message', payload);
});

export {axios, Eko, Pusher, Suwal}