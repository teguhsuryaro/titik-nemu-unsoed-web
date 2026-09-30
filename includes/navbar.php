<?php
/**
 * includes/navbar.php — Navigasi utama
 *
 * Menggunakan session untuk menentukan menu yang ditampilkan.
 * Variabel $basePath harus sudah di-set sebelum include.
 */

$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $_SESSION['nama'] ?? '';
$userRole   = $_SESSION['role'] ?? '';
$isAdmin    = ($userRole === 'admin');

// Halaman aktif (untuk highlight menu)
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<!-- Skip to content (aksesibilitas) -->
<a href="#main-content" class="skip-link">Langsung ke konten</a>

<header class="navbar" id="navbar">
  <div class="container navbar__inner">

    <!-- Logo -->
    <a href="<?= $basePath ?>index.php" class="navbar__logo" aria-label="TitikNemu Unsoed — Beranda">
      <!-- Pin lokasi icon (inline SVG) -->
      <svg class="navbar__logo-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
        <circle cx="12" cy="10" r="3"/>
      </svg>
      <span class="navbar__logo-text">
        <span class="navbar__logo-brand">TitikNemu</span>
        <span class="navbar__logo-sub">Unsoed</span>
      </span>
    </a>

    <!-- Hamburger toggle (mobile) -->
    <button
      class="navbar__toggle"
      id="navbarToggle"
      aria-label="Buka menu navigasi"
      aria-expanded="false"
      aria-controls="navbarMenu"
    >
      <span class="navbar__toggle-bar"></span>
      <span class="navbar__toggle-bar"></span>
      <span class="navbar__toggle-bar"></span>
    </button>

    <!-- Menu navigasi -->
    <nav class="navbar__menu" id="navbarMenu" aria-label="Navigasi utama">
      <ul class="navbar__links">
        <li>
          <a href="<?= $basePath ?>index.php"
             class="navbar__link <?= $currentPage === 'index' ? 'navbar__link--active' : '' ?>">
            Beranda
          </a>
        </li>
        <li>
          <a href="<?= $basePath ?>search.php"
             class="navbar__link <?= $currentPage === 'search' ? 'navbar__link--active' : '' ?>">
            Pencarian
          </a>
        </li>

        <?php if ($isLoggedIn && $isAdmin): ?>
        <li>
          <a href="<?= $basePath ?>admin/dashboard.php"
             class="navbar__link <?= in_array($currentPage, ['dashboard','users','laporan']) && strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'navbar__link--active' : '' ?>">
            Admin
          </a>
        </li>
        <?php endif; ?>
      </ul>

      <!-- Sisi kanan: auth -->
      <div class="navbar__auth">
        <?php if ($isLoggedIn): ?>

          <!-- User dropdown -->
          <div class="navbar__user" id="userDropdown">
            <button
              class="navbar__user-btn"
              id="userDropdownBtn"
              aria-expanded="false"
              aria-haspopup="true"
              aria-controls="userDropdownMenu"
            >
              <!-- Avatar placeholder -->
              <span class="navbar__avatar" aria-hidden="true">
                <?= strtoupper(mb_substr($userName, 0, 1)) ?>
              </span>
              <span class="navbar__user-name"><?= htmlspecialchars($userName) ?></span>
              <svg class="navbar__chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"/>
              </svg>
            </button>

            <ul class="navbar__dropdown" id="userDropdownMenu" role="menu">
              <li role="none">
                <a href="<?= $basePath ?>dashboard.php" class="navbar__dropdown-item" role="menuitem">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                  Dashboard
                </a>
              </li>
              <li role="none">
                <a href="<?= $basePath ?>dashboard.php#laporan-saya" class="navbar__dropdown-item" role="menuitem">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  Laporan Saya
                </a>
              </li>
              <li role="none">
                <a href="<?= $basePath ?>profile.php" class="navbar__dropdown-item" role="menuitem">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  Profil
                </a>
              </li>
              <li role="none" class="navbar__dropdown-divider"></li>
              <li role="none">
                <a href="<?= $basePath ?>logout.php" class="navbar__dropdown-item navbar__dropdown-item--danger" role="menuitem">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                  Keluar
                </a>
              </li>
            </ul>
          </div>

        <?php else: ?>

          <a href="<?= $basePath ?>login.php" class="btn btn--outline btn--sm">Masuk</a>
          <a href="<?= $basePath ?>register.php" class="btn btn--primary btn--sm">Daftar</a>

        <?php endif; ?>
      </div>
    </nav>

  </div>
</header>

<main id="main-content">
