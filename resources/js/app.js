import './bootstrap';

import '/node_modules/mapbox-gl/dist/mapbox-gl.css';
import '/node_modules/@mapbox/mapbox-gl-geocoder/dist/mapbox-gl-geocoder.css';

import mapboxgl from 'mapbox-gl';
import MapboxGeocoder from '@mapbox/mapbox-gl-geocoder';
import {Html5QrcodeScanner} from "html5-qrcode";
import {Html5Qrcode} from "html5-qrcode";

window.mapboxgl = mapboxgl;
window.MapboxGeocoder = MapboxGeocoder;
window.Html5QrcodeScanner = Html5QrcodeScanner;
window.Html5Qrcode = Html5Qrcode;