<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hasil TripMate - AI Travel Planner</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Leaflet CSS & JS for Interactive Route Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .result-container { width: 100%; max-width: 1150px; margin: 0 auto; padding: 55px 25px 70px; }
        
        .result-header { margin-bottom: 35px; }
        .result-badge { 
            display: inline-block; 
            background: var(--bg-color); 
            box-shadow: var(--neu-shadow-sm);
            border-radius: 30px; 
            padding: 9px 17px; 
            font-size: 14px; 
            font-weight: 600;
            margin-bottom: 18px; 
        }
        .result-header h1 { margin: 0 0 10px; font-size: 42px; line-height: 1.15; text-shadow: 2px 2px 4px rgba(0,0,0,0.05); }
        .result-header p { margin: 0; color: var(--text-muted); font-size: 17px; }
        
        .destination-card, .day-card, .budget-card, .empty-result { 
            background: var(--glass-bg); 
            backdrop-filter: var(--glass-blur); 
            -webkit-backdrop-filter: var(--glass-blur);
            border-radius: 24px; 
            padding: 32px; 
            margin-bottom: 38px; 
            border: 1px solid var(--glass-border);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }
        
        .destination-title { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }
        .location-icon { width: 52px; height: 52px; border-radius: 16px; background: var(--bg-color); box-shadow: var(--neu-shadow-sm); display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
        .destination-title h2 { margin: 0; font-size: 25px; line-height: 1.3; }
        .destination-title p { margin: 5px 0 0; color: var(--text-muted); font-size: 13px; }
        
        .trip-info { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 22px; }
        .info-box { background: var(--bg-color); box-shadow: var(--neu-inset-sm); border-radius: 14px; padding: 16px; }
        .info-label { display: block; color: var(--text-muted); font-size: 12px; margin-bottom: 7px; font-weight: 600; }
        .info-value { display: block; font-size: 14px; font-weight: 700; }
        
        .category-title { font-size: 13px; font-weight: 800; margin-bottom: 10px; }
        .category-list { display: flex; flex-wrap: wrap; gap: 10px; }
        .category-badge { background: var(--bg-color); box-shadow: var(--neu-shadow-sm); border-radius: 30px; padding: 8px 15px; font-size: 12px; font-weight: 700; transition: all 0.3s ease; }
        .category-badge:hover { box-shadow: var(--neu-inset-sm); }
        
        .ai-section { margin-top: 10px; }
        .ai-section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .ai-section-header h2 { margin: 0; font-size: 25px; }
        .duration-badge { background: var(--bg-color); box-shadow: var(--neu-shadow-sm); border-radius: 30px; padding: 9px 15px; font-size: 13px; font-weight: 700; }
        
        .day-card { margin-bottom: 25px; padding: 28px; }
        .day-header { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .day-number { background: var(--bg-color); box-shadow: var(--neu-shadow-sm); border-radius: 12px; padding: 10px 14px; font-size: 13px; font-weight: 800; }
        .day-header h3 { margin: 0; font-size: 18px; }
        .day-date { color: var(--text-muted); font-size: 14px; margin-top: 4px; }
        
        .activity-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .activity-card { background: var(--bg-color); box-shadow: var(--neu-shadow-sm); border-radius: 16px; padding: 20px; transition: all 0.3s ease; border: none; }
        .activity-card:hover { box-shadow: var(--neu-shadow); transform: translateY(-3px); }
        .activity-category { font-size: 13px; font-weight: 700; margin-bottom: 10px; color: #4a5568; }
        .activity-name { display: block; font-size: 17px; font-weight: 800; line-height: 1.4; margin-bottom: 8px; }
        .activity-description { color: var(--text-muted); font-size: 14px; line-height: 1.6; }
        
        .budget-section { margin-top: 32px; }
        .budget-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .budget-title-wrapper h2 { margin: 0; font-size: 19px; }
        .budget-title-wrapper p { margin: 5px 0 0; color: var(--text-muted); font-size: 12px; }
        
        .budget-grid { display: grid; grid-template-columns: 1fr repeat(5, 1fr); gap: 15px; }
        .budget-item { min-height: 90px; background: var(--bg-color); box-shadow: var(--neu-inset-sm); border-radius: 14px; padding: 15px; display: flex; flex-direction: column; justify-content: space-between; }
        .budget-item.total { box-shadow: var(--neu-shadow); background: #2d3748; color: #fff; }
        .budget-item.total .budget-total-label { color: #a0aec0; }
        
        .budget-icon { font-size: 18px; margin-bottom: 8px; }
        .budget-item-name { font-size: 12px; font-weight: 700; margin-bottom: 5px; }
        .budget-amount { font-size: 14px; font-weight: 800; }
        .budget-total-label { font-size: 12px; color: var(--text-muted); margin-bottom: 5px; }
        .budget-total-value { font-size: 20px; font-weight: 800; }
        
        .budget-status { margin-top: 20px; padding: 15px; border-radius: 14px; background: var(--bg-color); box-shadow: var(--neu-inset-sm); font-size: 14px; font-weight: 700; line-height: 1.8; }
        .budget-note { margin-top: 15px; color: var(--text-muted); font-size: 12px; line-height: 1.6; }
        
        .bottom-action { text-align: center; margin-top: 35px; }
        .back-button { display: inline-block; background: var(--bg-color); box-shadow: var(--neu-shadow); color: var(--text-main); text-decoration: none; border-radius: 30px; padding: 12px 24px; font-size: 14px; font-weight: 700; transition: all 0.3s ease; }
        .back-button:hover { box-shadow: var(--neu-shadow-sm); transform: translateY(2px); }
        .back-button:active { box-shadow: var(--neu-inset); }
        
        @media (max-width: 900px) {
            .activity-list { grid-template-columns: 1fr; }
            .budget-grid { grid-template-columns: repeat(2, 1fr); }
            .trip-info { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .result-container { padding: 35px 16px 50px; }
            .result-header h1 { font-size: 31px; }
            .destination-card, .day-card, .budget-card { padding: 20px; }
            .budget-grid { grid-template-columns: 1fr; }
            .ai-section-header { align-items: flex-start; gap: 10px; flex-direction: column; }
            .trip-info { grid-template-columns: 1fr; }
        }
    </style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<header class="navbar">

    <div class="logo">
        ✈️ TripMate
    </div>

    <nav>

        <a href="{{ url('/') }}">
            Home
        </a>

        <a href="{{ route('travel.history') }}">
            My Trips
        </a>

    </nav>

</header>


<main class="result-container">


<!-- =========================
     HEADER
========================= -->

<div class="result-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 15px;">

    <div>
        <div class="result-badge">
            ✨ AI Travel Planner
        </div>

        <h1>
            Rencana Perjalananmu
        </h1>

        <p>
            TripMate menyusun rekomendasi berdasarkan
            destinasi, tanggal, kategori, dan budget kamu.
        </p>
    </div>

    <div style="align-self: center; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        @if(isset($savedTripId))
            <a id="btn-pdf-link" href="{{ route('travel.pdf', $savedTripId) }}" target="_blank" style="background: rgba(20, 184, 166, 0.15); border: 1px solid rgba(20, 184, 166, 0.4); color: #0d9488; padding: 10px 18px; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                📄 Export PDF
            </a>
            <div id="saved-status-badge" style="background: rgba(20, 184, 166, 0.15); border: 1px solid rgba(20, 184, 166, 0.4); color: #0d9488; padding: 10px 18px; border-radius: 30px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                ✅ Perjalanan Tersimpan (#{{ $savedTripId }})
            </div>
        @else
            <button id="btn-save-trip" onclick="saveTrip()" style="background: linear-gradient(135deg, #6366f1, #4f46e5); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3); transition: all 0.2s ease;">
                💾 Simpan Perjalanan
            </button>
            <a id="btn-pdf-link" href="#" target="_blank" style="display: none; background: rgba(20, 184, 166, 0.15); border: 1px solid rgba(20, 184, 166, 0.4); color: #0d9488; padding: 10px 18px; border-radius: 12px; font-weight: 700; text-decoration: none; align-items: center; gap: 8px;">
                📄 Export PDF
            </a>
            <div id="saved-status-badge" style="display: none; background: rgba(20, 184, 166, 0.15); border: 1px solid rgba(20, 184, 166, 0.4); color: #0d9488; padding: 10px 18px; border-radius: 30px; font-weight: 700; align-items: center; gap: 8px;">
                ✅ Perjalanan Tersimpan
            </div>
        @endif
    </div>


</div>




<!-- =========================
     DESTINATION
========================= -->

<div class="destination-card">

    <div class="destination-title">

        <div class="location-icon">
            📍
        </div>

        <div>

            <h2>
                {{ $destination['display_name'] ?? $trip['destination'] }}
            </h2>

            <p>
                Destinasi perjalanan
            </p>

        </div>

    </div>


    <div class="trip-info">


        <!-- TANGGAL -->

        <div class="info-box">

            <span class="info-label">
                📅 Tanggal
            </span>

            <span class="info-value">

                {{ $trip['start_date'] }}

                —

                {{ $trip['end_date'] }}

            </span>

        </div>


        <!-- PESERTA -->

        <div class="info-box">

            <span class="info-label">
                👥 Rombongan
            </span>

            <span class="info-value">

                {{ $trip['jumlah_orang'] ?? 1 }} Orang

            </span>

        </div>


        <!-- BUDGET -->

        <div class="info-box">

            <span class="info-label">
                💰 Budget
            </span>

            <span class="info-value">

                Rp {{ number_format($trip['budget'], 0, ',', '.') }}

            </span>

        </div>


        <!-- DURASI -->

        <div class="info-box">

            <span class="info-label">
                🗺️ Durasi
            </span>

            <span class="info-value">

                {{ $aiResult['jumlah_hari'] ?? 1 }}

                hari

            </span>

        </div>


    </div>


    <!-- KATEGORI -->

    <div class="category-title">
        🎯 Kategori wisata
    </div>


    <div class="category-list">

        @foreach($trip['preferences'] ?? [] as $preference)

            <div class="category-badge">

                @if($preference === 'pantai')

                    🏖️ Pantai

                @elseif($preference === 'kuliner')

                    🍜 Kuliner

                @elseif($preference === 'alam')

                    🌿 Alam

                @elseif($preference === 'budaya')

                    🏛️ Budaya

                @elseif($preference === 'hiburan')

                    🎡 Hiburan

                @else

                    📍 {{ ucfirst($preference) }}

                @endif

            </div>

        @endforeach

</div>

<!-- =========================
     MAP ROUTE SECTION
========================= -->
<div class="map-card" style="background: #ffffff; border-radius: 24px; padding: 28px; margin-bottom: 38px; box-shadow: 0 8px 30px rgba(20, 30, 50, 0.05);">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h2 style="margin: 0; font-size: 24px; color: #172033;">🗺️ Peta Rute Perjalanan</h2>
            <p style="margin: 5px 0 0; color: #68758a; font-size: 14px;">Visualisasi rute titik lokasi wisata per hari di peta</p>
        </div>
        <div id="map-legend" style="display: flex; gap: 12px; font-size: 12px; font-weight: bold; flex-wrap: wrap;"></div>
    </div>
    <div id="map" style="height: 420px; width: 100%; border-radius: 18px; border: 1px solid #e2e8f0; z-index: 1;"></div>
</div>




<!-- =========================
     AI RECOMMENDATION
========================= -->

<section class="ai-section">


    <div class="ai-section-header">

        <h2>
            ✨ Rekomendasi Perjalanan
        </h2>

        <div class="duration-badge">

            {{ $aiResult['jumlah_hari'] ?? count($aiResult['itinerary'] ?? []) }}

            hari

        </div>

    </div>



    @if(!empty($aiResult['itinerary']) && is_array($aiResult['itinerary']))


        @foreach($aiResult['itinerary'] as $day)


            <div class="day-card">


                <!-- HEADER HARI -->

                <div class="day-header">

                    <div class="day-number">

                        HARI {{ $day['hari'] ?? $loop->iteration }}

                    </div>


                    <div>

                        <h3>

                            Hari {{ $day['hari'] ?? $loop->iteration }}

                        </h3>

                        <div class="day-date">

                            📅 {{ $day['tanggal'] ?? '-' }}

                        </div>

                    </div>

                </div>



                <!-- REKOMENDASI -->

                @if(!empty($day['rekomendasi']) && is_array($day['rekomendasi']))


                    <div class="activity-list">

                        @foreach($day['rekomendasi'] as $actIdx => $activity)
                            @php
                                $cardId = 'card-day-' . ($day['hari'] ?? $loop->parent->iteration) . '-' . $actIdx;
                                $isAvailable = isset($activity['is_available']) ? (bool)$activity['is_available'] : (!str_contains(strtolower($activity['nama_tempat'] ?? ''), 'tidak tersedia'));
                            @endphp

                            <div class="activity-card" id="{{ $cardId }}" style="{{ !$isAvailable ? 'background: #fff1f2; border: 1px solid #fecdd3;' : '' }}">

                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <div class="activity-category">
                                        @if(($activity['kategori'] ?? '') === 'pantai')
                                            🏖️ Pantai
                                        @elseif(($activity['kategori'] ?? '') === 'kuliner')
                                            🍜 Kuliner
                                        @elseif(($activity['kategori'] ?? '') === 'alam')
                                            🌿 Alam
                                        @elseif(($activity['kategori'] ?? '') === 'budaya')
                                            🏛️ Budaya
                                        @elseif(($activity['kategori'] ?? '') === 'hiburan')
                                            🎡 Hiburan
                                        @else
                                            📍 Wisata
                                        @endif

                                        @if(!$isAvailable)
                                            <span style="display: inline-block; background: #be123c; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px; margin-left: 6px;">⚠️ Tidak Tersedia</span>
                                        @endif
                                    </div>

                                    @if($isAvailable)
                                    <button 
                                        type="button" 
                                        class="btn-regenerate"
                                        onclick="regenerateItem('{{ addslashes($trip['destination']) }}', '{{ $activity['kategori'] ?? 'wisata' }}', '{{ addslashes($activity['nama_tempat'] ?? '') }}', '{{ addslashes($trip['context'] ?? '') }}', '{{ $cardId }}')"
                                        style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s;"
                                        onmouseover="this.style.background='#e2e8f0'"
                                        onmouseout="this.style.background='#f1f5f9'"
                                    >
                                        🔄 Ganti Tempat
                                    </button>
                                    @endif
                                </div>

                                <div class="activity-name" id="name-{{ $cardId }}" style="{{ !$isAvailable ? 'color: #be123c;' : '' }}">
                                    {{ $activity['nama_tempat'] ?? $activity['rekomendasi'] ?? 'Tempat belum ditemukan' }}
                                </div>

                                <div class="activity-description" id="desc-{{ $cardId }}">
                                    {{ $activity['deskripsi'] ?? 'Tidak ada deskripsi untuk tempat ini.' }}
                                </div>

                                <div style="margin-top: 10px; font-size: 13px; font-weight: 700; color: {{ !$isAvailable ? '#be123c' : '#059669' }};" id="cost-{{ $cardId }}">
                                    Estimasi Biaya: Rp {{ number_format($activity['estimasi_biaya'] ?? 0, 0, ',', '.') }}
                                </div>

                            </div>

                        @endforeach

                    </div>


                @else


                    <div class="empty-result">

                        Belum ada rekomendasi untuk hari ini.

                    </div>


                @endif


            </div>


        @endforeach


    @else


        <div class="empty-result">

            <strong>
                Rekomendasi perjalanan belum tersedia.
            </strong>

            <br>

            AI tidak mengembalikan data itinerary.

        </div>


    @endif


</section>



<!-- =========================
     BUDGET
========================= -->

@php

    $budget = $aiResult['estimasi_budget'] ?? [];

    $transportasi = $budget['transportasi'] ?? 0;

    $penginapan = $budget['penginapan'] ?? 0;

    $makan = $budget['makan'] ?? 0;

    $tiket = $budget['tiket_wisata'] ?? 0;

    $lainnya = $budget['lainnya'] ?? 0;

    $total = $budget['total'] ?? 0;

    $sisa = $budget['sisa_budget'] ?? 0;

    $status = $budget['status'] ?? '';

@endphp



<section class="budget-section">


    <div class="budget-card">


        <div class="budget-header">

            <div class="budget-title-wrapper">

                <h2>
                    💰 Perkiraan Budget
                </h2>

                <p>
                    Estimasi penggunaan budget berdasarkan
                    rencana perjalanan.
                </p>

            </div>

        </div>



        <div class="budget-grid">


            <!-- TOTAL -->

            <div class="budget-item total">

                <div>

                    <div class="budget-total-label">
                        Total Budget
                    </div>

                    <div class="budget-total-value">

                        Rp {{ number_format($trip['budget'], 0, ',', '.') }}

                    </div>

                </div>

            </div>



            <!-- TRANSPORTASI -->

            <div class="budget-item">

                <div>

                    <div class="budget-icon">
                        🚗
                    </div>

                    <div class="budget-item-name">
                        Transportasi
                    </div>

                </div>

                <div class="budget-amount">

                    Rp {{ number_format($transportasi, 0, ',', '.') }}

                </div>

            </div>



            <!-- PENGINAPAN -->

            <div class="budget-item">

                <div>

                    <div class="budget-icon">
                        🏨
                    </div>

                    <div class="budget-item-name">
                        Penginapan
                    </div>

                </div>

                <div class="budget-amount">

                    Rp {{ number_format($penginapan, 0, ',', '.') }}

                </div>

            </div>



            <!-- MAKAN -->

            <div class="budget-item">

                <div>

                    <div class="budget-icon">
                        🍜
                    </div>

                    <div class="budget-item-name">
                        Makan
                    </div>

                </div>

                <div class="budget-amount">

                    Rp {{ number_format($makan, 0, ',', '.') }}

                </div>

            </div>



            <!-- TIKET -->

            <div class="budget-item">

                <div>

                    <div class="budget-icon">
                        🎟️
                    </div>

                    <div class="budget-item-name">
                        Tiket Wisata
                    </div>

                </div>

                <div class="budget-amount">

                    Rp {{ number_format($tiket, 0, ',', '.') }}

                </div>

            </div>



            <!-- LAINNYA -->

            <div class="budget-item">

                <div>

                    <div class="budget-icon">
                        🪙
                    </div>

                    <div class="budget-item-name">
                        Lainnya
                    </div>

                </div>

                <div class="budget-amount">

                    Rp {{ number_format($lainnya, 0, ',', '.') }}

                </div>

            </div>


        </div>



        <!-- TOTAL PENGELUARAN -->

        <div class="budget-status">

            📊 Total estimasi perjalanan:

            <strong>
                Rp {{ number_format($total, 0, ',', '.') }}
            </strong>

            <br>

            💵 Sisa budget:

            <strong>
                Rp {{ number_format($sisa, 0, ',', '.') }}
            </strong>


            @if($status === 'cukup')

                <br>

                ✅ Budget masih mencukupi.

            @elseif($status === 'melebihi_budget')

                <br>

                ⚠️ Estimasi perjalanan melebihi budget yang tersedia.

            @endif

        </div>



        <div class="budget-note">

            * Biaya merupakan estimasi. Harga aktual dapat berbeda
            berdasarkan pilihan transportasi, penginapan,
            makanan, tiket, dan kondisi tempat wisata.

        </div>


    </div>

</section>



<!-- =========================
     BUTTON
========================= -->

<div class="bottom-action">

    <a
        href="{{ url('/') }}"
        class="back-button"
    >

        ← Buat Perjalanan Baru

    </a>

</div>


</main>


<script>
    const itineraryData = @json($aiResult['itinerary'] ?? []);
    const destName = @json($trip['destination'] ?? 'Indonesia');
    const destLat = {{ $destination['lat'] ?? -7.7956 }};
    const destLon = {{ $destination['lon'] ?? 110.3695 }};
    const dayColors = ['#2563eb', '#16a34a', '#dc2626', '#9333ea', '#d97706', '#0891b2', '#e11d48'];

    let map = null;
    let mapMarkers = [];
    let mapPolylines = [];

    document.addEventListener('DOMContentLoaded', function () {
        initMap();
    });

    function initMap() {
        if (!document.getElementById('map')) return;

        map = L.map('map').setView([destLat, destLon], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        renderMap();
    }

    function renderMap() {
        if (!map) return;

        // Reset existing markers and polylines
        mapMarkers.forEach(m => map.removeLayer(m));
        mapPolylines.forEach(p => map.removeLayer(p));
        mapMarkers = [];
        mapPolylines = [];

        const legendContainer = document.getElementById('map-legend');
        if (legendContainer) legendContainer.innerHTML = '';
        const allCoords = [];

        itineraryData.forEach((day, index) => {
            const color = dayColors[index % dayColors.length];
            const dayCoords = [];

            if (legendContainer) {
                const legendItem = document.createElement('div');
                legendItem.style.display = 'flex';
                legendItem.style.alignItems = 'center';
                legendItem.style.gap = '6px';
                legendItem.style.background = '#f8fafc';
                legendItem.style.padding = '4px 10px';
                legendItem.style.borderRadius = '12px';
                legendItem.style.border = '1px solid #e2e8f0';
                legendItem.innerHTML = `<span style="width:10px;height:10px;border-radius:50%;background:${color};display:inline-block;"></span> Hari ${day.hari || (index + 1)}`;
                legendContainer.appendChild(legendItem);
            }

            if (day.rekomendasi && Array.isArray(day.rekomendasi)) {
                day.rekomendasi.forEach(act => {
                    if (act.lat && act.lon && act.is_available !== false) {
                        const latLng = [parseFloat(act.lat), parseFloat(act.lon)];
                        dayCoords.push(latLng);
                        allCoords.push(latLng);

                        const marker = L.circleMarker(latLng, {
                            radius: 8,
                            fillColor: color,
                            color: '#ffffff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.9
                        }).addTo(map);

                        marker.bindPopup(`<b>Hari ${day.hari || (index + 1)}: ${act.nama_tempat}</b><br>Kategori: ${act.kategori}<br>${act.deskripsi}`);
                        mapMarkers.push(marker);
                    }
                });
            }

            if (dayCoords.length > 1) {
                const polyline = L.polyline(dayCoords, {
                    color: color,
                    weight: 4,
                    opacity: 0.8,
                    dashArray: '6, 8'
                }).addTo(map);
                mapPolylines.push(polyline);
            }
        });

        if (allCoords.length > 0) {
            const bounds = L.latLngBounds(allCoords);
            map.fitBounds(bounds, { padding: [40, 40] });
        }
    }

    let currentSavedTripId = {{ isset($savedTripId) ? $savedTripId : 'null' }};

    function saveTrip() {
        const saveBtn = document.getElementById('btn-save-trip');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '⌛ Menyimpan...';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('travel.save') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                destination: @json($trip['destination']),
                start_date: @json($trip['start_date']),
                end_date: @json($trip['end_date']),
                jumlah_orang: @json($trip['jumlah_orang'] ?? 1),
                budget: @json($trip['budget']),
                preferences: @json($trip['preferences'] ?? []),
                context: @json($trip['context'] ?? ''),
                summary: @json($aiResult['summary'] ?? ''),
                budget_breakdown: @json($aiResult['budget_breakdown'] ?? []),
                itinerary: itineraryData
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                currentSavedTripId = data.trip_id;
                if (saveBtn) saveBtn.style.display = 'none';
                const badge = document.getElementById('saved-status-badge');
                if (badge) {
                    badge.style.display = 'inline-flex';
                    badge.innerHTML = '✅ Perjalanan Tersimpan (#' + data.trip_id + ')';
                }
                const pdfBtn = document.getElementById('btn-pdf-link');
                if (pdfBtn) {
                    pdfBtn.href = '/trips/' + data.trip_id + '/pdf';
                    pdfBtn.style.display = 'inline-flex';
                }
                alert('🎉 ' + data.message);

            } else {
                alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '💾 Simpan Perjalanan';
                }
            }
        })
        .catch(err => {
            console.error('Save error:', err);
            alert('Terjadi kesalahan saat menyimpan perjalanan.');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '💾 Simpan Perjalanan';
            }
        });
    }

    function regenerateItem(destination, category, currentPlace, context, cardId) {
        const btn = document.querySelector(`#${cardId} .btn-regenerate`);
        const nameEl = document.getElementById(`name-${cardId}`);
        const descEl = document.getElementById(`desc-${cardId}`);
        const costEl = document.getElementById(`cost-${cardId}`);

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '⌛ Memuat...';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const parts = cardId.split('-');
        let dayIdx = null, actIdx = null;
        if (parts.length >= 4) {
            dayIdx = parseInt(parts[2]) - 1;
            actIdx = parseInt(parts[3]);
        }

        fetch("{{ route('travel.regenerate_item') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                destination: destination,
                category: category,
                current_place: currentPlace,
                context: context,
                trip_id: currentSavedTripId,
                day_index: dayIdx,
                activity_index: actIdx
            })
        })

        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.item) {
                const item = data.item;
                if (nameEl) nameEl.textContent = item.nama_tempat;
                if (descEl) descEl.textContent = item.deskripsi;
                if (costEl && item.estimasi_biaya !== undefined) {
                    costEl.textContent = 'Estimasi Biaya: Rp ' + new Intl.NumberFormat('id-ID').format(item.estimasi_biaya);
                }

                // Update handler tombol agar penggantian berikutnya menggunakan nama tempat yang baru
                if (btn) {
                    const safeDest = destination.replace(/'/g, "\\'");
                    const safeCat = category.replace(/'/g, "\\'");
                    const safeNewPlace = (item.nama_tempat || '').replace(/'/g, "\\'");
                    const safeContext = context.replace(/'/g, "\\'");
                    btn.setAttribute('onclick', `regenerateItem('${safeDest}', '${safeCat}', '${safeNewPlace}', '${safeContext}', '${cardId}')`);
                }

                // Update data itinerary pada memori JS
                const parts = cardId.split('-');
                if (parts.length >= 4) {
                    const dayNum = parseInt(parts[2]);
                    const actIdx = parseInt(parts[3]);
                    const dayObj = itineraryData.find(d => (d.hari || 0) === dayNum) || itineraryData[dayNum - 1];
                    if (dayObj && dayObj.rekomendasi && dayObj.rekomendasi[actIdx]) {
                        dayObj.rekomendasi[actIdx].nama_tempat = item.nama_tempat;
                        dayObj.rekomendasi[actIdx].rekomendasi = item.nama_tempat;
                        dayObj.rekomendasi[actIdx].deskripsi = item.deskripsi;
                        dayObj.rekomendasi[actIdx].estimasi_biaya = item.estimasi_biaya;
                        dayObj.rekomendasi[actIdx].lat = item.lat;
                        dayObj.rekomendasi[actIdx].lon = item.lon;
                        dayObj.rekomendasi[actIdx].is_available = item.is_available !== false;
                    }
                }

                // Re-render seluruh marker pin dan garis rute di peta
                renderMap();

                // Berikan efek sorotan (highlight marker) pada lokasi baru
                if (item.lat && item.lon && map) {
                    const latLng = [parseFloat(item.lat), parseFloat(item.lon)];
                    const highlightMarker = L.circleMarker(latLng, {
                        radius: 12,
                        fillColor: '#f59e0b',
                        color: '#ffffff',
                        weight: 3,
                        opacity: 1,
                        fillOpacity: 1
                    }).addTo(map);
                    highlightMarker.bindPopup(`<b>Alternatif Baru: ${item.nama_tempat}</b><br>${item.deskripsi}`).openPopup();
                    map.setView(latLng, 13);
                }
            } else {
                alert(data.message || 'Tidak menemukan alternatif tempat lain.');
            }
        })
        .catch(err => {
            console.error('Regenerate error:', err);
            alert('Gagal mengganti tempat. Pastikan server Python AI berjalan.');
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '🔄 Ganti Tempat';
            }
        });
    }
</script>
</body>
</html>