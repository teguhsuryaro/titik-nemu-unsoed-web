<?php
/**
 * index.php — Halaman Beranda & Pencarian Barang (Desain Hero Banner Sesuai Referensi)
 */

session_start();
require_once 'config/database.php';

$basePath   = '';
$pageTitle  = 'Beranda — TitikNemu Unsoed';
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

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- 1. Hero Banner Section (Sesuai Desain Referensi) -->
<section class="hero-banner-section">
  <div class="container">
    
    <div class="hero-banner">
      <div class="hero-banner__badge">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        TitikNemu Unsoed &bull; Cari. Temukan. Kembalikan.
      </div>

      <?php if ($isLoggedIn): ?>
        <h1 class="hero-banner__title">Halo, <?= htmlspecialchars($firstName) ?> 👋 Mau cari apa hari ini?</h1>
      <?php else: ?>
        <h1 class="hero-banner__title">Cari, Temukan, dan Kembalikan Barang di Kampus</h1>
      <?php endif; ?>

      <p class="hero-banner__sub">
        Platform terpusat civitas Universitas Jenderal Soedirman untuk mencari dan mengembalikan barang yang tercecer di lingkungan kampus.
      </p>
    </div>

    <!-- 2. Floating Search Card (Menumpuk di Bawah Banner) -->
    <div class="floating-search-card">
      
      <!-- Tab Filter Jenis Barang -->
      <?php
        $tabParams = [];
        if ($q !== '') $tabParams['q'] = $q;
        if ($fak_id > 0) $tabParams['fakultas_id'] = $fak_id;
      ?>
      <div class="floating-tabs">
        <a href="index.php?<?= http_build_query(array_merge($tabParams, ['type' => 'all'])) ?>" 
           class="floating-tab <?= $type === 'all' ? 'floating-tab--active' : '' ?>">
          Semua Laporan
          <span class="floating-tab__count"><?= $counts['all'] ?></span>
        </a>

        <a href="index.php?<?= http_build_query(array_merge($tabParams, ['type' => 'lost'])) ?>" 
           class="floating-tab <?= $type === 'lost' ? 'floating-tab--active' : '' ?>">
          Barang Hilang
          <span class="floating-tab__count"><?= $counts['lost'] ?></span>
        </a>

        <a href="index.php?<?= http_build_query(array_merge($tabParams, ['type' => 'found'])) ?>" 
           class="floating-tab <?= $type === 'found' ? 'floating-tab--active' : '' ?>">
          Barang Ditemukan
          <span class="floating-tab__count"><?= $counts['found'] ?></span>
        </a>
      </div>

      <!-- Form Pencarian -->
      <form action="index.php" method="GET" class="floating-search-form">
        <?php if ($type !== 'all'): ?>
          <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
        <?php endif; ?>

        <!-- Field Fakultas -->
        <div class="floating-field floating-field--select">
          <label class="floating-field__label">Lokasi Kampus</label>
          <select name="fakultas_id" class="form-select" aria-label="Pilih Fakultas">
            <option value="0">Semua Fakultas</option>
            <?php foreach ($fakultasList as $fak): ?>
              <option value="<?= $fak['id'] ?>" <?= $fak_id === (int)$fak['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($fak['nama_fakultas']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Field Input Nama Barang / Lokasi -->
        <div class="floating-field floating-field--input">
          <label class="floating-field__label">Kata Kunci Barang</label>
          <input type="text" name="q" class="form-input" placeholder="Misal: Kunci motor, dompet, KTM, tumbler..." value="<?= htmlspecialchars($q) ?>" aria-label="Cari barang">
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="btn btn--primary floating-search-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          Cari
        </button>
      </form>

      <!-- Footer Search Card: Status Pencarian & Tombol Buat Laporan -->
      <div class="search-card-footer">
        <div class="search-card-status" id="results-count">
          <?php if ($isFiltering): ?>
            Ditemukan <strong><?= $totalData ?></strong> laporan &bull; 
            <a href="index.php<?= $type !== 'all' ? '?type=' . urlencode($type) : '' ?>" style="color: var(--danger); font-weight: 600;">Reset kata kunci &times;</a>
          <?php else: ?>
            Menampilkan <strong><?= $totalData ?></strong> laporan terbaru
          <?php endif; ?>
        </div>

        <div class="search-card-actions">
          <span style="font-size: var(--font-size-xs); color: var(--muted); font-weight: 500;">Ingin lapor?</span>
          <a href="laporan/tambah.php?type=lost" class="btn-pill-lost">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            + Lapor Kehilangan
          </a>
          <a href="laporan/tambah.php?type=found" class="btn-pill-found">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line></svg>
            + Lapor Temuan
          </a>
        </div>
      </div>

    </div>

  </div>
</section>

<!-- 3. Daftar Kartu Barang -->
<section class="section" style="padding-top: 3.5rem;">
  <div class="container" id="items-container">

    <?php if (empty($items)): ?>
      <div class="alert" style="background: var(--surface); border: 1px dashed var(--border); text-align: center; padding: 4rem 1.5rem; border-radius: var(--radius-lg);">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <h3 style="font-size: var(--font-size-lg); font-weight: 700; color: var(--ink); margin-bottom: .5rem;">Tidak Ada Laporan yang Ditemukan</h3>
        <p class="text-muted" style="margin-bottom: 1.25rem;">Coba gunakan kata kunci lain atau pilih fakultas yang berbeda.</p>
        <a href="index.php" class="btn btn--outline btn--sm">Lihat Semua Laporan</a>
      </div>
    <?php else: ?>
      <div class="card-grid">
        <?php foreach ($items as $item): ?>
          <a href="detail.php?id=<?= $item['id'] ?>" class="card">

            <!-- Foto dan Badge Wrapper -->
            <div class="card__img-wrapper">
              <?php if (!empty($item['foto']) && file_exists('uploads/' . $item['foto'])): ?>
                <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" alt="Foto <?= htmlspecialchars($item['nama_barang']) ?>" class="card__img" loading="lazy">
              <?php else: ?>
                <div class="card__img-placeholder">
                  <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                </div>
              <?php endif; ?>

              <div class="card__badge">
                <?php if ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found')): ?>
                  <span class="badge badge--returned">Selesai</span>
                <?php elseif ($item['type'] === 'lost'): ?>
                  <span class="badge badge--lost">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Hilang
                  </span>
                <?php else: ?>
                  <span class="badge badge--found">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line></svg>
                    Ditemukan
                  </span>
                <?php endif; ?>
              </div>
            </div>

            <!-- Isi Informasi Kartu -->
            <div class="card__body">
              <?php if (!empty($item['kategori'])): ?>
                <div class="card__category"><?= htmlspecialchars($item['kategori']) ?></div>
              <?php endif; ?>

              <h3 class="card__title"><?= htmlspecialchars($item['nama_barang']) ?></h3>

              <div class="card__location">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>
                  <?= htmlspecialchars($item['nama_fakultas']) ?>
                  <?php if (!empty($item['lokasi'])): ?>
                    &bull; <?= htmlspecialchars($item['lokasi']) ?>
                  <?php endif; ?>
                </span>
              </div>

              <div class="card__date">
                <?= date('d M Y', strtotime($item['tanggal'])) ?>
              </div>
            </div>

          </a>
        <?php endforeach; ?>
      </div>

      <!-- Paginasi Bersih -->
      <?php if ($totalPages > 1): ?>
        <div class="pagination" id="pagination-container">
          <?php if ($page > 1): ?>
            <?php 
              $prevParams = array_merge($_GET, ['page' => $page - 1]);
              $prevUrl = 'index.php?' . http_build_query($prevParams); 
            ?>
            <a href="<?= $prevUrl ?>" class="btn btn--outline">&larr; Sebelumnya</a>
          <?php endif; ?>

          <span class="pagination__info">Halaman <?= $page ?> dari <?= $totalPages ?></span>

          <?php if ($page < $totalPages): ?>
            <?php 
              $nextParams = array_merge($_GET, ['page' => $page + 1]);
              $nextUrl = 'index.php?' . http_build_query($nextParams); 
            ?>
            <a href="<?= $nextUrl ?>" class="btn btn--outline">Selanjutnya &rarr;</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</section>

<!-- Vanilla JS: AJAX Filter Tanpa Reload Halaman Penuh -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  var tabs = document.querySelectorAll('.floating-tab');
  var itemsContainer = document.getElementById('items-container');
  var resultsCount = document.getElementById('results-count');

  if (!tabs.length || !itemsContainer) return;

  tabs.forEach(function (tab) {
    tab.addEventListener('click', function (e) {
      e.preventDefault();
      var url = this.getAttribute('href');

      // Update class active tab seketika
      tabs.forEach(function (t) { t.classList.remove('floating-tab--active'); });
      this.classList.add('floating-tab--active');

      // Ambil konten via Fetch API
      fetch(url)
        .then(function (res) { return res.text(); })
        .then(function (html) {
          var parser = new DOMParser();
          var doc = parser.parseFromString(html, 'text/html');

          var newItems = doc.getElementById('items-container');
          var newCount = doc.getElementById('results-count');

          if (newItems && itemsContainer) {
            itemsContainer.innerHTML = newItems.innerHTML;
          }
          if (newCount && resultsCount) {
            resultsCount.innerHTML = newCount.innerHTML;
          }

          // Sinkronkan URL browser tanpa reload
          if (window.history && window.history.pushState) {
            window.history.pushState(null, '', url);
          }
        })
        .catch(function () {
          // Fallback jika fetch terhambat
          window.location.href = url;
        });
    });
  });
});
</script>

<?php require_once 'includes/footer.php'; ?>
