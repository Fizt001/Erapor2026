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

### 1. 👑 Modul Admin & Kurikulum
*Pusat kendali pengaturan akademik dan master data sekolah.*
- **Manajemen Tahun Ajaran & Titimangsa**: Pengaturan periode aktif (PSTS, PSAS/PSAT) untuk membatasi input nilai.
- **Struktur Kurikulum & Mata Pelajaran**: Pengelompokan mapel Umum, Kejuruan, dan Muatan Lokal.
- **Manajemen Data Master**: Kelola data Siswa, Kelas, dan Guru.
- **Plotting Akademik**: Penugasan Guru Mata Pelajaran dan penetapan Wali Kelas untuk tiap rombongan belajar.
- **Setup KKM & Template Deskripsi**: Pengaturan standar ketuntasan dan pembuatan *template* dinamis untuk narasi otomatis capaian belajar siswa di Rapor.

### 2. 👨‍🏫 Panel Guru Mata Pelajaran
*Ruang kerja guru untuk melakukan asesmen dan pelaporan nilai kelas.*
- **Dashboard Akademik Guru**: Ringkasan statistik jumlah kelas, beban mapel, dan total siswa yang diajar.
- **Input Asesmen Formatif (Harian)**: Matriks penilaian berkelanjutan berdasarkan Tujuan Pembelajaran (TP) per bab/materi.
- **Input Asesmen Sumatif (Ujian)**: Pengisian Nilai Akhir untuk periode PSTS (Tengah Semester) maupun PSAS (Akhir Semester).
- **Auto-Generate Deskripsi**: Sistem otomatis menerjemahkan angka capaian menjadi narasi/deskripsi kompetensi siswa berdasarkan *template* kurikulum.
- **Cetak Rekap Nilai Mapel**: Ekspor/cetak daftar nilai satu kelas penuh sebagai arsip fisik (Leger Mapel).

### 3. 👨‍💼 Panel Wali Kelas
*Pusat pengelolaan administrasi kelas dan pencetakan Rapor resmi.*
- **Dashboard Wali Kelas**: *Monitoring* kelengkapan nilai dari seluruh Guru Mapel yang mengajar di kelasnya.
- **Kelola Biodata & Validasi**: Memeriksa, melengkapi, dan memvalidasi biodata siswa (Identitas, Orang Tua, Wali, Asal Sekolah).
- **Input Ekstrakurikuler**: Memasukkan predikat dan deskripsi partisipasi kegiatan ekskul wajib maupun pilihan.
- **Input Kehadiran (Absensi)**: Perekaman akumulasi Sakit, Izin, dan Tanpa Keterangan (Alpa) dalam satu periode.
- **Input PKL & Kokurikuler**: Perekaman riwayat Praktik Kerja Lapangan dan catatan pengembangan karakter (Kokurikuler).
- **Catatan Wali Kelas**: Pemberian saran, motivasi, dan evaluasi personal tertulis untuk setiap siswa.
- **Cetak Rapor Otomatis**: Menghasilkan dokumen Rapor siap cetak (Kertas A4) yang secara dinamis menyembunyikan tombol UI (khusus layar *print*).

### 4. 🎓 Panel Siswa
*Portal transparansi akademik untuk siswa dan orang tua/wali.*
- **Dashboard Interaktif Siswa**: Menampilkan visualisasi data (Grafik/Chart) *Academic Growth*, ringkasan kehadiran 1 tahun ajaran, dan status keaktifan periode.
- **Pembaruan Biodata Mandiri**: Memungkinkan siswa untuk mengedit dan melengkapi data pribadi, data orang tua/wali, serta tempat PKL mereka sendiri.
- **Lihat Rapor (Read-Only)**: Akses digital untuk melihat Rapor (nilai akademik, catatan walas, absensi, ekskul) secara *real-time* begitu diterbitkan oleh sekolah, tanpa dapat mengubah data.

### 5. 🛡️ Panel Bimbingan Konseling (BK) *(Perencanaan Ke Depan)*
- Pencatatan buku kasus dan pelanggaran kedisiplinan siswa.
- Sistem Kalkulasi Poin Pelanggaran.
- Pelacakan tindak lanjut dan surat panggilan orang tua.

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

*Dikembangkan untuk digitalisasi pendidikan Indonesia.* 🇮🇩
