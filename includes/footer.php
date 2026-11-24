<?php
/**
 * includes/footer.php — Footer + Bottom Nav + penutup HTML
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$isSubLaporan = strpos($_SERVER['PHP_SELF'], '/laporan/') !== false;
$isLoggedIn = isset($_SESSION['user_id']);
?>
</main><!-- /#main-content -->

<footer class="footer">
  <div class="container footer__inner">
    <p class="footer__text">
      &copy; <?= date('Y') ?> <strong>TitikNemu Unsoed</strong> &mdash; Cari. Temukan. Kembalikan.
    </p>
    <p class="footer__sub">
      Platform informasi barang hilang &amp; ditemukan mahasiswa Universitas Jenderal Soedirman.
    </p>
  </div>
</footer>

<!-- Mobile Bottom Navigation (< 1024px) -->
<nav class="bottom-nav" aria-label="Navigasi bawah">
  <a href="<?= $basePath ?>index.php"
     class="bottom-nav__item <?= ($currentPage === 'index' && !$isSubLaporan) ? 'bottom-nav__item--active' : '' ?>">
    <svg class="icon icon--md" aria-hidden="true"><use href="#i-home"/></svg>
    <span>Beranda</span>
  </a>

  <a href="<?= $basePath ?>laporan/tambah.php"
     class="bottom-nav__item <?= ($isSubLaporan && $currentPage === 'tambah') ? 'bottom-nav__item--active' : '' ?>">
    <svg class="icon icon--md" aria-hidden="true"><use href="#i-plus-circle"/></svg>
    <span>Lapor</span>
  </a>

  <a href="<?= $basePath ?><?= $isLoggedIn ? 'dashboard.php' : 'login.php' ?>"
     class="bottom-nav__item <?= ($currentPage === 'dashboard') ? 'bottom-nav__item--active' : '' ?>">
    <svg class="icon icon--md" aria-hidden="true"><use href="#i-list"/></svg>
    <span>Laporan</span>
  </a>

  <a href="<?= $basePath ?><?= $isLoggedIn ? 'profile.php' : 'login.php' ?>"
     class="bottom-nav__item <?= in_array($currentPage, ['login', 'register', 'profile']) ? 'bottom-nav__item--active' : '' ?>">
    <svg class="icon icon--md" aria-hidden="true"><use href="#i-user"/></svg>
    <span>Akun</span>
  </a>
</nav>

<!-- User dropdown toggle script -->
<script>
(function () {
  'use strict';

  var dropdownBtn  = document.getElementById('userDropdownBtn');
  var dropdownMenu = document.getElementById('userDropdownMenu');

  if (dropdownBtn && dropdownMenu) {
    dropdownBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var expanded = dropdownBtn.getAttribute('aria-expanded') === 'true';
      dropdownBtn.setAttribute('aria-expanded', String(!expanded));
      dropdownMenu.classList.toggle('navbar__dropdown--open');
    });

    document.addEventListener('click', function (e) {
      if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownBtn.setAttribute('aria-expanded', 'false');
        dropdownMenu.classList.remove('navbar__dropdown--open');
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && dropdownMenu.classList.contains('navbar__dropdown--open')) {
        dropdownBtn.setAttribute('aria-expanded', 'false');
        dropdownMenu.classList.remove('navbar__dropdown--open');
        dropdownBtn.focus();
      }
    });
  }

  /* ── Password visibility toggle ── */
  var passwordToggles = document.querySelectorAll('[data-password-toggle]');
  passwordToggles.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var targetId = this.getAttribute('data-password-toggle');
      var input = document.getElementById(targetId);
      var useEl = this.querySelector('use');
      if (!input) return;

      var isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      this.setAttribute('aria-pressed', String(isHidden));
      this.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
      if (useEl) {
        useEl.setAttribute('href', isHidden ? '#i-eye-off' : '#i-eye');
      }
    });
  });
})();
</script>

</body>
</html>
