# UI & UX Guideline — TitikNemu Unsoed

> **Cari. Temukan. Kembalikan.**

Dokumen ini menjadi acuan desain antarmuka dan perilaku pengalaman pengguna TitikNemu Unsoed. Arah visual merujuk pada `UI.png`, sedangkan isi dan alur mengikuti kebutuhan produk di `PRD.md`. Tujuannya bukan hanya membuat halaman terlihat rapi, tetapi juga membuat proses mencari, melaporkan, dan menghubungi pelapor terasa jelas dan ringan.

## 1. Prinsip desain

1. **Utamakan tugas pengguna.** Pencarian dan tindakan melaporkan barang harus mudah ditemukan sejak beranda.
2. **Bedakan status dengan konsisten.** Barang hilang menggunakan aksen kuning; barang ditemukan menggunakan aksen hijau. Selalu sertakan teks atau ikon, jangan mengandalkan warna saja.
3. **Tampilkan informasi yang membantu keputusan.** Nama barang, jenis laporan, fakultas, lokasi detail, dan tanggal harus mudah dipindai.
4. **Berikan umpan balik segera.** Setiap pencarian, pengiriman form, perubahan status, dan aksi penting mempunyai keadaan yang terlihat.
5. **Jaga antarmuka tetap tenang.** Gunakan ruang kosong, tipografi yang terbaca, dan animasi singkat. Hindari gerakan atau dekorasi yang mengganggu fokus.
6. **Mobile-first dan inklusif.** Semua alur utama dapat digunakan dengan layar kecil, keyboard, dan teknologi bantu.

## 2. Arah visual

Referensi menampilkan latar putih atau abu-abu sangat muda, permukaan kartu putih, teks gelap, sudut membulat, bayangan tipis, dan ilustrasi bernuansa kampus. Logo pin lokasi menjadi penanda identitas. Kuning menonjol untuk aksi utama dan laporan hilang; hijau menjadi penanda laporan ditemukan.

Kesan yang dituju: **ramah, terpercaya, sederhana, dan dekat dengan kehidupan kampus**. Gunakan ilustrasi hanya untuk membantu konteks atau mengisi keadaan kosong; jangan sampai bersaing dengan informasi laporan.

## 3. Token visual

Nilai di bawah merupakan titik awal implementasi. Pertahankan konsistensinya; sesuaikan bila pengukuran kontras atau aset logo mengharuskan perubahan.

### Warna

| Token | Nilai awal | Penggunaan |
|---|---|---|
| `brand-yellow` | `#FFD84D` | Tombol aksi utama, aksen merek, indikator barang hilang |
| `brand-yellow-soft` | `#FFF3C4` | Latar badge dan sorotan lembut barang hilang |
| `brand-green` | `#43B96B` | Indikator barang ditemukan |
| `brand-green-soft` | `#DDF5E3` | Latar badge dan sorotan lembut barang ditemukan |
| `ink` | `#172033` | Judul, isi utama, ikon penting |
| `muted` | `#647084` | Teks sekunder, metadata, placeholder instruktif |
| `surface` | `#FFFFFF` | Kartu, form, navigasi |
| `page` | `#F7F8FA` | Latar halaman |
| `border` | `#E2E7EE` | Garis batas input dan kartu |
| `danger` | `#C83F45` | Kesalahan validasi dan aksi destruktif |
| `focus` | `#2864C5` | Fokus keyboard yang terlihat |

Pastikan teks pada tombol dan badge memiliki kontras yang cukup terhadap latarnya. Untuk kuning terang, gunakan teks gelap. Status selalu ditulis, misalnya **Hilang** atau **Ditemukan**, disertai ikon bila sesuai.

### Tipografi

- Gunakan sans-serif yang bersih dan tersedia luas, misalnya `Inter`, `system-ui`, sans-serif. Hindari ketergantungan pada font eksternal agar tampilan tetap cepat.
- Gunakan hierarki yang tegas: judul halaman paling menonjol, nama barang menjadi fokus kartu, metadata lebih kecil tetapi tetap terbaca.
- Ukuran dasar isi 16 px pada desktop dan mobile. Metadata tidak boleh terlalu kecil; usahakan minimal 13–14 px.
- Batasi paragraf panjang pada lebar baca yang nyaman. Gunakan kalimat langsung dan label yang familiar.

### Bentuk, jarak, dan elevasi

- Gunakan skala jarak konsisten berbasis kelipatan 4 atau 8 px.
- Radius 8–12 px untuk input dan tombol; 12–16 px untuk kartu atau panel utama.
- Gunakan garis batas tipis sebagai pemisah utama. Bayangan lembut hanya untuk memberi kedalaman pada kartu atau navigasi yang mengambang.
- Area klik kontrol minimal sekitar 44 × 44 px pada layar sentuh.
- Ikon harus berasal dari satu keluarga gaya dan memiliki ukuran serta ketebalan garis yang konsisten. Beri label aksesibel pada ikon tanpa teks.

## 4. Tata letak responsif

### Desktop

- Header ringkas berisi logo/nama, navigasi, serta akses akun atau notifikasi jika fitur tersedia.
- Beranda dapat menggunakan area pengantar dua kolom: salam, pencarian, dan tombol jenis laporan di satu sisi; ilustrasi pendukung di sisi lain.
- Daftar terbaru menggunakan grid kartu yang menyesuaikan lebar. Batasi lebar konten agar baris teks tidak terlalu panjang.
- Filter dapat ditempatkan di atas hasil atau pada sisi daftar jika ruang memadai.

### Mobile

- Susun konten dalam satu kolom. Prioritaskan pencarian dan tombol **Barang Hilang** / **Barang Ditemukan** di area awal halaman.
- Gunakan kartu laporan yang ringkas; foto dan metadata tetap cukup besar untuk dipahami sekilas.
- Navigasi utama dapat memakai bilah bawah seperti referensi: Beranda, Lapor, Pencarian, Akun. Perhitungkan ruang aman perangkat dan jangan menutupi tombol atau konten terakhir.
- Form panjang tetap satu kolom, dengan label di atas input. Jangan mengandalkan placeholder sebagai pengganti label.
- Hindari elemen yang memerlukan hover agar fungsi penting dapat dijangkau pada layar sentuh.

### Breakpoint

Pilih breakpoint berdasarkan kebutuhan konten, bukan tipe perangkat tertentu. Sebagai titik awal, gunakan sekitar 640 px untuk perubahan kecil dan 900–1024 px untuk tata letak multi-kolom. Uji agar tidak ada gulir horizontal pada lebar sempit.

## 5. Komponen dan aturan penggunaan

### Header dan navigasi

- Tampilkan identitas TitikNemu Unsoed secara konsisten.
- Beri penanda yang jelas pada halaman aktif. Menu mobile harus dapat dibuka dan ditutup dengan keyboard, memiliki nama aksesibel, dan tidak mengunci fokus secara keliru.
- Navigasi hanya menampilkan fitur yang memang tersedia. Jangan menampilkan ikon notifikasi tanpa perilaku yang berfungsi.

### Pencarian dan filter

- Placeholder memberi contoh singkat, seperti “Cari nama barang, fakultas, atau lokasi…”, sementara label aksesibel tetap tersedia.
- Tampilkan jenis filter yang aktif dan sediakan cara mudah untuk menghapus filter.
- Pencarian dapat dikirim dengan tombol atau Enter. Jika hasil dimuat tanpa reload, pertahankan query dan beri status pemuatan serta hasil yang diperbarui kepada pembaca layar.
- Jangan menghapus input pengguna ketika hasil kosong atau terjadi kesalahan.

### Kartu laporan

Urutan informasi yang disarankan:

1. Foto barang dengan rasio konsisten dan pemotongan yang tidak menghilangkan objek utama.
2. Badge **Hilang** atau **Ditemukan**.
3. Nama barang.
4. Fakultas dan lokasi detail bila tersedia.
5. Tanggal kejadian.
6. Tautan atau tombol untuk membuka detail.

Seluruh kartu boleh menjadi tautan jika tidak memuat kontrol lain yang dapat diklik. Jika ada beberapa aksi, gunakan tombol/tautan terpisah agar tidak terjadi tautan bersarang. Berikan teks alternatif foto yang menggambarkan barang; foto dekoratif memakai teks alternatif kosong.

### Tombol

- **Utama:** satu aksi paling penting per area, seperti “Kirim Laporan”. Gunakan kuning dengan teks gelap.
- **Sekunder:** aksi pendamping, seperti melihat daftar, dengan latar netral dan batas jelas.
- **Jenis laporan:** gunakan kuning untuk “Barang Hilang” dan hijau untuk “Barang Ditemukan”, lengkap dengan teks dan ikon.
- **Destruktif:** hapus atau moderasi memakai warna bahaya dan, untuk aksi yang sulit dibatalkan, konfirmasi yang menyebut objek yang akan dihapus.
- Saat sedang diproses, tombol menampilkan keadaan sibuk, mencegah pengiriman ganda, dan tetap mempertahankan label yang menjelaskan aksi.

### Formulir

- Setiap input mempunyai label tetap, penanda wajib yang jelas, dan petunjuk format hanya bila diperlukan.
- Validasi dekat dengan field terkait, ditulis dalam bahasa yang membantu memperbaiki masalah. Setelah pengiriman gagal, fokuskan atau arahkan pengguna ke ringkasan/field error pertama.
- Jangan menghapus nilai yang sudah dimasukkan saat validasi gagal.
- Dropdown fakultas dan program studi menunjukkan keadaan memuat, gagal, dan kosong. Saat fakultas berubah, jelaskan bahwa pilihan program studi diperbarui; cegah pilihan lama yang tidak lagi cocok.
- Untuk unggah foto, jelaskan format/ukuran yang diterima berdasarkan aturan implementasi, tampilkan nama atau pratinjau file, dan sediakan cara mengganti/menghapusnya.
- Form laporan membedakan dengan jelas apakah tanggal adalah tanggal kehilangan atau tanggal penemuan sesuai jenis laporan.

### Status dan umpan balik

- Status laporan ditampilkan sebagai badge berteks. Bedakan status jenis laporan (hilang/ditemukan) dari status penyelesaian (misalnya dikembalikan/selesai) agar tidak membingungkan.
- Aksi berhasil memberi konfirmasi singkat dan langkah berikutnya, misalnya tautan ke detail laporan yang baru dibuat.
- Kesalahan jaringan atau server menjelaskan bahwa aksi belum berhasil dan menyediakan cara mencoba lagi. Jangan menyatakan berhasil sebelum server mengonfirmasi.
- Dialog konfirmasi hanya digunakan untuk keputusan penting/destruktif. Dialog harus memiliki judul, pilihan yang jelas, dapat ditutup dengan Escape, dan mengelola fokus dengan benar.

## 6. Panduan per halaman dan alur

### Beranda

- Sapa pengguna yang masuk secara singkat; untuk pengunjung, jelaskan tujuan layanan.
- Tempatkan pencarian, aksi membuat laporan, dan daftar barang terbaru pada urutan yang mendukung tugas utama.
- “Lihat Semua” menuju daftar dengan konteks filter yang relevan bila ada.
- Jangan membuat statistik atau jumlah laporan tampak sebagai tautan jika tidak dapat dibuka.

### Masuk dan daftar

- Jelaskan bahwa layanan ditujukan kepada mahasiswa Unsoed dan email memakai domain `@mhs.unsoed.ac.id` sesuai PRD.
- Jika bagian domain ditampilkan tetap di samping input, pastikan pembaca layar memahami bahwa keduanya membentuk satu alamat email.
- Tampilkan pesan autentikasi secara jelas tanpa mengungkap apakah email tertentu terdaftar.
- Sediakan tautan antar halaman masuk dan daftar. Setelah berhasil, arahkan pengguna ke tujuan yang wajar dan berikan konfirmasi.

### Pilih jenis dan buat laporan

- Mulai dengan pilihan **Barang Hilang** atau **Barang Ditemukan** sebelum menampilkan form, atau tampilkan pilihan tersebut secara jelas di awal form.
- Ubah label tanggal dan konteks lokasi mengikuti jenis laporan.
- Susun field dari informasi inti ke informasi tambahan. Tunjukkan lokasi detail dan deskripsi sebagai opsional bila memang demikian dalam validasi sistem.
- Setelah pengiriman berhasil, arahkan ke detail laporan yang dibuat atau tampilkan tautan langsung menuju detail.

### Daftar dan pencarian

- Tampilkan filter **Semua**, **Hilang**, dan **Ditemukan** jika sesuai dengan fungsi yang tersedia.
- Pertahankan pencarian dan filter saat pengguna membuka detail lalu kembali, bila memungkinkan.
- Urutkan hasil dengan aturan yang konsisten dan jelaskan urutan bila tidak jelas bagi pengguna.
- Bedakan keadaan memuat, hasil ditemukan, hasil kosong, dan gagal memuat.

### Detail barang

- Utamakan nama barang, badge jenis/status, foto, fakultas, lokasi detail, tanggal, dan deskripsi.
- Letakkan aksi **Hubungi Pelapor** dengan jelas. Tautan WhatsApp sebaiknya membuka tab/aplikasi baru dengan perilaku yang dapat dipahami, dan tidak menampilkan nomor yang tidak diperlukan.
- Untuk pemilik laporan, tampilkan aksi edit, hapus, atau perbarui status hanya jika diizinkan. Jangan tampilkan aksi pemilik kepada pengguna lain.
- Beri konteks sebelum pengguna membuka WhatsApp, misalnya bahwa mereka akan melanjutkan percakapan di WhatsApp.

### Dashboard dan akun

- Dashboard menonjolkan laporan milik pengguna dan statusnya; ringkasan jumlah menjadi elemen sekunder.
- Sediakan akses yang jelas untuk mengelola laporan sendiri dan keluar.
- Fungsi admin dibedakan secara jelas dari fungsi pengguna biasa, tanpa membuat navigasi pengguna penuh sesak.

## 7. Loading, transisi, dan respons interaksi

Kinerja yang terasa cepat bergantung pada kecepatan aktual sekaligus umpan balik. Gunakan indikator yang relevan dan hindari animasi yang menunda pekerjaan.

### Saat data dimuat

- Untuk halaman penuh, tampilkan kerangka konten sederhana atau indikator pada area utama. Hindari layar kosong yang lama.
- Untuk pencarian/filter/AJAX, pertahankan konten lama bila aman, tandai area hasil sedang diperbarui, lalu ganti setelah data baru siap. Jangan membuat seluruh halaman berkedip.
- Placeholder skeleton mengikuti bentuk konten yang akan muncul dan tidak bergerak berlebihan. Jika waktu tunggu singkat, indikator sederhana lebih baik daripada skeleton panjang.
- Gambar memakai ukuran/rasio yang ditetapkan agar tata letak tidak meloncat ketika foto selesai dimuat. Tunda pemuatan gambar yang jauh di bawah area awal bila sesuai.
- Tampilkan error dan tombol coba lagi jika permintaan gagal; jangan biarkan spinner berjalan tanpa batas.

### Transisi dan gerak

- Gunakan transisi pendek, umumnya sekitar 120–220 ms, untuk hover, fokus, perubahan warna, buka/tutup menu, dan umpan balik ringan.
- Gerakan masuk/keluar harus sederhana dan tidak memindahkan konten jauh. Hindari parallax, animasi berulang, dan efek yang memperlambat navigasi.
- Hormati `prefers-reduced-motion`: hilangkan atau kurangi transisi non-esensial bagi pengguna yang meminta gerakan dikurangi.
- Jangan menganimasikan tinggi daftar/form secara mencolok. Perubahan layout besar membuat pengguna kehilangan posisi baca.
- Pada perangkat sentuh, gunakan umpan balik visual saat ditekan; jangan mengandalkan hover.

### Aksi asinkron dan pencegahan kesalahan

- Pencarian tidak mengirim permintaan berulang untuk setiap karakter tanpa kendali. Gunakan submit eksplisit atau jeda singkat yang sesuai, dan cegah respons lama menimpa hasil query terbaru.
- Cegah klik ganda saat laporan sedang disimpan. Beri status “Mengirim…” dan pulihkan tombol bila gagal.
- Saat status laporan berubah, tampilkan status baru setelah server mengonfirmasi.
- Untuk aksi hapus atau tindakan admin yang berdampak besar, jelaskan akibatnya sebelum konfirmasi dan sediakan pembatalan bila memungkinkan.
- Hindari pemindahan fokus atau scroll otomatis yang mengejutkan. Setelah aksi, pindahkan fokus hanya bila diperlukan agar pengguna mengetahui hasilnya.

## 8. Aksesibilitas

- Gunakan elemen HTML semantik: heading berurutan, tombol untuk aksi, tautan untuk navigasi, serta label form yang terhubung ke input.
- Semua fungsi dapat diakses dengan keyboard. Urutan tab logis dan indikator fokus selalu terlihat.
- Pastikan kontras teks dan kontrol memadai; jangan menyampaikan status hanya melalui warna, posisi, atau ikon.
- Tombol ikon memiliki nama aksesibel. Gambar barang memiliki teks alternatif yang sesuai.
- Pesan sukses, error, dan perubahan hasil dinamis diumumkan secara wajar menggunakan live region bila diperlukan.
- Gunakan bahasa Indonesia yang sederhana, tanggal yang konsisten, dan format tanggal lokal yang mudah dipahami.
- Saat menampilkan modal/menu, pastikan fokus masuk, tetap di konteks yang benar, dan kembali ke pemicu saat ditutup.

## 9. Nada bahasa antarmuka

Gunakan bahasa Indonesia yang ramah, singkat, dan tidak menyalahkan pengguna. Satu pesan menyampaikan satu hal dan menyertakan tindakan yang bisa dilakukan.

Contoh:

- **Kosong:** “Belum ada laporan yang cocok. Coba ubah kata kunci atau hapus filter.”
- **Validasi:** “Pilih fakultas tempat barang ditemukan.”
- **Berhasil:** “Laporan berhasil dibuat. Lihat detail laporan.”
- **Gagal:** “Laporan belum terkirim. Periksa koneksi lalu coba lagi.”
- **Loading:** “Memuat laporan…”

Hindari pesan teknis seperti kode server, stack trace, atau istilah basis data di antarmuka pengguna.

## 10. Konsistensi dan batas implementasi

- Implementasikan dengan HTML, CSS, PHP Native, MySQL, dan JavaScript vanilla secukupnya sesuai PRD. Interaksi dasar seperti tautan dan submit form tetap berfungsi tanpa JavaScript jika memungkinkan.
- Jangan membuat kontrol visual yang tidak memiliki aksi. Jangan menjanjikan notifikasi, chat internal, atau kemampuan lain yang tidak termasuk versi produk.
- Gunakan komponen dan token yang sama lintas halaman. Jika ada pengecualian, harus didorong kebutuhan alur yang jelas.
- Pertahankan foto dan data pengguna secara proporsional; jangan menampilkan informasi kontak selain yang diperlukan untuk menghubungi pelapor.
- Panduan ini melengkapi PRD. Aturan produk dan keamanan data tetap mengikuti spesifikasi implementasi yang berlaku.

## 11. Checklist review UI/UX

Sebelum sebuah halaman dianggap siap, pastikan:

- [ ] Pengguna memahami tujuan halaman dan aksi utama tanpa menebak.
- [ ] Tampilan mengikuti palet, tipografi, jarak, dan gaya komponen yang konsisten.
- [ ] Status hilang/ditemukan memiliki label teks dan tidak hanya dibedakan lewat warna.
- [ ] Tata letak berfungsi pada mobile dan desktop tanpa gulir horizontal.
- [ ] Form memiliki label, validasi yang dapat ditindaklanjuti, dan mempertahankan input saat gagal.
- [ ] Keadaan loading, kosong, sukses, dan error tersedia untuk data/asynchronous action.
- [ ] Tombol memberi umpan balik, mencegah pengiriman ganda, dan tidak menyembunyikan kegagalan.
- [ ] Transisi singkat, tidak mengganggu, dan menghormati pengaturan reduced motion.
- [ ] Semua kontrol dapat dipakai dengan keyboard dan memiliki fokus yang terlihat.
- [ ] Teks, foto, ikon, dan metadata mudah dibaca serta memiliki alternatif aksesibel.
- [ ] Tidak ada kontrol atau janji fitur yang belum bekerja.

