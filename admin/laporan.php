<?php
/**
 * admin/laporan.php — Manajemen Laporan
 */

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/database.php';
$basePath  = '../';
$pageTitle = 'Manajemen Laporan — TitikNemu Unsoed';

// ── Ambil Filter ──
$q      = trim($_GET['q'] ?? '');
$type   = $_GET['type'] ?? 'all';
$status = $_GET['status'] ?? 'all';

// ── Bangun Query ──
$whereConditions = [];
$queryParams     = [];
$paramTypes      = "";

if ($q !== '') {
    $whereConditions[] = "(i.nama_barang LIKE ? OR i.deskripsi LIKE ?)";
    $likeQuery = "%{$q}%";
    array_push($queryParams, $likeQuery, $likeQuery);
    $paramTypes .= "ss";
}
if ($type === 'lost' || $type === 'found') {
    $whereConditions[] = "i.type = ?";
    $queryParams[] = $type;
    $paramTypes .= "s";
}
if ($status === 'active' || $status === 'returned') {
    if ($status === 'active') {
        $whereConditions[] = "(i.status != 'returned' AND NOT (i.type='lost' AND i.status='found'))";
    } else {
        $whereConditions[] = "(i.status = 'returned' OR (i.type='lost' AND i.status='found'))";
    }
}

$whereSQL = "";
if (!empty($whereConditions)) {
    $whereSQL = "WHERE " . implode(" AND ", $whereConditions);
}

$sql = "
    SELECT i.id, i.nama_barang, i.type, i.status, i.created_at, u.nama AS pelapor, u.is_banned, f.nama_fakultas
    FROM items i
    JOIN users u ON i.user_id = u.id
    JOIN fakultas f ON i.fakultas_id = f.id
    {$whereSQL}
    ORDER BY i.created_at DESC
";

$stmt = $conn->prepare($sql);
if (!empty($queryParams)) {
    $stmt->bind_param($paramTypes, ...$queryParams);
}
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<style>
.table--admin tbody tr:nth-child(even) { background-color: var(--page); }
.table--admin tbody tr:hover { background-color: #f1f5f9; }
.filter-bar { background: var(--surface); padding: 1rem; border: 1px solid var(--border); border-radius: var(--radius-lg); margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; }
</style>

<section class="section">
  <div class="container">
    <div class="page-header">
      <a href="dashboard.php" class="btn btn--outline btn--sm mb-2" style="display: inline-flex;">&larr; Kembali ke Dashboard Admin</a>
      <h1 class="page-header__title">Manajemen Laporan</h1>
      <p class="page-header__sub">Lihat, filter, dan hapus laporan barang dari semua pengguna.</p>
    </div>

    <?php if (isset($_SESSION['flash_msg'])): ?>
      <div class="alert alert--success mb-3"><?= htmlspecialchars($_SESSION['flash_msg']) ?></div>
      <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    <!-- Filter Bar -->
    <form action="laporan.php" method="GET" class="filter-bar">
      <div style="flex: 1; min-width: 250px;">
        <input type="text" name="q" class="form-input" placeholder="Cari nama barang..." value="<?= htmlspecialchars($q) ?>" style="min-height: 40px; padding: .5rem .75rem;">
      </div>
      <div>
        <select name="type" class="form-select" onchange="this.form.submit()" style="min-height: 40px; padding: .5rem 2rem .5rem .75rem;">
          <option value="all">Semua Jenis</option>
          <option value="lost" <?= $type === 'lost' ? 'selected' : '' ?>>Barang Hilang</option>
          <option value="found" <?= $type === 'found' ? 'selected' : '' ?>>Barang Ditemukan</option>
        </select>
      </div>
      <div>
        <select name="status" class="form-select" onchange="this.form.submit()" style="min-height: 40px; padding: .5rem 2rem .5rem .75rem;">
          <option value="all">Semua Status</option>
          <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Aktif</option>
          <option value="returned" <?= $status === 'returned' ? 'selected' : '' ?>>Selesai/Dikembalikan</option>
        </select>
      </div>
      <div>
        <button type="submit" class="btn btn--primary" style="min-height: 40px; padding: .5rem 1rem;">Filter</button>
      </div>
      <?php if ($q !== '' || $type !== 'all' || $status !== 'all'): ?>
        <div>
          <a href="laporan.php" class="text-muted" style="font-size: var(--font-size-sm); text-decoration: underline;">Reset</a>
        </div>
      <?php endif; ?>
    </form>

    <!-- Table -->
    <div class="table-responsive" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);">
      <table class="table table--admin">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Jenis</th>
            <th>Status</th>
            <th>Pelapor</th>
            <th>Tanggal</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($items)): ?>
            <tr><td colspan="7" class="text-center text-muted" style="padding: 2rem;">Tidak ada laporan yang cocok.</td></tr>
          <?php else: ?>
            <?php $no = 1; foreach ($items as $item): ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td>
                  <div style="font-weight: 600;">
                    <a href="../detail.php?id=<?= $item['id'] ?>" target="_blank" style="color: var(--ink); text-decoration: none;">
                      <?= htmlspecialchars($item['nama_barang']) ?>
                    </a>
                  </div>
                  <div style="font-size: var(--font-size-xs); color: var(--muted);"><?= htmlspecialchars($item['nama_fakultas']) ?></div>
                </td>
                <td>
                  <?php if ($item['type'] === 'lost'): ?>
                    <span class="badge badge--lost">Hilang</span>
                  <?php else: ?>
                    <span class="badge badge--found">Ditemukan</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php 
                    $isSelesai = ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found'));
                    if ($isSelesai) echo '<span class="text-muted" style="font-weight:600; font-size:var(--font-size-sm);">Selesai</span>';
                    else echo '<span style="color:var(--brand-green); font-weight:600; font-size:var(--font-size-sm);">Aktif</span>';
                  ?>
                </td>
                <td>
                  <?= htmlspecialchars($item['pelapor']) ?>
                  <?php if ($item['is_banned']): ?>
                    <span class="badge" style="background:#fef2f2; color:var(--danger); border: 1px solid #fecaca; font-size: 10px; padding: 2px 4px; margin-left: 4px;">Banned</span>
                  <?php endif; ?>
                </td>
                <td class="text-muted" style="font-size: var(--font-size-sm);">
                  <?= date('d M Y', strtotime($item['created_at'])) ?>
                </td>
                <td style="text-align: right;">
                  <a href="../detail.php?id=<?= $item['id'] ?>" class="btn btn--outline btn--sm" target="_blank" title="Lihat Detail" style="padding: .25rem .5rem; min-height: auto;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  </a>
                  
                  <form action="../laporan/hapus.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.');">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <button type="submit" class="btn btn--outline btn--sm" style="color: var(--danger); border-color: transparent; padding: .25rem .5rem; min-height: auto;" title="Hapus">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php require_once '../includes/footer.php'; ?>
