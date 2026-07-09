# 🎓 e-Rapor 2026 (SMK)

e-Rapor 2026 adalah aplikasi rapor elektronik modern berbasis web yang dirancang khusus untuk Sekolah Menengah Kejuruan (SMK). Aplikasi ini difokuskan untuk mendukung implementasi **Kurikulum Merdeka**, menyederhanakan alur penilaian guru, dan menghasilkan cetakan rapor yang sesuai dengan standar pendidikan terbaru.

---

## 🛠️ Technology Stack (Teknologi & Versi yang Digunakan)

Proyek ini mengadopsi arsitektur **Decoupled (Headless)**, yang memisahkan secara penuh antara *backend* (pengolah data) dan *frontend* (antarmuka pengguna). Daftar teknologi dan versi di bawah ini dapat dijadikan acuan untuk **BAB II (Landasan Teori)** penulisan Skripsi/Tugas Akhir:

### Bahasa Pemrograman Dasar
- **HTML:** HTML5 (Hypertext Markup Language versi 5)
- **CSS:** CSS3 (Cascading Style Sheets versi 3)
- **JavaScript:** ECMAScript 2021+ (ES12+) / Node.js versi 18+ atau 20+
- **PHP:** PHP versi 8.2 ke atas (Minimal versi yang mendukung Laravel 11)

### Backend (API & Server-Side)
- **Framework:** [Laravel](https://laravel.com/) versi 11.x
- **Architecture:** RESTful API
- **Authentication:** Laravel Sanctum (Bearer Token Auth)
- **Database:** MySQL versi 8.0+ atau MariaDB 10.6+

### Frontend (User Interface & Client-Side)
- **Core Framework:** [Vue.js](https://vuejs.org/) versi 3 (Composition API)
- **Meta-Framework:** [Nuxt.js](https://nuxt.com/) versi 3 (Vue.js Framework untuk SPA / SSR)
- **Styling/CSS Framework:** [Tailwind CSS](https://tailwindcss.com/) versi 3.4+
- **State Management:** Vue Reactivity API (`ref`, `reactive`)
- **HTTP Client:** Nuxt `$fetch` (Native Fetch API wrapper)

---

## 🏗️ Konsep & Arsitektur Sistem

1. **API-First Design:** Seluruh proses logika bisnis, perhitungan nilai, dan manipulasi *database* dilakukan di Laravel (Backend). Frontend hanya bertugas mengkonsumsi API tersebut dan menampilkannya secara interaktif kepada pengguna.
2. **Role-Based Access Control (RBAC):** Sistem mengotentikasi pengguna dan memberikan akses menu/fitur yang berbeda berdasarkan *role* (Admin, Kurikulum, Guru, Wali Kelas, BK).
3. **Single Page Application (SPA) Feel:** Berkat Nuxt 3, perpindahan halaman terasa instan tanpa memuat ulang (*reload*) *browser*, memberikan pengalaman pengguna (*User Experience*) yang cepat layaknya aplikasi *desktop/mobile*.

---

## 🌟 Modul & Fitur Utama (Blueprint Sistem)

Aplikasi ini memiliki pembagian Hak Akses (Role-Based Access) yang komprehensif. Berikut adalah rincian fitur untuk masing-masing *role* yang telah diselesaikan:

### 1. 👑 Modul Admin (LOCKED 🔒)
*Pusat kendali pengaturan akademik dan master data sekolah. Telah 100% selesai dengan arsitektur UI "Dock & Flow".*
- **Manajemen Tahun Ajaran & Titimangsa**: Pengaturan periode aktif (PSTS, PSAS/PSAT) secara ketat untuk membatasi input nilai.
- **Manajemen Data Master & Referensi**: Kelola data Siswa, Kelas, Guru, Kejuruan, dan sinkronisasi referensi.
- **Kelola Buku Induk**: Sentralisasi data identitas, akademik, dan riwayat siswa.

### 2. ⚙️ Modul Waka Kurikulum (LOCKED 🔒)
*Pengelolaan kurikulum dan penjadwalan. Telah 100% selesai dengan arsitektur UI "Dock & Flow" dan peringatan statistik real-time.*
- **Struktur Kurikulum & Mata Pelajaran**: Pengelompokan mapel Umum, Kejuruan, dan Muatan Lokal secara terstruktur.
- **Plotting Akademik**: Penugasan Guru Mata Pelajaran (Pengampu) dan penetapan Wali Kelas untuk tiap rombongan belajar berbasis filter tahun ajaran aktif.
- **Setup KKM & Template Deskripsi**: Pengaturan standar ketuntasan dan pembuatan *template* dinamis untuk narasi otomatis capaian belajar siswa di Rapor.

### 3. 🛡️ Panel Bimbingan Konseling (BK) (LOCKED 🔒)
*Pusat penanganan kedisiplinan dan karakter. Telah 100% selesai dengan filter cerdas per periode.*
- **Buku Jurnal Kasus**: Pencatatan kasus pelanggaran kedisiplinan siswa yang terintegrasi dengan filter periode akademik.
- **Kalkulasi Poin Pelanggaran**: Sistem otomatis penambahan dan pengurangan poin pelanggaran secara real-time.
- **Tindak Lanjut & Penanganan**: Pelacakan riwayat bimbingan/solusi yang diberikan BK kepada siswa.
- **Fitur Cetak Responsif**: Pencetakan riwayat kasus per siswa per periode akademik dengan kop otomatis dan tanda tangan dinamis.

### 4. 👨‍🏫 Panel Guru Mata Pelajaran
*Ruang kerja guru untuk melakukan asesmen dan pelaporan nilai kelas.*
- **Dashboard Akademik Guru**: Ringkasan statistik jumlah kelas, beban mapel, dan total siswa yang diajar.
- **Input Asesmen Formatif (Harian)**: Matriks penilaian berkelanjutan berdasarkan Tujuan Pembelajaran (TP) per bab/materi.
- **Input Asesmen Sumatif (Ujian)**: Pengisian Nilai Akhir untuk periode PSTS (Tengah Semester) maupun PSAS (Akhir Semester).
- **Auto-Generate Deskripsi**: Sistem otomatis menerjemahkan angka capaian menjadi narasi/deskripsi kompetensi siswa berdasarkan *template* kurikulum.
- **Cetak Rekap Nilai Mapel**: Ekspor/cetak daftar nilai satu kelas penuh sebagai arsip fisik (Leger Mapel).

### 5. 👨‍💼 Panel Wali Kelas
*Pusat pengelolaan administrasi kelas dan pencetakan Rapor resmi.*
- **Dashboard Wali Kelas**: *Monitoring* kelengkapan nilai dari seluruh Guru Mapel yang mengajar di kelasnya.
- **Kelola Biodata & Validasi**: Memeriksa, melengkapi, dan memvalidasi biodata siswa (Identitas, Orang Tua, Wali, Asal Sekolah).
- **Input Ekstrakurikuler**: Memasukkan predikat dan deskripsi partisipasi kegiatan ekskul wajib maupun pilihan.
- **Input Kehadiran (Absensi)**: Perekaman akumulasi Sakit, Izin, dan Tanpa Keterangan (Alpa) dalam satu periode.
- **Input PKL & Kokurikuler**: Perekaman riwayat Praktik Kerja Lapangan dan catatan pengembangan karakter (Kokurikuler).
- **Catatan Wali Kelas**: Pemberian saran, motivasi, dan evaluasi personal tertulis untuk setiap siswa.
- **Cetak Rapor Otomatis**: Menghasilkan dokumen Rapor siap cetak (Kertas A4) yang secara dinamis menyembunyikan tombol UI (khusus layar *print*).

### 6. 🎓 Panel Siswa
*Portal transparansi akademik untuk siswa dan orang tua/wali.*
- **Dashboard Interaktif Siswa**: Menampilkan visualisasi data (Grafik/Chart) *Academic Growth*, ringkasan kehadiran 1 tahun ajaran, dan status keaktifan periode.
- **Pembaruan Biodata Mandiri**: Memungkinkan siswa untuk mengedit dan melengkapi data pribadi, data orang tua/wali, serta tempat PKL mereka sendiri.
- **Lihat Rapor (Read-Only)**: Akses digital untuk melihat Rapor (nilai akademik, catatan walas, absensi, ekskul) secara *real-time* begitu diterbitkan oleh sekolah, tanpa dapat mengubah data.

---

## 🚀 Cara Menjalankan Aplikasi (Local Development)

Karena aplikasi terpisah menjadi dua (*frontend* dan *backend*), Anda harus menjalankan keduanya secara bersamaan.

### Menjalankan Backend (API)
Buka terminal pada direktori `erapor-api`:
```bash
composer install
php artisan migrate --seed  # Untuk membuat database beserta data awal
php artisan serve
```
*Backend akan berjalan di: `http://localhost:8000`*

### Menjalankan Frontend
Buka terminal baru pada direktori `erapor-fe`:
```bash
npm install
npm run dev
```
*Frontend akan berjalan di: `http://localhost:3000`*

---

## 🎨 Panduan Desain & Standarisasi UI/UX (Locked Guide)

Selama pengembangan modul Admin, kita telah mengunci pola desain (*design system*) baku yang **WAJIB DIKUATIP** untuk pengembangan modul/role selanjutnya (Kurikulum, Guru, Wali Kelas, dll). Hal ini demi menjaga konsistensi, estetika premium, dan *User Experience* (UX) aplikasi e-Rapor.

### 1. Pola Layout "Dock & Flow" (2-Panel)
Setiap halaman fitur utama harus menggunakan pola *2-Panel Desktop Layout* ini:
- **Panel Dock (Kiri):** Lebar statis (`xl:w-[360px]`), latar putih (`bg-white`), diberi *shadow* tipis ke kanan. Digunakan untuk form *input*, menu tabulasi navigasi vertikal, atau pengaturan spesifik. Menyembunyikan dirinya secara otomatis di layar *mobile* dan berubah menjadi sistem Tab.
- **Panel Flow (Kanan):** Lebar fleksibel (`flex-1`), latar abu-abu terang (`bg-slate-50`). Digunakan untuk menampilkan data utama, tabel, grafik, atau rekapitulasi data.
- **Sidebar (Navigasi Kiri Utama):** Lebar menyusut (`w-[72px]`) saat tidak di-*hover*, memanjang saat di-*hover* atau di-klik (`w-64`), lengkap dengan *Profile Dropdown* di pojok kanan atas Navbar. Logo ditempatkan rata tengah presisi (menggunakan *padding* spesifik).

### 2. Tipografi & Gaya Teks
- **Label / Judul Kecil:** Gunakan teks kapital sangat tebal dengan spasi renggang.
  - Class: `text-[10px] (atau 11px) font-black text-slate-500 uppercase tracking-widest`
- **Warna Teks Utama:** `text-slate-800` untuk teks primer, `text-slate-500` atau `text-slate-400` untuk teks pendukung/sub-teks.

### 3. Komponen Form (Input & Label)
- **Label Form:** Teks kecil tebal di atas input (`text-[11px] font-black text-slate-500 uppercase mb-1.5 ml-1`).
- **Input Text/Email/Select:** Berbentuk kapsul tebal.
  - Class: `px-4 py-3 rounded-2xl border-2 border-slate-200/70 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all font-semibold`.

### 4. Tombol & Aksi (Buttons)
- **Tombol Utama (Submit/Aksi Besar):** Latar belakang gradasi dengan bayangan bersinar dan efek pantul saat *hover*.
  - Class: `bg-gradient-to-r from-emerald-500 to-emerald-600 (atau teal-600) text-white font-bold rounded-2xl shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 transition-all`.
- **Tombol Aksi Tabel (Edit/Hapus):** Ikon emoji di dalam kotak kecil beraksen.
  - Class Umum: `w-8 h-8 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center transition-colors`.
  - Khusus Edit (✏️): `hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-600`.
  - Khusus Hapus (🗑️): `hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600`.
  - Khusus Reset Password (🔑): `hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600`.

### 5. Dialog & Alert (PENTING!)
- **Pemberitahuan Kecil (Toast):** Gunakan fungsi bawaan kustom `useSwal().toast('Pesan', 'success/error')`. Akan muncul elegan di pojok kanan atas layar tanpa perlu tombol *close*.
- **Modal Konfirmasi / Pop-up Besar:** **DILARANG MENGGUNAKAN SWEETALERT2 DEFAULT** untuk modal konfirmasi interaktif di tengah layar karena ukurannya tidak serasi dengan tema.
- **Standard Modal Kustom:** Buat Modal Overlay menggunakan *Tailwind* langsung di dalam halaman Vue.
  - Struktur dasar: `fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm animate-fadeIn`.
  - Kotak Konten: `w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 animate-slideUpFade`.
  - Ciri Khas: Di bagian tengah atas *modal*, wajib ada lingkaran besar berisi emoji fokus peringatan (contoh: bg merah muda terang untuk ikon Peringatan ⚠️, bg kuning terang untuk ikon Kunci 🔑).

### 6. Tabel Data
- **Header Tabel:** Melayang (sticky) dan menggunakan teks kapital mini.
  - Class: `sticky top-0 bg-slate-50 border-b border-slate-200 shadow-sm`. Teks `text-[9px] uppercase tracking-widest font-black text-slate-500`.
- **Isi Tabel (Row):** Interaktif.
  - Class `tr`: `hover:bg-slate-50/80 transition-colors bg-white group`.
  - Tombol aksi (Edit/Hapus) tersembunyi secara otomatis (`opacity-0`) dan baru muncul saat baris di-*hover* (`group-hover:opacity-100`), guna menjaga estetika tabel yang bersih.

---

*Dikembangkan untuk digitalisasi pendidikan Indonesia.* 🇮🇩
