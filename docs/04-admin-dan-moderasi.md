# TitikNemu Unsoed — Admin & Moderasi

## 8. Admin

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
