# Website Pembayaran SPP (Sistem Informasi Pembayaran Sekolah)

Website Pembayaran SPP adalah aplikasi web berbasis **PHP native (PHP 8)** yang berfungsi untuk mengelola data siswa dan transaksi pembayaran SPP sederhana.  
Proyek ini dibangun dengan konsep **full-stack sederhana** yang menerapkan fungsionalitas **CRUD (Create, Read, Update, Delete)** untuk data siswa dan pembayaran, serta memiliki sistem login multi-role (Admin & Siswa).

---

### Dibuat Oleh:
**Nama:** Syafiq Hafizh Farizi  
**NIM:** 2409106009  
**Kelas:** Informatika A 2024  

---

## Fitur Tersedia

Aplikasi ini mencakup seluruh spesifikasi utama untuk sistem manajemen pembayaran SPP sekolah:

### Autentikasi
- Sistem **Login & Logout** dengan **PHP Session**.
- Mendukung dua peran pengguna: **Admin** dan **Siswa**.
- Halaman admin & siswa dilindungi dari akses langsung tanpa sesi login.
- Logout aman menggunakan `session_destroy()`.

### CRUD Data Siswa
- **Create:** Tambah data siswa baru (tambahdata.php).  
- **Read:** Lihat dan cari data siswa (lihatdata.php).  
- **Update:** Edit data siswa & status SPP (updatedata.php).  
- **Delete:** Hapus data siswa secara permanen (hapusdata.php) dengan konfirmasi JavaScript.

### CRUD Pembayaran SPP
- Admin dapat menambah, melihat, dan menghapus data pembayaran.
- Validasi input & pengecekan NISN otomatis.
- Tabel pembayaran terhubung langsung dengan tabel siswa.
- Nominal & status pembayaran ditampilkan secara dinamis.

### Fitur Siswa
- Halaman dashboard pribadi menampilkan nama, NISN, kelas, semester, dan status pembayaran.
- Riwayat pembayaran ditampilkan secara rapi dalam tabel.

### Keamanan
- Semua query menggunakan **PDO Prepared Statements** (anti SQL Injection).  
- Seluruh data output difilter dengan `htmlspecialchars()` (anti XSS).  
- Password disimpan menggunakan `password_hash()` dan diverifikasi dengan `password_verify()`.  
- Validasi format input (termasuk NISN & nominal).  
- Penanganan kesalahan menggunakan `try...catch` tanpa menampilkan stack trace.

### User Interface
- Tampilan dashboard modern dengan layout sidebar.
- Tabel data responsif dan rapi.
- Pesan sukses & error ditampilkan secara elegan.
- Konfirmasi hapus menggunakan pop-up `confirm()`.

---

## Kebutuhan Sistem

| Komponen | Rekomendasi |
|-----------|--------------|
| PHP | ≥ 8.0 |
| MySQL | ≥ 5.7 |
| Web Server | Apache / Nginx |
| Browser | Chrome, Firefox, Edge |
| Ekstensi PHP | pdo_mysql, session |

---

## 🚀 Cara Instalasi dan Konfigurasi

### 1️⃣ Clone atau Download Proyek
```bash
git clone https://github.com/username/website-spp.git
