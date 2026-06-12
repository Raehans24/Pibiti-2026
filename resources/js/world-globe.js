/**
 * World Prediction Globe
 *
 * Handles the interactive 3D Earth globe using Three.js.
 * Responsibilities:
 *  - Scene setup (globe, atmosphere, stars, lighting)
 *  - Mouse/wheel interaction (drag, zoom, click-to-inspect)
 *  - Layer switching (weather, wind, commodities, events)
 *  - Fetching data from backend API endpoints
 */

import * as THREE from 'three';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';

// ── Constants ────────────────────────────────────────────────
const BASE_URL     = window.location.origin;
const GLOBE_RADIUS = 1;

// ── State ────────────────────────────────────────────────────
let currentLat   = null;
let currentLon   = null;
let currentLayer = 'default';
let nasaEvents   = [];
let isDragging   = false;
let autoRotate   = true;

const previousMouse    = { x: 0, y: 0 };
const rotationVelocity = { x: 0, y: 0 };

// ── DOM Refs ─────────────────────────────────────────────────
const canvas    = document.getElementById('globe-canvas');
const container = document.getElementById('globe-container');

if (!canvas || !container) {
    // Not on the globe page; do nothing.
    throw new Error('Globe elements not found — skipping world-globe init.');
}

// ── Scene Setup ──────────────────────────────────────────────
const scene    = new THREE.Scene();
const camera   = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
camera.position.z = 2.5;

const renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true });
renderer.setSize(container.clientWidth, container.clientHeight);
renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
renderer.setClearColor(0x000000, 0);

// ── Lighting ─────────────────────────────────────────────────
const ambientLight = new THREE.AmbientLight(0xffffff, 0.3); // Lowered for more contrast
scene.add(ambientLight);

const sunLight = new THREE.DirectionalLight(0xffffff, 1.8);
sunLight.position.set(5, 3, 5);
scene.add(sunLight);

const rimLight = new THREE.DirectionalLight(0x3b82f6, 0.6);
rimLight.position.set(-5, -3, -5);
scene.add(rimLight);

// ── Post-Processing (Bloom) ──────────────────────────────────
const renderScene = new RenderPass(scene, camera);
const bloomPass = new UnrealBloomPass(new THREE.Vector2(container.clientWidth, container.clientHeight), 1.5, 0.4, 0.85);
bloomPass.threshold = 0.15;
bloomPass.strength = 0.4;
bloomPass.radius = 0.5;

const composer = new EffectComposer(renderer);
composer.addPass(renderScene);
composer.addPass(bloomPass);

// Handle resize for composer
window.addEventListener('resize', () => {
    composer.setSize(container.clientWidth, container.clientHeight);
});

// ── Globe ────────────────────────────────────────────────────
const textureLoader = new THREE.TextureLoader();

const [earthTexture, earthNormal, earthSpecular, earthDisplacement, cloudTexture] = [
    'https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/textures/planets/earth_atmos_2048.jpg',
    'https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/textures/planets/earth_normal_2048.jpg',
    'https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/textures/planets/earth_specular_2048.jpg',
    'https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/textures/planets/earth_normal_2048.jpg', // Using normal as bump/displacement for extra relief
    'https://raw.githubusercontent.com/mrdoob/three.js/dev/examples/textures/planets/earth_clouds_1024.png',
].map(url => textureLoader.load(url, undefined, undefined, () => {}));

const globeMaterial = new THREE.MeshPhongMaterial({
    map: earthTexture,
    normalMap: earthNormal,
    normalScale: new THREE.Vector2(0.85, 0.85),
    specularMap: earthSpecular,
    specular: new THREE.Color(0x333333),
    shininess: 25,
    displacementMap: earthDisplacement,
    displacementScale: 0.015,
});

const globe = new THREE.Mesh(new THREE.SphereGeometry(GLOBE_RADIUS, 128, 128), globeMaterial);
scene.add(globe);

// ── Atmosphere & Clouds ──────────────────────────────────────
const cloudsMaterial = new THREE.MeshLambertMaterial({
    map: cloudTexture,
    transparent: true,
    opacity: 0.6,
    blending: THREE.AdditiveBlending,
    side: THREE.DoubleSide
});
const clouds = new THREE.Mesh(new THREE.SphereGeometry(GLOBE_RADIUS * 1.006, 128, 128), cloudsMaterial);
scene.add(clouds);

const atmosphere = new THREE.Mesh(
    new THREE.SphereGeometry(GLOBE_RADIUS * 1.025, 64, 64),
    new THREE.MeshPhongMaterial({ color: 0x0066ff, transparent: true, opacity: 0.08, side: THREE.FrontSide, blending: THREE.AdditiveBlending }),
);
scene.add(atmosphere);

const glowMesh = new THREE.Mesh(
    new THREE.SphereGeometry(GLOBE_RADIUS * 1.09, 64, 64),
    new THREE.MeshPhongMaterial({ color: 0x1a56db, transparent: true, opacity: 0.05, side: THREE.BackSide }),
);
scene.add(glowMesh);

// ── Stars ────────────────────────────────────────────────────
const starPositions = new Float32Array(4000 * 3).map(() => (Math.random() - 0.5) * 200);
const starsGeo = new THREE.BufferGeometry();
starsGeo.setAttribute('position', new THREE.BufferAttribute(starPositions, 3));
scene.add(new THREE.Points(starsGeo, new THREE.PointsMaterial({ color: 0xffffff, size: 0.15, sizeAttenuation: true, transparent: true, opacity: 0.8 })));

// ── Click marker (Pulsing Ring) ──────────────────────────────
const clickMarker = new THREE.Group();
const innerDot = new THREE.Mesh(
    new THREE.SphereGeometry(0.005, 16, 16),
    new THREE.MeshBasicMaterial({ color: 0xffffff })
);
const pulseRing = new THREE.Mesh(
    new THREE.RingGeometry(0.006, 0.015, 32),
    new THREE.MeshBasicMaterial({ color: 0x3b82f6, transparent: true, opacity: 0.8, side: THREE.DoubleSide })
);
clickMarker.add(innerDot);
clickMarker.add(pulseRing);
clickMarker.visible = false;
scene.add(clickMarker);

let pulseTime = 0;

// ── Camera Lerping Target ────────────────────────────────────
let targetCameraZ = 2.5;

// ── Mouse Interaction ────────────────────────────────────────
canvas.style.cursor = 'grab';

canvas.addEventListener('mousedown', e => {
    isDragging = false;
    previousMouse.x = e.clientX;
    previousMouse.y = e.clientY;
    autoRotate = false;
    canvas.style.cursor = 'grabbing';
});

canvas.addEventListener('mousemove', e => {
    if (e.buttons !== 1) { return; }
    isDragging = true;
    const sensitivity = 0.005;
    rotationVelocity.y = (e.clientX - previousMouse.x) * sensitivity;
    rotationVelocity.x = (e.clientY - previousMouse.y) * sensitivity;
    globe.rotation.y += rotationVelocity.y;
    globe.rotation.x = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, globe.rotation.x + rotationVelocity.x));
    previousMouse.x = e.clientX;
    previousMouse.y = e.clientY;
});

canvas.addEventListener('mouseup', e => {
    canvas.style.cursor = 'grab';
    if (!isDragging) { handleGlobeClick(e); }
    setTimeout(() => { autoRotate = true; }, 5000);
});

canvas.addEventListener('wheel', e => {
    e.preventDefault();
    targetCameraZ = Math.max(1.03, Math.min(5, targetCameraZ + e.deltaY * 0.001));
    autoRotate = false;
}, { passive: false });

// ── Click → lat/lon ──────────────────────────────────────────
function handleGlobeClick(event) {
    const rect   = canvas.getBoundingClientRect();
    const mouse  = new THREE.Vector2(
        ((event.clientX - rect.left) / rect.width) * 2 - 1,
        -((event.clientY - rect.top) / rect.height) * 2 + 1,
    );

    const raycaster = new THREE.Raycaster();
    raycaster.setFromCamera(mouse, camera);
    const hits = raycaster.intersectObject(globe);

    if (hits.length === 0) { return; }

    const point  = hits[0].point;
    const lat    = Math.asin(point.y / GLOBE_RADIUS) * (180 / Math.PI);
    const rawLon = Math.atan2(-point.z, point.x) * (180 / Math.PI) - globe.rotation.y * (180 / Math.PI);
    const lon    = ((rawLon + 180) % 360) - 180;

    currentLat = parseFloat(lat.toFixed(4));
    currentLon = parseFloat(lon.toFixed(4));

    // Calculate rotation to center the clicked point
    const targetRotX = lat * (Math.PI / 180);
    const targetRotY = -lon * (Math.PI / 180);
    
    // Set rotation velocities for smooth pan to target
    rotationVelocity.x = (targetRotX - globe.rotation.x) * 0.05;
    
    // Ensure shortest path for Y rotation
    let dy = targetRotY - globe.rotation.y;
    while(dy > Math.PI) dy -= Math.PI * 2;
    while(dy < -Math.PI) dy += Math.PI * 2;
    rotationVelocity.y = dy * 0.05;

    // Zoom in
    targetCameraZ = 1.25;

    // Place marker
    clickMarker.position.copy(point.clone().normalize().multiplyScalar(GLOBE_RADIUS * 1.01));
    clickMarker.lookAt(point.clone().multiplyScalar(2)); // make ring face outward
    clickMarker.visible = true;

    document.getElementById('coord-display').textContent = formatCoords(currentLat, currentLon);
    showInfoPanel(currentLat, currentLon);
}

function formatCoords(lat, lon) {
    const latStr = `${Math.abs(lat)}°${lat >= 0 ? 'N' : 'S'}`;
    const lonStr = `${Math.abs(lon)}°${lon >= 0 ? 'E' : 'W'}`;
    return `${latStr}  ${lonStr}`;
}

// ── Info Panel ───────────────────────────────────────────────
function showInfoPanel(lat, lon) {
    document.getElementById('info-panel').classList.add('visible');
    document.getElementById('info-lat').textContent = `${lat}°`;
    document.getElementById('info-lon').textContent = `${lon}°`;
    document.getElementById('location-name').textContent = getApproxRegion(lat, lon);

    if (currentLayer === 'weather' || currentLayer === 'default') { fetchWeather(lat, lon); }
    if (currentLayer === 'wind') { fetchWind(lat, lon); }
}

window.closeInfoPanel = function () {
    document.getElementById('info-panel').classList.remove('visible');
    clickMarker.visible = false;
};

// ── Region Detection ─────────────────────────────────────────
function getApproxRegion(lat, lon) {
    const regions = [
        [lat > 60,                                                             '🧊 Arktik'],
        [lat < -60,                                                            '🧊 Antarktika'],
        [lat > 23 && lat < 65 && lon > -10 && lon < 40,                       '🇪🇺 Eropa'],
        [lat > 10 && lat < 55 && lon > 40  && lon < 80,                       '🌏 Asia Tengah'],
        [lat > 10 && lat < 55 && lon > 80  && lon < 145,                      '🌏 Asia Timur'],
        [lat > -10 && lat < 30 && lon > 60  && lon < 110,                     '🌏 Asia Selatan'],
        [lat > -10 && lat < 20 && lon > 95  && lon < 145,                     '🌴 Asia Tenggara'],
        [lat > 15 && lat < 55 && lon > -130 && lon < -60,                     '🇺🇸 Amerika Utara'],
        [lat > -55 && lat < 15 && lon > -80  && lon < -35,                    '🌎 Amerika Selatan'],
        [lat > -35 && lat < 37 && lon > -20  && lon < 55,                     '🌍 Afrika'],
        [lat > -45 && lat < -10 && lon > 110 && lon < 155,                    '🦘 Australia'],
    ];

    const match = regions.find(([condition]) => condition);
    return match ? match[1] : `🌊 ${lat.toFixed(2)}, ${lon.toFixed(2)}`;
}

// ── API Fetches ──────────────────────────────────────────────
async function fetchWeather(lat, lon) {
    setCard('weather');
    setText('w-temp', '⏳ Memuat...');

    try {
        const { current } = await apiFetch(`/api/world/weather?lat=${lat}&lon=${lon}`);
        if (!current) { return; }

        setText('w-temp',     `${current.temperature_2m} °C`);
        setText('w-feels',    `${current.apparent_temperature} °C`);
        setText('w-humidity', `${current.relative_humidity_2m} %`);
        setText('w-precip',   `${current.precipitation} mm`);
        setText('w-wind',     `${current.wind_speed_10m} km/h`);

        document.getElementById('weather-badge-container').innerHTML =
            `<div class="weather-badge">${weatherEmoji(current.weather_code)} ${weatherDesc(current.weather_code)}</div>`;
    } catch {
        setText('w-temp', 'Gagal memuat data');
    }
}

async function fetchWind(lat, lon) {
    setCard('wind');
    setText('wind-speed', '⏳ Memuat...');

    try {
        const { current } = await apiFetch(`/api/world/weather?lat=${lat}&lon=${lon}`);
        if (!current) { return; }

        const dir = current.wind_direction_10m;
        setText('wind-speed', `${current.wind_speed_10m} km/h`);
        setText('wind-gust',  `${(current.wind_speed_10m * 1.3).toFixed(1)} km/h`);
        setText('wind-dir',   `${dir}° ${degToCompass(dir)}`);

        const arrow = document.getElementById('wind-arrow-el');
        if (arrow) { arrow.style.transform = `translateX(-50%) rotate(${dir}deg)`; }
    } catch {
        setText('wind-speed', 'Gagal memuat data');
    }
}

async function fetchCommodities() {
    const panel = document.getElementById('commodities-panel');
    panel.innerHTML = '<div class="commodity-card"><div class="commodity-name">Memuat...</div></div>';

    try {
        const { commodities } = await apiFetch('/api/world/commodities');
        if (!commodities) { return; }

        panel.innerHTML = commodities.map(c => `
            <div class="commodity-card">
                <div class="commodity-name">${c.name}</div>
                <div class="commodity-price">$${c.price.toLocaleString()}</div>
                <div class="commodity-change ${c.trend}">${c.trend === 'up' ? '▲' : '▼'} ${Math.abs(c.change)}%</div>
                <div class="commodity-unit">${c.unit}</div>
            </div>
        `).join('');
    } catch {
        panel.innerHTML = '<div class="commodity-card"><div class="commodity-name">Gagal memuat</div></div>';
    }
}

async function fetchNasaEvents() {
    try {
        const { events = [] } = await apiFetch('/api/world/events');
        nasaEvents = events;
        renderEventSpheres();
    } catch (err) {
        console.warn('NASA events failed:', err);
    }
}

// ── Event Spheres ────────────────────────────────────────────
const EVENT_COLORS = {
    wildfires: 0xff4500, severeStorms: 0xfbbf24,
    volcanoes: 0xef4444, earthquakes: 0xa78bfa,
    floods: 0x60a5fa,    default: 0xf97316,
};

function renderEventSpheres() {
    nasaEvents.forEach(event => {
        const geo = event.geometry?.at(-1);
        if (!geo?.coordinates) { return; }
        const [lon, lat] = geo.coordinates;
        const categoryId = event.categories?.[0]?.id ?? 'default';
        addEventSphere(lat, lon, categoryId);
    });
}

function addEventSphere(lat, lon, categoryId) {
    const color = EVENT_COLORS[categoryId] ?? EVENT_COLORS.default;
    const phi   = (90 - lat) * (Math.PI / 180);
    const theta = (lon + 180) * (Math.PI / 180);
    const r     = GLOBE_RADIUS * 1.02;

    const dot = new THREE.Mesh(
        new THREE.SphereGeometry(0.012, 6, 6),
        new THREE.MeshBasicMaterial({ color }),
    );
    dot.position.set(-r * Math.sin(phi) * Math.cos(theta), r * Math.cos(phi), r * Math.sin(phi) * Math.sin(theta));
    dot.userData.isEvent = true;
    scene.add(dot);
}

function removeEventSpheres() {
    scene.children.filter(c => c.userData.isEvent).forEach(c => scene.remove(c));
}

// ── Layer Switching ──────────────────────────────────────────
document.querySelectorAll('.layer-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.layer-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentLayer = btn.dataset.layer;
        handleLayerChange(currentLayer);
    });
});

function handleLayerChange(layer) {
    document.getElementById('commodities-panel').classList.remove('visible');
    removeEventSpheres();
    hideAllCards();

    const prompt = label => {
        document.getElementById('info-panel').classList.add('visible');
        document.getElementById(label).style.display = 'block';
        document.getElementById('location-name').textContent = 'Klik wilayah di bumi';
    };

    switch (layer) {
        case 'weather':
            currentLat !== null ? fetchWeather(currentLat, currentLon) : prompt('weather-card');
            break;
        case 'wind':
            currentLat !== null ? fetchWind(currentLat, currentLon) : prompt('wind-card');
            break;
        case 'commodities':
            document.getElementById('commodities-panel').classList.add('visible');
            fetchCommodities();
            break;
        case 'events':
            fetchNasaEvents();
            break;
    }
}

// ── Weather Helpers ──────────────────────────────────────────
function weatherEmoji(code) {
    if (code === 0) { return '☀️'; }
    if (code <= 3)  { return '⛅'; }
    if (code <= 48) { return '🌫️'; }
    if (code <= 67) { return '🌧️'; }
    if (code <= 77) { return '🌨️'; }
    if (code <= 82) { return '🌦️'; }
    if (code <= 99) { return '⛈️'; }
    return '🌤️';
}

function weatherDesc(code) {
    if (code === 0) { return 'Cerah'; }
    if (code <= 2)  { return 'Berawan Sebagian'; }
    if (code === 3) { return 'Mendung'; }
    if (code <= 48) { return 'Berkabut'; }
    if (code <= 55) { return 'Gerimis'; }
    if (code <= 67) { return 'Hujan'; }
    if (code <= 77) { return 'Salju'; }
    if (code <= 82) { return 'Hujan Lebat'; }
    if (code <= 99) { return 'Badai Petir'; }
    return 'Tidak Diketahui';
}

function degToCompass(deg) {
    return ['U', 'TL', 'T', 'TG', 'S', 'BD', 'B', 'BL'][Math.round(deg / 45) % 8];
}

// ── Utilities ────────────────────────────────────────────────
async function apiFetch(path) {
    const res = await fetch(`${BASE_URL}${path}`);
    return res.json();
}

function setText(id, text) {
    const el = document.getElementById(id);
    if (el) { el.textContent = text; }
}

function setCard(type) {
    hideAllCards();
    document.getElementById(`${type}-card`).style.display = 'block';
}

function hideAllCards() {
    ['weather-card', 'wind-card'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.style.display = 'none'; }
    });
}

// ── Animation Loop ───────────────────────────────────────────
const clock = new THREE.Clock();

function animate() {
    requestAnimationFrame(animate);
    const delta = clock.getDelta(); // keep clock ticking

    // Camera Zoom Lerp
    camera.position.z += (targetCameraZ - camera.position.z) * 0.1;

    // Cloud rotation (independent)
    clouds.rotation.y += 0.0003;

    // Twinkling stars
    const positions = starsGeo.attributes.position.array;
    for (let i = 0; i < positions.length; i += 3) {
        if (Math.random() > 0.99) {
            positions[i + 1] += (Math.random() - 0.5) * 0.5; // subtle movement
        }
    }
    starsGeo.attributes.position.needsUpdate = true;

    // Pulse animation
    if (clickMarker.visible) {
        pulseTime += delta * 3;
        const scale = 1 + Math.sin(pulseTime) * 0.5;
        pulseRing.scale.set(scale, scale, scale);
        pulseRing.material.opacity = 0.8 - (scale - 0.5) * 0.5;
    }

    if (autoRotate) {
        globe.rotation.y      += 0.0008;
        atmosphere.rotation.y += 0.0006;
    } else {
        globe.rotation.y += rotationVelocity.y;
        globe.rotation.x  = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, globe.rotation.x + rotationVelocity.x));
        rotationVelocity.x *= 0.95;
        rotationVelocity.y *= 0.95;
        atmosphere.rotation.copy(globe.rotation);
    }

    composer.render(); // Use composer for bloom instead of renderer.render()
}

// ── Resize Handler ───────────────────────────────────────────
window.addEventListener('resize', () => {
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});

// ── Init ─────────────────────────────────────────────────────
animate();

setTimeout(() => {
    const loading = document.getElementById('globe-loading');
    if (!loading) { return; }
    loading.style.transition = 'opacity 0.5s ease';
    loading.style.opacity = '0';
    setTimeout(() => { loading.style.display = 'none'; }, 500);
}, 1500);
