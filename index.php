<?php
/**
 * index.php — Halaman Beranda
 */

session_start();
require_once 'config/database.php';

$basePath  = '';
$pageTitle = 'Beranda — TitikNemu Unsoed';
$isLoggedIn = isset($_SESSION['user_id']);
$userName   = $isLoggedIn ? $_SESSION['nama'] : '';

// Sapaan dinamis (ambil nama depan saja)
$firstName = $userName;
if (strpos($userName, ' ') !== false) {
    $firstName = explode(' ', $userName)[0];
}

// Ambil 8 laporan terbaru dari user yang tidak di-banned
$itemsQuery = "
    SELECT i.id, i.type, i.nama_barang, i.lokasi, i.tanggal, i.foto, i.status, f.nama_fakultas 
    FROM items i
    JOIN users u ON i.user_id = u.id
    JOIN fakultas f ON i.fakultas_id = f.id
    WHERE u.is_banned = 0
    ORDER BY i.created_at DESC
    LIMIT 8
";
$itemsResult = $conn->query($itemsQuery);
$latestItems = [];
if ($itemsResult) {
    while ($row = $itemsResult->fetch_assoc()) {
        $latestItems[] = $row;
    }
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<!-- Hero Section -->
<section class="container">
  <div class="hero">
    <div class="hero__content">
      
      <?php if ($isLoggedIn): ?>
        <h1 class="hero__title">Halo, <?= htmlspecialchars($firstName) ?> 👋</h1>
      <?php else: ?>
        <h1 class="hero__title">Cari. Temukan. <br>Kembalikan.</h1>
      <?php endif; ?>
      
      <p class="hero__sub">
        Cari barang yang hilang atau temukan barang yang mungkin milik orang lain di lingkungan Unsoed.
      </p>

      <!-- Search Bar -->
      <form action="search.php" method="GET" class="hero-search">
        <input type="text" name="q" class="form-input" placeholder="Cari nama barang, tag fakultas, atau lokasi..." aria-label="Cari barang">
        <button type="submit" class="btn btn--primary" aria-label="Cari">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </button>
      </form>

      <!-- Call to Actions -->
      <div class="hero-actions">
        <a href="laporan/tambah.php?type=lost" class="btn btn--primary btn--lg">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          Barang Hilang
        </a>
        <a href="laporan/tambah.php?type=found" class="btn btn--green-soft btn--lg">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
          Barang Ditemukan
        </a>
      </div>
    </div>
    
    <!-- Ilustrasi Dekstop -->
    <div class="hero__image" aria-hidden="true">
      <!-- Placeholder ilustrasi yang terinspirasi dari UI.png (bentuk tas dan pin kuning) -->
      <svg width="100%" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="200" cy="150" r="120" fill="var(--brand-yellow-soft)"/>
        <!-- Tas sederhana -->
        <rect x="130" y="80" width="140" height="160" rx="20" fill="var(--ink)"/>
        <path d="M160 80 V50 A40 40 0 0 1 240 50 V80" stroke="var(--ink)" stroke-width="16" stroke-linecap="round"/>
        <rect x="130" y="180" width="140" height="30" fill="var(--brand-yellow)"/>
        <!-- Pin lokasi mengambang -->
        <path d="M300 70 C300 85 285 105 285 105 C285 105 270 85 270 70 C270 60 276.7 50 285 50 C293.3 50 300 60 300 70 Z" fill="var(--brand-yellow)"/>
        <circle cx="285" cy="65" r="5" fill="var(--ink)"/>
        <text x="320" y="120" font-family="var(--font-family)" font-size="14" font-weight="600" fill="var(--ink)" transform="rotate(-10 320 120)">
          <tspan x="320" dy="0">Barangmu</tspan>
          <tspan x="325" dy="20">bisa kembali...</tspan>
        </text>
      </svg>
    </div>
  </div>
</section>

<!-- Barang Terbaru -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-header__title">Barang Terbaru</h2>
      <a href="search.php" class="btn btn--outline btn--sm">Lihat Semua &rarr;</a>
    </div>

    <?php if (empty($latestItems)): ?>
      <div class="alert alert--info">Belum ada laporan barang yang masuk.</div>
    <?php else: ?>
      <div class="card-grid">
        <?php foreach ($latestItems as $item): ?>
          <a href="detail.php?id=<?= $item['id'] ?>" class="card">
            
            <!-- Foto dan Badge Wrapper -->
            <div class="card__img-wrapper">
              <?php if (!empty($item['foto']) && file_exists('uploads/' . $item['foto'])): ?>
                <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" alt="Foto <?= htmlspecialchars($item['nama_barang']) ?>" class="card__img" loading="lazy">
              <?php else: ?>
                <div class="card__img-placeholder">
                  <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                </div>
              <?php endif; ?>
              
              <div class="card__badge">
                <?php if ($item['status'] === 'returned'): ?>
                  <span class="badge badge--returned">Dikembalikan</span>
                <?php elseif ($item['type'] === 'lost'): ?>
                  <span class="badge badge--lost">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Hilang
                  </span>
                <?php else: ?>
                  <span class="badge badge--found">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    Ditemukan
                  </span>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="card__body">
              <!-- Nama -->
              <h3 class="card__title"><?= htmlspecialchars($item['nama_barang']) ?></h3>
              
              <!-- Metadata -->
              <div class="card__meta">
                <span class="card__meta-item" title="Fakultas">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                  <?= htmlspecialchars($item['nama_fakultas']) ?>
                </span>
                
                <?php if (!empty($item['lokasi'])): ?>
                <span class="card__meta-item" title="Lokasi detail">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                  <span style="max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?= htmlspecialchars($item['lokasi']) ?>
                  </span>
                </span>
                <?php endif; ?>
                
                <span class="card__meta-item" style="width: 100%;" title="Tanggal kejadian">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                  <?= date('d M Y', strtotime($item['tanggal'])) ?>
                </span>
                <span class="card__arrow">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </span>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
