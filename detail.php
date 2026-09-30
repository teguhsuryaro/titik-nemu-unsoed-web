<?php
/**
 * detail.php — Halaman Detail Barang
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
      <div class="container text-center" style="padding: 4rem 0;">
        <h1 style="font-size: 4rem; color: var(--border); margin-bottom: 1rem;">404</h1>
        <h2 style="font-size: var(--font-size-xl); margin-bottom: 1rem;">Barang tidak ditemukan</h2>
        <p class="text-muted mb-3">Laporan yang Anda cari tidak ada, sudah dihapus, atau disembunyikan oleh sistem.</p>
        <a href="search.php" class="btn btn--primary">Cari Barang Lain</a>
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
?>

<style>
.detail-layout {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  max-width: 900px;
  margin: 0 auto;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  overflow: hidden;
}
.detail-photo {
  width: 100%;
  background: var(--page);
  display: flex;
  align-items: center;
  justify-content: center;
  border-bottom: 1px solid var(--border);
}
.detail-photo img {
  width: 100%;
  max-height: 500px;
  object-fit: cover;
}
.detail-content {
  padding: 1.5rem;
}
.detail-header {
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid var(--border);
}
.detail-title {
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--ink);
  margin-bottom: .5rem;
  line-height: 1.3;
}
.detail-meta-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
  margin-bottom: 2rem;
}
.meta-box {
  display: flex;
  flex-direction: column;
  gap: .25rem;
}
.meta-box__label {
  font-size: var(--font-size-xs);
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 600;
}
.meta-box__val {
  font-size: var(--font-size-base);
  font-weight: 500;
  color: var(--ink);
}
.detail-desc {
  margin-bottom: 2rem;
}
.detail-desc p {
  white-space: pre-wrap;
  color: var(--ink);
  line-height: 1.7;
}
.detail-reporter {
  background: var(--page);
  border-radius: var(--radius-sm);
  padding: 1rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.detail-reporter__avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #E2E7EE;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  color: var(--muted);
  flex-shrink: 0;
}
@media (min-width: 640px) {
  .detail-layout {
    flex-direction: row;
    align-items: flex-start;
  }
  .detail-photo {
    flex: 1;
    border-bottom: none;
    border-right: 1px solid var(--border);
    position: sticky;
    top: 64px; /* offset navbar */
  }
  .detail-content {
    flex: 1;
    padding: 2rem;
  }
  .detail-meta-grid {
    grid-template-columns: 1fr 1fr;
  }
}
</style>

<section class="section">
  <div class="container">
    
    <!-- Flash message update status -->
    <?php if (isset($_SESSION['flash_msg'])): ?>
      <div class="alert alert--success mb-3" style="max-width: 900px; margin-left: auto; margin-right: auto;">
        <?= htmlspecialchars($_SESSION['flash_msg']) ?>
      </div>
      <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    <!-- Breadcrumb back -->
    <div style="max-width: 900px; margin: 0 auto 1rem;">
      <a href="search.php" class="text-muted" style="font-size: var(--font-size-sm); display: inline-flex; align-items: center; gap: .25rem;">
        &larr; Kembali ke Daftar Barang
      </a>
    </div>

    <div class="detail-layout">
      <!-- Sisi Kiri: Foto -->
      <div class="detail-photo">
        <?php if (!empty($item['foto']) && file_exists('uploads/' . $item['foto'])): ?>
          <img src="uploads/<?= htmlspecialchars($item['foto']) ?>" alt="Foto <?= htmlspecialchars($item['nama_barang']) ?>">
        <?php else: ?>
          <div style="padding: 4rem; color: var(--border);">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
          </div>
        <?php endif; ?>
      </div>

      <!-- Sisi Kanan: Informasi -->
      <div class="detail-content">
        
        <div class="detail-header">
          <div style="margin-bottom: 1rem;">
            <?php if ($isReturned): ?>
              <span class="badge badge--returned">Dikembalikan / Selesai</span>
            <?php elseif ($item['type'] === 'lost'): ?>
              <span class="badge badge--lost">Barang Hilang</span>
            <?php else: ?>
              <span class="badge badge--found">Barang Ditemukan</span>
            <?php endif; ?>
          </div>
          <h1 class="detail-title"><?= htmlspecialchars($item['nama_barang']) ?></h1>
          <div class="text-muted" style="font-size: var(--font-size-sm);">
            Dilaporkan pada <?= date('d M Y, H:i', strtotime($item['created_at'])) ?>
          </div>
        </div>

        <div class="detail-meta-grid">
          <div class="meta-box">
            <span class="meta-box__label">Kategori</span>
            <span class="meta-box__val"><?= htmlspecialchars($item['kategori']) ?></span>
          </div>
          <div class="meta-box">
            <span class="meta-box__label">Fakultas</span>
            <span class="meta-box__val"><?= htmlspecialchars($item['nama_fakultas']) ?></span>
          </div>
          <div class="meta-box">
            <span class="meta-box__label"><?= $item['type'] === 'lost' ? 'Tanggal Kehilangan' : 'Tanggal Ditemukan' ?></span>
            <span class="meta-box__val"><?= date('d F Y', strtotime($item['tanggal'])) ?></span>
          </div>
          <div class="meta-box">
            <span class="meta-box__label">Lokasi Detail</span>
            <span class="meta-box__val"><?= !empty($item['lokasi']) ? htmlspecialchars($item['lokasi']) : '-' ?></span>
          </div>
        </div>

        <div class="detail-desc">
          <div class="meta-box__label" style="margin-bottom: .5rem;">Deskripsi / Ciri-ciri Tambahan</div>
          <p><?= !empty($item['deskripsi']) ? htmlspecialchars($item['deskripsi']) : '<em class="text-muted">Tidak ada deskripsi tambahan.</em>' ?></p>
        </div>

        <!-- Info Pelapor -->
        <div class="detail-reporter">
          <div class="detail-reporter__avatar">
            <?= strtoupper(mb_substr($item['pelapor_nama'], 0, 1)) ?>
          </div>
          <div>
            <div style="font-weight: 600; color: var(--ink); line-height: 1.2;">
              <?= htmlspecialchars($item['pelapor_nama']) ?>
              <?php if ($isOwner): ?> <span class="badge badge--returned" style="margin-left: 4px; padding: 2px 6px;">Anda</span> <?php endif; ?>
            </div>
            <div style="font-size: var(--font-size-xs); color: var(--muted);">
              <?= htmlspecialchars($item['jenjang'] . ' - ' . $item['nama_prodi']) ?>
            </div>
          </div>
        </div>

        <!-- Tombol Aksi -->
        <?php if ($isOwner || $isAdmin): ?>
          
          <div style="display: flex; flex-direction: column; gap: .75rem;">
            <!-- Jika milik user (atau admin yang ingin hapus) -->
            <?php if ($isOwner && !$isReturned): ?>
              <form action="laporan/update_status.php" method="POST" style="width:100%;" onsubmit="return confirm('Tandai laporan ini sebagai selesai/dikembalikan?');">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn btn--primary btn--block btn--lg">
                  Tandai Sebagai Selesai
                </button>
              </form>
            <?php endif; ?>

            <div style="display: flex; gap: .5rem;">
              <?php if ($isOwner): ?>
                <a href="laporan/edit.php?id=<?= $item['id'] ?>" class="btn btn--outline" style="flex: 1;">Edit</a>
              <?php endif; ?>

              <form action="laporan/hapus.php" method="POST" style="flex: 1;" onsubmit="return confirm('Yakin ingin menghapus laporan ini secara permanen?');">
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                <button type="submit" class="btn btn--outline" style="width: 100%; color: var(--danger); border-color: var(--danger);">Hapus</button>
              </form>
            </div>
          </div>

        <?php elseif (!$isReturned): ?>
          
          <!-- Tombol WhatsApp untuk pengunjung / user lain -->
          <a href="<?= $waLink ?>" target="_blank" rel="noopener noreferrer" class="btn btn--green btn--block btn--lg">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
            Hubungi via WhatsApp
          </a>
          <p class="text-center text-muted mt-1" style="font-size: var(--font-size-xs);">
            Anda akan diarahkan ke aplikasi WhatsApp untuk menghubungi pelapor.
          </p>

        <?php else: ?>
          
          <div class="alert alert--info text-center" style="margin-bottom: 0;">
            Barang ini sudah dikembalikan ke pemiliknya.
          </div>

        <?php endif; ?>

      </div>
    </div>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
