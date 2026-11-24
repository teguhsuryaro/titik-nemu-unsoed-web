<?php
/**
 * admin/laporan.php — Manajemen Laporan
 * Refactored: Pure Semantic HTML5, No <style> or Inline Styles, Shared Admin Nav
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

<section class="section">
  <div class="container">
    <div class="page-header">
      <h1 class="page-header__title">Manajemen Laporan</h1>
      <p class="page-header__sub">Lihat, saring, dan moderasi seluruh laporan barang dari mahasiswa.</p>
    </div>

    <!-- Tab Navigasi Admin -->
    <?php require_once '../includes/admin_nav.php'; ?>

    <?php if (isset($_SESSION['flash_msg'])): ?>
      <div class="alert alert--success mb-3" role="status">
        <svg class="icon" aria-hidden="true"><use href="#i-check-circle"/></svg>
        <span><?= htmlspecialchars($_SESSION['flash_msg']) ?></span>
      </div>
      <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    <!-- Filter Bar Admin -->
    <form action="laporan.php" method="GET" class="admin-filter">
      <div>
        <input 
          type="text" 
          name="q" 
          class="form-control" 
          placeholder="Cari nama barang atau ciri..." 
          value="<?= htmlspecialchars($q) ?>"
        >
      </div>

      <div>
        <select name="type" class="form-select" onchange="this.form.submit()">
          <option value="all">Semua Jenis</option>
          <option value="lost" <?= $type === 'lost' ? 'selected' : '' ?>>Barang Hilang</option>
          <option value="found" <?= $type === 'found' ? 'selected' : '' ?>>Barang Temuan</option>
        </select>
      </div>

      <div>
        <select name="status" class="form-select" onchange="this.form.submit()">
          <option value="all">Semua Status</option>
          <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Aktif</option>
          <option value="returned" <?= $status === 'returned' ? 'selected' : '' ?>>Selesai / Dikembalikan</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <button type="submit" class="btn btn--primary">
          <svg class="icon icon--sm" aria-hidden="true"><use href="#i-search"/></svg>
          <span>Filter</span>
        </button>

        <?php if ($q !== '' || $type !== 'all' || $status !== 'all'): ?>
          <a href="laporan.php" class="btn btn--secondary btn--sm">Reset</a>
        <?php endif; ?>
      </div>
    </form>

    <!-- Table Laporan -->
    <div class="table-wrap">
      <table class="table table--stack">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Barang</th>
            <th>Jenis</th>
            <th>Status</th>
            <th>Pelapor</th>
            <th>Tanggal Lapor</th>
            <th class="text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($items)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-6">
                Tidak ada laporan yang cocok dengan filter yang dipilih.
              </td>
            </tr>
          <?php else: ?>
            <?php $no = 1; foreach ($items as $item): ?>
              <tr>
                <td data-label="No" class="text-muted"><?= $no++ ?></td>
                <td data-label="Nama Barang">
                  <div class="font-semibold">
                    <a href="../detail.php?id=<?= $item['id'] ?>" target="_blank" class="text-ink underline">
                      <?= htmlspecialchars($item['nama_barang']) ?>
                    </a>
                  </div>
                  <div class="text-xs text-muted"><?= htmlspecialchars($item['nama_fakultas']) ?></div>
                </td>
                <td data-label="Jenis">
                  <?php if ($item['type'] === 'lost'): ?>
                    <span class="badge badge--lost">Hilang</span>
                  <?php else: ?>
                    <span class="badge badge--found">Temuan</span>
                  <?php endif; ?>
                </td>
                <td data-label="Status">
                  <?php 
                    $isSelesai = ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found'));
                    if ($isSelesai) {
                      echo '<span class="badge badge--done">Selesai</span>';
                    } else {
                      echo '<span class="badge badge--active">Aktif</span>';
                    }
                  ?>
                </td>
                <td data-label="Pelapor">
                  <span><?= htmlspecialchars($item['pelapor']) ?></span>
                  <?php if ($item['is_banned']): ?>
                    <span class="badge badge--banned">Banned</span>
                  <?php endif; ?>
                </td>
                <td data-label="Tanggal Lapor" class="text-muted text-sm">
                  <?= date('d M Y', strtotime($item['created_at'])) ?>
                </td>
                <td data-label="Aksi" class="text-right">
                  <div class="flex items-center justify-end gap-2">
                    <a href="../detail.php?id=<?= $item['id'] ?>" class="btn btn--secondary btn--sm" target="_blank" title="Lihat Detail" aria-label="Lihat detail <?= htmlspecialchars($item['nama_barang']) ?>">
                      <svg class="icon icon--sm" aria-hidden="true"><use href="#i-search"/></svg>
                    </a>
                    
                    <form action="../laporan/hapus.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini secara permanen?');">
                      <input type="hidden" name="id" value="<?= $item['id'] ?>">
                      <button type="submit" class="btn btn--danger-outline btn--sm" title="Hapus Laporan" aria-label="Hapus laporan <?= htmlspecialchars($item['nama_barang']) ?>">
                        <svg class="icon icon--sm" aria-hidden="true"><use href="#i-trash"/></svg>
                      </button>
                    </form>
                  </div>
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
