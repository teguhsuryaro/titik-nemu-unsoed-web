# Panduan Prompt — Membangun TitikNemu Unsoed dari Nol

> Dokumen ini berisi kumpulan prompt dan panduan langkah demi langkah untuk membangun website TitikNemu Unsoed menggunakan AI coding assistant. Setiap fase memiliki prompt siap pakai yang bisa langsung diberikan ke AI.

---

## Daftar Isi

1. [Persiapan & Setup Awal](#fase-1-persiapan--setup-awal)
2. [Database & Seed Data](#fase-2-database--seed-data)
3. [Konfigurasi & Fondasi](#fase-3-konfigurasi--fondasi)
4. [Autentikasi (Register & Login)](#fase-4-autentikasi-register--login)
5. [Halaman Beranda / Dashboard](#fase-5-halaman-beranda--dashboard)
6. [Form Laporan Barang](#fase-6-form-laporan-barang)
7. [Daftar & Pencarian Barang](#fase-7-daftar--pencarian-barang)
8. [Detail Barang & Hubungi Pelapor](#fase-8-detail-barang--hubungi-pelapor)
9. [Manajemen Laporan (Edit, Hapus, Status)](#fase-9-manajemen-laporan-edit-hapus-status)
10. [Panel Admin](#fase-10-panel-admin)
11. [Styling & Polish](#fase-11-styling--polish)
12. [Testing & Finishing](#fase-12-testing--finishing)

---

## Cara Menggunakan Dokumen Ini

1. **Baca dokumen referensi** — Sebelum memulai, pastikan AI memiliki konteks dari file-file di folder `docs/` dan file `DESIGN.md`.
2. **Jalankan prompt secara berurutan** — Setiap fase membangun di atas fase sebelumnya.
3. **Verifikasi setiap fase** — Setelah setiap prompt, uji hasilnya di browser melalui Laragon sebelum lanjut.
4. **Sesuaikan jika perlu** — Prompt di bawah adalah template. Sesuaikan dengan kebutuhan spesifik kamu.

---

## Prompt Konteks Awal

> Berikan prompt ini di awal percakapan agar AI memahami keseluruhan proyek.

```
Baca semua file di folder docs/ dan file DESIGN.md serta UI.png di dalam proyek ini. File-file tersebut berisi PRD (Product Requirements Document) dan panduan desain UI/UX untuk website "TitikNemu Unsoed" — sebuah sistem informasi barang hilang & ditemukan untuk mahasiswa Unsoed.

Teknologi yang digunakan:
- Frontend: HTML, CSS, JavaScript vanilla (minimal)
- Backend: PHP Native (tanpa framework)
- Database: MySQL
- Local dev: Laragon

Pahami keseluruhan dokumen ini karena kita akan membangun website ini langkah demi langkah.
```

---

## Fase 1: Persiapan & Setup Awal

### Tujuan
Menyiapkan struktur folder proyek sesuai PRD.

### Prompt

```
Buatkan struktur folder proyek TitikNemu Unsoed sesuai yang sudah didefinisikan di docs/06-struktur-dan-teknologi.md.

Buat semua folder dan file kosong (placeholder) yang diperlukan. Struktur yang dimaksud:

TitikNemu-Unsoed/
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── search.php
├── detail.php
├── profile.php
├── laporan/
│   ├── tambah.php
│   ├── edit.php
│   ├── hapus.php
│   └── update_status.php
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   └── laporan.php
├── ajax/
│   └── get_prodi.php
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
├── css/
│   └── style.css
├── js/
│   └── fakultas-prodi.js
└── uploads/

Untuk setiap file PHP, cukup buat placeholder dengan komentar yang menjelaskan fungsi file tersebut.
```

### Checklist
- [ ] Semua folder sudah dibuat
- [ ] Semua file placeholder sudah ada
- [ ] Folder `uploads/` sudah dibuat (untuk simpan foto)

---

## Fase 2: Database & Seed Data

### Tujuan
Membuat database MySQL, semua tabel, dan data awal (fakultas & prodi Unsoed).

### Prompt

```
Buatkan file SQL lengkap untuk database TitikNemu Unsoed berdasarkan docs/05-database.md.

Yang perlu dibuat:
1. CREATE DATABASE titiknemu_unsoed
2. Tabel `fakultas` — data referensi fakultas
3. Tabel `prodi` — data referensi program studi, terhubung ke fakultas, dengan field tambahan `jenjang` (S1/D3)
4. Tabel `users` — data pengguna (dengan field: id, nama, email, no_hp, fakultas_id, prodi_id, password, role ENUM('user','admin'), is_banned TINYINT DEFAULT 0, created_at)
5. Tabel `items` — data laporan barang (dengan field: id, user_id, type ENUM('lost','found'), nama_barang, kategori, fakultas_id, lokasi, tanggal, deskripsi, foto, status ENUM('lost','found','returned'), created_at)

Sertakan juga:
- INSERT seed data untuk SEMUA fakultas dan program studi di Unsoed — ambil data dari file `data-fakultas-prodi-unsoed.csv` yang sudah tersedia di root proyek. Dari file CSV itu, gunakan kolom Fakultas (untuk tabel fakultas), Nama Program Studi, Jenjang, dan Fakultas (untuk tabel prodi). Kolom lain (Kategori, No, Kode, Peringkat) tidak perlu disimpan.
- INSERT 1 akun admin default (email: admin@mhs.unsoed.ac.id, password: hash dari 'admin123')
- INSERT beberapa data dummy laporan barang untuk testing

Simpan file ini sebagai `database/titiknemu_unsoed.sql`.
```

### Checklist
- [ ] File SQL berhasil diimport ke MySQL via phpMyAdmin
- [ ] Tabel fakultas terisi data real Unsoed
- [ ] Tabel prodi terisi dan relasi ke fakultas benar
- [ ] Akun admin bisa digunakan

---

## Fase 3: Konfigurasi & Fondasi

### Tujuan
Menyiapkan koneksi database, header/footer/navbar reusable.

### Prompt

```
Buatkan file-file fondasi proyek TitikNemu Unsoed:

1. **config/database.php** — Koneksi ke database MySQL menggunakan mysqli. Host: localhost, user: root, pass: (kosong untuk Laragon), db: titiknemu_unsoed. Tambahkan error handling.

2. **includes/header.php** — HTML head standar dengan:
   - Meta charset UTF-8, viewport
   - Title dinamis (bisa di-set per halaman)
   - Link ke css/style.css
   - Link ke Google Fonts Inter

3. **includes/navbar.php** — Navbar responsif sesuai DESIGN.md:
   - Logo "TitikNemu Unsoed" dengan ikon pin lokasi
   - Menu navigasi: Beranda, Pencarian
   - Jika login: tampilkan nama user dan dropdown (Dashboard, Laporan Saya, Profil, Logout)
   - Jika admin: tambahkan menu Admin
   - Jika belum login: tampilkan tombol Masuk/Daftar
   - Mobile: hamburger menu

4. **includes/footer.php** — Footer sederhana dengan copyright dan penutup HTML.

Pastikan menggunakan session_start() di header untuk mengecek status login.
Ikuti token warna dari DESIGN.md (brand-yellow: #FFD84D, ink: #172033, dll).
```

### Checklist
- [ ] Koneksi database berhasil
- [ ] Navbar muncul di semua halaman
- [ ] Responsif di mobile
- [ ] Menu berubah sesuai status login

---

## Fase 4: Autentikasi (Register & Login)

### Tujuan
Membuat sistem registrasi dan login dengan validasi email Unsoed.

### Prompt

```
Buatkan halaman register.php dan login.php untuk TitikNemu Unsoed sesuai docs/03-fitur-sistem.md bagian 7.1 dan 7.2.

**register.php:**
- Form dengan field: Nama Lengkap, Email (hanya bagian depan, domain @mhs.unsoed.ac.id ditampilkan sebagai teks tetap di samping input), No. HP/WhatsApp, Fakultas (dropdown dari database), Program Studi (dropdown dinamis via AJAX), Password, Konfirmasi Password.
- Dropdown prodi menampilkan teks dengan format "S1 - Nama Prodi" atau "D3 - Nama Prodi" (menggabungkan field jenjang dan nama_prodi dari tabel prodi). Contoh: "S1 - Teknik Sipil", "D3 - Bahasa Inggris".
- Validasi server-side: semua field wajib diisi, email unik, password minimal 6 karakter, password dan konfirmasi harus sama.
- Password di-hash dengan password_hash() sebelum disimpan.
- Setelah berhasil daftar, redirect ke login.php dengan pesan sukses.

**login.php:**
- Form: Email (bagian depan + domain tetap), Password
- Cek kredensial di database
- Cek apakah user is_banned — jika iya, tampilkan pesan "Akun Anda telah dinonaktifkan"
- Jika berhasil: buat session (user_id, nama, role, email), redirect ke dashboard.php
- Tampilkan link "Belum punya akun? Daftar"

**ajax/get_prodi.php:**
- Menerima parameter fakultas_id via GET
- Return JSON array berisi id, jenjang, dan nama_prodi dari tabel prodi sesuai fakultas
- Contoh response: [{"id":1,"jenjang":"S1","nama_prodi":"Teknik Sipil"}, ...]

**js/fakultas-prodi.js:**
- Event listener pada dropdown fakultas
- Fetch ke ajax/get_prodi.php
- Update dropdown prodi secara dinamis, tampilkan teks option sebagai "S1 - Nama Prodi" atau "D3 - Nama Prodi"

Data fakultas & prodi sudah tersedia di file `data-fakultas-prodi-unsoed.csv` dan sudah di-seed ke database.
Ikuti style dari DESIGN.md — form bersih, label di atas input, radius 8-12px, warna sesuai token.
```

### Checklist
- [ ] Registrasi berhasil menyimpan ke database
- [ ] Email duplikat ditolak
- [ ] Domain email otomatis ditambahkan
- [ ] Dropdown Fakultas → Prodi bekerja via AJAX
- [ ] Login berhasil membuat session
- [ ] User banned tidak bisa login
- [ ] Validasi form mempertahankan input yang sudah diisi

---

## Fase 5: Halaman Beranda / Dashboard

### Tujuan
Membuat halaman utama yang menampilkan sapaan, pencarian, tombol lapor, dan daftar barang terbaru.

### Prompt

```
Buatkan halaman beranda (index.php) dan dashboard (dashboard.php) TitikNemu Unsoed sesuai DESIGN.md dan UI.png.

**index.php (Beranda — bisa diakses semua orang):**
- Hero section: sapaan "Halo, [Nama]! 👋" (jika login) atau "Cari barang yang hilang atau temukan barang yang mungkin milik orang lain." (jika belum login)
- Ilustrasi/gambar pendukung di sisi kanan (desktop), hidden di mobile
- Search bar: "Cari nama barang, tag fakultas, atau lokasi..."
- Dua tombol besar: "Barang Hilang" (kuning, ikon search) dan "Barang Ditemukan" (hijau outline, ikon tas)
- Section "Barang Terbaru" dengan grid kartu — ambil 8 laporan terbaru dari database (JOIN ke users dan fakultas, hanya user yang tidak banned)
- Setiap kartu menampilkan: foto (atau placeholder), badge Hilang/Ditemukan, nama barang, fakultas, lokasi detail, tanggal, dan link ke detail
- Tombol "Lihat Semua →" menuju search.php

**dashboard.php (khusus user yang sudah login):**
- Sapaan + statistik jumlah barang hilang dan ditemukan (keseluruhan)
- Daftar "Laporan Saya" — laporan milik user yang login, dengan status dan tombol aksi

Warna badge: Hilang = brand-yellow-soft (#FFF3C4) dengan teks kuning, Ditemukan = brand-green-soft (#DDF5E3) dengan teks hijau.
Layout desktop: 4 kartu per baris. Mobile: 1 kartu per baris.
```

### Checklist
- [ ] Beranda tampil baik di desktop dan mobile
- [ ] Kartu barang menampilkan data dari database
- [ ] Badge warna sesuai jenis laporan
- [ ] Tombol Barang Hilang/Ditemukan berfungsi
- [ ] Search bar redirect ke search.php dengan query
- [ ] Dashboard menampilkan laporan milik user

---

## Fase 6: Form Laporan Barang

### Tujuan
Membuat form untuk membuat laporan barang hilang dan barang ditemukan.

### Prompt

```
Buatkan halaman laporan/tambah.php untuk membuat laporan barang baru di TitikNemu Unsoed sesuai docs/03-fitur-sistem.md bagian 7.4 dan 7.5.

Halaman ini hanya bisa diakses oleh user yang sudah login (redirect ke login jika belum).

**Alur:**
1. User memilih jenis laporan: "Barang Hilang" atau "Barang Ditemukan" (bisa dari parameter GET ?type=lost atau ?type=found, atau pilihan di awal form)
2. Tampilkan form sesuai jenis yang dipilih

**Form fields:**
- Nama Barang (wajib)
- Kategori (dropdown: Kunci, Dompet/Tas, Elektronik, Dokumen, Pakaian/Aksesoris, Alat Tulis, Lainnya)
- Fakultas (dropdown dari database, wajib)
- Lokasi Detail (text input, opsional) — label berubah: "Lokasi kehilangan" untuk hilang, "Lokasi ditemukan" untuk ditemukan
- Tanggal Kejadian (date input, wajib) — label berubah: "Tanggal kehilangan" atau "Tanggal ditemukan"
- Deskripsi (textarea, opsional)
- Foto Barang (file upload, opsional, maksimal 2MB, format jpg/png/jpeg)

**Proses submit:**
- Validasi server-side
- Upload foto ke folder uploads/ dengan nama unik (timestamp + random)
- Simpan data ke tabel items (user_id dari session, type: lost/found, status awal: lost untuk barang hilang, found untuk barang ditemukan)
- Redirect ke detail.php?id=xxx dengan pesan sukses

Desain form sesuai UI.png — satu kolom, label di atas input, tombol kuning "Kirim Laporan" di bawah.
```

### Checklist
- [ ] Form membedakan barang hilang dan ditemukan
- [ ] Upload foto berhasil tersimpan di uploads/
- [ ] Validasi berjalan (ukuran file, format, field wajib)
- [ ] Data tersimpan ke database
- [ ] Redirect ke halaman detail setelah sukses

---

## Fase 7: Daftar & Pencarian Barang

### Tujuan
Membuat halaman pencarian dan daftar barang dengan filter.

### Prompt

```
Buatkan halaman search.php untuk pencarian dan daftar barang TitikNemu Unsoed sesuai docs/03-fitur-sistem.md bagian 7.6 dan 7.7.

**Fitur:**
- Search bar di atas: "Cari nama barang, fakultas, atau lokasi..."
- Filter tabs/pills: Semua, Hilang, Ditemukan
- Filter dropdown: Fakultas (dari database, opsional)
- Hasil ditampilkan sebagai grid kartu (sama seperti di beranda)
- Pencarian menggunakan LIKE di MySQL pada field nama_barang, lokasi, deskripsi
- Filter berdasarkan: type (lost/found), fakultas_id
- Hanya tampilkan laporan dari user yang TIDAK banned (JOIN ke users WHERE is_banned = 0)
- Urutkan berdasarkan created_at DESC (terbaru dulu)
- Jika tidak ada hasil: tampilkan pesan "Belum ada laporan yang cocok. Coba ubah kata kunci atau hapus filter."

**Teknis:**
- Gunakan GET parameters: ?q=keyword&type=lost&fakultas_id=1
- Pertahankan nilai pencarian/filter di form setelah submit
- Pagination sederhana jika diperlukan (opsional tapi direkomendasikan)

Desain sesuai UI.png — filter pills di atas, grid kartu di bawah. Mobile: 1 kolom, Desktop: 3-4 kolom.
```

### Checklist
- [ ] Pencarian keyword berfungsi
- [ ] Filter jenis laporan berfungsi
- [ ] Filter fakultas berfungsi
- [ ] Kombinasi filter berfungsi
- [ ] Hasil kosong menampilkan pesan yang tepat
- [ ] Parameter pencarian dipertahankan di form

---

## Fase 8: Detail Barang & Hubungi Pelapor

### Tujuan
Membuat halaman detail barang dan tombol hubungi via WhatsApp.

### Prompt

```
Buatkan halaman detail.php untuk menampilkan detail laporan barang di TitikNemu Unsoed sesuai docs/03-fitur-sistem.md bagian 7.8 dan 7.9.

**Yang ditampilkan:**
- Foto barang (besar, di atas)
- Badge jenis: "Hilang" (kuning) atau "Ditemukan" (hijau)
- Nama barang (heading besar)
- Fakultas (dari tabel fakultas)
- Lokasi detail (jika ada)
- Tanggal kejadian
- Deskripsi lengkap
- Info pelapor: Nama — Program Studi
- Tombol "Hubungi Pelapor" (hijau, besar, di bawah) — link ke https://wa.me/62xxxx (nomor dari tabel users, konversi format 08 ke 62)

**Kondisi khusus:**
- Jika user yang melihat adalah pemilik laporan: tampilkan tombol Edit, Hapus, dan Ubah Status (bukan tombol Hubungi)
- Jika barang sudah berstatus "returned/dikembalikan": tampilkan badge "Dikembalikan" dan sembunyikan tombol Hubungi
- Jika id tidak ditemukan: tampilkan halaman 404 sederhana

**Teknis:**
- Parameter: detail.php?id=xxx
- Query JOIN items, users, fakultas, prodi
- Pastikan tidak menampilkan laporan dari user banned

Desain sesuai UI.png — foto di atas, info di bawah, tombol full-width di mobile.
```

### Checklist
- [ ] Detail barang tampil lengkap
- [ ] Tombol WhatsApp membuka link wa.me yang benar
- [ ] Format nomor telepon dikonversi dengan benar
- [ ] Pemilik laporan melihat tombol edit/hapus/status
- [ ] Laporan dari user banned tidak ditampilkan

---

## Fase 9: Manajemen Laporan (Edit, Hapus, Status)

### Tujuan
Membuat fitur edit, hapus, dan update status laporan.

### Prompt

```
Buatkan file-file manajemen laporan TitikNemu Unsoed:

**1. laporan/edit.php:**
- Hanya bisa diakses oleh pemilik laporan (cek user_id = session user_id)
- Pre-fill form dengan data laporan yang sudah ada
- Bisa mengubah: nama barang, kategori, fakultas, lokasi, tanggal, deskripsi, foto (opsional ganti)
- Jika foto baru diupload, hapus foto lama dari folder uploads/
- Redirect ke detail setelah berhasil

**2. laporan/hapus.php:**
- Hanya bisa diakses oleh pemilik laporan ATAU admin
- Tampilkan konfirmasi "Yakin ingin menghapus laporan [nama barang]?"
- Jika dikonfirmasi: hapus foto dari uploads/, hapus data dari items, redirect ke dashboard
- Method POST untuk keamanan

**3. laporan/update_status.php:**
- Hanya bisa diakses oleh pemilik laporan
- Untuk barang hilang (type=lost): bisa ubah status menjadi "found" (Ditemukan/Selesai)
- Untuk barang ditemukan (type=found): bisa ubah status menjadi "returned" (Dikembalikan)
- Tampilkan konfirmasi sebelum mengubah status
- Redirect ke detail setelah berhasil

Semua file harus mengecek session dan otorisasi. Jika tidak berhak, redirect ke dashboard dengan pesan error.
```

### Checklist
- [ ] Edit form pre-fill data yang benar
- [ ] Update berhasil (termasuk ganti foto)
- [ ] Hapus menghapus file foto juga
- [ ] Update status sesuai alur (lost→found, found→returned)
- [ ] Otorisasi benar (hanya pemilik/admin)

---

## Fase 10: Panel Admin

### Tujuan
Membuat halaman admin untuk moderasi.

### Prompt

```
Buatkan halaman-halaman admin TitikNemu Unsoed sesuai docs/04-admin-dan-moderasi.md:

Semua halaman admin HANYA bisa diakses jika session role = 'admin'. Redirect ke beranda jika bukan admin.

**1. admin/dashboard.php:**
- Statistik: total user, total laporan, laporan hilang aktif, laporan ditemukan aktif, barang dikembalikan
- Daftar laporan terbaru (semua user)
- Link ke manajemen user dan manajemen laporan

**2. admin/users.php:**
- Tabel daftar semua user: No, Nama, Email, Fakultas, Prodi, No HP, Status (Aktif/Banned), Tanggal daftar
- Tombol "Ban" untuk user aktif (dengan konfirmasi)
- Tombol "Unban" untuk user yang sudah banned
- Proses ban/unban via POST, update is_banned di database
- Jangan tampilkan akun admin sendiri di daftar

**3. admin/laporan.php:**
- Tabel daftar semua laporan: No, Nama Barang, Jenis, Pelapor, Fakultas, Status, Tanggal, Aksi
- Tombol "Hapus" dengan konfirmasi
- Filter berdasarkan jenis dan status
- Pencarian berdasarkan nama barang

Desain: tabel bersih dengan zebra striping, tombol aksi kecil, responsif (horizontal scroll di mobile jika perlu).
```

### Checklist
- [ ] Hanya admin yang bisa akses
- [ ] Ban/unban mengubah is_banned di database
- [ ] User banned tidak bisa login (cek di login.php)
- [ ] Laporan user banned tersembunyi dari daftar publik
- [ ] Admin bisa hapus laporan siapa saja

---

## Fase 11: Styling & Polish

### Tujuan
Menyempurnakan tampilan sesuai DESIGN.md dan UI.png.

### Prompt

```
Review dan sempurnakan css/style.css untuk TitikNemu Unsoed agar sesuai dengan DESIGN.md dan UI.png.

Pastikan:

1. **Warna** sesuai token: brand-yellow #FFD84D, brand-green #43B96B, ink #172033, muted #647084, surface #FFFFFF, page #F7F8FA, border #E2E7EE, danger #C83F45, focus #2864C5

2. **Tipografi**: Font Inter dari Google Fonts, ukuran dasar 16px, hierarki heading yang jelas

3. **Komponen**:
   - Kartu laporan: sudut 12-16px, bayangan tipis, hover effect halus
   - Badge Hilang/Ditemukan: pill shape, warna sesuai
   - Tombol: radius 8-12px, transisi hover 150ms
   - Input/form: border #E2E7EE, focus ring brand-yellow
   - Navbar: sticky, bayangan tipis

4. **Responsif**:
   - Mobile first approach
   - Breakpoint: 640px (small), 1024px (large)
   - Grid kartu: 1 kolom mobile, 2 kolom tablet, 4 kolom desktop
   - Navbar: hamburger menu di mobile

5. **Animasi & Transisi**:
   - Hover tombol/kartu: scale dan shadow halus
   - Transition 120-220ms
   - Respect prefers-reduced-motion

6. **Aksesibilitas**:
   - Focus ring yang terlihat
   - Kontras warna memadai
   - Skip to content link

Jangan mengubah fungsionalitas PHP, hanya CSS.
```

### Checklist
- [ ] Tampilan mirip UI.png
- [ ] Responsif sempurna di semua ukuran layar
- [ ] Warna konsisten sesuai token
- [ ] Animasi halus dan tidak berlebihan
- [ ] Fokus keyboard terlihat

---

## Fase 12: Testing & Finishing

### Tujuan
Memastikan semua fitur berfungsi dengan benar.

### Prompt

```
Bantu saya melakukan review akhir dan perbaikan TitikNemu Unsoed. Periksa hal-hal berikut:

1. **Keamanan dasar:**
   - Semua input user di-escape dengan htmlspecialchars() saat ditampilkan
   - Query database menggunakan prepared statements (mysqli)
   - File upload divalidasi (tipe, ukuran)
   - Session diperiksa di setiap halaman yang memerlukan login
   - CSRF protection pada form (opsional tapi direkomendasikan)

2. **Edge cases:**
   - Akses halaman tanpa login yang seharusnya perlu login
   - Akses detail barang dengan ID yang tidak ada
   - Upload file bukan gambar
   - Input kosong atau terlalu panjang
   - User banned mencoba login
   - User biasa mencoba akses halaman admin

3. **User experience:**
   - Pesan sukses/error yang informatif
   - Redirect yang benar setelah setiap aksi
   - Form mempertahankan input saat validasi gagal
   - Halaman 404 yang ramah

4. **Konsistensi:**
   - Semua halaman menggunakan header, navbar, footer yang sama
   - Penamaan variabel dan komentar konsisten
   - Tidak ada halaman yang broken/blank

Berikan daftar masalah yang ditemukan dan perbaikannya.
```

### Checklist
- [ ] Semua form terproteksi dari XSS
- [ ] Prepared statements di semua query
- [ ] Semua halaman terlindungi sesuai otorisasi
- [ ] Tidak ada error PHP yang muncul di browser
- [ ] Alur lengkap bisa dilakukan: daftar → login → lapor → cari → detail → hubungi → update status

---

## Tips Tambahan

### Jika AI "Lupa" Konteks

```
Baca ulang file docs/03-fitur-sistem.md dan DESIGN.md. 
Kita sedang mengerjakan [nama fase]. 
Konteks: [jelaskan apa yang sudah selesai dan apa yang belum].
```

### Jika Ingin Memperbaiki Satu Halaman

```
Buka file [nama-file.php]. Ada masalah: [jelaskan masalahnya].
Perbaiki sesuai dengan spesifikasi di docs/03-fitur-sistem.md bagian [nomor bagian] dan desain di DESIGN.md.
```

### Jika Ingin Menambah Fitur Baru

```
Saya ingin menambahkan fitur [nama fitur] ke TitikNemu Unsoed.
Fitur ini [jelaskan apa yang dilakukan].
Sesuaikan dengan gaya dan arsitektur yang sudah ada (PHP native, MySQL, desain dari DESIGN.md).
```

### Jika Ingin Review Kode

```
Review semua kode PHP di proyek TitikNemu Unsoed.
Fokus pada:
1. Keamanan (SQL injection, XSS)
2. Best practices PHP
3. Konsistensi kode
4. Error handling
Berikan daftar perbaikan yang diperlukan.
```

---

## Urutan Prioritas Pengerjaan

Jika waktu terbatas, kerjakan dengan urutan prioritas:

| Prioritas | Fase | Alasan |
|---|---|---|
| 🔴 Wajib | Fase 1-4 | Fondasi — tanpa ini tidak ada yang bisa jalan |
| 🔴 Wajib | Fase 5-6 | Core feature — beranda dan form laporan |
| 🔴 Wajib | Fase 7-8 | Core feature — pencarian dan detail |
| 🟡 Penting | Fase 9 | Edit, hapus, status — melengkapi CRUD |
| 🟡 Penting | Fase 11 | Styling — tampilan harus bagus untuk presentasi |
| 🟢 Opsional | Fase 10 | Admin — bisa disederhanakan jika waktu mepet |
| 🟢 Opsional | Fase 12 | Testing — idealnya dilakukan tapi bisa dipangkas |

---

> **Catatan:** Dokumen ini dirancang agar bisa digunakan dengan AI coding assistant manapun. Setiap prompt sudah ditulis secara detail agar AI memiliki konteks yang cukup untuk menghasilkan kode yang sesuai dengan spesifikasi TitikNemu Unsoed.
