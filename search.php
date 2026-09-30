<?php
/**
 * search.php — Pencarian & Daftar Barang
 */

session_start();
require_once 'config/database.php';

$basePath  = '';
$pageTitle = 'Pencarian — TitikNemu Unsoed';

// ── Ambil & sanitasi parameter GET ──
$q         = trim($_GET['q'] ?? '');
$type      = $_GET['type'] ?? 'all'; // all, lost, found
$fak_id    = (int)($_GET['fakultas_id'] ?? 0);
$page      = max(1, (int)($_GET['page'] ?? 1));
$limit     = 12; // Jumlah data per halaman
$offset    = ($page - 1) * $limit;

// Ambil daftar fakultas untuk dropdown
$fakultasList = [];
$resFak = $conn->query("SELECT id, nama_fakultas FROM fakultas ORDER BY nama_fakultas ASC");
if ($resFak) {
    while ($row = $resFak->fetch_assoc()) {
        $fakultasList[] = $row;
    }
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
$totalData = $stmtCount->get_result()->fetch_assoc()['total'] ?? 0;
$stmtCount->close();

$totalPages = ceil($totalData / $limit);

// ── Ambil Data ──
$sql = "
    SELECT i.id, i.type, i.nama_barang, i.lokasi, i.tanggal, i.foto, i.status, f.nama_fakultas 
    FROM items i
    JOIN users u ON i.user_id = u.id
    JOIN fakultas f ON i.fakultas_id = f.id
    WHERE {$whereSQL}
    ORDER BY i.created_at DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
if (!empty($queryParams)) {
    // Tambahkan parameter untuk LIMIT dan OFFSET (integer)
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

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<style>
/* CSS Tambahan untuk Search & Pills */
.search-header {
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  padding: 2rem 0;
  margin-bottom: 2rem;
}
.search-form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 0.75rem;
  max-width: 800px;
  margin: 0 auto;
}
@media (min-width: 640px) {
  .search-form-grid {
    grid-template-columns: 2fr 1fr auto;
    gap: 1rem;
  }
}
@media (max-width: 639px) {
  .search-select-wrapper { display: none; } /* Hide on small screens to save space */
  .search-btn-wrapper .btn { padding: 0.6rem; }
  .search-form-grid { grid-template-columns: 1fr auto; }
}

/* Filter Pills */
.filter-pills {
  display: flex;
  gap: .75rem;
  justify-content: center;
  margin-top: 1.5rem;
  flex-wrap: wrap;
}
.filter-pills__label {
  cursor: pointer;
}
.filter-pills__input {
  display: none;
}
.filter-pills__text {
  display: inline-block;
  padding: .5rem 1.25rem;
  background: var(--page);
  border: 1px solid var(--border);
  border-radius: 999px;
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--muted);
  transition: all var(--transition);
}
.filter-pills__text:hover {
  background: #eaeef3;
  color: var(--ink);
}
.filter-pills__input:checked + .filter-pills__text {
  background: var(--ink);
  border-color: var(--ink);
  color: var(--surface);
}

/* Paginasi */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: .5rem;
  margin-top: 3rem;
}
.pagination .btn {
  min-height: 36px;
  padding: .375rem .75rem;
}
.pagination__info {
  font-size: var(--font-size-sm);
  color: var(--muted);
  margin: 0 1rem;
}
</style>

<div class="search-header">
  <div class="container">
    <div class="page-header text-center" style="margin-bottom: 1rem;">
      <h1 class="page-header__title" style="font-size: var(--font-size-xl);">Cari Barang</h1>
      <p class="page-header__sub" style="font-size: var(--font-size-sm);">Temukan laporan barang yang hilang atau ditemukan.</p>
    </div>

    <!-- Form Pencarian -->
    <form action="search.php" method="GET" id="searchForm">
      <div class="search-form-grid">
        <!-- Input Text -->
        <div class="search-input-wrapper">
          <input type="text" name="q" class="form-input" placeholder="Cari nama barang, lokasi, atau deskripsi..." value="<?= htmlspecialchars($q) ?>">
        </div>
        
        <!-- Dropdown Fakultas -->
        <div class="search-select-wrapper">
          <select name="fakultas_id" class="form-select" onchange="document.getElementById('searchForm').submit();">
            <option value="0">Semua Fakultas</option>
            <?php foreach ($fakultasList as $fak): ?>
              <option value="<?= $fak['id'] ?>" <?= $fak_id === (int)$fak['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($fak['nama_fakultas']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Tombol Cari -->
        <div class="search-btn-wrapper">
          <button type="submit" class="btn btn--primary" style="width: 100%;">
            Cari
          </button>
        </div>
      </div>

      <!-- Filter Tipe (Pills) -->
      <div class="filter-pills">
        <label class="filter-pills__label">
          <input type="radio" name="type" value="all" class="filter-pills__input" onchange="document.getElementById('searchForm').submit();" <?= $type === 'all' ? 'checked' : '' ?>>
          <span class="filter-pills__text">Semua</span>
        </label>
        
        <label class="filter-pills__label">
          <input type="radio" name="type" value="lost" class="filter-pills__input" onchange="document.getElementById('searchForm').submit();" <?= $type === 'lost' ? 'checked' : '' ?>>
          <span class="filter-pills__text">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            Barang Hilang
          </span>
        </label>
        
        <label class="filter-pills__label">
          <input type="radio" name="type" value="found" class="filter-pills__input" onchange="document.getElementById('searchForm').submit();" <?= $type === 'found' ? 'checked' : '' ?>>
          <span class="filter-pills__text">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            Barang Ditemukan
          </span>
        </label>
      </div>
    </form>
  </div>
</div>

<section class="section" style="padding-top: 0;">
  <div class="container">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h2 style="font-size: var(--font-size-lg); font-weight: 600;">Hasil Pencarian</h2>
      <span class="text-muted" style="font-size: var(--font-size-sm);">Ditemukan <?= $totalData ?> laporan</span>
    </div>

    <!-- Empty State -->
    <?php if (empty($items)): ?>
      <div class="alert" style="background: var(--surface); border: 1px dashed var(--border); text-align: center; padding: 4rem 1rem;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 1rem;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <h3 style="font-size: var(--font-size-lg); font-weight: 600; color: var(--ink); margin-bottom: .5rem;">Belum ada laporan yang cocok</h3>
        <p class="text-muted">Coba ubah kata kunci atau hapus filter untuk melihat laporan lainnya.</p>
        <?php if ($q !== '' || $type !== 'all' || $fak_id !== 0): ?>
          <a href="search.php" class="btn btn--outline mt-2">Hapus Semua Filter</a>
        <?php endif; ?>
      </div>
    <?php else: ?>

      <!-- Grid Kartu Laporan -->
      <div class="card-grid">
        <?php foreach ($items as $item): ?>
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
                
                <span class="card__meta-item" title="Tanggal kejadian">
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

      <!-- Paginasi -->
      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <!-- Tombol Prev -->
          <?php if ($page > 1): ?>
            <?php 
              $prevUrl = '?q=' . urlencode($q) . '&type=' . urlencode($type) . '&fakultas_id=' . $fak_id . '&page=' . ($page - 1); 
            ?>
            <a href="<?= $prevUrl ?>" class="btn btn--outline">
              &larr; Sebelumnya
            </a>
          <?php endif; ?>
          
          <span class="pagination__info">Halaman <?= $page ?> dari <?= $totalPages ?></span>
          
          <!-- Tombol Next -->
          <?php if ($page < $totalPages): ?>
            <?php 
              $nextUrl = '?q=' . urlencode($q) . '&type=' . urlencode($type) . '&fakultas_id=' . $fak_id . '&page=' . ($page + 1); 
            ?>
            <a href="<?= $nextUrl ?>" class="btn btn--outline">
              Selanjutnya &rarr;
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
