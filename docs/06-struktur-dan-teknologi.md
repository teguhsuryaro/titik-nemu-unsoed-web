# TitikNemu Unsoed — Struktur Halaman & Teknologi

## 10. Struktur Halaman

Struktur halaman yang direncanakan:

```text
TitikNemu-Unsoed/
│
├── index.php
├── login.php
├── register.php
│
├── dashboard.php
├── search.php
├── detail.php
├── profile.php
│
├── laporan/
│   ├── tambah.php
│   ├── edit.php
│   ├── hapus.php
│   └── update_status.php
│
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   └── laporan.php
│
├── ajax/
│   └── get_prodi.php
│
├── config/
│   └── database.php
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
│
├── css/
│   └── style.css
│
├── js/
│   └── fakultas-prodi.js
│
└── uploads/
```

Struktur tersebut dapat disederhanakan atau dikembangkan sesuai kebutuhan saat implementasi.

---

## 11. Teknologi

TitikNemu Unsoed dibuat menggunakan teknologi dasar pengembangan web:

### Frontend

- HTML
- CSS
- JavaScript vanilla — dipakai **secukupnya**, khususnya untuk AJAX dropdown Fakultas → Program Studi agar tidak perlu reload halaman

### Backend

- PHP Native

### Database

- MySQL

### Local Development

- Laragon

Proyek ini tidak menggunakan framework seperti Laravel agar tetap sesuai dengan tujuan tugas Pemrograman Web, dan penggunaan JavaScript diminimalisir kecuali memang benar-benar dibutuhkan (seperti kasus dropdown dinamis di atas).

---

## 12. Konsep CRUD

TitikNemu Unsoed juga menerapkan konsep **CRUD (Create, Read, Update, Delete)**.

### Create

Pengguna membuat laporan barang.

### Read

Pengguna melihat daftar dan detail barang.

### Update

Pengguna mengubah informasi laporan atau status barang miliknya sendiri; admin dapat mengubah status pengguna (banned).

### Delete

Pengguna/admin menghapus laporan.

Dengan demikian, proyek dapat menunjukkan penerapan operasi database menggunakan PHP dan MySQL.
