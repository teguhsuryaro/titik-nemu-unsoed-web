<?php
/**
 * index.php — Halaman Beranda & Pencarian Barang
 * Refactored: Pure PHP Native, Responsive, Accessible, No Inline Styles
 */

session_start();
require_once 'config/database.php';

$basePath   = '';
$pageTitle  = 'TitikNemu Unsoed — Cari. Temukan. Kembalikan.';
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $isLoggedIn ? $_SESSION['nama'] : '';

// Sapaan dinamis (ambil nama depan saja)
$firstName = $userName;
if (strpos($userName, ' ') !== false) {
    $firstName = explode(' ', $userName)[0];
}

// ── Ambil & sanitasi parameter pencarian/filter ──
$q      = trim($_GET['q'] ?? '');
$type   = $_GET['type'] ?? 'all'; // all, lost, found
$fak_id = (int)($_GET['fakultas_id'] ?? 0);
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 12;
$offset = ($page - 1) * $limit;

// Ambil daftar fakultas untuk dropdown filter
$fakultasList = [];
$resFak = $conn->query("SELECT id, nama_fakultas FROM fakultas ORDER BY nama_fakultas ASC");
if ($resFak) {
    while ($row = $resFak->fetch_assoc()) {
        $fakultasList[] = $row;
    }
}

// ── Hitung data per tab (Global) ──
$counts = ['all' => 0, 'lost' => 0, 'found' => 0];
$countRes = $conn->query("
    SELECT 
        COUNT(i.id) AS total,
        SUM(CASE WHEN i.type = 'lost' THEN 1 ELSE 0 END) AS total_lost,
        SUM(CASE WHEN i.type = 'found' THEN 1 ELSE 0 END) AS total_found
    FROM items i
    JOIN users u ON i.user_id = u.id
    WHERE u.is_banned = 0
");
if ($countRes && $row = $countRes->fetch_assoc()) {
    $counts['all']   = (int)$row['total'];
    $counts['lost']  = (int)($row['total_lost'] ?? 0);
    $counts['found'] = (int)($row['total_found'] ?? 0);
}

// ── Bangun Query Dinamis ──
$whereConditions = ["u.is_banned = 0"];
$queryParams     = [];
$paramTypes      = "";

if ($q !== '') {
    $whereConditions[] = "(i.nama_barang LIKE ? OR i.lokasi LIKE ? OR i.deskripsi LIKE ?)";
    $likeQuery = "%{$q}%";
    array_push($queryParams, $likeQuery, $likeQuery, $likeQuery);
    $paramTypes .= "sss";
}

if ($type === 'lost' || $type === 'found') {
    $whereConditions[] = "i.type = ?";
    $queryParams[] = $type;
    $paramTypes .= "s";
}

if ($fak_id > 0) {
    $whereConditions[] = "i.fakultas_id = ?";
    $queryParams[] = $fak_id;
    $paramTypes .= "i";
}

$whereSQL = implode(" AND ", $whereConditions);

// ── Hitung Total (Untuk Paginasi) ──
$countQuery = "SELECT COUNT(i.id) AS total FROM items i JOIN users u ON i.user_id = u.id WHERE {$whereSQL}";
$stmtCount = $conn->prepare($countQuery);
if (!empty($queryParams)) {
    $stmtCount->bind_param($paramTypes, ...$queryParams);
}
$stmtCount->execute();
$totalData = (int)($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
$stmtCount->close();

$totalPages = max(1, (int)ceil($totalData / $limit));

// ── Ambil Data Laporan ──
$sql = "
    SELECT i.id, i.type, i.nama_barang, i.kategori, i.lokasi, i.tanggal, i.foto, i.status, f.nama_fakultas 
    FROM items i
    JOIN users u ON i.user_id = u.id
    JOIN fakultas f ON i.fakultas_id = f.id
    WHERE {$whereSQL}
    ORDER BY i.created_at DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
if (!empty($queryParams)) {
    $stmt->bind_param($paramTypes . "ii", ...array_merge($queryParams, [$limit, $offset]));
} else {
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

$isFiltering = ($q !== '' || $fak_id > 0);

// Ikon placeholder kategori
$catIcons = [
    'Kunci'              => 'i-key',
    'Dompet/Tas'         => 'i-wallet',
    'Elektronik'         => 'i-phone',
    'Dokumen'            => 'i-file',
    'Pakaian/Aksesoris'  => 'i-glasses',
    'Alat Tulis'         => 'i-pencil',
    'Lainnya'            => 'i-box'
];

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- 1. Hero Section ("Peta Kampus") -->
<section class="hero">
  <div class="container hero__container">
    
    <div class="hero__content">
      <div class="hero__badge">
        <svg class="icon icon--sm" aria-hidden="true"><use href="#i-pin"/></svg>
        <span>TitikNemu Unsoed &bull; Cari. Temukan. Kembalikan.</span>
      </div>

      <?php if ($isLoggedIn): ?>
        <h1 class="hero__title">Halo, <?= htmlspecialchars($firstName) ?>! 👋<br>Mau cari atau lapor apa hari ini?</h1>
      <?php else: ?>
        <h1 class="hero__title">Cari, Temukan, dan Kembalikan Barang di Kampus</h1>
      <?php endif; ?>

      <p class="hero__sub">
        Platform terpusat civitas Universitas Jenderal Soedirman untuk mencari dan mengembalikan barang yang tercecer di lingkungan kampus.
      </p>

      <div class="hero__cta-group">
        <a href="laporan/tambah.php?type=lost" class="cta--lost">
          <svg class="icon" aria-hidden="true"><use href="#i-search"/></svg>
          <span>Lapor Kehilangan</span>
        </a>
        <a href="laporan/tambah.php?type=found" class="cta--found">
          <svg class="icon" aria-hidden="true"><use href="#i-gift"/></svg>
          <span>Lapor Temuan</span>
        </a>
      </div>
    </div>

    <!-- Hero Illustration (Tas Kampus & Pin Melayang) -->
    <div class="hero__illustration" aria-hidden="true">
      <svg class="hero-art" viewBox="0 0 320 260" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="160" cy="225" rx="100" ry="14" fill="#E2E7EE"/>
        <rect x="100" y="70" width="120" height="140" rx="28" fill="#172033"/>
        <rect x="115" y="125" width="90" height="70" rx="14" fill="#243048"/>
        <path d="M115 145h90" stroke="#FFD84D" stroke-width="3" stroke-linecap="round"/>
        <circle cx="160" cy="165" r="4" fill="#FFD84D"/>
        <path d="M135 70V52a12 12 0 0 1 12-12h26a12 12 0 0 1 12 12v18" stroke="#172033" stroke-width="8" stroke-linecap="round"/>
        <rect x="86" y="90" width="14" height="90" rx="7" fill="#647084"/>
        <rect x="220" y="90" width="14" height="90" rx="7" fill="#647084"/>
        <path d="M100 100h120" stroke="#243048" stroke-width="4"/>
        <g class="hero-art__pin">
          <ellipse cx="230" cy="80" rx="18" ry="6" fill="rgba(23, 32, 51, 0.15)"/>
          <path d="M230 72c-13.25 0-24-10.75-24-24 0-18 24-42 24-42s24 24 24 42c0 13.25-10.75 24-24 24Z" fill="#FFD84D" stroke="#172033" stroke-width="3"/>
          <circle cx="230" cy="48" r="8" fill="#172033"/>
        </g>
      </svg>
    </div>

  </div>
</section>

<!-- 2. Search & Filter Bar -->
<div class="container search-wrapper">
  <div class="search-box">
    
    <form action="index.php" method="GET" class="search-form" id="search-form">
      <input type="hidden" name="type" id="search-type" value="<?= htmlspecialchars($type) ?>">

      <!-- Input Kata Kunci -->
      <div class="search-field">
        <svg class="icon search-field__icon" aria-hidden="true"><use href="#i-search"/></svg>
        <input 
          type="text" 
          name="q" 
          id="search-query"
          class="search-field__input" 
          placeholder="Cari nama barang, misal: kunci, dompet, KTM..." 
          value="<?= htmlspecialchars($q) ?>" 
          aria-label="Cari nama barang">
      </div>

      <!-- Pilihan Fakultas -->
      <div class="search-field">
        <select name="fakultas_id" id="search-fakultas" class="search-select" aria-label="Filter Fakultas">
          <option value="0">Semua Fakultas</option>
          <?php foreach ($fakultasList as $fak): ?>
            <option value="<?= $fak['id'] ?>" <?= $fak_id === (int)$fak['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($fak['nama_fakultas']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Tombol Submit -->
      <button type="submit" class="btn btn--primary">
        <svg class="icon" aria-hidden="true"><use href="#i-search"/></svg>
        <span>Cari</span>
      </button>
    </form>

  </div>
</div>

<!-- 3. Daftar Hasil Laporan -->
<section class="section">
  <div class="container">

    <!-- Header Status Hasil & Filter Jenis -->
    <?php
      $tabParams = [];
      if ($q !== '') $tabParams['q'] = $q;
      if ($fak_id > 0) $tabParams['fakultas_id'] = $fak_id;
    ?>
    <div class="results-bar">
      <div class="segmented" role="tablist" aria-label="Filter jenis laporan">
        <a href="index.php?<?= http_build_query(array_merge($tabParams, ['type' => 'all'])) ?>" 
           class="segmented__item <?= $type === 'all' ? 'segmented__item--active' : '' ?>"
           data-filter-tab="all"
           role="tab"
           aria-selected="<?= $type === 'all' ? 'true' : 'false' ?>">
          <span>Semua</span>
          <span class="segmented__count"><?= $counts['all'] ?></span>
        </a>

        <a href="index.php?<?= http_build_query(array_merge($tabParams, ['type' => 'lost'])) ?>" 
           class="segmented__item <?= $type === 'lost' ? 'segmented__item--active' : '' ?>"
           data-filter-tab="lost"
           role="tab"
           aria-selected="<?= $type === 'lost' ? 'true' : 'false' ?>">
          <span>Hilang</span>
          <span class="segmented__count"><?= $counts['lost'] ?></span>
        </a>

        <a href="index.php?<?= http_build_query(array_merge($tabParams, ['type' => 'found'])) ?>" 
           class="segmented__item <?= $type === 'found' ? 'segmented__item--active' : '' ?>"
           data-filter-tab="found"
           role="tab"
           aria-selected="<?= $type === 'found' ? 'true' : 'false' ?>">
          <span>Temuan</span>
          <span class="segmented__count"><?= $counts['found'] ?></span>
        </a>
      </div>

      <div class="results-bar__count" id="results-count" aria-live="polite">
        <?php if ($isFiltering): ?>
          Ditemukan <strong><?= $totalData ?></strong> laporan &bull; 
          <a href="index.php<?= $type !== 'all' ? '?type=' . urlencode($type) : '' ?>" class="text-danger font-semibold">Reset filter &times;</a>
        <?php else: ?>
          Menampilkan <strong><?= $totalData ?></strong> laporan terbaru
        <?php endif; ?>
      </div>
    </div>

    <!-- Grid Container Kartu Barang -->
    <div id="items-container">
      <?php if (empty($items)): ?>
        <div class="empty-state">
          <div class="empty-state__icon">
            <svg class="icon icon--xl" aria-hidden="true"><use href="#i-search"/></svg>
          </div>
          <h2 class="empty-state__title">Belum ada laporan yang cocok</h2>
          <p class="empty-state__desc">Coba ubah kata kunci pencarian atau pilih fakultas yang berbeda.</p>
          <a href="index.php" class="btn btn--secondary btn--sm">Lihat Semua Laporan</a>
        </div>
      <?php else: ?>
        <div class="item-grid">
          <?php foreach ($items as $i => $item): ?>
            <a href="detail.php?id=<?= $item['id'] ?>" class="item-card" style="--i:<?= $i ?>">
              
              <div class="item-card__media">
                <?php if (!empty($item['foto']) && file_exists('uploads/' . $item['foto'])): ?>
                  <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" 
                       alt="Foto <?= htmlspecialchars($item['nama_barang']) ?>" 
                       class="item-card__img" 
                       loading="lazy">
                <?php else: ?>
                  <?php $iconId = $catIcons[$item['kategori'] ?? ''] ?? 'i-box'; ?>
                  <div class="item-card__placeholder">
                    <svg class="icon icon--xl" aria-hidden="true"><use href="#<?= $iconId ?>"/></svg>
                    <span class="text-xs text-muted"><?= htmlspecialchars($item['kategori'] ?? 'Barang') ?></span>
                  </div>
                <?php endif; ?>

                <div class="item-card__badge">
                  <?php if ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found')): ?>
                    <span class="badge badge--done">
                      <svg class="icon icon--sm" aria-hidden="true"><use href="#i-check"/></svg>
                      <span>Selesai</span>
                    </span>
                  <?php elseif ($item['type'] === 'lost'): ?>
                    <span class="badge badge--lost">
                      <svg class="icon icon--sm" aria-hidden="true"><use href="#i-search"/></svg>
                      <span>Hilang</span>
                    </span>
                  <?php else: ?>
                    <span class="badge badge--found">
                      <svg class="icon icon--sm" aria-hidden="true"><use href="#i-gift"/></svg>
                      <span>Temuan</span>
                    </span>
                  <?php endif; ?>
                </div>
              </div>

              <div class="item-card__body">
                <?php if (!empty($item['kategori'])): ?>
                  <div class="item-card__category"><?= htmlspecialchars($item['kategori']) ?></div>
                <?php endif; ?>

                <h3 class="item-card__title"><?= htmlspecialchars($item['nama_barang']) ?></h3>

                <div class="item-card__meta">
                  <div class="item-card__meta-row">
                    <svg class="icon icon--sm" aria-hidden="true"><use href="#i-school"/></svg>
                    <span><?= htmlspecialchars($item['nama_fakultas']) ?></span>
                  </div>
                  <?php if (!empty($item['lokasi'])): ?>
                    <div class="item-card__meta-row">
                      <svg class="icon icon--sm" aria-hidden="true"><use href="#i-pin"/></svg>
                      <span><?= htmlspecialchars($item['lokasi']) ?></span>
                    </div>
                  <?php endif; ?>
                  <div class="item-card__meta-row">
                    <svg class="icon icon--sm" aria-hidden="true"><use href="#i-calendar"/></svg>
                    <span><?= date('d M Y', strtotime($item['tanggal'])) ?></span>
                  </div>
                </div>
              </div>

            </a>
          <?php endforeach; ?>
        </div>

        <!-- Paginasi -->
        <?php if ($totalPages > 1): ?>
          <nav class="pagination" aria-label="Navigasi halaman laporan" id="pagination-container">
            <?php if ($page > 1): ?>
              <?php 
                $prevParams = array_merge($_GET, ['page' => $page - 1]);
                $prevUrl = 'index.php?' . http_build_query($prevParams); 
              ?>
              <a href="<?= $prevUrl ?>" class="pagination__link" aria-label="Halaman sebelumnya">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"/></svg>
              </a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <?php if ($p === 1 || $p === $totalPages || ($p >= $page - 1 && $p <= $page + 1)): ?>
                <?php 
                  $pageParams = array_merge($_GET, ['page' => $p]);
                  $pageUrl = 'index.php?' . http_build_query($pageParams); 
                ?>
                <a href="<?= $pageUrl ?>" 
                   class="pagination__link <?= $p === $page ? 'pagination__link--active' : '' ?>"
                   aria-current="<?= $p === $page ? 'page' : 'false' ?>">
                  <?= $p ?>
                </a>
              <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
              <?php 
                $nextParams = array_merge($_GET, ['page' => $page + 1]);
                $nextUrl = 'index.php?' . http_build_query($nextParams); 
              ?>
              <a href="<?= $nextUrl ?>" class="pagination__link" aria-label="Halaman selanjutnya">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-right"/></svg>
              </a>
            <?php endif; ?>
          </nav>
        <?php endif; ?>

      <?php endif; ?>
    </div>

  </div>
</section>

<!-- Vanilla JS: AJAX Filter & Tab Switcher (AbortController, sync form, no auto-scroll) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  var tabs = document.querySelectorAll('.segmented__item');
  var itemsContainer = document.getElementById('items-container');
  var resultsCount = document.getElementById('results-count');
  var hiddenTypeInput = document.getElementById('search-type');
  var currentAbortController = null;

  function loadUrl(url, pushHistory) {
    if (currentAbortController) {
      currentAbortController.abort();
    }
    currentAbortController = new AbortController();

    if (itemsContainer) {
      itemsContainer.style.opacity = '0.5';
    }

    fetch(url, { signal: currentAbortController.signal })
      .then(function (res) { return res.text(); })
      .then(function (html) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');

        var newItems = doc.getElementById('items-container');
        var newCount = doc.getElementById('results-count');

        if (newItems && itemsContainer) {
          itemsContainer.innerHTML = newItems.innerHTML;
          itemsContainer.style.opacity = '1';
        }
        if (newCount && resultsCount) {
          resultsCount.innerHTML = newCount.innerHTML;
        }

        if (pushHistory && window.history && window.history.pushState) {
          window.history.pushState(null, '', url);
        }
      })
      .catch(function (err) {
        if (err.name === 'AbortError') return;
        window.location.href = url;
      });
  }

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function (e) {
      e.preventDefault();
      var url = this.getAttribute('href');
      var filterType = this.getAttribute('data-filter-tab') || 'all';

      // Update hidden input agar pencarian berikutnya mempertahankan filter jenis
      if (hiddenTypeInput) {
        hiddenTypeInput.value = filterType;
      }

      // Update active state visual pada tabs
      tabs.forEach(function (t) {
        t.classList.remove('segmented__item--active');
        t.setAttribute('aria-selected', 'false');
      });
      this.classList.add('segmented__item--active');
      this.setAttribute('aria-selected', 'true');

      loadUrl(url, true);
    });
  });

  // Dukung tombol Back / Forward browser
  window.addEventListener('popstate', function () {
    loadUrl(window.location.href, false);
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>
