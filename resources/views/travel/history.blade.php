<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Trips - TripMate AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .logo-icon { background: var(--bg-color); box-shadow: var(--neu-shadow-sm); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--text-main); font-size: 1.2rem; }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-link { color: var(--text-muted); text-decoration: none; font-weight: 600; font-size: 0.95rem; padding: 0.5rem 1rem; border-radius: 8px; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; }
        .nav-link:hover, .nav-link.active { color: var(--text-main); background: var(--bg-color); box-shadow: var(--neu-shadow-sm); }
        
        .main-container { max-width: 1200px; margin: 0 auto; padding: 2.5rem 1.5rem; width: 100%; flex: 1; }
        .header-section { margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem; }
        .header-title h1 { font-size: 2rem; font-weight: 800; color: var(--text-main); }
        .header-title p { color: var(--text-muted); margin-top: 0.25rem; font-size: 0.95rem; }
        
        .btn-new-trip { background: var(--bg-color); color: var(--text-main); text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 700; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: var(--neu-shadow); transition: all 0.2s ease; }
        .btn-new-trip:hover { box-shadow: var(--neu-shadow-sm); transform: translateY(-2px); }
        
        .alert-success { background: rgba(220, 252, 231, 0.7); backdrop-filter: var(--glass-blur); border: 1px solid rgba(134, 239, 172, 0.5); color: #166534; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; }
        
        .trips-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.75rem; }
        
        .trip-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 18px; padding: 1.5rem; backdrop-filter: var(--glass-blur); -webkit-backdrop-filter: var(--glass-blur); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.3s ease; position: relative; overflow: hidden; }
        .trip-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(31, 38, 135, 0.15); }
        
        .trip-destination { font-size: 1.35rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .trip-dates { color: var(--text-muted); font-size: 0.85rem; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
        
        .trip-meta { display: flex; gap: 1rem; background: var(--bg-color); box-shadow: var(--neu-inset-sm); padding: 0.85rem; border-radius: 12px; margin-bottom: 1rem; }
        .meta-item { flex: 1; }
        .meta-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.2rem; font-weight: 600; }
        .meta-value { font-size: 0.95rem; font-weight: 700; color: var(--text-main); }
        
        .preferences-badges { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1.25rem; }
        .badge-pref { background: var(--bg-color); box-shadow: var(--neu-shadow-sm); color: var(--text-main); font-size: 0.75rem; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 600; text-transform: capitalize; }
        
        .card-actions { display: flex; gap: 0.75rem; margin-top: auto; padding-top: 1rem; border-top: 1px solid rgba(0,0,0,0.05); }
        .btn-detail, .btn-pdf, .btn-delete { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.65rem 1rem; border-radius: 10px; font-size: 0.9rem; font-weight: 700; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        
        .btn-detail { background: var(--bg-color); box-shadow: var(--neu-shadow-sm); color: var(--text-main); }
        .btn-detail:hover { box-shadow: var(--neu-inset); }
        
        .btn-pdf { flex: 0.7; background: rgba(20, 184, 166, 0.15); border: 1px solid rgba(20, 184, 166, 0.3); color: #0d9488; }
        .btn-pdf:hover { background: #0d9488; color: white; }
        
        .btn-delete { flex: 0.5; background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.3); color: #be123c; }
        .btn-delete:hover { background: #be123c; color: white; }
        
        .empty-state { background: var(--glass-bg); backdrop-filter: var(--glass-blur); border: 1px dashed var(--glass-border); border-radius: 20px; padding: 4rem 2rem; text-align: center; max-width: 550px; margin: 3rem auto; box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05); }
        .empty-icon { font-size: 3.5rem; color: var(--text-muted); margin-bottom: 1rem; opacity: 0.6; }
        .empty-state h2 { font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--text-main); }
        .empty-state p { color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.95rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="{{ route('travel.index') }}" class="logo">
                <div class="logo-icon">
                    <i class="fa-solid fa-compass"></i>
                </div>
                <span>TripMate AI</span>
            </a>
            <div class="nav-links">
                <a href="{{ route('travel.index') }}" class="nav-link">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Buat Rencana
                </a>
                <a href="{{ route('travel.history') }}" class="nav-link active">
                    <i class="fa-solid fa-suitcase"></i> My Trips
                </a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="header-section">
            <div class="header-title">
                <h1>Riwayat Perjalanan Saya</h1>
                <p>Daftar rencana perjalanan yang telah Anda simpan</p>
            </div>
            <a href="{{ route('travel.index') }}" class="btn-new-trip">
                <i class="fa-solid fa-plus"></i> Buat Rencana Baru
            </a>
        </div>

        @if(session('success'))
            <div class="alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($trips->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <h2>Belum Ada Perjalanan Tersimpan</h2>
                <p>Anda belum menyimpan rencana perjalanan apapun. Mulai buat itinerary impian Anda sekarang!</p>
                <a href="{{ route('travel.index') }}" class="btn-new-trip">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Buat Rencana Sekarang
                </a>
            </div>
        @else
            <div class="trips-grid">
                @foreach($trips as $trip)
                    @php
                        $startDate = $trip->start_date ? \Carbon\Carbon::parse($trip->start_date) : null;
                        $endDate = $trip->end_date ? \Carbon\Carbon::parse($trip->end_date) : null;
                        $durasi = ($startDate && $endDate) ? $startDate->diffInDays($endDate) + 1 : 1;
                    @endphp
                    <div class="trip-card">
                        <div>
                            <div class="trip-destination">
                                <i class="fa-solid fa-location-dot" style="color: #6366f1;"></i>
                                {{ $trip->destination }}
                            </div>
                            <div class="trip-dates">
                                <i class="fa-regular fa-calendar"></i>
                                @if($startDate && $endDate)
                                    {{ $startDate->translatedFormat('d M Y') }} - {{ $endDate->translatedFormat('d M Y') }} ({{ $durasi }} Hari)
                                @else
                                    -
                                @endif
                            </div>

                            <div class="trip-meta">
                                <div class="meta-item">
                                    <div class="meta-label">Peserta</div>
                                    <div class="meta-value"><i class="fa-solid fa-users" style="font-size: 0.8rem; color: #14b8a6;"></i> {{ $trip->jumlah_orang }} Orang</div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-label">Total Budget</div>
                                    <div class="meta-value" style="color: #2dd4bf;">Rp {{ number_format($trip->budget, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            @if(!empty($trip->preferences))
                                <div class="preferences-badges">
                                    @foreach($trip->preferences as $pref)
                                        <span class="badge-pref">#{{ $pref }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="card-actions">
                            <a href="{{ route('travel.show', $trip->id) }}" class="btn-detail">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                            <a href="{{ route('travel.pdf', $trip->id) }}" target="_blank" class="btn-pdf" title="Cetak / Simpan PDF">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </a>
                            <form action="{{ route('travel.destroy', $trip->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rencana perjalanan ke {{ $trip->destination }}?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" title="Hapus Perjalanan">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
