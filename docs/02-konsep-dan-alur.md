# TitikNemu Unsoed — Konsep & Alur Sistem

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
