<?php
/**
 * detail.php — Halaman Detail Barang
 * Refactored: Pure Semantic HTML5, No <style> or Inline Styles, Sprite Icons
 */

session_start();
require_once 'config/database.php';

$basePath  = '';
$pageTitle = 'Detail Barang — TitikNemu Unsoed';
$isLoggedIn = isset($_SESSION['user_id']);
$currentUserId = $isLoggedIn ? (int)$_SESSION['user_id'] : 0;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ── Query Detail Laporan ──
$sql = "
    SELECT 
        i.*, 
        u.id AS pelapor_id, 
        u.nama AS pelapor_nama, 
        u.no_hp, 
        f.nama_fakultas, 
        p.jenjang, 
        p.nama_prodi 
    FROM items i
    JOIN users u ON i.user_id = u.id
    JOIN fakultas f ON i.fakultas_id = f.id
    JOIN prodi p ON u.prodi_id = p.id
    WHERE i.id = ? AND u.is_banned = 0
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

require_once 'includes/header.php';
require_once 'includes/navbar.php';

// ── 404 Not Found ──
if (!$item) {
    ?>
    <section class="section">
      <div class="container text-center">
        <div class="empty-state">
          <div class="empty-state__icon">
            <svg class="icon icon--xl" aria-hidden="true"><use href="#i-alert"/></svg>
          </div>
          <h1 class="empty-state__title">Barang tidak ditemukan</h1>
          <p class="empty-state__desc">Laporan yang Anda cari tidak ada, sudah dihapus, atau dinonaktifkan oleh sistem.</p>
          <a href="index.php" class="btn btn--primary">Cari Barang Lain</a>
        </div>
      </div>
    </section>
    <?php
    require_once 'includes/footer.php';
    exit;
}

// ── Logika Status & Kepemilikan ──
$isOwner = ($currentUserId === (int)$item['pelapor_id']);
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$isReturned = ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found'));

$pageTitle = htmlspecialchars($item['nama_barang']) . ' — TitikNemu Unsoed';

// Format nomor WhatsApp (Ubah "08..." menjadi "628...")
$waNumber = preg_replace('/[^0-9]/', '', $item['no_hp']);
if (strpos($waNumber, '08') === 0) {
    $waNumber = '62' . substr($waNumber, 1);
}

// Pesan default WA
$jenis = $item['type'] === 'lost' ? 'kehilangan' : 'menemukan';
$waMessage = rawurlencode("Halo {$item['pelapor_nama']}, saya melihat laporan di TitikNemu Unsoed bahwa Anda {$jenis} barang: *{$item['nama_barang']}*. Apakah kita bisa berdiskusi lebih lanjut?");
$waLink = "https://wa.me/{$waNumber}?text={$waMessage}";

// Mapping ikon kategori
$catIcons = [
    'Kunci'              => 'i-key',
    'Dompet/Tas'         => 'i-wallet',
    'Elektronik'         => 'i-phone',
    'Dokumen'            => 'i-file',
    'Pakaian/Aksesoris'  => 'i-glasses',
    'Alat Tulis'         => 'i-pencil',
    'Lainnya'            => 'i-box'
];
$iconId = $catIcons[$item['kategori'] ?? ''] ?? 'i-box';
?>

<section class="section detail-page">
  <div class="container">
    
    <!-- Flash message update status -->
    <?php if (isset($_SESSION['flash_msg'])): ?>
      <div class="alert alert--success" role="status">
        <svg class="icon" aria-hidden="true"><use href="#i-check-circle"/></svg>
        <span><?= htmlspecialchars($_SESSION['flash_msg']) ?></span>
      </div>
      <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    <!-- Breadcrumb back link -->
    <div class="mb-3">
      <a href="index.php" class="btn btn--secondary btn--sm">
        <svg class="icon icon--sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>
        <span>Kembali ke Beranda</span>
      </a>
    </div>

    <div class="detail-grid">
      <!-- Sisi Kiri: Foto / Placeholder Media -->
      <div class="detail-gallery">
        <?php if (!empty($item['foto']) && file_exists('uploads/' . $item['foto'])): ?>
          <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" 
               alt="Foto barang <?= htmlspecialchars($item['nama_barang']) ?>"
               loading="lazy">
        <?php else: ?>
          <div class="detail-gallery__placeholder">
            <svg class="icon icon--xl" aria-hidden="true"><use href="#<?= $iconId ?>"/></svg>
            <span class="text-sm text-muted">Tidak ada foto barang</span>
          </div>
        <?php endif; ?>
      </div>

      <!-- Sisi Kanan: Informasi Lengkap -->
      <div class="detail-info">
        
        <div class="detail-header">
          <div>
            <?php if ($isReturned): ?>
              <span class="badge badge--done">
                <svg class="icon icon--sm" aria-hidden="true"><use href="#i-check"/></svg>
                <span>Selesai / Dikembalikan</span>
              </span>
            <?php elseif ($item['type'] === 'lost'): ?>
              <span class="badge badge--lost">
                <svg class="icon icon--sm" aria-hidden="true"><use href="#i-search"/></svg>
                <span>Barang Hilang</span>
              </span>
            <?php else: ?>
              <span class="badge badge--found">
                <svg class="icon icon--sm" aria-hidden="true"><use href="#i-gift"/></svg>
                <span>Barang Ditemukan</span>
              </span>
            <?php endif; ?>
          </div>

          <h1 class="detail-title"><?= htmlspecialchars($item['nama_barang']) ?></h1>
        </div>

        <!-- Metadata Grid -->
        <div class="detail-meta-box">
          <div class="meta-field">
            <span class="meta-field__label">Kategori</span>
            <span class="meta-field__val"><?= htmlspecialchars($item['kategori']) ?></span>
          </div>
          <div class="meta-field">
            <span class="meta-field__label">Fakultas</span>
            <span class="meta-field__val"><?= htmlspecialchars($item['nama_fakultas']) ?></span>
          </div>
          <div class="meta-field">
            <span class="meta-field__label"><?= $item['type'] === 'lost' ? 'Tanggal Kehilangan' : 'Tanggal Ditemukan' ?></span>
            <span class="meta-field__val"><?= date('d F Y', strtotime($item['tanggal'])) ?></span>
          </div>
          <div class="meta-field">
            <span class="meta-field__label">Lokasi Detail</span>
            <span class="meta-field__val"><?= !empty($item['lokasi']) ? htmlspecialchars($item['lokasi']) : '-' ?></span>
          </div>
        </div>

        <!-- Deskripsi Barang -->
        <div class="detail-desc">
          <div class="detail-desc__title">Deskripsi &amp; Ciri-ciri Tambahan</div>
          <div class="detail-desc__text"><?= !empty($item['deskripsi']) ? htmlspecialchars($item['deskripsi']) : '<span class="text-muted">Tidak ada deskripsi tambahan.</span>' ?></div>
        </div>

        <!-- Info Pelapor -->
        <div class="reporter-card">
          <div class="reporter-avatar" aria-hidden="true">
            <?= strtoupper(mb_substr($item['pelapor_nama'], 0, 1)) ?>
          </div>
          <div class="reporter-info">
            <div class="reporter-name">
              <?= htmlspecialchars($item['pelapor_nama']) ?>
              <?php if ($isOwner): ?>
                <span class="badge badge--done">Anda</span>
              <?php endif; ?>
            </div>
            <div class="reporter-sub">
              <?= htmlspecialchars($item['jenjang'] . ' - ' . $item['nama_prodi']) ?>
            </div>
          </div>
        </div>

        <!-- Tombol Aksi (Tergantung Role / Kepemilikan) -->
        <div class="detail-actions">
          <?php if ($isOwner || $isAdmin): ?>
            
            <?php if ($isOwner && !$isReturned): ?>
              <form action="laporan/update_status.php" method="POST" class="btn--block" onsubmit="return confirm('Tandai laporan ini sebagai selesai/dikembalikan?');">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn btn--primary btn--lg btn--block">
                  <svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>
                  <span>Tandai Sebagai Selesai</span>
                </button>
              </form>
            <?php endif; ?>

            <div class="flex gap-2 btn--block">
              <?php if ($isOwner): ?>
                <a href="laporan/edit.php?id=<?= $item['id'] ?>" class="btn btn--secondary flex-1">
                  <svg class="icon icon--sm" aria-hidden="true"><use href="#i-pencil"/></svg>
                  <span>Edit Laporan</span>
                </a>
              <?php endif; ?>

              <form action="laporan/hapus.php" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus laporan ini secara permanen? Tindakan ini tidak dapat dibatalkan.');">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn btn--danger-outline btn--block">
                  <svg class="icon icon--sm" aria-hidden="true"><use href="#i-trash"/></svg>
                  <span>Hapus</span>
                </button>
              </form>
            </div>

          <?php elseif (!$isReturned): ?>
            
            <!-- Tombol WhatsApp untuk Pengunjung / Pengguna Lain -->
            <a href="<?= $waLink ?>" target="_blank" rel="noopener noreferrer" class="btn btn--found btn--lg btn--block">
              <svg class="icon" aria-hidden="true"><use href="#i-message"/></svg>
              <span>Hubungi via WhatsApp</span>
            </a>
            <p class="text-xs text-muted text-center mt-1">
              Anda akan diarahkan ke obrolan WhatsApp untuk berdiskusi dengan pelapor.
            </p>

          <?php else: ?>
            
            <div class="alert alert--info text-center" role="status">
              <svg class="icon" aria-hidden="true"><use href="#i-info"/></svg>
              <span>Barang ini sudah dikembalikan kepada pemiliknya.</span>
            </div>

          <?php endif; ?>
        </div>

      </div>
    </div>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
