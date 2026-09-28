# TitikNemu Unsoed — Struktur Database

## 9. Struktur Database

TitikNemu Unsoed menggunakan beberapa tabel utama.

### 9.1 Tabel `fakultas`

Tabel referensi data fakultas.

| Field | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| nama_fakultas | VARCHAR | Nama fakultas |

### 9.2 Tabel `prodi`

Tabel referensi data program studi, terhubung ke fakultas.

| Field | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| fakultas_id | INT | Foreign key ke `fakultas.id` |
| jenjang | VARCHAR | Jenjang pendidikan (S1 / D3) |
| nama_prodi | VARCHAR | Nama program studi |

### Sumber Data Seed

Data awal fakultas dan program studi diambil dari file **`data-fakultas-prodi-unsoed.csv`** yang ada di root proyek. File CSV ini bersumber dari website Adkesma Unsoed.

Dari file CSV tersebut, kolom yang digunakan untuk seed database:
- **Fakultas** → tabel `fakultas.nama_fakultas` (ambil nilai unik)
- **Nama Program Studi** → tabel `prodi.nama_prodi`
- **Jenjang** → tabel `prodi.jenjang` (S1 atau D3)
- **Fakultas** → untuk menentukan relasi `prodi.fakultas_id`

Kolom lain di CSV (Kategori, No, Kode, Peringkat) tidak perlu disimpan ke database.

### Format Tampilan Program Studi

Pada dropdown pendaftaran dan tampilan di website, program studi ditampilkan dengan format:

```
[Jenjang] - [Nama Prodi]
```

Contoh:
- `S1 - Teknik Sipil`
- `D3 - Bahasa Inggris`
- `S1 - Informatika`

Format ini menggabungkan field `jenjang` dan `nama_prodi` dari tabel `prodi` agar pengguna bisa langsung membedakan jenjang program studi.

### 9.3 Tabel `users`

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

### 9.4 Tabel `items`

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

### Relasi Antar Tabel

```text
fakultas ──< prodi

fakultas ──< users ──< items
```

Satu fakultas dapat memiliki banyak prodi. Satu user dapat memiliki banyak laporan barang. Setiap laporan barang menandai satu fakultas sebagai lokasi.

### 9.5 Tabel `messages` (opsional)

Tabel ini bersifat opsional jika sistem komunikasi ingin dibuat.

| Field | Tipe | Keterangan |
|---|---|---|
| id | INT | Primary key |
| sender_id | INT | Pengirim pesan |
| receiver_id | INT | Penerima pesan |
| item_id | INT | Barang yang dibicarakan |
| message | TEXT | Isi pesan |
| created_at | TIMESTAMP | Waktu pesan |

Untuk tahap awal tugas, tabel ini **tidak wajib digunakan** — kontak antar pengguna sudah cukup ditangani melalui tombol "Hubungi via WhatsApp" (lihat fitur 7.9).
