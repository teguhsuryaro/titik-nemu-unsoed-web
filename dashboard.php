<?php
/**
 * dashboard.php — Dashboard Pengguna
 */

session_start();

// Harus login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$basePath  = '';
$pageTitle = 'Dashboard — TitikNemu Unsoed';
$userId    = (int)$_SESSION['user_id'];
$userName  = $_SESSION['nama'];

require_once 'config/database.php';

// Ambil sapaan depan
$firstName = explode(' ', $userName)[0];

// ── 1. Statistik Keseluruhan (Global, sesuai PRD) ──
$statsQuery = "
    SELECT 
        SUM(CASE WHEN i.type = 'lost' AND i.status != 'returned' THEN 1 ELSE 0 END) AS total_lost,
        SUM(CASE WHEN i.type = 'found' AND i.status != 'returned' THEN 1 ELSE 0 END) AS total_found
    FROM items i
    JOIN users u ON i.user_id = u.id
    WHERE u.is_banned = 0
";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();
$totalLost  = $stats['total_lost'] ?? 0;
$totalFound = $stats['total_found'] ?? 0;


// ── 2. Laporan Milik User ──
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
while ($row = $myItemsResult->fetch_assoc()) {
    $myItems[] = $row;
}
$stmt->close();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<section class="section">
  <div class="container">
    
    <div class="page-header">
      <h1 class="page-header__title">Selamat datang, <?= htmlspecialchars($firstName) ?>!</h1>
      <p class="page-header__sub">Ini adalah ringkasan informasi dan laporan barang Anda.</p>
    </div>

    <!-- Statistik -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card__num" style="color: #92400e;"><?= (int)$totalLost ?></div>
        <div class="stat-card__label">Barang Hilang (Aktif)</div>
      </div>
      <div class="stat-card">
        <div class="stat-card__num" style="color: #166534;"><?= (int)$totalFound ?></div>
        <div class="stat-card__label">Barang Ditemukan (Aktif)</div>
      </div>
    </div>

    <!-- Cek Alert Status -->
    <?php
      $needsUpdate = false;
      foreach ($myItems as $item) {
          $isSelesai = ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found'));
          if (!$isSelesai) {
              $needsUpdate = true;
              break;
          }
      }
    ?>
    <?php if ($needsUpdate && !empty($myItems)): ?>
      <div class="alert alert--warning mb-3">
        <strong>⚠️ Barang kamu sudah ditemukan/kembali?</strong><br>
        Gunakan tombol "Tandai Selesai/Dikembalikan" supaya laporan tidak lagi tampil aktif di daftar pencarian.
      </div>
    <?php endif; ?>

    <!-- Flash message -->
    <?php if (isset($_SESSION['flash_msg'])): ?>
      <div class="alert alert--success mb-3">
        <?= htmlspecialchars($_SESSION['flash_msg']) ?>
      </div>
      <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    <!-- Laporan Saya -->
    <div id="laporan-saya" class="section-header mt-4">
      <h2 class="section-header__title">Laporan Saya</h2>
      <a href="laporan/tambah.php" class="btn btn--primary btn--sm">+ Buat Laporan</a>
    </div>

    <?php if (empty($myItems)): ?>
      <div class="alert alert--info">Anda belum membuat laporan apa pun.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Nama Barang</th>
              <th>Jenis</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th style="text-align: right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($myItems as $item): ?>
              <tr>
                <td style="font-weight: 500;">
                  <a href="detail.php?id=<?= $item['id'] ?>" style="color: inherit; text-decoration: underline; text-decoration-color: var(--border); text-underline-offset: 3px;">
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
                  <?php 
                    $isSelesai = ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found'));
                    if ($isSelesai) {
                        echo '<span class="text-muted" style="font-size:var(--font-size-sm);font-weight:600;">Selesai / Dikembalikan</span>';
                    } elseif ($item['status'] === 'lost') {
                        echo '<span style="color:#92400e;font-size:var(--font-size-sm);font-weight:600;">Masih Dicari</span>';
                    } else {
                        echo '<span style="color:#166534;font-size:var(--font-size-sm);font-weight:600;">Menunggu Diambil</span>';
                    }
                  ?>
                </td>
                <td class="text-muted" style="font-size: var(--font-size-sm);">
                  <?= date('d M Y', strtotime($item['tanggal'])) ?>
                </td>
                <td style="text-align: right;">
                  
                  <?php if (!$isSelesai): ?>
                    <!-- Update status -->
                    <form action="laporan/update_status.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Tandai laporan ini sebagai selesai/dikembalikan?');">
                      <input type="hidden" name="id" value="<?= $item['id'] ?>">
                      <button type="submit" class="btn btn--green-outline btn--sm" title="Tandai Selesai/Dikembalikan">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span class="sr-only">Selesai</span>
                      </button>
                    </form>
                  <?php endif; ?>
                  
                  <!-- Edit -->
                  <a href="laporan/edit.php?id=<?= $item['id'] ?>" class="btn btn--outline btn--sm" title="Edit Laporan" style="padding: .375rem .5rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                  </a>
                  
                  <!-- Hapus -->
                  <form action="laporan/hapus.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus laporan ini secara permanen?');">
                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                    <button type="submit" class="btn btn--outline btn--sm" style="color: var(--danger); border-color: transparent;" title="Hapus Laporan">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                  </form>
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
