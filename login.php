<?php
/**
 * login.php — Halaman Login
 */

$basePath  = '';
$pageTitle = 'Masuk — TitikNemu Unsoed';

require_once 'config/database.php';

// Redirect jika sudah login
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Ambil flash message dari redirect (misal setelah register berhasil)
$flashSuccess = '';
if (!empty($_SESSION['flash_success'])) {
    $flashSuccess = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

// ── Proses form ──
$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailPrefix = trim($_POST['email_prefix'] ?? '');
    $password    = $_POST['password'] ?? '';

    $old['email_prefix'] = $emailPrefix;

    // Validasi dasar
    if ($emailPrefix === '') {
        $errors['email_prefix'] = 'Email wajib diisi.';
    }

    if ($password === '') {
        $errors['password'] = 'Password wajib diisi.';
    }

    // Cek kredensial
    if (empty($errors)) {
        // Hapus domain jika user terlanjur mengetiknya
        $emailPrefix = str_replace('@mhs.unsoed.ac.id', '', strtolower($emailPrefix));
        $emailFull = $emailPrefix . '@mhs.unsoed.ac.id';

        $stmt = $conn->prepare(
            "SELECT id, nama, email, password, role, is_banned FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param('s', $emailFull);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if (!$user || !password_verify($password, $user['password'])) {
            // Pesan generik — jangan bocorkan apakah email terdaftar atau bukan
            $errors['general'] = 'Email atau password salah.';
        } elseif ((int)$user['is_banned'] === 1) {
            $errors['general'] = 'Akun Anda telah dinonaktifkan. Hubungi admin untuk informasi lebih lanjut.';
        } else {
            // Login berhasil — buat session
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);

            header('Location: dashboard.php');
            exit;
        }
    }
}

require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<section class="section">
  <div class="container">
    <div class="auth-card">
      <div class="auth-card__header">
        <h1 class="auth-card__title">Masuk</h1>
        <p class="auth-card__sub">Masuk ke akun TitikNemu Unsoed Anda.</p>
      </div>

      <?php if ($flashSuccess): ?>
        <div class="alert alert--success"><?= htmlspecialchars($flashSuccess) ?></div>
      <?php endif; ?>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error"><?= htmlspecialchars($errors['general']) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php" class="auth-form" novalidate>

        <!-- Email -->
        <div class="form-group">
          <label for="email_prefix" class="form-label">
            Email Unsoed <span class="required" aria-label="wajib">*</span>
          </label>
          <div class="input-group">
            <input
              type="text" id="email_prefix" name="email_prefix"
              class="form-input <?= isset($errors['email_prefix']) ? 'form-input--error' : '' ?>"
              value="<?= htmlspecialchars($old['email_prefix'] ?? '') ?>"
              placeholder="nama.lengkap"
              autocapitalize="none"
              autocomplete="username"
              required
            >
            <span class="input-group__append" aria-label="domain email">@mhs.unsoed.ac.id</span>
          </div>
          <?php if (isset($errors['email_prefix'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['email_prefix']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password" class="form-label">
            Password <span class="required" aria-label="wajib">*</span>
          </label>
          <input
            type="password" id="password" name="password"
            class="form-input <?= isset($errors['password']) ? 'form-input--error' : '' ?>"
            placeholder="Masukkan password"
            autocomplete="current-password"
            required
          >
          <?php if (isset($errors['password'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['password']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Submit -->
        <div class="form-group mt-2">
          <button type="submit" class="btn btn--primary btn--block btn--lg">Masuk</button>
        </div>

        <p class="auth-card__footer">
          Belum punya akun? <a href="register.php">Daftar di sini</a>
        </p>

      </form>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
