<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TripMate - AI Travel Planner</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>


<body>

<header class="navbar">

    <div class="logo">
        ✈️ TripMate
    </div>

    <nav>
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ route('travel.history') }}">My Trips</a>
    </nav>

</header>


<main class="hero">

    <div class="hero-content">

        <div class="badge">
            ✨ AI Travel Planner
        </div>

        <h1>
            Rencanakan perjalananmu
            <span>lebih mudah.</span>
        </h1>

        <p>
            Masukkan destinasi, tanggal perjalanan, budget,
            dan kategori wisata yang kamu sukai.
            TripMate akan membantu menyusun rekomendasi
            perjalanan secara otomatis.
        </p>

    </div>


    <div class="planner-card">

        <h2>
            Buat Rencana Perjalanan
        </h2>


        {{-- ERROR VALIDASI --}}

        @if ($errors->any())

            <div class="error-message">

                @foreach ($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif


        {{-- ERROR DARI CONTROLLER --}}

        @if (session('error'))

            <div class="error-message">
                {{ session('error') }}
            </div>

        @endif


        {{-- FORM --}}

        <form
            action="{{ route('travel.plan') }}"
            method="POST"
        >

            @csrf


            {{-- DESTINASI --}}

            <div class="form-group" style="margin-bottom: 15px;">

                <label for="province">
                    📍 Provinsi
                </label>

                <select
                    id="province"
                    class="form-control"
                    required
                >
                    <option value="">-- Pilih Provinsi --</option>
                </select>

                <label for="destination">
                    📍 Kabupaten/Kota (Destinasi)
                </label>

                <select
                    id="destination"
                    name="destination"
                    class="form-control"
                    required
                    disabled
                >
                    <option value="">-- Pilih Kabupaten/Kota --</option>
                </select>

            </div>


            {{-- TANGGAL --}}

            <div class="form-row">

                <div class="form-group">

                    <label for="start_date">
                        📅 Mulai
                    </label>

                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ old('start_date') }}"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="end_date">
                        📅 Selesai
                    </label>

                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="{{ old('end_date') }}"
                        required
                    >

                </div>

            </div>


            {{-- JUMLAH ORANG & BUDGET --}}

            <div class="form-row">

                <div class="form-group">

                    <label for="jumlah_orang">
                        👥 Jumlah Orang
                    </label>

                    <input
                        type="number"
                        id="jumlah_orang"
                        name="jumlah_orang"
                        value="{{ old('jumlah_orang', 1) }}"
                        min="1"
                        placeholder="1"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="budget">
                        💰 Total Budget (Semua)
                    </label>

                    <input
                        type="number"
                        id="budget"
                        name="budget"
                        value="{{ old('budget') }}"
                        min="0"
                        placeholder="Contoh: 1500000"
                        required
                    >

                </div>

            </div>


            {{-- KATEGORI --}}

            <div class="form-group">

                <label>
                    🎯 Kategori Wisata
                </label>

                <div class="preference-options">


                    <label class="preference-item">

                        <input
                            type="checkbox"
                            name="preferences[]"
                            value="pantai"
                            {{ in_array('pantai', old('preferences', [])) ? 'checked' : '' }}
                        >

                        <span>
                            🏖️ Pantai
                        </span>

                    </label>


                    <label class="preference-item">

                        <input
                            type="checkbox"
                            name="preferences[]"
                            value="kuliner"
                            {{ in_array('kuliner', old('preferences', [])) ? 'checked' : '' }}
                        >

                        <span>
                            🍜 Kuliner
                        </span>

                    </label>


                    <label class="preference-item">

                        <input
                            type="checkbox"
                            name="preferences[]"
                            value="alam"
                            {{ in_array('alam', old('preferences', [])) ? 'checked' : '' }}
                        >

                        <span>
                            🌿 Alam
                        </span>

                    </label>


                    <label class="preference-item">

                        <input
                            type="checkbox"
                            name="preferences[]"
                            value="budaya"
                            {{ in_array('budaya', old('preferences', [])) ? 'checked' : '' }}
                        >

                        <span>
                            🏛️ Budaya
                        </span>

                    </label>


                    <label class="preference-item">

                        <input
                            type="checkbox"
                            name="preferences[]"
                            value="hiburan"
                            {{ in_array('hiburan', old('preferences', [])) ? 'checked' : '' }}
                        >

                        <span>
                            🎡 Hiburan
                        </span>

                    </label>


                </div>

            </div>


            {{-- KETERANGAN TAMBAHAN --}}

            <div class="form-group" style="margin-top: 20px;">

                <label for="context">
                    📝 Keterangan Tambahan (Opsional)
                </label>

                <textarea
                    id="context"
                    name="context"
                    placeholder="Contoh: Perjalanan santai, bawa lansia, hindari aktivitas fisik berat."
                >{{ old('context') }}</textarea>

            </div>


            {{-- BUTTON --}}

            <button
                type="submit"
                id="btn-submit-plan"
                class="plan-button"
            >

                ✨ Buat Rencana Perjalanan

            </button>


        </form>

    </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const provinceSelect = document.getElementById('province');
        const destinationSelect = document.getElementById('destination');

        // Fetch Provinces via local proxy
        fetch("{{ route('api.wilayah.provinces') }}")
            .then(response => response.json())
            .then(res => {
                if(res.data) {
                    res.data.forEach(province => {
                        let option = document.createElement('option');
                        option.value = province.code; // wilayah.id menggunakan property 'code'
                        option.text = province.name;
                        provinceSelect.add(option);
                    });
                }
            })
            .catch(error => console.error('Error fetching provinces:', error));

        // Fetch Regencies (Kota/Kabupaten) via local proxy
        provinceSelect.addEventListener('change', function () {
            const provinceId = this.value;
            destinationSelect.innerHTML = '<option value="">-- Memuat Data... --</option>';
            destinationSelect.disabled = true;

            if (provinceId) {
                fetch(`/api/wilayah/regencies/${provinceId}`)
                    .then(response => response.json())
                    .then(res => {
                        destinationSelect.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                        if(res.data) {
                            res.data.forEach(regency => {
                                let option = document.createElement('option');
                                // Gunakan nama kabupaten/kota sebagai value yang akan dikirim ke backend
                                option.value = regency.name;
                                option.text = regency.name;
                                destinationSelect.add(option);
                            });
                        }
                        destinationSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Error fetching regencies:', error);
                        destinationSelect.innerHTML = '<option value="">-- Gagal memuat data --</option>';
                    });
            } else {
                destinationSelect.innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
            }
        });

        const form = document.querySelector('form');
        const submitBtn = document.getElementById('btn-submit-plan');
        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.8';
                submitBtn.innerHTML = '⌛ AI sedang menyusun rencana perjalanan... (Mohon tunggu beberapa saat)';
            });
        }
    });
</script>

</body>
</html>