# TitikNemu Unsoed

> **Cari. Temukan. Kembalikan.**

## 1. Deskripsi Singkat

**TitikNemu Unsoed** adalah sistem informasi berbasis web yang dibuat khusus untuk membantu mahasiswa **Universitas Jenderal Soedirman (Unsoed)** dalam melaporkan, mencari, dan menemukan kembali barang yang hilang atau ditemukan di lingkungan kampus.

Sistem ini menyediakan satu tempat terpusat untuk menginformasikan barang hilang dan barang ditemukan. Dengan adanya sistem ini, pengguna tidak perlu hanya mengandalkan penyebaran informasi melalui grup kelas, grup angkatan, atau bertanya secara langsung kepada orang-orang di sekitar lokasi.

TitikNemu Unsoed mempertemukan dua kondisi utama, yaitu **orang yang kehilangan barang** dan **orang yang menemukan barang**, sehingga informasi mengenai suatu barang dapat ditemukan oleh pihak yang membutuhkan.

Untuk memastikan sistem ini benar-benar hanya digunakan oleh civitas Unsoed, pendaftaran akun dibatasi hanya untuk pemilik email `@mhs.unsoed.ac.id`.

---

## 2. Latar Belakang

Kehilangan barang di lingkungan kampus merupakan permasalahan yang dapat terjadi kepada mahasiswa maupun civitas akademika. Ketika kehilangan barang, seseorang biasanya akan mencari dengan cara bertanya kepada teman, mahasiswa di sekitar lokasi, cleaning service, satpam, atau menyebarkan informasi melalui grup percakapan.

Cara tersebut memiliki keterbatasan karena informasi tidak selalu dapat menjangkau semua orang di lingkungan kampus.

Di sisi lain, seseorang yang menemukan barang belum tentu mengetahui siapa pemilik barang tersebut. Barang yang ditemukan terkadang hanya disimpan oleh penemu tanpa adanya informasi yang dapat menghubungkan barang tersebut dengan pemiliknya.

Sebagai contoh, seorang mahasiswa kehilangan kunci di lingkungan kampus. Setelah bertanya kepada orang-orang di sekitar lokasi dan menyebarkan informasi melalui beberapa grup, ternyata kunci tersebut sebenarnya telah ditemukan oleh mahasiswa dari program studi lain. Namun, mahasiswa yang menemukan kunci tersebut tidak mengetahui siapa pemiliknya dan tidak menyebarkan informasi mengenai penemuan tersebut.

Permasalahan tersebut menunjukkan perlunya sebuah media terpusat yang dapat digunakan untuk mencatat informasi mengenai barang hilang dan barang ditemukan.

TitikNemu Unsoed dibuat sebagai solusi sederhana untuk membantu proses tersebut, dengan fokus awal khusus di lingkungan kampus Unsoed.

---

## 3. Tujuan

TitikNemu Unsoed memiliki beberapa tujuan, yaitu:

1. Memudahkan pengguna dalam melaporkan barang yang hilang.
2. Memudahkan pengguna dalam melaporkan barang yang ditemukan.
3. Menyediakan tempat terpusat untuk mencari informasi barang.
4. Mempertemukan pemilik barang dengan orang yang menemukan barang.
5. Mengurangi ketergantungan terhadap penyebaran informasi melalui banyak grup.
6. Membantu proses pengembalian barang kepada pemiliknya.
7. Membuat proses pendataan barang hilang dan ditemukan menjadi lebih terorganisir.
8. Memastikan pengguna sistem benar-benar berasal dari civitas Unsoed melalui validasi domain email.

---

## 4. Target Pengguna

Karena pendaftaran akun dibatasi hanya untuk email `@mhs.unsoed.ac.id`, target pengguna TitikNemu Unsoed versi awal ini difokuskan khusus pada **mahasiswa aktif Universitas Jenderal Soedirman**.

Pengembangan ke civitas akademika lain (dosen, tenaga kependidikan, staff) dapat menjadi arah pengembangan pada versi berikutnya apabila dibutuhkan.

---

## 5. Konsep Utama Sistem

TitikNemu Unsoed memiliki dua jenis laporan utama:

### 5.1 Barang Hilang

Digunakan ketika pengguna kehilangan suatu barang.

Contoh:

> **Nama barang:** Kunci motor
> **Fakultas:** Fakultas Teknik
> **Lokasi detail:** Parkiran motor Gedung F1
> **Tanggal:** 24 September 2026
> **Deskripsi:** Kunci motor dengan gantungan berwarna hitam.

### 5.2 Barang Ditemukan

Digunakan ketika pengguna menemukan barang milik orang lain.

Contoh:

> **Nama barang:** Kunci
> **Fakultas ditemukan:** Fakultas Teknik
> **Lokasi detail:** Sekitar gedung F1
> **Tanggal:** 24 September 2026
> **Deskripsi:** Sebuah kunci dengan gantungan berwarna hitam ditemukan di sekitar gedung.

Setiap laporan kini menyertakan **tag Fakultas** (dropdown) sebagai penanda lokasi utama, ditambah **kolom lokasi detail** (isian bebas) untuk keterangan yang lebih spesifik. Tag Fakultas ini yang nantinya digunakan untuk pencarian/filter, sedangkan lokasi detail hanya bersifat deskriptif.

Kedua jenis laporan tersebut dapat dilihat dan dicari oleh pengguna sehingga memungkinkan pemilik menemukan informasi mengenai barangnya.

---

## 6. Alur Utama Sistem

```text
                    TITIKNEMU UNSOED
                        │
              ┌─────────┴─────────┐
              │                   │
              ▼                   ▼
        BARANG HILANG       BARANG DITEMUKAN
              │                   │
              ▼                   ▼
       Buat Laporan         Buat Laporan
       (+ tag Fakultas)     (+ tag Fakultas)
              │                   │
              └─────────┬─────────┘
                        ▼
                 Database Sistem
                        │
                        ▼
                 Pencarian & Filter Barang
                        │
                        ▼
                  Detail Barang
                        │
                        ▼
             Hubungi via WhatsApp (wa.me)
                        │
                        ▼
        Pelapor menandai status laporannya
                        │
                        ▼
                Barang Dikembalikan
```

---

# 7. Fitur Sistem

## 7.1 Registrasi

Pengguna dapat membuat akun baru dengan mengisi:

- Email Unsoed (lihat 7.1.1)
- Nama lengkap
- Nomor HP/WhatsApp
- Fakultas (dropdown)
- Program studi (dropdown, mengikuti fakultas yang dipilih — lihat 7.1.2)
- Password
- Konfirmasi password

Contoh form:

```text
Daftar Akun TitikNemu Unsoed

Nama Lengkap      : [ ......................... ]
Email             : [ teguh.romadon ] @mhs.unsoed.ac.id
No. HP/WhatsApp   : [ 08xxxxxxxxxx ]
Fakultas          : [ Pilih Fakultas        ▼ ]
Program Studi     : [ Pilih Program Studi   ▼ ]
Password          : [ ******** ]
Konfirmasi Pass   : [ ******** ]

[ Daftar ]
```

Data pengguna (kecuali konfirmasi password) akan disimpan ke database.

### 7.1.1 Validasi Domain Email

Pengguna hanya perlu mengisi bagian depan alamat email (contoh: `teguh.romadon`). Bagian domain `@mhs.unsoed.ac.id` ditampilkan sebagai teks tetap di sebelah input (tanpa memerlukan JavaScript), lalu digabungkan dengan input pengguna sebelum disimpan/divalidasi di sisi server.

Validasi tambahan di backend memastikan:

- Format bagian depan sesuai (tidak kosong, tidak mengandung karakter tidak valid).
- Email hasil gabungan bersifat unik (belum terdaftar sebelumnya).

### 7.1.2 Dropdown Fakultas & Program Studi

- Pilihan **Fakultas** dan **Program Studi** menggunakan data referensi yang sudah tersedia (tabel `fakultas` dan `prodi`, lihat Bagian 9).
- Ketika pengguna memilih Fakultas, pilihan Program Studi otomatis terfilter agar hanya menampilkan prodi yang ada di fakultas tersebut.
- Mekanisme ini menggunakan **AJAX (vanilla JavaScript)**: saat opsi Fakultas berubah, JavaScript mengirim permintaan ke endpoint `ajax/get_prodi.php` untuk mengambil daftar prodi terkait secara dinamis, tanpa perlu reload halaman.
- Ini menjadi salah satu bagian yang memang membutuhkan JavaScript, sesuai prinsip "gunakan JS hanya bila benar-benar diperlukan".

Field konfirmasi password hanya digunakan untuk validasi pada form (dicocokkan dengan field password) dan **tidak disimpan** ke database.

---

## 7.2 Login

Pengguna dapat masuk ke sistem menggunakan email Unsoed dan password.

Setelah berhasil login, sistem akan membuat session untuk mengenali pengguna.

Jika akun pengguna berstatus **banned** oleh admin, sistem menolak proses login dan menampilkan pesan bahwa akun telah dinonaktifkan (lihat Bagian 8).

---

## 7.3 Dashboard

Dashboard menampilkan informasi utama mengenai sistem.

Contohnya:

```text
Selamat datang, Teguh!

┌──────────────────┐  ┌──────────────────┐
│  Barang Hilang   │  │ Barang Ditemukan  │
│       25         │  │        18        │
└──────────────────┘  └──────────────────┘

Laporan Saya
────────────────────────────────────────
Kunci Motor       Ditemukan
Dompet            Hilang
Flashdisk         Dikembalikan
```

---

## 7.4 Membuat Laporan Barang Hilang

Pengguna dapat membuat laporan ketika kehilangan barang.

Form dapat berisi:

- Nama barang
- Kategori
- Fakultas (dropdown, penanda lokasi kehilangan)
- Lokasi detail (isian bebas, opsional)
- Tanggal kehilangan
- Deskripsi
- Foto barang

Informasi kontak **tidak lagi diisi manual** — sistem otomatis menggunakan nomor HP/WhatsApp yang telah didaftarkan pengguna saat registrasi.

Contoh:

```text
Nama Barang        : Kunci Motor
Kategori           : Kunci
Fakultas           : [ Fakultas Teknik ▼ ]
Lokasi Detail      : Parkiran motor Gedung F1
Tanggal            : 24 September 2026
Deskripsi          : Kunci dengan gantungan hitam
Foto               : [Upload Foto]

[ Kirim Laporan ]
```

---

## 7.5 Membuat Laporan Barang Ditemukan

Pengguna juga dapat membuat laporan ketika menemukan barang.

Form dapat berisi:

- Nama barang
- Kategori
- Fakultas (dropdown, penanda lokasi ditemukan)
- Lokasi detail (isian bebas, opsional)
- Tanggal ditemukan
- Deskripsi
- Foto barang

Sama seperti laporan barang hilang, informasi kontak diambil otomatis dari nomor HP/WhatsApp yang terdaftar, tidak perlu diisi ulang di form.

---

## 7.6 Daftar Barang

Sistem menyediakan halaman yang menampilkan laporan barang.

Pengguna dapat membedakan:

- Barang hilang
- Barang ditemukan

Contoh:

```text
┌─────────────────────────────────────┐
│ 🔑 Kunci Motor                      │
│ Barang Ditemukan                    │
│ Fakultas Teknik                     │
│ 24 September 2026                   │
│                                     │
│ [ Lihat Detail ]                    │
└─────────────────────────────────────┘
```

---

## 7.7 Pencarian Barang

Pengguna dapat mencari barang berdasarkan kata kunci, dan memfilter berdasarkan Fakultas.

Contoh:

```text
Cari barang: [ kunci ]

Kategori : [ Semua        ▼ ]
Fakultas : [ Semua Fakultas ▼ ]

[ Cari ]
```

Sistem kemudian menampilkan laporan yang sesuai dengan pencarian.

Pencarian dapat dikembangkan berdasarkan:

- Nama barang
- Kategori
- Fakultas
- Jenis laporan

---

## 7.8 Detail Barang

Setiap laporan memiliki halaman detail.

Contoh:

```text
Kunci Motor

Status     : Ditemukan
Kategori   : Kunci
Fakultas   : Fakultas Teknik
Lokasi     : Parkiran motor Gedung F1
Tanggal    : 24 September 2026

Deskripsi:
Ditemukan sebuah kunci dengan gantungan
berwarna hitam di sekitar Gedung F1.

Dilaporkan oleh:
Andi - Informatika

[ Hubungi via WhatsApp ]
```

---

## 7.9 Hubungi Pelapor

Pengguna yang menemukan laporan yang dianggap berkaitan dengan barangnya dapat langsung menghubungi pelapor melalui **WhatsApp**.

Sistem **tidak menyediakan fitur chat/pesan internal**. Nomor kontak diambil otomatis dari nomor HP/WhatsApp yang didaftarkan pelapor saat registrasi.

Tombol **"Hubungi via WhatsApp"** akan langsung mengarahkan pengguna ke chat WhatsApp pelapor menggunakan tautan `https://wa.me/<nomor>`, sehingga pengguna cukup satu klik tanpa perlu sistem pesan tambahan di dalam website.

> Catatan: pendekatan ini hanya memanfaatkan tautan deep-link WhatsApp biasa (`wa.me`), **bukan** integrasi WhatsApp Business API resmi — sehingga tetap sesuai dengan batasan proyek versi awal (lihat Bagian 14).

---

## 7.10 Status Barang

Setiap laporan memiliki status untuk menunjukkan kondisi terbaru barang.

Status yang digunakan:

```text
Hilang
   ↓
Ditemukan
   ↓
Dikembalikan
```

### Hilang

Barang masih dicari oleh pemilik.

### Ditemukan

Barang telah ditemukan oleh seseorang.

### Dikembalikan

Barang telah berhasil dikembalikan kepada pemilik.

Perubahan status dilakukan secara mandiri oleh masing-masing pelapor pada laporan miliknya sendiri:

- Pelapor **Barang Hilang** dapat menandai laporannya menjadi **Ditemukan/Selesai** setelah barangnya berhasil ditemukan kembali.
- Pelapor **Barang Ditemukan** dapat menandai laporannya menjadi **Dikembalikan** setelah barang berhasil diserahkan ke pemiliknya.

### 7.10.1 Alert Pembaruan Status

Untuk menjaga timeline tetap relevan dan tidak dipenuhi laporan yang sebenarnya sudah selesai, sistem menampilkan alert/reminder kepada pelapor, contoh:

```text
⚠️ Barang kamu sudah ditemukan/kembali?
Klik tombol "Tandai Selesai" supaya laporan ini
tidak lagi tampil aktif di daftar pencarian.
```

Alert ini dapat ditampilkan sebagai banner sederhana di halaman "Laporan Saya" atau di halaman detail laporan milik pengguna sendiri.

---

## 7.11 Riwayat Laporan

Pengguna dapat melihat laporan yang pernah dibuat.

Contoh:

```text
Laporan Saya

┌────────────────────────────────────┐
│ Kunci Motor                        │
│ Hilang                             │
│ 24 September 2026                  │
│ [ Tandai Selesai ]                 │
└────────────────────────────────────┘

┌────────────────────────────────────┐
│ Flashdisk                          │
│ Dikembalikan                       │
│ 20 September 2026                  │
└────────────────────────────────────┘
```

---

# 8. Admin

TitikNemu Unsoed menggunakan **satu akun admin** (dibuat manual/seed langsung di database, bukan melalui alur pendaftaran biasa) yang bertugas memantau jalannya sistem.

Admin tetap mengakses tampilan/halaman yang sama seperti pengguna biasa (dashboard, daftar barang, pencarian, dll.), namun mendapat **menu dan hak akses tambahan**:

- Melihat seluruh laporan dari semua pengguna.
- Menghapus laporan/upload yang tidak sesuai.
- Melihat daftar pengguna yang terdaftar.
- Melakukan **banned** terhadap pengguna tertentu.
- Mengelola kategori barang (opsional).

### Efek Banned

Ketika seorang pengguna di-banned oleh admin:

- Pengguna tersebut **tidak dapat login kembali** ke sistem.
- Seluruh laporan miliknya otomatis **tidak lagi tampil** di timeline/daftar barang.
- Secara teknis, ini dilakukan dengan melakukan `JOIN` ke tabel `users` saat query daftar laporan, dan hanya menampilkan laporan dari pengguna dengan `is_banned = 0`. Data laporan tidak dihapus permanen dari database, hanya disembunyikan dari tampilan selama akun pemiliknya masih berstatus banned.

Untuk versi tugas sederhana, fungsi admin dibuat secukupnya tanpa sistem moderasi kompleks (tanpa alasan banned terstruktur, tanpa proses banding, dsb).

---

# 9. Struktur Database

TitikNemu Unsoed menggunakan beberapa tabel utama.

## 9.1 Tabel `fakultas`

Tabel referensi data fakultas.

| Field | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| nama_fakultas | VARCHAR | Nama fakultas |

## 9.2 Tabel `prodi`

Tabel referensi data program studi, terhubung ke fakultas.

| Field | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| fakultas_id | INT | Foreign key ke `fakultas.id` |
| nama_prodi | VARCHAR | Nama program studi |

Data awal kedua tabel di atas diisi (seed) dari data referensi fakultas & program studi Unsoed yang sudah tersedia.

## 9.3 Tabel `users`

Digunakan untuk menyimpan data pengguna.

| Field | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| nama | VARCHAR | Nama lengkap pengguna |
| email | VARCHAR | Email pengguna, unik, format `depan@mhs.unsoed.ac.id` |
| no_hp | VARCHAR | Nomor HP/WhatsApp, dipakai untuk tombol "Hubungi via WhatsApp" |
| fakultas_id | INT | Foreign key ke `fakultas.id` |
| prodi_id | INT | Foreign key ke `prodi.id` |
| password | VARCHAR | Password yang telah di-hash |
| role | ENUM | user/admin |
| is_banned | TINYINT(1) | Status banned, default 0 |
| created_at | TIMESTAMP | Waktu pembuatan akun |

Catatan: field "konfirmasi password" pada form registrasi hanya divalidasi di sisi form/server (dicocokkan dengan field password) dan tidak memiliki kolom tersendiri di tabel ini.

## 9.4 Tabel `items`

Digunakan untuk menyimpan laporan barang.

| Field | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| user_id | INT | ID pengguna pembuat laporan |
| type | ENUM | lost/found |
| nama_barang | VARCHAR | Nama barang |
| kategori | VARCHAR | Kategori barang |
| fakultas_id | INT | Foreign key ke `fakultas.id`, penanda lokasi kehilangan/penemuan |
| lokasi | VARCHAR | Keterangan lokasi detail, isian bebas (opsional) |
| tanggal | DATE | Tanggal kejadian |
| deskripsi | TEXT | Deskripsi barang |
| foto | VARCHAR | Nama/path foto |
| status | ENUM | lost/found/returned |
| created_at | TIMESTAMP | Waktu laporan dibuat |

Relasi:

```text
fakultas ──< prodi

fakultas ──< users ──< items
```

Satu fakultas dapat memiliki banyak prodi. Satu user dapat memiliki banyak laporan barang. Setiap laporan barang menandai satu fakultas sebagai lokasi.

## 9.5 Tabel `messages` (opsional)

Tabel ini bersifat opsional jika sistem komunikasi ingin dibuat.

| Field | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| sender_id | INT | Pengirim pesan |
| receiver_id | INT | Penerima pesan |
| item_id | INT | Barang yang dibicarakan |
| message | TEXT | Isi pesan |
| created_at | TIMESTAMP | Waktu pesan |

Untuk tahap awal tugas, tabel ini **tidak wajib digunakan** — kontak antar pengguna sudah cukup ditangani melalui tombol "Hubungi via WhatsApp" (lihat 7.9).

---

# 10. Struktur Halaman

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

# 11. Teknologi

TitikNemu Unsoed dibuat menggunakan teknologi dasar pengembangan web:

### Frontend

- HTML
- CSS
- JavaScript vanilla — dipakai **secukupnya**, khususnya untuk AJAX dropdown Fakultas → Program Studi (lihat 7.1.2) agar tidak perlu reload halaman

### Backend

- PHP Native

### Database

- MySQL

### Local Development

- Laragon

Proyek ini tidak menggunakan framework seperti Laravel agar tetap sesuai dengan tujuan tugas Pemrograman Web, dan penggunaan JavaScript diminimalisir kecuali memang benar-benar dibutuhkan (seperti kasus dropdown dinamis di atas).

---

# 12. Konsep CRUD

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

---

# 13. Skenario Penggunaan

### Skenario 1 — Kehilangan Barang

Teguh kehilangan kunci motor di Gedung Teknik.

1. Teguh login ke TitikNemu Unsoed menggunakan email `teguh.romadon@mhs.unsoed.ac.id`.
2. Memilih **Laporkan Barang Hilang**.
3. Mengisi informasi kunci, termasuk memilih tag Fakultas Teknik.
4. Mengirim laporan.
5. Laporan tampil sebagai barang hilang.
6. Beberapa waktu kemudian seseorang menemukan kunci tersebut.
7. Orang tersebut membuat laporan barang ditemukan, juga menandai Fakultas Teknik.
8. Teguh mencari `kunci` di TitikNemu Unsoed, atau memfilter berdasarkan Fakultas Teknik.
9. Teguh menemukan laporan yang sesuai.
10. Teguh klik tombol **"Hubungi via WhatsApp"** untuk langsung chat dengan penemu.
11. Barang dikembalikan.
12. Teguh menandai laporannya sendiri sebagai **Ditemukan/Selesai**.

---

### Skenario 2 — Menemukan Barang

Seorang mahasiswa menemukan dompet di depan gedung kampus.

1. Mahasiswa login menggunakan email Unsoed.
2. Memilih **Laporkan Barang Ditemukan**.
3. Mengisi informasi dompet, termasuk tag Fakultas lokasi penemuan.
4. Mengunggah foto jika diperlukan.
5. Mengirim laporan.
6. Laporan muncul di daftar barang ditemukan.
7. Pemilik mencari barangnya melalui TitikNemu Unsoed.
8. Pemilik klik tombol **"Hubungi via WhatsApp"** untuk menghubungi penemu.
9. Barang dikembalikan.
10. Penemu menandai laporannya sendiri sebagai **Dikembalikan**.

---

# 14. Batasan Sistem

Karena TitikNemu Unsoed dibuat sebagai proyek sederhana menggunakan HTML, CSS, PHP, dan MySQL, sistem memiliki beberapa batasan.

Fitur yang **tidak menjadi fokus versi awal**:

- Chat real-time di dalam website (kontak antar pengguna tetap diarahkan keluar ke WhatsApp pribadi masing-masing, bukan fitur pesan internal).
- Notifikasi email otomatis.
- Integrasi WhatsApp Business API resmi — sistem hanya memakai tautan deep-link `wa.me` biasa untuk membuka chat, bukan API resmi/berbayar.
- GPS atau pelacakan lokasi secara real-time.
- AI untuk mencocokkan foto barang.
- Aplikasi Android/iOS.
- Cloud storage.
- Verifikasi identitas otomatis (selain validasi format & keunikan domain email `@mhs.unsoed.ac.id`).
- Sistem keamanan tingkat production.
- Integrasi dengan sistem akademik kampus (SIA, dsb).

Fokus utama sistem adalah:

```text
Login (email Unsoed)
   ↓
Laporan Barang (+ tag Fakultas)
   ↓
Pencarian & Filter
   ↓
Detail Barang
   ↓
Hubungi via WhatsApp
   ↓
Pengembalian Barang
```

---

# 15. Pengembangan Selanjutnya

Apabila TitikNemu Unsoed dikembangkan lebih lanjut, beberapa fitur yang dapat ditambahkan adalah:

- Notifikasi ketika ada barang yang diduga cocok.
- Sistem chat internal antara pemilik dan penemu.
- Filter lokasi yang lebih detail per gedung/ruangan (bukan hanya per Fakultas).
- Upload lebih dari satu foto.
- Sistem verifikasi klaim kepemilikan.
- QR Code untuk laporan barang.
- Notifikasi email atau WhatsApp otomatis.
- Sistem reputasi pengguna.
- Dashboard statistik kehilangan dan penemuan barang.
- Integrasi dengan sistem akademik kampus.
- Perluasan target pengguna ke dosen/tenaga kependidikan/civitas non-mahasiswa.
- PWA atau aplikasi mobile.

Fitur-fitur tersebut tidak diperlukan untuk versi tugas dasar, tetapi dapat menjadi gambaran pengembangan TitikNemu Unsoed apabila sistem dikembangkan menjadi aplikasi yang lebih serius.

---

# 16. Kesimpulan

**TitikNemu Unsoed** merupakan sistem informasi berbasis web yang bertujuan membantu proses pelaporan dan pencarian barang hilang maupun barang ditemukan di lingkungan kampus Unsoed, dengan akses yang dibatasi khusus untuk mahasiswa pemilik email `@mhs.unsoed.ac.id`.

Sistem ini menyediakan tempat terpusat bagi pengguna untuk melaporkan barang (lengkap dengan tag Fakultas), mencari/memfilter informasi barang, melihat detail laporan, menghubungi pelapor langsung via WhatsApp, dan memperbarui status barang secara mandiri hingga berhasil dikembalikan. Seorang admin tunggal bertugas memantau jalannya sistem, termasuk kemampuan menghapus laporan dan mem-banned pengguna yang bermasalah.

Dengan menggunakan **HTML, CSS, PHP Native, MySQL, dan sedikit JavaScript vanilla (khusus dropdown Fakultas-Prodi)**, TitikNemu Unsoed dapat mengimplementasikan berbagai konsep dasar pemrograman web seperti:

- Form HTML
- Styling dengan CSS
- PHP
- Session dan autentikasi (termasuk validasi domain email)
- AJAX sederhana
- CRUD
- Database MySQL
- Relasi antar tabel (termasuk data referensi fakultas/prodi)
- Upload file
- Pencarian & filter data
- Pengelolaan status laporan
- Manajemen pengguna oleh admin (banned)

Konsep utama TitikNemu Unsoed dapat dirangkum dalam satu alur:

> **Barang hilang → Dilaporkan (+ tag Fakultas) → Barang ditemukan → Informasi dipertemukan → Hubungi via WhatsApp → Barang dikembalikan.**