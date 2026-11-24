<?php
/**
 * includes/footer.php — Footer + penutup HTML
 */
?>
</main><!-- /#main-content -->

<footer class="footer">
  <div class="container footer__inner">
    <p class="footer__text">
      &copy; <?= date('Y') ?> <strong>TitikNemu Unsoed</strong> &mdash; Cari. Temukan. Kembalikan.
    </p>
    <p class="footer__sub">
      Sistem informasi barang hilang &amp; ditemukan untuk mahasiswa Universitas Jenderal Soedirman.
    </p>
  </div>
</footer>

<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!-- Mobile Bottom Navigation -->
<nav class="mobile-nav">
  <a href="<?= $basePath ?? '' ?>index.php" class="mobile-nav__item <?= $currentPage === 'index' ? 'mobile-nav__item--active' : '' ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
    Beranda
  </a>
  <a href="<?= $basePath ?? '' ?>laporan/tambah.php" class="mobile-nav__item <?= $currentPage === 'tambah' ? 'mobile-nav__item--active' : '' ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="12" y1="18" x2="12" y2="12"></line><line x1="9" y1="15" x2="15" y2="15"></line></svg>
    Lapor
  </a>
  <a href="<?= $basePath ?? '' ?><?= isset($_SESSION['user_id']) ? 'dashboard.php' : 'login.php' ?>" class="mobile-nav__item <?= $currentPage === 'dashboard' ? 'mobile-nav__item--active' : '' ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path><rect x="9" y="3" width="6" height="4" rx="2"></rect><line x1="9" y1="12" x2="15" y2="12"></line><line x1="9" y1="16" x2="15" y2="16"></line></svg>
    Laporan
  </a>
  <a href="<?= $basePath ?? '' ?><?= isset($_SESSION['user_id']) ? 'profile.php' : 'login.php' ?>" class="mobile-nav__item <?= in_array($currentPage, ['login', 'register', 'profile']) ? 'mobile-nav__item--active' : '' ?>">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
    Akun
  </a>
</nav>

<!-- Navbar mobile toggle + dropdown (vanilla JS, minimal) -->
<script>
(function () {
  'use strict';

  /* ── Hamburger menu ── */
  var toggle = document.getElementById('navbarToggle');
  var menu   = document.getElementById('navbarMenu');

  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var expanded = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!expanded));
      menu.classList.toggle('navbar__menu--open');
      toggle.classList.toggle('navbar__toggle--active');
    });
  }

  /* ── User dropdown ── */
  var dropdownBtn  = document.getElementById('userDropdownBtn');
  var dropdownMenu = document.getElementById('userDropdownMenu');

  if (dropdownBtn && dropdownMenu) {
    dropdownBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var expanded = dropdownBtn.getAttribute('aria-expanded') === 'true';
      dropdownBtn.setAttribute('aria-expanded', String(!expanded));
      dropdownMenu.classList.toggle('navbar__dropdown--open');
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function (e) {
      if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownBtn.setAttribute('aria-expanded', 'false');
        dropdownMenu.classList.remove('navbar__dropdown--open');
      }
    });

    // Tutup dropdown saat tekan Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && dropdownMenu.classList.contains('navbar__dropdown--open')) {
        dropdownBtn.setAttribute('aria-expanded', 'false');
        dropdownMenu.classList.remove('navbar__dropdown--open');
        dropdownBtn.focus();
      }
    });
  }
})();
</script>

</body>
</html>
