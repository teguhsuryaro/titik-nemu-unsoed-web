<?php
/**
 * dashboard.php — Halaman Laporan Saya (Dashboard Pengguna)
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
while ($row = $myItemsResult->fetch_assoc()) {
    $myItems[] = $row;
}
$stmt->close();

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<section class="section">
  <div class="container">

    <div class="section-header" style="margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h1 class="page-header__title" style="margin-bottom: .25rem;">Laporan Saya</h1>
        <p class="page-header__sub" style="margin-bottom: 0;">Halo <?= htmlspecialchars($firstName) ?>, kelola daftar barang yang pernah Anda laporkan.</p>
      </div>
      <a href="laporan/tambah.php" class="btn btn--primary">+ Buat Laporan</a>
    </div>

    <!-- Flash message -->
    <?php if (isset($_SESSION['flash_msg'])): ?>
      <div class="alert alert--success mb-3">
        <?= htmlspecialchars($_SESSION['flash_msg']) ?>
      </div>
      <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    <?php if (empty($myItems)): ?>
      <div class="alert" style="background: var(--surface); border: 1px dashed var(--border); text-align: center; padding: 3rem 1rem;">
        <p class="text-muted" style="margin-bottom: 1rem;">Anda belum membuat laporan apa pun.</p>
        <a href="laporan/tambah.php" class="btn btn--primary">Buat Laporan Pertama</a>
      </div>
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
              <?php $isSelesai = ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found')); ?>
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
                  <?php if ($isSelesai): ?>
                    <span class="badge badge--returned">Selesai</span>
                  <?php elseif ($item['status'] === 'lost'): ?>
                    <span style="color:#92400e;font-size:var(--font-size-sm);font-weight:600;">Masih Dicari</span>
                  <?php else: ?>
                    <span style="color:#166534;font-size:var(--font-size-sm);font-weight:600;">Menunggu Diambil</span>
                  <?php endif; ?>
                </td>
                <td class="text-muted" style="font-size: var(--font-size-sm); white-space: nowrap;">
                  <?= date('d M Y', strtotime($item['tanggal'])) ?>
                </td>
                <td style="text-align: right; white-space: nowrap;">
                  <div style="display: inline-flex; align-items: center; gap: .5rem;">
                    <?php if (!$isSelesai): ?>
                      <form action="laporan/update_status.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Tandai laporan ini sebagai selesai?');">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn--green-outline btn--sm" title="Tandai Selesai">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                          Selesai
                        </button>
                      </form>
                    <?php endif; ?>

                    <a href="laporan/edit.php?id=<?= $item['id'] ?>" class="btn btn--outline btn--sm" title="Edit Laporan" style="padding: .375rem .5rem;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </a>

                    <form action="laporan/hapus.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menghapus laporan ini secara permanen?');">
                      <input type="hidden" name="id" value="<?= $item['id'] ?>">
                      <button type="submit" class="btn btn--outline btn--sm" style="color: var(--danger); border-color: transparent; padding: .375rem .5rem;" title="Hapus Laporan">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
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
