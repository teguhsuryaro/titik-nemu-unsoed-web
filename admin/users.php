<?php
/**
 * admin/users.php — Manajemen Pengguna
 * Refactored: Pure Semantic HTML5, No <style> or Inline Styles, Shared Admin Nav
 */

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/database.php';
$basePath  = '../';
$pageTitle = 'Manajemen Pengguna — TitikNemu Unsoed';
$currentAdminId = (int)$_SESSION['user_id'];

// ── Proses Ban / Unban ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['user_id'])) {
    $targetId = (int)$_POST['user_id'];
    $action   = $_POST['action'];

    if ($targetId !== $currentAdminId) {
        $isBanned = ($action === 'ban') ? 1 : 0;
        $stmtUpdate = $conn->prepare("UPDATE users SET is_banned = ? WHERE id = ? AND role = 'user'");
        $stmtUpdate->bind_param('ii', $isBanned, $targetId);
        
        if ($stmtUpdate->execute()) {
            $_SESSION['flash_msg'] = "Status pengguna berhasil diperbarui.";
        } else {
            $_SESSION['flash_msg'] = "Gagal memperbarui status pengguna.";
        }
        $stmtUpdate->close();
    }
    header('Location: users.php');
    exit;
}

// ── Pagination (10 per halaman) ──
$stmtCount = $conn->prepare("SELECT COUNT(u.id) AS total FROM users u WHERE u.id != ? AND u.role = 'user'");
$stmtCount->bind_param('i', $currentAdminId);
$stmtCount->execute();
$totalData = (int)($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
$stmtCount->close();

$limit      = 10;
$totalPages = max(1, (int)ceil($totalData / $limit));
$page       = min($totalPages, max(1, (int)($_GET['page'] ?? 1)));
$offset     = ($page - 1) * $limit;

// ── Ambil Daftar User ──
$sql = "
    SELECT u.id, u.nama, u.email, u.no_hp, u.is_banned, u.created_at, f.nama_fakultas, p.nama_prodi 
    FROM users u
    JOIN fakultas f ON u.fakultas_id = f.id
    JOIN prodi p ON u.prodi_id = p.id
    WHERE u.id != ? AND u.role = 'user'
    ORDER BY u.created_at DESC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $currentAdminId, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<section class="section">
  <div class="container">
    <div class="page-header">
      <h1 class="page-header__title">Manajemen Pengguna</h1>
      <p class="page-header__sub">Lihat daftar mahasiswa yang terdaftar dan atur status akses mereka.</p>
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

    <div class="table-wrap">
      <table class="table table--stack">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama &amp; Email</th>
            <th>Fakultas &amp; Prodi</th>
            <th>No. HP</th>
            <th>Status</th>
            <th>Tanggal Daftar</th>
            <th class="text-right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr>
              <td colspan="7" class="text-center text-muted py-6">
                Belum ada pengguna terdaftar.
              </td>
            </tr>
          <?php else: ?>
            <?php $no = $offset + 1; foreach ($users as $u): ?>
              <tr>
                <td data-label="No" class="text-muted"><?= $no++ ?></td>
                <td data-label="Nama & Email">
                  <div class="font-semibold text-ink"><?= htmlspecialchars($u['nama']) ?></div>
                  <div class="text-xs text-muted"><?= htmlspecialchars($u['email']) ?></div>
                </td>
                <td data-label="Fakultas & Prodi">
                  <div class="font-semibold"><?= htmlspecialchars($u['nama_fakultas']) ?></div>
                  <div class="text-xs text-muted"><?= htmlspecialchars($u['nama_prodi']) ?></div>
                </td>
                <td data-label="No. HP" class="text-sm">
                  <?= htmlspecialchars($u['no_hp']) ?>
                </td>
                <td data-label="Status">
                  <?php if ($u['is_banned']): ?>
                    <span class="badge badge--banned">Diblokir</span>
                  <?php else: ?>
                    <span class="badge badge--active">Aktif</span>
                  <?php endif; ?>
                </td>
                <td data-label="Tanggal Daftar" class="text-muted text-sm">
                  <?= date('d M Y', strtotime($u['created_at'])) ?>
                </td>
                <td data-label="Aksi" class="text-right">
                  <form action="users.php" method="POST" onsubmit="return confirm('Yakin ingin <?= $u['is_banned'] ? 'mengaktifkan kembali' : 'menonaktifkan (ban)' ?> akun ini?');">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <?php if ($u['is_banned']): ?>
                      <input type="hidden" name="action" value="unban">
                      <button type="submit" class="btn btn--secondary btn--sm">
                        <svg class="icon icon--sm text-success" aria-hidden="true"><use href="#i-check"/></svg>
                        <span>Pulihkan</span>
                      </button>
                    <?php else: ?>
                      <input type="hidden" name="action" value="ban">
                      <button type="submit" class="btn btn--danger-outline btn--sm">
                        <svg class="icon icon--sm" aria-hidden="true"><use href="#i-alert"/></svg>
                        <span>Blokir</span>
                      </button>
                    <?php endif; ?>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php
      $paginationBaseUrl   = 'users.php';
      $paginationAriaLabel = 'Navigasi halaman manajemen pengguna';
      $paginationId        = '';
      require '../includes/pagination.php';
    ?>
  </div>
</section>

<?php require_once '../includes/footer.php'; ?>
