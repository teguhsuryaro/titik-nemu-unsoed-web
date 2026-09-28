# TitikNemu Unsoed — Fitur Sistem

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

- Pilihan **Fakultas** dan **Program Studi** menggunakan data referensi yang sudah tersedia di file **`data-fakultas-prodi-unsoed.csv`** (di root proyek, bersumber dari website Adkesma Unsoed). Data dari CSV ini di-seed ke tabel `fakultas` dan `prodi` di database (lihat dokumen database).
- Ketika pengguna memilih Fakultas, pilihan Program Studi otomatis terfilter agar hanya menampilkan prodi yang ada di fakultas tersebut.
- **Format tampilan prodi** di dropdown: `[Jenjang] - [Nama Prodi]`, contoh: `S1 - Teknik Sipil`, `D3 - Bahasa Inggris`. Format ini mengambil field `jenjang` dan `nama_prodi` dari tabel `prodi`.
- Mekanisme ini menggunakan **AJAX (vanilla JavaScript)**: saat opsi Fakultas berubah, JavaScript mengirim permintaan ke endpoint `ajax/get_prodi.php` untuk mengambil daftar prodi terkait secara dinamis, tanpa perlu reload halaman.
- Ini menjadi salah satu bagian yang memang membutuhkan JavaScript, sesuai prinsip "gunakan JS hanya bila benar-benar diperlukan".

Field konfirmasi password hanya digunakan untuk validasi pada form (dicocokkan dengan field password) dan **tidak disimpan** ke database.

---

## 7.2 Login

Pengguna dapat masuk ke sistem menggunakan email Unsoed dan password.

Setelah berhasil login, sistem akan membuat session untuk mengenali pengguna.

Jika akun pengguna berstatus **banned** oleh admin, sistem menolak proses login dan menampilkan pesan bahwa akun telah dinonaktifkan (lihat dokumen admin).

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

> Catatan: pendekatan ini hanya memanfaatkan tautan deep-link WhatsApp biasa (`wa.me`), **bukan** integrasi WhatsApp Business API resmi — sehingga tetap sesuai dengan batasan proyek versi awal.

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
