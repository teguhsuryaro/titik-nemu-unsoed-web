<?php
/**
 * profile.php — Halaman Profil Pengguna
 * Refactored: Pure Semantic HTML5, No Inline Styles, Sprite Icons
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$basePath  = '';
$pageTitle = 'Profil Saya — TitikNemu Unsoed';
$userId    = (int)$_SESSION['user_id'];

// Ambil data lengkap pengguna
$sql = "
    SELECT u.nama, u.email, u.no_hp, u.created_at, f.nama_fakultas, p.jenjang, p.nama_prodi 
    FROM users u
    JOIN fakultas f ON u.fakultas_id = f.id
    JOIN prodi p ON u.prodi_id = p.id
    WHERE u.id = ? LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: logout.php');
    exit;
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<section class="section">
  <div class="container form-wrapper">
    
    <div class="page-header text-center">
      <div class="profile-avatar" aria-hidden="true">
        <?= strtoupper(mb_substr($user['nama'], 0, 1)) ?>
      </div>
      <h1 class="page-header__title"><?= htmlspecialchars($user['nama']) ?></h1>
      <p class="page-header__sub">Akun Mahasiswa Universitas Jenderal Soedirman</p>
    </div>

    <div class="panel mb-4">
      <div class="profile-field">
        <span class="profile-field__label">Nama Lengkap</span>
        <span class="profile-field__val"><?= htmlspecialchars($user['nama']) ?></span>
      </div>

      <div class="profile-field">
        <span class="profile-field__label">Email Kampus</span>
        <span class="profile-field__val"><?= htmlspecialchars($user['email']) ?></span>
      </div>

      <div class="profile-field">
        <span class="profile-field__label">No. HP / WhatsApp</span>
        <span class="profile-field__val"><?= htmlspecialchars($user['no_hp']) ?></span>
      </div>

      <div class="profile-field">
        <span class="profile-field__label">Akademik</span>
        <span class="profile-field__val">
          <?= htmlspecialchars($user['nama_fakultas']) ?><br>
          <span class="text-sm text-muted"><?= htmlspecialchars($user['jenjang'] . ' - ' . $user['nama_prodi']) ?></span>
        </span>
      </div>

      <div class="profile-field">
        <span class="profile-field__label">Bergabung Sejak</span>
        <span class="profile-field__val"><?= date('d F Y', strtotime($user['created_at'])) ?></span>
      </div>
    </div>

    <div class="text-center">
      <a href="logout.php" class="btn btn--danger-outline btn--lg">
        <svg class="icon" aria-hidden="true"><use href="#i-log-out"/></svg>
        <span>Keluar dari Akun</span>
      </a>
    </div>

  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
