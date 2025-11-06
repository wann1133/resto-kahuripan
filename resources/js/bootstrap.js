import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Bootstrap Echo for realtime kitchen updates
window.Pusher = Pusher;

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY || import.meta.env.VITE_REVERB_APP_KEY;
const pusherHost = import.meta.env.VITE_PUSHER_HOST || import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const pusherPort = Number(import.meta.env.VITE_PUSHER_PORT || import.meta.env.VITE_REVERB_PORT || 6001);
const useTLS = (import.meta.env.VITE_PUSHER_SCHEME || import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https';

if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        wsHost: pusherHost,
        wsPort: pusherPort,
        wssPort: pusherPort,
        forceTLS: useTLS,
        enabledTransports: ['ws', 'wss'],
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
        authEndpoint: '/broadcasting/auth',
    });
}
