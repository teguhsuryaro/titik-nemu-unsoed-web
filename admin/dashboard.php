<?php
/**
 * admin/dashboard.php — Dashboard Admin
 * Refactored: Pure Semantic HTML5, No <style> or Inline Styles, Shared Admin Nav
 */

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/database.php';
$basePath  = '../';
$pageTitle = 'Dashboard Admin — TitikNemu Unsoed';
$adminName = $_SESSION['nama'];

// ── Ambil Statistik ──
$stats = [
    'total_user' => 0,
    'total_laporan' => 0,
    'hilang_aktif' => 0,
    'ditemukan_aktif' => 0,
    'dikembalikan' => 0
];

$res = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'user'");
if ($res) $stats['total_user'] = $res->fetch_assoc()['c'];

$res = $conn->query("SELECT COUNT(*) AS c FROM items");
if ($res) $stats['total_laporan'] = $res->fetch_assoc()['c'];

$res = $conn->query("SELECT COUNT(*) AS c FROM items WHERE type = 'lost' AND status = 'lost'");
if ($res) $stats['hilang_aktif'] = $res->fetch_assoc()['c'];

$res = $conn->query("SELECT COUNT(*) AS c FROM items WHERE type = 'found' AND status = 'found'");
if ($res) $stats['ditemukan_aktif'] = $res->fetch_assoc()['c'];

$res = $conn->query("SELECT COUNT(*) AS c FROM items WHERE status = 'returned' OR (type='lost' AND status='found')");
if ($res) $stats['dikembalikan'] = $res->fetch_assoc()['c'];

// ── Ambil Laporan Terbaru (semua user) ──
$recentQuery = "
    SELECT i.id, i.nama_barang, i.type, i.status, i.created_at, u.nama AS pelapor, u.is_banned
    FROM items i
    JOIN users u ON i.user_id = u.id
    ORDER BY i.created_at DESC
    LIMIT 5
";
$recentItems = [];
$res = $conn->query($recentQuery);
if ($res) {
    while ($row = $res->fetch_assoc()) $recentItems[] = $row;
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<section class="section">
  <div class="container">
    
    <div class="page-header">
      <h1 class="page-header__title">Panel Admin</h1>
      <p class="page-header__sub">Selamat datang, <?= htmlspecialchars(explode(' ', $adminName)[0]) ?>. Ringkasan aktivitas TitikNemu Unsoed.</p>
    </div>

    <!-- Tab Navigasi Admin -->
    <?php require_once '../includes/admin_nav.php'; ?>

    <!-- Statistik -->
    <div class="stats-grid">
      <div class="stat-card">
        <span class="stat-card__num"><?= $stats['total_user'] ?></span>
        <span class="stat-card__label">Total Pengguna</span>
      </div>
      <div class="stat-card">
        <span class="stat-card__num text-warning"><?= $stats['hilang_aktif'] ?></span>
        <span class="stat-card__label">Hilang (Aktif)</span>
      </div>
      <div class="stat-card">
        <span class="stat-card__num text-success"><?= $stats['ditemukan_aktif'] ?></span>
        <span class="stat-card__label">Ditemukan (Aktif)</span>
      </div>
      <div class="stat-card">
        <span class="stat-card__num text-muted"><?= $stats['dikembalikan'] ?></span>
        <span class="stat-card__label">Selesai</span>
      </div>
    </div>

    <!-- Laporan Terbaru -->
    <div class="panel">
      <div class="section-header mb-3">
        <h2 class="text-md font-bold">Laporan Terbaru</h2>
        <a href="laporan.php" class="btn btn--secondary btn--sm">
          <span>Lihat Semua</span>
          <svg class="icon icon--sm" aria-hidden="true"><use href="#i-arrow-right"/></svg>
        </a>
      </div>
      
      <?php if (empty($recentItems)): ?>
        <p class="text-muted text-sm">Belum ada laporan.</p>
      <?php else: ?>
        <div class="table-wrap">
          <table class="table table--stack">
            <thead>
              <tr>
                <th>Nama Barang</th>
                <th>Jenis</th>
                <th>Pelapor</th>
                <th>Status</th>
                <th>Tanggal Upload</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentItems as $item): ?>
                <tr>
                  <td data-label="Nama Barang">
                    <a href="../detail.php?id=<?= $item['id'] ?>" target="_blank" class="font-semibold underline">
                      <?= htmlspecialchars($item['nama_barang']) ?>
                    </a>
                  </td>
                  <td data-label="Jenis">
                    <?php if ($item['type'] === 'lost'): ?>
                      <span class="badge badge--lost">Hilang</span>
                    <?php else: ?>
                      <span class="badge badge--found">Ditemukan</span>
                    <?php endif; ?>
                  </td>
                  <td data-label="Pelapor">
                    <?= htmlspecialchars($item['pelapor']) ?>
                    <?php if ($item['is_banned']): ?>
                      <span class="badge badge--banned">Banned</span>
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
                  <td data-label="Tanggal Upload" class="text-muted text-sm">
                    <?= date('d M Y, H:i', strtotime($item['created_at'])) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php require_once '../includes/footer.php'; ?>
