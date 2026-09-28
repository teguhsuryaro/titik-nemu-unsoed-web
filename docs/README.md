# 📁 Dokumentasi TitikNemu Unsoed

> Folder ini berisi PRD (Product Requirements Document) yang sudah dipecah ke dalam beberapa file terpisah, plus panduan prompt untuk membangun website dari nol.

## Dokumen PRD (dipecah dari `PRD.md`)

| No | File | Isi |
|---|---|---|
| 1 | [01-ringkasan-proyek.md](01-ringkasan-proyek.md) | Deskripsi singkat, latar belakang, tujuan, target pengguna |
| 2 | [02-konsep-dan-alur.md](02-konsep-dan-alur.md) | Konsep utama sistem (barang hilang/ditemukan) dan alur utama |
| 3 | [03-fitur-sistem.md](03-fitur-sistem.md) | Semua fitur: registrasi, login, dashboard, laporan, pencarian, detail, status, riwayat |
| 4 | [04-admin-dan-moderasi.md](04-admin-dan-moderasi.md) | Fitur admin, efek banned, moderasi |
| 5 | [05-database.md](05-database.md) | Struktur database: tabel fakultas, prodi, users, items, messages |
| 6 | [06-struktur-dan-teknologi.md](06-struktur-dan-teknologi.md) | Struktur folder/halaman, teknologi (HTML/CSS/PHP/MySQL), CRUD |
| 7 | [07-skenario-penggunaan.md](07-skenario-penggunaan.md) | Skenario lengkap: kehilangan barang & menemukan barang |
| 8 | [08-batasan-dan-kesimpulan.md](08-batasan-dan-kesimpulan.md) | Batasan sistem, rencana pengembangan, kesimpulan |

## Panduan Development

| File | Isi |
|---|---|
| [PANDUAN-PROMPT.md](PANDUAN-PROMPT.md) | **Prompt-prompt siap pakai** untuk membangun website dari 0 menggunakan AI, disusun per fase (12 fase) dengan checklist |

## Dokumen Lain (di root proyek)

| File | Isi |
|---|---|
| [DESIGN.md](../DESIGN.md) | Panduan UI/UX: prinsip desain, token visual, tata letak, komponen, aksesibilitas |
| [UI.png](../UI.png) | Referensi visual mockup tampilan website |
| [PRD.md](../PRD.md) | PRD lengkap (versi asli, sebelum dipecah) |
| [data-fakultas-prodi-unsoed.csv](../data-fakultas-prodi-unsoed.csv) | **Data seed** fakultas & program studi Unsoed (sumber: Adkesma Unsoed). Digunakan untuk mengisi tabel `fakultas` dan `prodi` di database |
