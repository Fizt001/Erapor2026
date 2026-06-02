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

## 🌟 Modul & Fitur Utama

### 1. Manajemen Kurikulum & Admin
- Pengaturan Tahun Ajaran & Titimangsa (Periode Asesmen).
- Manajemen Struktur Kurikulum Umum & Kejuruan (Konsentrasi Keahlian).
- Plotting Guru Pengampu & Wali Kelas.
- Penentuan Standar KKM & Template Deskripsi Capaian.

### 2. Panel Guru Mata Pelajaran
- **Dashboard Guru:** Statistik kelas, jumlah siswa, dan mata pelajaran yang diajar.
- **Penilaian Formatif:** Pencatatan perkembangan harian siswa untuk tiap Tujuan Pembelajaran (TP).
- **Penilaian Sumatif:** Input Nilai Akhir (STS / SAS) untuk rapor.
- **Rekapitulasi Akhir:** Melihat dan mencetak PDF (Print Layout khusus) rekap nilai seluruh siswa secara otomatis tanpa rumus manual.

### 3. Panel Wali Kelas *(Sedang Dikembangkan)*
- *Monitoring* kelengkapan nilai dari seluruh Guru Mapel.
- Input data tambahan rapor (Ketidakhadiran, Catatan Wali Kelas, Ekstrakurikuler).
- Evaluasi & penilaian Proyek P5.
- Cetak Rapor Akhir Siswa (Leger & Rapor Individu).

### 4. Panel Bimbingan Konseling (BK)
- Pencatatan buku kasus dan pelanggaran kedisiplinan siswa.
- Sistem Poin Pelanggaran.
- Input dan pelacakan tindak lanjut/penanganan kasus siswa.

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
