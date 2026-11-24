<?php
/**
 * includes/header.php — HTML head + session
 *
 * Variabel opsional (set sebelum include):
 *   $pageTitle  — judul halaman (default: 'TitikNemu Unsoed')
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tentukan base path relatif dari file pemanggil ke root proyek
// Halaman di root: ''  |  di subfolder (admin/, laporan/): '../'
if (!isset($basePath)) {
    $basePath = '';
}

if (!isset($pageTitle)) {
    $pageTitle = 'TitikNemu Unsoed — Cari. Temukan. Kembalikan.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="TitikNemu Unsoed — Sistem informasi barang hilang dan ditemukan untuk mahasiswa Universitas Jenderal Soedirman.">
  <meta name="theme-color" content="#FFD84D">

  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z' fill='%23FFD84D' stroke='%23172033' stroke-width='1.8'/%3E%3Ccircle cx='12' cy='10' r='3' fill='%23172033'/%3E%3C/svg%3E">

  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Stylesheet (dengan cache-busting otomatis) -->
  <link rel="stylesheet" href="<?= $basePath ?>css/style.css?v=<?= file_exists(__DIR__ . '/../css/style.css') ? filemtime(__DIR__ . '/../css/style.css') : time() ?>">
</head>
<body class="site">
