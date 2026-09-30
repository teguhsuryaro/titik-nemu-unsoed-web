<?php
/**
 * admin/users.php — Manajemen Pengguna
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

// ── Ambil Daftar User ──
$sql = "
    SELECT u.id, u.nama, u.email, u.no_hp, u.is_banned, u.created_at, f.nama_fakultas, p.nama_prodi 
    FROM users u
    JOIN fakultas f ON u.fakultas_id = f.id
    JOIN prodi p ON u.prodi_id = p.id
    WHERE u.id != ? AND u.role = 'user'
    ORDER BY u.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $currentAdminId);
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

<style>
.table--admin tbody tr:nth-child(even) { background-color: var(--page); }
.table--admin tbody tr:hover { background-color: #f1f5f9; }
</style>

<section class="section">
  <div class="container">
    <div class="page-header">
      <a href="dashboard.php" class="btn btn--outline btn--sm mb-2" style="display: inline-flex;">&larr; Kembali ke Dashboard Admin</a>
      <h1 class="page-header__title">Manajemen Pengguna</h1>
      <p class="page-header__sub">Lihat daftar mahasiswa yang terdaftar dan atur status akses mereka.</p>
    </div>

    <?php if (isset($_SESSION['flash_msg'])): ?>
      <div class="alert alert--success mb-3"><?= htmlspecialchars($_SESSION['flash_msg']) ?></div>
      <?php unset($_SESSION['flash_msg']); ?>
    <?php endif; ?>

    <div class="table-responsive" style="background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);">
      <table class="table table--admin">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama & Email</th>
            <th>Fakultas & Prodi</th>
            <th>No. HP</th>
            <th>Status</th>
            <th>Tanggal Daftar</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
            <tr><td colspan="7" class="text-center text-muted" style="padding: 2rem;">Belum ada pengguna terdaftar.</td></tr>
          <?php else: ?>
            <?php $no = 1; foreach ($users as $u): ?>
              <tr>
                <td class="text-muted"><?= $no++ ?></td>
                <td>
                  <div style="font-weight: 600; color: var(--ink);"><?= htmlspecialchars($u['nama']) ?></div>
                  <div style="font-size: var(--font-size-xs); color: var(--muted);"><?= htmlspecialchars($u['email']) ?></div>
                </td>
                <td>
                  <div style="font-weight: 500;"><?= htmlspecialchars($u['nama_fakultas']) ?></div>
                  <div style="font-size: var(--font-size-xs); color: var(--muted);"><?= htmlspecialchars($u['nama_prodi']) ?></div>
                </td>
                <td style="font-size: var(--font-size-sm);">
                  <?= htmlspecialchars($u['no_hp']) ?>
                </td>
                <td>
                  <?php if ($u['is_banned']): ?>
                    <span class="badge" style="background:#fef2f2; color:var(--danger); border: 1px solid #fecaca;">Banned</span>
                  <?php else: ?>
                    <span class="badge" style="background:var(--brand-green-soft); color:#166534;">Aktif</span>
                  <?php endif; ?>
                </td>
                <td class="text-muted" style="font-size: var(--font-size-sm);">
                  <?= date('d M Y', strtotime($u['created_at'])) ?>
                </td>
                <td style="text-align: right;">
                  <form action="users.php" method="POST" onsubmit="return confirm('Yakin ingin <?= $u['is_banned'] ? 'mengaktifkan kembali' : 'menonaktifkan (ban)' ?> akun ini?');">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <?php if ($u['is_banned']): ?>
                      <input type="hidden" name="action" value="unban">
                      <button type="submit" class="btn btn--outline btn--sm">Unban</button>
                    <?php else: ?>
                      <input type="hidden" name="action" value="ban">
                      <button type="submit" class="btn btn--outline btn--sm" style="color: var(--danger); border-color: var(--danger);">Ban</button>
                    <?php endif; ?>
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
