<?php
/**
 * includes/navbar.php — Navigasi utama
 *
 * Menggunakan session untuk menentukan menu yang ditampilkan.
 * Variabel $basePath harus sudah di-set sebelum include.
 */

require_once __DIR__ . '/icons.php';

$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['nama'] ?? '';
$userRole   = $_SESSION['role'] ?? '';
$isAdmin    = ($userRole === 'admin');

// Halaman aktif (untuk highlight menu)
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$isSubAdmin  = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
?>

<!-- Skip to content (aksesibilitas) -->
<a href="#main-content" class="skip-link">Langsung ke konten</a>

<header class="navbar" id="navbar">
  <div class="container navbar__inner">

    <!-- Logo -->
    <a href="<?= $basePath ?>index.php" class="navbar__logo" aria-label="TitikNemu Unsoed — Beranda">
      <svg class="icon icon--lg navbar__logo-icon" aria-hidden="true"><use href="#i-pin"/></svg>
      <span class="navbar__logo-text">TitikNemu<span class="navbar__logo-sub">Unsoed</span></span>
    </a>

    <!-- Navigasi Desktop (≥ 1024px) -->
    <div class="navbar__desktop">
      <nav aria-label="Navigasi desktop">
        <ul class="navbar__links">
          <li>
            <a href="<?= $basePath ?>index.php"
               class="navbar__link <?= ($currentPage === 'index' && !$isSubAdmin) ? 'navbar__link--active' : '' ?>">
              Beranda
            </a>
          </li>

          <?php if ($isLoggedIn): ?>
          <li>
            <a href="<?= $basePath ?>dashboard.php"
               class="navbar__link <?= ($currentPage === 'dashboard' && !$isSubAdmin) ? 'navbar__link--active' : '' ?>">
              Laporan Saya
            </a>
          </li>
          <li>
            <a href="<?= $basePath ?>profile.php"
               class="navbar__link <?= ($currentPage === 'profile' && !$isSubAdmin) ? 'navbar__link--active' : '' ?>">
              Profil
            </a>
          </li>
          <?php if ($isAdmin): ?>
          <li>
            <a href="<?= $basePath ?>admin/dashboard.php"
               class="navbar__link <?= $isSubAdmin ? 'navbar__link--active' : '' ?>">
              Admin
            </a>
          </li>
          <?php endif; ?>
          <?php endif; ?>
        </ul>
      </nav>
    </div>

    <!-- Sisi kanan: User / Auth -->
    <div class="navbar__auth">
      <?php if ($isLoggedIn): ?>
        <a href="<?= $basePath ?>profile.php"
           class="navbar__avatar-link"
           aria-label="Profil <?= htmlspecialchars($userName) ?>"
           title="<?= htmlspecialchars($userName) ?>">
          <?= strtoupper(mb_substr($userName, 0, 1)) ?>
        </a>
      <?php else: ?>
        <a href="<?= $basePath ?>login.php" class="btn btn--secondary btn--sm">Masuk</a>
        <a href="<?= $basePath ?>register.php" class="btn btn--primary btn--sm">Daftar</a>
      <?php endif; ?>
    </div>

  </div>
</header>

<main id="main-content" class="main">
