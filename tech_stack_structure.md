# 🏛️ Arsitektur & Teknologi Erapor2026

Dokumen ini merangkum tumpukan teknologi (Tech Stack) dan struktur program yang digunakan dalam pengembangan sistem **Erapor2026**.

## 🚀 Teknologi Utama

Proyek ini dibangun menggunakan arsitektur *Headless / Decoupled*, di mana Frontend dan Backend terpisah sepenuhnya dan berkomunikasi melalui **RESTful API**.

### 💻 Frontend (FE)
* **Framework:** Nuxt 3 (Vue.js 3 - Composition API)
* **Styling:** Tailwind CSS (dengan *print modifiers* kustom)
* **HTTP Client:** Built-in `$fetch` dari Nuxt
* **State/Store:** Vue `ref` / `computed`
* **Penyimpanan Lokal:** Nuxt `useCookie` (untuk `auth_token` & `user_profile`)
* **Notifikasi & UI Feedback:** SweetAlert2 (kustom *toast* dan dialog modal)
* **Arsitektur Tampilan:** Konsep spesifik proyek **"Dock & Flow"** (2 panel responsif untuk Sidebar Filter di kiri dan Konten Tabel di kanan).

### ⚙️ Backend (BE)
* **Framework:** Laravel 11 (PHP 8.x)
* **Database:** MySQL
* **Autentikasi:** Laravel Sanctum (Token-based API Authentication)
* **Routing:** Berbasis API (melalui `routes/api.php`)
* **Response Format:** Standarisasi respon JSON tunggal yang konsisten (`success` boolean, `message` string, `data` object/array).

---

## 📂 Struktur Program & Direktori

### 🎨 Struktur Frontend (`/erapor-fe/app`)
* **`pages/`** : Berisi file *routing* halaman aplikasi (contoh: `pages/bk/buku-kasus.vue`, `pages/bk/dashboard.vue`).
* **`layouts/`** : Kerangka *wrapper* utama (contoh: `layouts/admin.vue` dan `layouts/bk.vue` yang berisi *Sidebar* dan *Navbar* untuk masing-masing *role*).
* **`middleware/`** : Pengecekan otorisasi (contoh: `middleware/bk.js` untuk mengamankan halaman agar hanya pengguna dengan *role* BK yang bisa mengakses).
* **`composables/`** : Kumpulan fungsi logika *reusable* global (contoh: `useSwal()` untuk menampilkan popup notifikasi dari SweetAlert2, `useSekolah()` untuk menarik data profil sekolah).
* **`components/`** : Potongan UI *reusable* (tombol, modal, dll).

### 🛠️ Struktur Backend (`/erapor-api/app`)
* **`Http/Controllers/Api/`** : Tempat penulisan logika bisnis, dikelompokkan berdasarkan *role* (contoh: `Api/Bk/BkBukuKasusController.php` mengatur algoritma penarikan data pelanggaran siswa dan filter semester).
* **`Models/`** : Representasi relasional ORM Eloquent ke struktur tabel (contoh: `Siswa`, `PoinLog`, `PenangananPelanggaran`, `Titimangsa`).
* **`Http/Middleware/`** : Lapis pertahanan rute (misalnya pengecekan hak akses admin vs bk).
* **`routes/api.php`** : Pemetaan URL ke Controller. Semua komunikasi FE-BE murni terjadi di file rute ini, tidak ada yang dirender *server-side* langsung oleh Laravel.

---

## 🔗 Alur Komunikasi Interaktif (Data Flow)
1. **User (Guru BK)** menekan suatu elemen di UI Frontend (seperti tombol cetak, pilih filter siswa, dll).
2. **Vue.js / Nuxt 3 (FE)** menangkap *event* tersebut dan mengirimkan HTTP *Request* (via `$fetch`), menyertakan `Bearer Token` JWT/Sanctum untuk keamanan.
3. **Laravel 11 (BE)** memvalidasi token, menarik data rekam jejak siswa dari **MySQL** menggunakan *Query Builder* / Eloquent. Data kemudian disaring berdasarkan aturan bisnis (misal: hanya ujian akhir/semester tertentu).
4. Data dikembalikan ke Frontend dalam bentuk **JSON API**.
5. **Vue.js (FE)** merespon JSON tersebut, memperbarui *state* reaktif (seperti daftar siswa / log), dan UI otomatis berubah, lengkap dengan adaptasi ke **Mode Print** menggunakan Tailwind CSS secara interaktif.
