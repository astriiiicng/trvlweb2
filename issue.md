# 🎨 UI/UX Revamp: Neumorphism + Glassmorphism Integration

## 📌 Deskripsi Masalah
Tampilan website saat ini kurang konsisten dan terasa tertinggal (outdated). Kita perlu melakukan perombakan UI secara menyeluruh untuk membuat setiap halaman terlihat lebih modern, bersih, dan senada (konsisten). 

## 🎯 Solusi & Konsep Desain
Menggabungkan dua tren desain modern yang saling melengkapi:
1. **Neumorphism (Soft UI):** Digunakan untuk elemen dasar seperti *background*, formulir (input), dan tombol untuk memberikan kesan 3D yang lembut (timbul atau tenggelam) dengan warna solid yang serasi.
2. **Glassmorphism:** Digunakan untuk elemen *overlay* (seperti *navbar*, *modal*, *floating card*, atau *dropdown*) untuk memberikan efek kaca buram (frosted glass) transparan yang menembus warna background, menciptakan ilusi kedalaman.

## 🎨 Design System & Guidelines

### 1. Palet Warna (Color Palette)
- **Background Utama (Neumorphism):** Warna solid yang lembut (off-white, light gray `#e0e5ec`, atau pastel) agar bayangan (shadow) terlihat natural.
- **Aksen (Glassmorphism):** Warna gradasi cerah atau bentuk geometris berwarna yang diletakkan di bawah elemen *glass* untuk mempertegas efek transparan.
- **Teks:** Abu-abu gelap (Dark slate) untuk kontras yang baik, bukan hitam pekat.

### 2. Aturan Bayangan (Shadows) & Efek (Blur)
- **Neumorphism (Timbul):** 
  - Bayangan terang (putih) di sudut kiri atas.
  - Bayangan gelap (abu-abu/hitam transparan) di sudut kanan bawah.
- **Neumorphism (Tenggelam - untuk Input/Active State):** Inset shadow dengan pola yang sama.
- **Glassmorphism:** 
  - `backdrop-filter: blur(10px)` (atau lebih).
  - Background semi-transparan (`rgba(255, 255, 255, 0.1)`).
  - Border tipis dan terang (`border: 1px solid rgba(255, 255, 255, 0.3)`) untuk efek pinggiran kaca.

### 3. Tipografi & Bentuk
- **Font:** Menggunakan font Sans-serif modern dan membulat (seperti *Inter*, *Poppins*, atau *Outfit*).
- **Border Radius:** Sudut membulat yang konsisten (misal: `16px` atau `24px` untuk *card*, `12px` untuk *button*).

---

## 🛠️ Fase Implementasi (Plan of Action)

### Phase 1: Setup & CSS Architecture
- [ ] Buat file `variables.css` (atau setup Tailwind config) khusus untuk menyimpan variabel warna, shadow Neumorphism, dan efek Glassmorphism.
- [ ] Tentukan global font.
- [ ] Terapkan warna background global untuk Neumorphism.

### Phase 2: Pembuatan Komponen Dasar (UI Kit)
- [ ] **Tombol (Buttons):** Desain Neumorphic (default timbul, `active`/ditekan tenggelam).
- [ ] **Input & Form:** Desain Neumorphic inset (tenggelam).
- [ ] **Cards (Static):** Kombinasi Neumorphic base dengan Glassmorphic element di dalamnya (jika ada gambar).
- [ ] **Navbar / Sidebar:** Menggunakan Glassmorphism yang mengambang di atas konten utama.
- [ ] **Modals / Dialogs:** Menggunakan Glassmorphism penuh beserta *dimmed background*.

### Phase 3: Penerapan pada Halaman
- [ ] **Halaman Autentikasi (Login/Register):** Form Neumorphic di dalam Card Glassmorphic dengan background bergradasi/bentuk geometris.
- [ ] **Halaman Utama (Home/Dashboard):** Refactor grid, layout, dan hierarki visual menggunakan komponen yang baru dibuat.
- [ ] **Halaman Detail/Lainnya:** Pastikan konsistensi padding, margin, dan gaya visual senada.

### Phase 4: Polish & Interaksi
- [ ] Tambahkan animasi transisi halus (`transition: all 0.3s ease`) pada tombol saat di-hover/diklik.
- [ ] Uji responsivitas di tampilan *Mobile* dan *Tablet*.
- [ ] Cek *Accessibility* (pastikan kontras teks cukup terbaca di atas background transparan maupun soft background).

---

## 📝 Catatan Tambahan
- Pastikan tidak *overuse* efek blur dari Glassmorphism karena dapat mempengaruhi performa render (FPS) pada perangkat spesifikasi rendah.
- Fokuskan Glassmorphism hanya pada elemen yang membutuhkan perhatian utama pengguna (Z-index tinggi).
