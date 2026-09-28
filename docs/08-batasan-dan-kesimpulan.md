# TitikNemu Unsoed — Batasan, Pengembangan & Kesimpulan

## 14. Batasan Sistem

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

## 15. Pengembangan Selanjutnya

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

## 16. Kesimpulan

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
