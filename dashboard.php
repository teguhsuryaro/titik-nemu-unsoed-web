<?php
/**
 * dashboard.php — Halaman Laporan Saya (Dashboard Pengguna)
 * Refactored: Stackable Mobile Table, Semantic Stats, Sprite Icons, No Inline Styles
 */

session_start();

// Harus login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$basePath  = '';
$pageTitle = 'Laporan Saya — TitikNemu Unsoed';
$userId    = (int)$_SESSION['user_id'];
$userName  = $_SESSION['nama'];

require_once 'config/database.php';

// Ambil sapaan depan
$firstName = explode(' ', $userName)[0];

// ── Laporan Milik User ──
$myItemsQuery = "
    SELECT id, type, nama_barang, status, tanggal, created_at 
    FROM items 
    WHERE user_id = ? 
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($myItemsQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$myItemsResult = $stmt->get_result();

$myItems = [];
$stats = ['total' => 0, 'active_lost' => 0, 'active_found' => 0, 'done' => 0];

while ($row = $myItemsResult->fetch_assoc()) {
    $myItems[] = $row;
    $stats['total']++;
    $isSelesai = ($row['status'] === 'returned' || ($row['type'] === 'lost' && $row['status'] === 'found'));
    if ($isSelesai) {
        $stats['done']++;
    } elseif ($row['type'] === 'lost') {
        $stats['active_lost']++;
    } else {
        $stats['active_found']++;
    }
}
$stmt->close();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<section class="section">
  <div class="container">

    <div class="section-header">
      <div>
        <h1 class="page-header__title">Laporan Saya</h1>
        <p class="page-header__sub">Halo <?= htmlspecialchars($firstName) ?>, pantau dan kelola seluruh laporan barang Anda.</p>
      </div>
      <a href="laporan/tambah.php" class="btn btn--primary">
        <svg class="icon icon--sm" aria-hidden="true"><use href="#i-plus-circle"/></svg>
        <span>Buat Laporan</span>
      </a>
    </div>

    <!-- Flash message -->
    <?php if (isset($_SESSION['flash_msg'])): ?>
      <div class="alert alert--success" role="status">
        <svg class="icon" aria-hidden="true"><use href="#i-check-circle"/></svg>
        <span><?= htmlspecialchars($_SESSION['flash_msg']) ?></span>
      </div>
      <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    <!-- Ringkasan Statistik User -->
    <div class="stats-grid">
      <div class="stat-card">
        <span class="stat-card__num"><?= $stats['total'] ?></span>
        <span class="stat-card__label">Total Laporan</span>
      </div>
      <div class="stat-card">
        <span class="stat-card__num text-warning"><?= $stats['active_lost'] ?></span>
        <span class="stat-card__label">Barang Hilang</span>
      </div>
      <div class="stat-card">
        <span class="stat-card__num text-success"><?= $stats['active_found'] ?></span>
        <span class="stat-card__label">Barang Ditemukan</span>
      </div>
      <div class="stat-card">
        <span class="stat-card__num text-muted"><?= $stats['done'] ?></span>
        <span class="stat-card__label">Selesai</span>
      </div>
    </div>

    <?php if (empty($myItems)): ?>
      <div class="empty-state">
        <div class="empty-state__icon">
          <svg class="icon icon--xl" aria-hidden="true"><use href="#i-list"/></svg>
        </div>
        <h2 class="empty-state__title">Belum ada laporan</h2>
        <p class="empty-state__desc">Anda belum pernah membuat laporan barang hilang maupun penemuan barang.</p>
        <a href="laporan/tambah.php" class="btn btn--primary btn--sm">Buat Laporan Pertama</a>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="table table--stack">
          <thead>
            <tr>
              <th>Nama Barang</th>
              <th>Jenis</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th class="text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($myItems as $item): ?>
              <?php $isSelesai = ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found')); ?>
              <tr>
                <td data-label="Nama Barang">
                  <a href="detail.php?id=<?= $item['id'] ?>" class="font-semibold underline">
                    <?= htmlspecialchars($item['nama_barang']) ?>
                  </a>
                </td>
                <td data-label="Jenis">
                  <?php if ($item['type'] === 'lost'): ?>
                    <span class="badge badge--lost">
                      <svg class="icon icon--sm" aria-hidden="true"><use href="#i-search"/></svg>
                      <span>Hilang</span>
                    </span>
                  <?php else: ?>
                    <span class="badge badge--found">
                      <svg class="icon icon--sm" aria-hidden="true"><use href="#i-gift"/></svg>
                      <span>Ditemukan</span>
                    </span>
                  <?php endif; ?>
                </td>
                <td data-label="Status">
                  <?php if ($isSelesai): ?>
                    <span class="badge badge--done">
                      <svg class="icon icon--sm" aria-hidden="true"><use href="#i-check"/></svg>
                      <span>Selesai</span>
                    </span>
                  <?php elseif ($item['status'] === 'lost'): ?>
                    <span class="text-sm font-semibold text-warning">Masih Dicari</span>
                  <?php else: ?>
                    <span class="text-sm font-semibold text-success">Menunggu Diambil</span>
                  <?php endif; ?>
                </td>
                <td data-label="Tanggal" class="text-muted text-sm">
                  <?= date('d M Y', strtotime($item['tanggal'])) ?>
                </td>
                <td data-label="Aksi" class="text-right">
                  <div class="flex items-center justify-end gap-2">
                    <?php if (!$isSelesai): ?>
                      <form action="laporan/update_status.php" method="POST" onsubmit="return confirm('Tandai laporan ini sebagai selesai/dikembalikan?');">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn--found-soft btn--sm" title="Tandai Selesai">
                          <svg class="icon icon--sm" aria-hidden="true"><use href="#i-check"/></svg>
                          <span>Selesai</span>
                        </button>
                      </form>
                    <?php endif; ?>

                    <a href="laporan/edit.php?id=<?= $item['id'] ?>" class="btn btn--secondary btn--sm" title="Edit Laporan" aria-label="Edit laporan <?= htmlspecialchars($item['nama_barang']) ?>">
                      <svg class="icon icon--sm" aria-hidden="true"><use href="#i-pencil"/></svg>
                    </a>

                    <form action="laporan/hapus.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini secara permanen?');">
                      <input type="hidden" name="id" value="<?= $item['id'] ?>">
                      <button type="submit" class="btn btn--danger-outline btn--sm" title="Hapus Laporan" aria-label="Hapus laporan <?= htmlspecialchars($item['nama_barang']) ?>">
                        <svg class="icon icon--sm" aria-hidden="true"><use href="#i-trash"/></svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
