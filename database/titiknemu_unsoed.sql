-- ============================================================
-- TitikNemu Unsoed — Database Setup
-- ============================================================
-- File ini membuat database, semua tabel, seed data fakultas
-- & prodi dari data-fakultas-prodi-unsoed.csv, akun admin
-- default, dan beberapa data dummy untuk testing.
-- ============================================================

-- Buat database
CREATE DATABASE IF NOT EXISTS titiknemu_unsoed
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE titiknemu_unsoed;

-- ============================================================
-- 1. Tabel fakultas
-- ============================================================
CREATE TABLE fakultas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_fakultas VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ============================================================
-- 2. Tabel prodi
-- ============================================================
CREATE TABLE prodi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fakultas_id INT NOT NULL,
  jenjang VARCHAR(5) NOT NULL,
  nama_prodi VARCHAR(100) NOT NULL,
  FOREIGN KEY (fakultas_id) REFERENCES fakultas(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 3. Tabel users
-- ============================================================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  no_hp VARCHAR(20) NOT NULL,
  fakultas_id INT NOT NULL,
  prodi_id INT NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  is_banned TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (fakultas_id) REFERENCES fakultas(id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (prodi_id) REFERENCES prodi(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 4. Tabel items
-- ============================================================
CREATE TABLE items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  type ENUM('lost','found') NOT NULL,
  nama_barang VARCHAR(150) NOT NULL,
  kategori VARCHAR(50) NOT NULL,
  fakultas_id INT NOT NULL,
  lokasi VARCHAR(255) DEFAULT NULL,
  tanggal DATE NOT NULL,
  deskripsi TEXT,
  foto VARCHAR(255) DEFAULT NULL,
  status ENUM('lost','found','returned') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (fakultas_id) REFERENCES fakultas(id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 5. Seed Data — Fakultas
-- ============================================================
-- 12 fakultas unik dari data-fakultas-prodi-unsoed.csv

INSERT INTO fakultas (id, nama_fakultas) VALUES
  (1,  'Pertanian'),
  (2,  'Biologi'),
  (3,  'Ekonomi dan Bisnis'),
  (4,  'Peternakan'),
  (5,  'Hukum'),
  (6,  'Ilmu Sosial dan Ilmu Politik'),
  (7,  'Kedokteran'),
  (8,  'Teknik'),
  (9,  'Ilmu-ilmu Kesehatan'),
  (10, 'Ilmu Budaya'),
  (11, 'Matematika dan Ilmu Pengetahuan Alam'),
  (12, 'Perikanan dan Ilmu Kelautan');

-- ============================================================
-- 6. Seed Data — Program Studi
-- ============================================================
-- Semua prodi dari CSV, termasuk kelas internasional dan D3.
-- Format tampilan di dropdown: "[Jenjang] - [Nama Prodi]"

INSERT INTO prodi (fakultas_id, jenjang, nama_prodi) VALUES
  -- Pertanian (fakultas_id = 1)
  (1, 'S1', 'Teknik Pertanian'),
  (1, 'S1', 'Teknologi Pangan'),
  (1, 'S1', 'Agribisnis'),
  (1, 'S1', 'Agroteknologi'),
  (1, 'S1', 'Proteksi Tanaman'),
  (1, 'D3', 'Agribisnis'),
  (1, 'D3', 'Perencanaan Sumber Daya Lahan'),

  -- Biologi (fakultas_id = 2)
  (2, 'S1', 'Biologi'),
  (2, 'S1', 'Mikrobiologi'),
  (2, 'S1', 'Biologi Terapan'),
  (2, 'S1', 'Biologi Internasional'),
  (2, 'D3', 'Budi Daya Ikan'),

  -- Ekonomi dan Bisnis (fakultas_id = 3)
  (3, 'S1', 'Ekonomi Pembangunan'),
  (3, 'S1', 'Manajemen'),
  (3, 'S1', 'Akuntansi'),
  (3, 'S1', 'Pendidikan Ekonomi'),
  (3, 'S1', 'Ekonomi Pembangunan Kelas Internasional'),
  (3, 'S1', 'Manajemen Kelas Internasional'),
  (3, 'S1', 'Akuntansi Kelas Internasional'),
  (3, 'D3', 'Bisnis Internasional'),
  (3, 'D3', 'Akuntansi'),
  (3, 'D3', 'Administrasi Perkantoran'),
  (3, 'D3', 'Administrasi Bisnis'),

  -- Peternakan (fakultas_id = 4)
  (4, 'S1', 'Peternakan'),
  (4, 'D3', 'Budi Daya Ternak'),

  -- Hukum (fakultas_id = 5)
  (5, 'S1', 'Hukum'),
  (5, 'S1', 'Hukum Kelas Internasional'),

  -- Ilmu Sosial dan Ilmu Politik (fakultas_id = 6)
  (6, 'S1', 'Administrasi Publik'),
  (6, 'S1', 'Ilmu Politik'),
  (6, 'S1', 'Sosiologi'),
  (6, 'S1', 'Ilmu Komunikasi'),
  (6, 'S1', 'Hubungan Internasional'),
  (6, 'S1', 'Hubungan Internasional kelas Internasional'),

  -- Kedokteran (fakultas_id = 7)
  (7, 'S1', 'Pendidikan Dokter'),
  (7, 'S1', 'Kedokteran Gigi'),

  -- Teknik (fakultas_id = 8)
  (8, 'S1', 'Teknik Elektro'),
  (8, 'S1', 'Teknik Mesin'),
  (8, 'S1', 'Teknik Sipil'),
  (8, 'S1', 'Teknik Industri'),
  (8, 'S1', 'Teknik Geologi'),
  (8, 'S1', 'Informatika'),
  (8, 'S1', 'Teknik Komputer'),
  (8, 'S1', 'Arsitektur'),
  (8, 'S1', 'Teknik Pertambangan'),

  -- Ilmu-ilmu Kesehatan (fakultas_id = 9)
  (9, 'S1', 'Kesehatan Masyarakat'),
  (9, 'S1', 'Ilmu Gizi'),
  (9, 'S1', 'Keperawatan'),
  (9, 'S1', 'Farmasi'),
  (9, 'S1', 'Pendidikan Jasmani'),
  (9, 'S1', 'Pendidikan Kepelatihan Olahraga'),
  (9, 'S1', 'Keperawatan Kelas Internasional'),

  -- Ilmu Budaya (fakultas_id = 10)
  (10, 'S1', 'Sastra Indonesia'),
  (10, 'S1', 'Sastra Inggris'),
  (10, 'S1', 'Sastra Jepang'),
  (10, 'S1', 'Pendidikan Bahasa Indonesia'),
  (10, 'S1', 'Pendidikan Bahasa Inggris'),
  (10, 'S1', 'Pendidikan Bahasa Jepang'),
  (10, 'D3', 'Bahasa Inggris'),
  (10, 'D3', 'Bahasa Mandarin'),

  -- Matematika dan Ilmu Pengetahuan Alam (fakultas_id = 11)
  (11, 'S1', 'Matematika'),
  (11, 'S1', 'Fisika'),
  (11, 'S1', 'Kimia'),
  (11, 'S1', 'Statistika'),

  -- Perikanan dan Ilmu Kelautan (fakultas_id = 12)
  (12, 'S1', 'Ilmu Kelautan'),
  (12, 'S1', 'Manajemen Sumberdaya Perairan'),
  (12, 'S1', 'Akuakultur');

-- ============================================================
-- 7. Akun Admin Default
-- ============================================================
-- Email : admin@mhs.unsoed.ac.id
-- Pass  : admin123
-- Hash  : password_hash('admin123', PASSWORD_DEFAULT)

INSERT INTO users (nama, email, no_hp, fakultas_id, prodi_id, password, role)
VALUES (
  'Administrator',
  'admin@mhs.unsoed.ac.id',
  '081200000000',
  8,   -- Teknik
  41,  -- Informatika (prodi id 41)
  '$2y$10$yGiKsQ6tFiL3fovfcATSCeekLpWXySsNHLnvDEEB3ImHJkiZNZ5zi',
  'admin'
);

-- ============================================================
-- 8. User Dummy untuk Testing
-- ============================================================
-- Password semua user dummy: "password" (di-hash)
-- Hash password_hash('password', PASSWORD_DEFAULT)

INSERT INTO users (nama, email, no_hp, fakultas_id, prodi_id, password, role) VALUES
  ('Teguh Romadon',    'teguh.romadon@mhs.unsoed.ac.id',    '081234567890', 8,  41, '$2y$10$LB6Megi4wV7vAReq0uyQQeArqxkUS1L4vp/s3bsMrp4voFaIr/Bru', 'user'),
  ('Siti Nurhaliza',   'siti.nurhaliza@mhs.unsoed.ac.id',   '081298765432', 3,  13, '$2y$10$LB6Megi4wV7vAReq0uyQQeArqxkUS1L4vp/s3bsMrp4voFaIr/Bru', 'user'),
  ('Andi Pratama',     'andi.pratama@mhs.unsoed.ac.id',     '085712345678', 5,  26, '$2y$10$LB6Megi4wV7vAReq0uyQQeArqxkUS1L4vp/s3bsMrp4voFaIr/Bru', 'user'),
  ('Dewi Lestari',     'dewi.lestari@mhs.unsoed.ac.id',     '087812345678', 9,  47, '$2y$10$LB6Megi4wV7vAReq0uyQQeArqxkUS1L4vp/s3bsMrp4voFaIr/Bru', 'user'),
  ('Rizky Firmansyah', 'rizky.firmansyah@mhs.unsoed.ac.id', '089612345678', 6,  29, '$2y$10$LB6Megi4wV7vAReq0uyQQeArqxkUS1L4vp/s3bsMrp4voFaIr/Bru', 'user');

-- ============================================================
-- 9. Data Dummy Laporan Barang
-- ============================================================

INSERT INTO items (user_id, type, nama_barang, kategori, fakultas_id, lokasi, tanggal, deskripsi, foto, status) VALUES

  -- Teguh (user_id=2) kehilangan kunci motor di Teknik
  (2, 'lost', 'Kunci Motor Honda',
   'Kunci', 8, 'Parkiran motor Gedung F1',
   '2026-09-20',
   'Kunci motor Honda dengan gantungan berwarna hitam bertuliskan "H". Hilang sekitar pukul 10 pagi saat parkir di depan Gedung F1.',
   'kunci.jpg', 'lost'),

  -- Siti (user_id=3) menemukan dompet di FEB
  (3, 'found', 'Dompet Kulit Coklat',
   'Dompet/Tas', 3, 'Kantin Fakultas Ekonomi dan Bisnis',
   '2026-09-21',
   'Dompet kulit warna coklat ditemukan di meja kantin FEB. Di dalamnya terdapat KTM dan beberapa kartu.',
   NULL, 'found'),

  -- Andi (user_id=4) kehilangan laptop charger
  (4, 'lost', 'Charger Laptop Asus',
   'Elektronik', 5, 'Ruang Baca Fakultas Hukum',
   '2026-09-22',
   'Charger laptop Asus warna hitam tertinggal di ruang baca lantai 2 Fakultas Hukum sekitar pukul 14.00.',
   'charger.jpg', 'lost'),

  -- Dewi (user_id=5) menemukan KTM
  (5, 'found', 'KTM atas nama Ahmad',
   'Dokumen', 9, 'Lobby Gedung Ilmu-ilmu Kesehatan',
   '2026-09-23',
   'KTM (Kartu Tanda Mahasiswa) atas nama Ahmad, prodi Kesehatan Masyarakat. Ditemukan di lantai lobby gedung utama.',
   'ktm.jpg', 'found'),

  -- Rizky (user_id=6) kehilangan tumbler
  (6, 'lost', 'Tumbler Stainless Biru',
   'Lainnya', 6, 'Ruang kelas FISIP Lt. 3',
   '2026-09-24',
   'Tumbler stainless steel warna biru dongker dengan stiker kucing. Kemungkinan tertinggal di ruang kelas 301.',
   'tumbler.jpg', 'lost'),

  -- Teguh (user_id=2) menemukan flashdisk
  (2, 'found', 'Flashdisk SanDisk 32GB',
   'Elektronik', 8, 'Lab Komputer Teknik',
   '2026-09-25',
   'Flashdisk SanDisk warna merah-hitam 32GB ditemukan tertancap di komputer Lab Komputer lantai 2.',
   NULL, 'found'),

  -- Siti (user_id=3) kehilangan buku
  (3, 'lost', 'Buku Pengantar Akuntansi',
   'Lainnya', 3, 'Perpustakaan FEB',
   '2026-09-26',
   'Buku "Pengantar Akuntansi" karangan Warren Reeve Fess. Cover biru dengan banyak sticky notes kuning di dalamnya.',
   'buku.jpg', 'lost'),

  -- Andi (user_id=4) menemukan kacamata
  (4, 'found', 'Kacamata Minus Frame Hitam',
   'Pakaian/Aksesoris', 5, 'Taman depan Fakultas Hukum',
   '2026-09-27',
   'Kacamata minus dengan frame hitam dan case biru. Ditemukan di bangku taman depan gedung utama FH.',
   'kacamata.jpg', 'found'),

  -- Item yang sudah dikembalikan (untuk testing status returned)
  (5, 'found', 'Jaket Almamater Unsoed',
   'Pakaian/Aksesoris', 9, 'Gedung Kuliah Bersama (GKB)',
   '2026-09-18',
   'Jaket almamater Unsoed ukuran L. Sudah berhasil dikembalikan ke pemiliknya.',
   'almamater.jpg', 'returned'),

  -- Rizky (user_id=6) menemukan kunci
  (6, 'found', 'Kunci dengan Gantungan Doraemon',
   'Kunci', 6, 'Mushola FISIP',
   '2026-09-28',
   'Satu set kunci (3 buah) dengan gantungan kunci berbentuk Doraemon. Ditemukan di rak sepatu mushola FISIP.',
   'kunci.jpg', 'found');
