<?php
/**
 * profile.php — Halaman Profil Pengguna
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
    // Jika data tidak ditemukan (kasus langka), hapus sesi
    header('Location: logout.php');
    exit;
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<section class="section">
  <div class="container">
    <div style="max-width: 600px; margin: 0 auto;">
      
      <div class="page-header text-center">
        <div style="width: 80px; height: 80px; background: var(--brand-yellow); color: var(--ink); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; margin: 0 auto 1rem;">
          <?= strtoupper(mb_substr($user['nama'], 0, 1)) ?>
        </div>
        <h1 class="page-header__title">Profil Saya</h1>
        <p class="page-header__sub">Informasi akun TitikNemu Unsoed Anda.</p>
      </div>

      <div class="card" style="padding: 1.5rem; margin-bottom: 2rem;">
        <div style="display: grid; gap: 1.25rem;">
          
          <div style="border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
            <div class="text-muted" style="font-size: var(--font-size-sm); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: .25rem;">Nama Lengkap</div>
            <div style="font-size: var(--font-size-lg); font-weight: 500; color: var(--ink);"><?= htmlspecialchars($user['nama']) ?></div>
          </div>

          <div style="border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
            <div class="text-muted" style="font-size: var(--font-size-sm); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: .25rem;">Email Unsoed</div>
            <div style="font-size: 1rem; font-weight: 500; color: var(--ink);"><?= htmlspecialchars($user['email']) ?></div>
          </div>

          <div style="border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
            <div class="text-muted" style="font-size: var(--font-size-sm); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: .25rem;">Nomor HP / WhatsApp</div>
            <div style="font-size: 1rem; font-weight: 500; color: var(--ink);"><?= htmlspecialchars($user['no_hp']) ?></div>
          </div>

          <div style="border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
            <div class="text-muted" style="font-size: var(--font-size-sm); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: .25rem;">Akademik</div>
            <div style="font-size: 1rem; font-weight: 500; color: var(--ink);">
              <?= htmlspecialchars($user['nama_fakultas']) ?><br>
              <span class="text-muted" style="font-size: var(--font-size-sm);"><?= htmlspecialchars($user['jenjang'] . ' - ' . $user['nama_prodi']) ?></span>
            </div>
          </div>

          <div>
            <div class="text-muted" style="font-size: var(--font-size-sm); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: .25rem;">Bergabung Sejak</div>
            <div style="font-size: 1rem; font-weight: 500; color: var(--ink);"><?= date('d F Y', strtotime($user['created_at'])) ?></div>
          </div>

        </div>
      </div>

      <div class="text-center" style="margin-top: 1.5rem;">
        <a href="logout.php" class="btn btn--danger btn--block btn--lg">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-right: 8px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
          Keluar dari Akun
        </a>
      </div>

    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
