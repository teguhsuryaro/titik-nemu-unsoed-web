<?php
/**
 * admin/dashboard.php — Dashboard Admin
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

<style>
/* Style tambahan admin */
.admin-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 1.5rem; }
.admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.table--admin tbody tr:nth-child(even) { background-color: var(--page); }
.table--admin tbody tr:hover { background-color: #f1f5f9; }
</style>

<section class="section">
  <div class="container">
    <div class="page-header">
      <h1 class="page-header__title">Panel Admin</h1>
      <p class="page-header__sub">Selamat datang, <?= htmlspecialchars(explode(' ', $adminName)[0]) ?>. Ringkasan aktivitas TitikNemu Unsoed.</p>
    </div>

    <!-- Quick Links -->
    <div style="display: flex; gap: .5rem; margin-bottom: 2rem;">
      <a href="users.php" class="btn btn--outline">Manajemen Pengguna</a>
      <a href="laporan.php" class="btn btn--outline">Manajemen Laporan</a>
    </div>

    <!-- Statistik -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card__num" style="color: var(--ink);"><?= $stats['total_user'] ?></div>
        <div class="stat-card__label">Total Pengguna</div>
      </div>
      <div class="stat-card">
        <div class="stat-card__num" style="color: var(--focus);"><?= $stats['total_laporan'] ?></div>
        <div class="stat-card__label">Total Laporan</div>
      </div>
      <div class="stat-card">
        <div class="stat-card__num" style="color: #92400e;"><?= $stats['hilang_aktif'] ?></div>
        <div class="stat-card__label">Hilang (Aktif)</div>
      </div>
      <div class="stat-card">
        <div class="stat-card__num" style="color: #166534;"><?= $stats['ditemukan_aktif'] ?></div>
        <div class="stat-card__label">Ditemukan (Aktif)</div>
      </div>
      <div class="stat-card">
        <div class="stat-card__num" style="color: var(--muted);"><?= $stats['dikembalikan'] ?></div>
        <div class="stat-card__label">Selesai/Dikembalikan</div>
      </div>
    </div>

    <!-- Laporan Terbaru -->
    <div class="admin-card mt-4">
      <div class="admin-header">
        <h2 style="font-size: var(--font-size-lg); font-weight: 600;">Laporan Terbaru</h2>
        <a href="laporan.php" class="btn btn--outline btn--sm">Lihat Semua</a>
      </div>
      
      <?php if (empty($recentItems)): ?>
        <p class="text-muted">Belum ada laporan.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table--admin">
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
                  <td style="font-weight: 500;">
                    <a href="../detail.php?id=<?= $item['id'] ?>" target="_blank" style="color: inherit; text-decoration: underline; text-underline-offset: 3px;">
                      <?= htmlspecialchars($item['nama_barang']) ?>
                    </a>
                  </td>
                  <td>
                    <?php if ($item['type'] === 'lost'): ?>
                      <span class="badge badge--lost">Hilang</span>
                    <?php else: ?>
                      <span class="badge badge--found">Ditemukan</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= htmlspecialchars($item['pelapor']) ?>
                    <?php if ($item['is_banned']): ?>
                      <span class="badge badge--lost" style="font-size: 10px; padding: 2px 4px; margin-left: 4px;">Banned</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php 
                      $isSelesai = ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found'));
                      if ($isSelesai) echo '<span class="text-muted">Selesai</span>';
                      else echo '<span style="color:var(--brand-green);">Aktif</span>';
                    ?>
                  </td>
                  <td class="text-muted" style="font-size: var(--font-size-sm);">
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
