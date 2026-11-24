<?php
/**
 * includes/admin_nav.php — Tab Navigasi Admin
 */
$adminCurrentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav class="admin-nav" aria-label="Navigasi panel admin">
  <a href="dashboard.php" class="admin-nav__item <?= $adminCurrentPage === 'dashboard' ? 'admin-nav__item--active' : '' ?>">
    <svg class="icon icon--sm" aria-hidden="true"><use href="#i-home"/></svg>
    <span>Ringkasan</span>
  </a>
  <a href="users.php" class="admin-nav__item <?= $adminCurrentPage === 'users' ? 'admin-nav__item--active' : '' ?>">
    <svg class="icon icon--sm" aria-hidden="true"><use href="#i-user"/></svg>
    <span>Manajemen Pengguna</span>
  </a>
  <a href="laporan.php" class="admin-nav__item <?= $adminCurrentPage === 'laporan' ? 'admin-nav__item--active' : '' ?>">
    <svg class="icon icon--sm" aria-hidden="true"><use href="#i-list"/></svg>
    <span>Manajemen Laporan</span>
  </a>
</nav>
