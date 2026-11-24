<?php
/**
 * includes/header.php — HTML head + session
 *
 * Variabel opsional (set sebelum include):
 *   $pageTitle  — judul halaman (default: 'TitikNemu Unsoed')
 *   $extraCss   — string CSS tambahan per halaman
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
    $pageTitle = 'TitikNemu Unsoed';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="TitikNemu Unsoed — Sistem informasi barang hilang dan ditemukan untuk mahasiswa Universitas Jenderal Soedirman.">

  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Stylesheet (dengan cache-busting otomatis) -->
  <link rel="stylesheet" href="<?= $basePath ?>css/style.css?v=<?= file_exists(__DIR__ . '/../css/style.css') ? filemtime(__DIR__ . '/../css/style.css') : time() ?>">

  <?php if (!empty($extraCss)): ?>
  <style><?= $extraCss ?></style>
  <?php endif; ?>
</head>
<body>
