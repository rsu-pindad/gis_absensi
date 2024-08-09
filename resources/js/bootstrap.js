/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });

import '/node_modules/mapbox-gl/dist/mapbox-gl.css';
import '/node_modules/@mapbox/mapbox-gl-geocoder/dist/mapbox-gl-geocoder.css';
import '/node_modules/tom-select/dist/css/tom-select.css';

import mapboxgl from 'mapbox-gl';
import MapboxGeocoder from '@mapbox/mapbox-gl-geocoder';
import { Html5QrcodeScanner } from "html5-qrcode";
import { Html5Qrcode } from "html5-qrcode";
import { detect } from 'un-detector';
import Swal from 'sweetalert2';
import FingerprintJS from '@fingerprintjs/fingerprintjs';
import TomSelect from 'tom-select';

window.mapboxgl = mapboxgl;
window.MapboxGeocoder = MapboxGeocoder;
window.Html5QrcodeScanner = Html5QrcodeScanner;
window.Html5Qrcode = Html5Qrcode;
window.detect = detect;
window.Swal = Swal;
window.FingerprintJS = FingerprintJS;
window.TomSelect = TomSelect;