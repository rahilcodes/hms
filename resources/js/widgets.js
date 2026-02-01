
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

document.addEventListener('DOMContentLoaded', () => {
    initWeather();
    initMap();
    initSocialProof();
});

// 1. WEATHER WIDGET (Simulated for KMR)
function initWeather() {
    const weatherContainer = document.getElementById('weather-widget');
    if (!weatherContainer) return;

    // Simulate API call
    const weather = { temp: 28, condition: 'Sunny', location: 'KMR' };

    weatherContainer.innerHTML = `
        <div class="flex items-center gap-2 text-sm font-bold text-slate-500 bg-white/80 backdrop-blur px-3 py-1 rounded-full shadow-sm border border-slate-200">
            <span>☀️</span>
            <span>${weather.temp}°C ${weather.condition}</span>
            <span class="text-xs uppercase tracking-wider text-slate-400">| ${weather.location}</span>
        </div>
    `;
}

// 2. INTERACTIVE MAP (Leaflet)
function initMap() {
    const mapElement = document.getElementById('titanium-map');
    if (!mapElement) return;

    // Default to a scenic location (e.g., Maldives coords or generic beach)
    const lat = 34.0837;
    const lng = 74.7973; // KMR coords roughly

    const map = L.map('titanium-map', {
        center: [lat, lng],
        zoom: 13,
        scrollWheelZoom: false,
        zoomControl: false
    });

    // Dark/Light Mode Tiles (CartoDB Voyager for premium look)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // Custom Marker
    const icon = L.divIcon({
        className: 'titanium-map-marker',
        html: `<div class="w-4 h-4 bg-blue-600 rounded-full border-2 border-white shadow-lg animate-pulse"></div>`,
        iconSize: [20, 20]
    });

    L.marker([lat, lng], { icon: icon }).addTo(map)
        .bindPopup('<b style="font-family: sans-serif;">LuxeStay Resort</b><br>5-Star Ocean View').openPopup();
}

// 3. SOCIAL PROOF TOASTS (Removed - Not needed for Admin Panels)
function initSocialProof() {
    // Disabled
}

function showToast(msg) {
    // Disabled
}
