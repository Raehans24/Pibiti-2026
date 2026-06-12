@extends('layouts.app')

@push('scripts')
    @vite('resources/js/world-globe.js')
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
