@extends('layouts.app')

@push('styles')
<style>
    /* ===== World Globe Page Styles ===== */
    #globe-container {
        position: relative;
        width: 100%;
        height: calc(100vh - 72px);
        background: radial-gradient(ellipse at center, #0a0e27 0%, #020408 100%);
        overflow: hidden;
    }

    /* Stars background */
    #globe-container::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            radial-gradient(1px 1px at 10% 15%, rgba(255,255,255,0.6) 0%, transparent 100%),
            radial-gradient(1px 1px at 25% 35%, rgba(255,255,255,0.4) 0%, transparent 100%),
            radial-gradient(1.5px 1.5px at 40% 10%, rgba(255,255,255,0.7) 0%, transparent 100%),
            radial-gradient(1px 1px at 55% 60%, rgba(255,255,255,0.5) 0%, transparent 100%),
            radial-gradient(1px 1px at 70% 25%, rgba(255,255,255,0.6) 0%, transparent 100%),
            radial-gradient(1.5px 1.5px at 85% 45%, rgba(255,255,255,0.4) 0%, transparent 100%),
            radial-gradient(1px 1px at 15% 75%, rgba(255,255,255,0.5) 0%, transparent 100%),
            radial-gradient(1px 1px at 90% 80%, rgba(255,255,255,0.6) 0%, transparent 100%),
            radial-gradient(1px 1px at 35% 85%, rgba(255,255,255,0.4) 0%, transparent 100%),
            radial-gradient(1.5px 1.5px at 65% 90%, rgba(255,255,255,0.3) 0%, transparent 100%);
        pointer-events: none;
        z-index: 0;
    }

    #globe-canvas {
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    /* ===== Left Layer Controls Panel ===== */
    #layer-panel {
        position: absolute;
        top: 50%;
        left: 20px;
        transform: translateY(-50%);
        z-index: 10;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .layer-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 12px;
        color: rgba(255,255,255,0.7);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        user-select: none;
    }

    .layer-btn:hover {
        background: rgba(255,255,255,0.15);
        color: white;
        transform: translateX(3px);
    }

    .layer-btn.active {
        background: rgba(59, 130, 246, 0.4);
        border-color: rgba(59, 130, 246, 0.7);
        color: white;
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }

    .layer-icon {
        font-size: 18px;
        line-height: 1;
    }

    /* ===== Info Panel (Right Side) ===== */
    #info-panel {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 320px;
        max-height: calc(100% - 40px);
        overflow-y: auto;
        z-index: 10;
        display: none;
        flex-direction: column;
        gap: 12px;
    }

    #info-panel.visible {
        display: flex;
    }

    .info-card {
        background: rgba(10, 14, 39, 0.85);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 16px;
        padding: 16px;
        color: white;
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .info-card-title {
        font-size: 14px;
        font-weight: 700;
        color: white;
        letter-spacing: 0.02em;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 13px;
    }

    .info-label {
        color: rgba(255,255,255,0.5);
    }

    .info-value {
        font-weight: 600;
        color: #93c5fd;
    }

    /* ===== Weather Code Badge ===== */
    .weather-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(59, 130, 246, 0.2);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 12px;
        color: #93c5fd;
        margin-bottom: 10px;
    }

    /* ===== Commodities Panel ===== */
    #commodities-panel {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10;
        display: none;
        gap: 8px;
        max-width: 90vw;
        overflow-x: auto;
        padding: 0 10px 4px;
    }

    #commodities-panel.visible {
        display: flex;
    }

    .commodity-card {
        background: rgba(10, 14, 39, 0.85);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 12px;
        padding: 10px 14px;
        color: white;
        min-width: 120px;
        flex-shrink: 0;
    }

    .commodity-name {
        font-size: 11px;
        color: rgba(255,255,255,0.5);
        margin-bottom: 4px;
    }

    .commodity-price {
        font-size: 16px;
        font-weight: 700;
        color: white;
        margin-bottom: 2px;
    }

    .commodity-change.up { color: #4ade80; }
    .commodity-change.down { color: #f87171; }

    .commodity-unit {
        font-size: 10px;
        color: rgba(255,255,255,0.3);
        margin-top: 2px;
    }

    /* ===== Top Header Info ===== */
    #globe-header {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        background: linear-gradient(to bottom, rgba(10,14,39,0.9) 0%, transparent 100%);
    }

    #globe-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #globe-title h2 {
        font-size: 18px;
        font-weight: 700;
        color: white;
        letter-spacing: -0.02em;
    }

    #globe-title span {
        font-size: 13px;
        color: rgba(255,255,255,0.5);
    }

    /* ===== Coordinate display ===== */
    #coord-display {
        background: rgba(255,255,255,0.08);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        color: rgba(255,255,255,0.6);
        font-family: monospace;
    }

    /* ===== Loading spinner ===== */
    #globe-loading {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 20;
        background: radial-gradient(ellipse at center, #0a0e27 0%, #020408 100%);
    }

    .globe-spinner {
        width: 60px;
        height: 60px;
        border: 3px solid rgba(59, 130, 246, 0.2);
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 16px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ===== Event markers ===== */
    .event-dot {
        position: absolute;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #f97316;
        border: 2px solid rgba(249,115,22,0.3);
        box-shadow: 0 0 12px rgba(249,115,22,0.7);
        animation: pulse-event 2s ease-in-out infinite;
        transform: translate(-50%, -50%);
        z-index: 5;
    }

    @keyframes pulse-event {
        0%, 100% { box-shadow: 0 0 6px rgba(249,115,22,0.5); }
        50% { box-shadow: 0 0 20px rgba(249,115,22,0.9); }
    }

    /* ===== Wind compass ===== */
    .wind-compass {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        position: relative;
        margin: 8px auto;
    }

    .wind-compass-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 2px solid rgba(99,179,237,0.3);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .wind-arrow {
        width: 3px;
        height: 24px;
        background: linear-gradient(to top, #60a5fa, #3b82f6);
        border-radius: 2px;
        transform-origin: center bottom;
        position: absolute;
        bottom: 50%;
        left: 50%;
        transform: translateX(-50%);
    }

    /* Scrollbar for info panel */
    #info-panel::-webkit-scrollbar { width: 4px; }
    #info-panel::-webkit-scrollbar-track { background: transparent; }
    #info-panel::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }

    /* Close btn */
    .close-btn {
        cursor: pointer;
        padding: 4px;
        border-radius: 6px;
        color: rgba(255,255,255,0.4);
        transition: all 0.2s;
        margin-left: auto;
    }
    .close-btn:hover { color: white; background: rgba(255,255,255,0.1); }
</style>
@endpush

@section('content')
<div id="globe-container">

    {{-- Loading Screen --}}
    <div id="globe-loading">
        <div class="globe-spinner"></div>
        <p class="text-white/60 text-sm font-medium">Memuat Globe 3D...</p>
        <p class="text-white/30 text-xs mt-1">Mengambil data dari NASA & Open-Meteo</p>
    </div>

    {{-- Canvas for Three.js --}}
    <canvas id="globe-canvas"></canvas>

    {{-- Header --}}
    <div id="globe-header">
        <div id="globe-title">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white text-lg">🌍</div>
            <div>
                <h2>World Prediction</h2>
                <span>Simulasi & Prediksi Bumi Real-Time</span>
            </div>
        </div>
        <div id="coord-display">Klik bumi untuk info lokasi</div>
    </div>

    {{-- Layer Controls --}}
    <div id="layer-panel">
        <button class="layer-btn active" data-layer="default" id="btn-default">
            <span class="layer-icon">🌐</span>
            Globe View
        </button>
        <button class="layer-btn" data-layer="weather" id="btn-weather">
            <span class="layer-icon">🌤</span>
            Cuaca
        </button>
        <button class="layer-btn" data-layer="wind" id="btn-wind">
            <span class="layer-icon">💨</span>
            Angin
        </button>
        <button class="layer-btn" data-layer="commodities" id="btn-commodities">
            <span class="layer-icon">📈</span>
            Komoditas
        </button>
        <button class="layer-btn" data-layer="events" id="btn-events">
            <span class="layer-icon">🌋</span>
            Event Alam
        </button>
    </div>

    {{-- Info Panel (right) --}}
    <div id="info-panel">
        <div class="info-card" id="location-card">
            <div class="info-card-header">
                <span>📍</span>
                <span class="info-card-title" id="location-name">Memuat lokasi...</span>
                <span class="close-btn" onclick="closeInfoPanel()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Latitude</span>
                <span class="info-value" id="info-lat">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Longitude</span>
                <span class="info-value" id="info-lon">-</span>
            </div>
        </div>

        <div class="info-card" id="weather-card" style="display:none">
            <div class="info-card-header">
                <span>🌤</span>
                <span class="info-card-title">Data Cuaca</span>
                <span class="info-value text-xs" id="weather-time"></span>
            </div>
            <div id="weather-badge-container"></div>
            <div class="info-row">
                <span class="info-label">Suhu</span>
                <span class="info-value" id="w-temp">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Terasa</span>
                <span class="info-value" id="w-feels">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelembaban</span>
                <span class="info-value" id="w-humidity">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Presipitasi</span>
                <span class="info-value" id="w-precip">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Angin</span>
                <span class="info-value" id="w-wind">-</span>
            </div>
        </div>

        <div class="info-card" id="wind-card" style="display:none">
            <div class="info-card-header">
                <span>💨</span>
                <span class="info-card-title">Data Angin</span>
            </div>
            <div class="wind-compass" id="wind-compass-container">
                <div class="wind-compass-circle">
                    <div class="wind-arrow" id="wind-arrow-el"></div>
                </div>
            </div>
            <div class="info-row">
                <span class="info-label">Kecepatan</span>
                <span class="info-value" id="wind-speed">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Hembusan</span>
                <span class="info-value" id="wind-gust">-</span>
            </div>
            <div class="info-row">
                <span class="info-label">Arah</span>
                <span class="info-value" id="wind-dir">-</span>
            </div>
        </div>
    </div>

    {{-- Commodities Panel (bottom) --}}
    <div id="commodities-panel">
        {{-- Cards populated by JS --}}
    </div>

    {{-- Events overlay dots --}}
    <div id="events-overlay" style="position:absolute;inset:0;z-index:5;pointer-events:none;"></div>

</div>
@endsection

@push('scripts')
{{-- Three.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<script>
// ============================================================
//  WORLD PREDICTION GLOBE - Main Script
// ============================================================

const BASE_URL = window.location.origin;
let currentLat = null;
let currentLon = null;
let currentLayer = 'default';
let nasaEvents = [];

// ============================================================
//  THREE.JS GLOBE SETUP
// ============================================================

const canvas = document.getElementById('globe-canvas');
const container = document.getElementById('globe-container');

// Scene
const scene = new THREE.Scene();

// Camera
const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
camera.position.z = 2.5;

// Renderer
const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
renderer.setSize(container.clientWidth, container.clientHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.setClearColor(0x000000, 0);

// ---- Lights ----
const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
scene.add(ambientLight);

const sunLight = new THREE.DirectionalLight(0xffffff, 1.2);
sunLight.position.set(5, 3, 5);
scene.add(sunLight);

const rimLight = new THREE.DirectionalLight(0x3b82f6, 0.3);
rimLight.position.set(-5, -3, -5);
scene.add(rimLight);

// ---- Globe Geometry ----
const globeRadius = 1;
const globeGeometry = new THREE.SphereGeometry(globeRadius, 64, 64);

// ---- Texture Loader ----
const textureLoader = new THREE.TextureLoader();

// Use a blue marble style globe
const earthTexture = textureLoader.load(
    'https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/textures/planets/earth_atmos_2048.jpg',
    () => { console.log('Earth texture loaded'); },
    undefined,
    () => {
        // Fallback: simple blue sphere if texture fails
        console.warn('Earth texture failed, using fallback.');
    }
);

const earthNormal = textureLoader.load(
    'https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/textures/planets/earth_normal_2048.jpg',
    undefined, undefined, () => {}
);

const earthSpecular = textureLoader.load(
    'https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/textures/planets/earth_specular_2048.jpg',
    undefined, undefined, () => {}
);

const globeMaterial = new THREE.MeshPhongMaterial({
    map: earthTexture,
    normalMap: earthNormal,
    specularMap: earthSpecular,
    specular: new THREE.Color(0x333333),
    shininess: 15,
});

const globe = new THREE.Mesh(globeGeometry, globeMaterial);
scene.add(globe);

// ---- Atmosphere glow ----
const atmosphereGeometry = new THREE.SphereGeometry(globeRadius * 1.02, 64, 64);
const atmosphereMaterial = new THREE.MeshPhongMaterial({
    color: 0x0066ff,
    transparent: true,
    opacity: 0.07,
    side: THREE.FrontSide,
});
const atmosphere = new THREE.Mesh(atmosphereGeometry, atmosphereMaterial);
scene.add(atmosphere);

// ---- Outer glow ring ----
const glowGeometry = new THREE.SphereGeometry(globeRadius * 1.08, 64, 64);
const glowMaterial = new THREE.MeshPhongMaterial({
    color: 0x1a56db,
    transparent: true,
    opacity: 0.04,
    side: THREE.BackSide,
});
const glowMesh = new THREE.Mesh(glowGeometry, glowMaterial);
scene.add(glowMesh);

// ---- Stars background ----
const starsGeometry = new THREE.BufferGeometry();
const starCount = 3000;
const starPositions = new Float32Array(starCount * 3);
for (let i = 0; i < starCount * 3; i++) {
    starPositions[i] = (Math.random() - 0.5) * 200;
}
starsGeometry.setAttribute('position', new THREE.BufferAttribute(starPositions, 3));
const starsMaterial = new THREE.PointsMaterial({ color: 0xffffff, size: 0.2, sizeAttenuation: true });
const stars = new THREE.Points(starsGeometry, starsMaterial);
scene.add(stars);

// ---- Click marker ----
const markerGeometry = new THREE.SphereGeometry(0.015, 8, 8);
const markerMaterial = new THREE.MeshBasicMaterial({ color: 0xef4444 });
const clickMarker = new THREE.Mesh(markerGeometry, markerMaterial);
clickMarker.visible = false;
scene.add(clickMarker);

// ---- Interaction state ----
let isDragging = false;
let previousMouse = { x: 0, y: 0 };
let rotationVelocity = { x: 0, y: 0 };
let targetRotation = { x: globe.rotation.x, y: globe.rotation.y };
let autoRotate = true;

// ---- Mouse/Touch Events ----
canvas.addEventListener('mousedown', (e) => {
    isDragging = false;
    previousMouse = { x: e.clientX, y: e.clientY };
    autoRotate = false;
    canvas.style.cursor = 'grabbing';
});

canvas.addEventListener('mousemove', (e) => {
    const dx = e.clientX - previousMouse.x;
    const dy = e.clientY - previousMouse.y;
    if (e.buttons === 1) {
        isDragging = true;
        const sensitivity = 0.005;
        rotationVelocity.y = dx * sensitivity;
        rotationVelocity.x = dy * sensitivity;
        globe.rotation.y += rotationVelocity.y;
        globe.rotation.x += rotationVelocity.x;
        globe.rotation.x = Math.max(-Math.PI/2, Math.min(Math.PI/2, globe.rotation.x));
        previousMouse = { x: e.clientX, y: e.clientY };
    }
});

canvas.addEventListener('mouseup', (e) => {
    canvas.style.cursor = 'grab';
    if (!isDragging) {
        handleGlobeClick(e);
    }
    setTimeout(() => { autoRotate = true; }, 3000);
});

canvas.addEventListener('wheel', (e) => {
    e.preventDefault();
    const zoomSensitivity = 0.001;
    camera.position.z = Math.max(1.3, Math.min(5, camera.position.z + e.deltaY * zoomSensitivity));
}, { passive: false });

canvas.style.cursor = 'grab';

// ---- Click to get lat/lon ----
function handleGlobeClick(event) {
    const rect = canvas.getBoundingClientRect();
    const mouse = new THREE.Vector2(
        ((event.clientX - rect.left) / rect.width) * 2 - 1,
        -((event.clientY - rect.top) / rect.height) * 2 + 1
    );

    const raycaster = new THREE.Raycaster();
    raycaster.setFromCamera(mouse, camera);
    const intersects = raycaster.intersectObject(globe);

    if (intersects.length > 0) {
        const point = intersects[0].point;
        // Convert 3D point on sphere to lat/lon
        const lat = Math.asin(point.y / globeRadius) * (180 / Math.PI);
        const lon = Math.atan2(-point.z, point.x) * (180 / Math.PI) - globe.rotation.y * (180 / Math.PI);
        const normLon = ((lon + 180) % 360) - 180;

        currentLat = parseFloat(lat.toFixed(4));
        currentLon = parseFloat(normLon.toFixed(4));

        // Place marker
        clickMarker.position.copy(point.clone().normalize().multiplyScalar(globeRadius * 1.02));
        clickMarker.visible = true;

        // Update coord display
        document.getElementById('coord-display').textContent =
            `${currentLat > 0 ? currentLat + '°N' : Math.abs(currentLat) + '°S'}  ${currentLon > 0 ? currentLon + '°E' : Math.abs(currentLon) + '°W'}`;

        // Show info panel
        showInfoPanel(currentLat, currentLon);
    }
}

function showInfoPanel(lat, lon) {
    const panel = document.getElementById('info-panel');
    panel.classList.add('visible');

    document.getElementById('info-lat').textContent = lat + '°';
    document.getElementById('info-lon').textContent = lon + '°';
    document.getElementById('location-name').textContent = getApproxLocationName(lat, lon);

    if (currentLayer === 'weather' || currentLayer === 'default') {
        fetchWeather(lat, lon);
    }
    if (currentLayer === 'wind') {
        fetchWind(lat, lon);
    }
}

function closeInfoPanel() {
    document.getElementById('info-panel').classList.remove('visible');
    clickMarker.visible = false;
}

// ---- Approximate location name ----
function getApproxLocationName(lat, lon) {
    if (lat > 60) return '🧊 Arktik';
    if (lat < -60) return '🧊 Antarktika';
    if (lat > 23 && lat < 65 && lon > -10 && lon < 40) return '🇪🇺 Eropa';
    if (lat > 10 && lat < 55 && lon > 40 && lon < 80) return '🌏 Asia Tengah';
    if (lat > 10 && lat < 55 && lon > 80 && lon < 145) return '🌏 Asia Timur';
    if (lat > -10 && lat < 30 && lon > 60 && lon < 110) return '🌏 Asia Selatan';
    if (lat > -10 && lat < 20 && lon > 95 && lon < 145) return '🌴 Asia Tenggara';
    if (lat > 15 && lat < 55 && lon > -130 && lon < -60) return '🇺🇸 Amerika Utara';
    if (lat > -55 && lat < 15 && lon > -80 && lon < -35) return '🌎 Amerika Selatan';
    if (lat > -35 && lat < 37 && lon > -20 && lon < 55) return '🌍 Afrika';
    if (lat > -45 && lat < -10 && lon > 110 && lon < 155) return '🦘 Australia';
    // Ocean fallback
    return `🌊 Koordinat ${lat.toFixed(2)}, ${lon.toFixed(2)}`;
}

// ============================================================
//  API CALLS
// ============================================================

async function fetchWeather(lat, lon) {
    document.getElementById('weather-card').style.display = 'block';
    document.getElementById('wind-card').style.display = 'none';
    document.getElementById('w-temp').textContent = '⏳ Memuat...';

    try {
        const res = await fetch(`${BASE_URL}/api/world/weather?lat=${lat}&lon=${lon}`);
        const data = await res.json();

        if (data.current) {
            const cur = data.current;
            document.getElementById('w-temp').textContent = cur.temperature_2m + ' °C';
            document.getElementById('w-feels').textContent = cur.apparent_temperature + ' °C';
            document.getElementById('w-humidity').textContent = cur.relative_humidity_2m + ' %';
            document.getElementById('w-precip').textContent = cur.precipitation + ' mm';
            document.getElementById('w-wind').textContent = cur.wind_speed_10m + ' km/h';
            document.getElementById('weather-badge-container').innerHTML =
                `<div class="weather-badge">${getWeatherEmoji(cur.weather_code)} ${getWeatherDesc(cur.weather_code)}</div>`;
        }
    } catch (err) {
        document.getElementById('w-temp').textContent = 'Gagal memuat data';
    }
}

async function fetchWind(lat, lon) {
    document.getElementById('weather-card').style.display = 'none';
    document.getElementById('wind-card').style.display = 'block';
    document.getElementById('wind-speed').textContent = '⏳ Memuat...';

    try {
        const res = await fetch(`${BASE_URL}/api/world/weather?lat=${lat}&lon=${lon}`);
        const data = await res.json();

        if (data.current) {
            const cur = data.current;
            document.getElementById('wind-speed').textContent = cur.wind_speed_10m + ' km/h';
            document.getElementById('wind-gust').textContent = (cur.wind_speed_10m * 1.3).toFixed(1) + ' km/h';
            const dir = cur.wind_direction_10m;
            document.getElementById('wind-dir').textContent = dir + '° ' + degToCompass(dir);

            // Rotate compass arrow
            const arrow = document.getElementById('wind-arrow-el');
            if (arrow) {
                arrow.style.transform = `translateX(-50%) rotate(${dir}deg)`;
            }
        }
    } catch (err) {
        document.getElementById('wind-speed').textContent = 'Gagal memuat data';
    }
}

async function fetchCommodities() {
    const panel = document.getElementById('commodities-panel');
    panel.innerHTML = '<div class="commodity-card"><div class="commodity-name">Memuat...</div></div>';

    try {
        const res = await fetch(`${BASE_URL}/api/world/commodities`);
        const data = await res.json();

        if (data.commodities) {
            panel.innerHTML = data.commodities.map(c => `
                <div class="commodity-card">
                    <div class="commodity-name">${c.name}</div>
                    <div class="commodity-price">$${c.price.toLocaleString()}</div>
                    <div class="commodity-change ${c.trend}">
                        ${c.trend === 'up' ? '▲' : '▼'} ${Math.abs(c.change)}%
                    </div>
                    <div class="commodity-unit">${c.unit}</div>
                </div>
            `).join('');
        }
    } catch (err) {
        panel.innerHTML = '<div class="commodity-card"><div class="commodity-name">Gagal memuat</div></div>';
    }
}

async function fetchNasaEvents() {
    try {
        const res = await fetch(`${BASE_URL}/api/world/events`);
        const data = await res.json();

        nasaEvents = data.events || [];
        renderEventMarkers();
    } catch (err) {
        console.warn('NASA events failed:', err);
    }
}

function renderEventMarkers() {
    // We'll render events as 3D spheres on the globe
    nasaEvents.forEach(event => {
        if (event.geometry && event.geometry.length > 0) {
            const geo = event.geometry[event.geometry.length - 1];
            if (geo.coordinates) {
                const lon = geo.coordinates[0];
                const lat = geo.coordinates[1];
                addEventSphere(lat, lon, event.categories[0]?.id || 'default');
            }
        }
    });
}

function addEventSphere(lat, lon, categoryId) {
    const colors = {
        'wildfires': 0xff4500,
        'severeStorms': 0xfbbf24,
        'volcanoes': 0xef4444,
        'earthquakes': 0xa78bfa,
        'floods': 0x60a5fa,
        'default': 0xf97316,
    };

    const color = colors[categoryId] || colors.default;
    const phi = (90 - lat) * (Math.PI / 180);
    const theta = (lon + 180) * (Math.PI / 180);

    const x = -(globeRadius * 1.02) * Math.sin(phi) * Math.cos(theta);
    const y = (globeRadius * 1.02) * Math.cos(phi);
    const z = (globeRadius * 1.02) * Math.sin(phi) * Math.sin(theta);

    const dotGeo = new THREE.SphereGeometry(0.012, 6, 6);
    const dotMat = new THREE.MeshBasicMaterial({ color });
    const dot = new THREE.Mesh(dotGeo, dotMat);
    dot.position.set(x, y, z);
    dot.userData.isEvent = true;
    scene.add(dot);
}

function removeEventSpheres() {
    const toRemove = scene.children.filter(c => c.userData.isEvent);
    toRemove.forEach(c => scene.remove(c));
}

// ============================================================
//  WEATHER HELPERS
// ============================================================

function getWeatherEmoji(code) {
    if (code === 0) return '☀️';
    if (code <= 3) return '⛅';
    if (code <= 48) return '🌫️';
    if (code <= 67) return '🌧️';
    if (code <= 77) return '🌨️';
    if (code <= 82) return '🌦️';
    if (code <= 99) return '⛈️';
    return '🌤️';
}

function getWeatherDesc(code) {
    if (code === 0) return 'Cerah';
    if (code <= 2) return 'Berawan Sebagian';
    if (code === 3) return 'Mendung';
    if (code <= 48) return 'Berkabut';
    if (code <= 55) return 'Gerimis';
    if (code <= 67) return 'Hujan';
    if (code <= 77) return 'Salju';
    if (code <= 82) return 'Hujan Lebat';
    if (code <= 99) return 'Badai Petir';
    return 'Tidak Diketahui';
}

function degToCompass(deg) {
    const dirs = ['U', 'TL', 'T', 'TG', 'S', 'BD', 'B', 'BL'];
    return dirs[Math.round(deg / 45) % 8];
}

// ============================================================
//  LAYER CONTROLS
// ============================================================

document.querySelectorAll('.layer-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.layer-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        currentLayer = btn.dataset.layer;
        handleLayerChange(currentLayer);
    });
});

function handleLayerChange(layer) {
    // Reset panels
    document.getElementById('commodities-panel').classList.remove('visible');
    removeEventSpheres();

    // Hide weather/wind cards by default
    document.getElementById('weather-card').style.display = 'none';
    document.getElementById('wind-card').style.display = 'none';

    switch (layer) {
        case 'default':
            globeMaterial.color.setHex(0xffffff);
            break;
        case 'weather':
            globeMaterial.color.setHex(0xffffff);
            if (currentLat !== null) {
                fetchWeather(currentLat, currentLon);
            } else {
                document.getElementById('info-panel').classList.add('visible');
                document.getElementById('weather-card').style.display = 'block';
                document.getElementById('location-name').textContent = 'Klik wilayah di bumi';
                document.getElementById('w-temp').textContent = '-';
            }
            break;
        case 'wind':
            globeMaterial.color.setHex(0xffffff);
            if (currentLat !== null) {
                document.getElementById('info-panel').classList.add('visible');
                fetchWind(currentLat, currentLon);
            } else {
                document.getElementById('info-panel').classList.add('visible');
                document.getElementById('wind-card').style.display = 'block';
                document.getElementById('location-name').textContent = 'Klik wilayah di bumi';
                document.getElementById('wind-speed').textContent = '-';
            }
            break;
        case 'commodities':
            globeMaterial.color.setHex(0xffffff);
            document.getElementById('commodities-panel').classList.add('visible');
            fetchCommodities();
            break;
        case 'events':
            globeMaterial.color.setHex(0xffffff);
            fetchNasaEvents();
            break;
    }
}

// ============================================================
//  ANIMATION LOOP
// ============================================================

const clock = new THREE.Clock();

function animate() {
    requestAnimationFrame(animate);

    const delta = clock.getDelta();

    // Auto rotate
    if (autoRotate) {
        globe.rotation.y += 0.0008;
        atmosphere.rotation.y += 0.0006;
    } else {
        // Apply momentum
        globe.rotation.y += rotationVelocity.y;
        globe.rotation.x += rotationVelocity.x;
        globe.rotation.x = Math.max(-Math.PI/2, Math.min(Math.PI/2, globe.rotation.x));
        rotationVelocity.x *= 0.95;
        rotationVelocity.y *= 0.95;
        atmosphere.rotation.copy(globe.rotation);
    }

    // Sync event spheres with globe rotation
    scene.children
        .filter(c => c.userData.isEvent)
        .forEach(c => {
            // Event dots are attached to scene but conceptually on globe — keep them synced
        });

    renderer.render(scene, camera);
}

// ============================================================
//  RESIZE HANDLER
// ============================================================

window.addEventListener('resize', () => {
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});

// ============================================================
//  INIT
// ============================================================

// Hide loading when textures are ready
function hideLoading() {
    const loading = document.getElementById('globe-loading');
    loading.style.transition = 'opacity 0.5s ease';
    loading.style.opacity = '0';
    setTimeout(() => { loading.style.display = 'none'; }, 500);
}

// Start rendering
animate();

// Wait a bit for textures then hide loading
setTimeout(hideLoading, 1500);
</script>
@endpush
