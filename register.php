<?php
/**
 * register.php — Halaman Registrasi
 */

$basePath  = '';
$pageTitle = 'Daftar Akun — TitikNemu Unsoed';

require_once 'config/database.php';

// Redirect jika sudah login
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Ambil daftar fakultas untuk dropdown
$fakultasList = [];
$resFak = $conn->query("SELECT id, nama_fakultas FROM fakultas ORDER BY nama_fakultas ASC");
if ($resFak) {
    while ($row = $resFak->fetch_assoc()) {
        $fakultasList[] = $row;
    }
}

// ── Proses form ──
$errors    = [];
$old       = []; // Simpan input lama agar tidak hilang saat validasi gagal

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil & trim input
    $nama         = trim($_POST['nama'] ?? '');
    $emailPrefix  = trim($_POST['email_prefix'] ?? '');
    $noHp         = trim($_POST['no_hp'] ?? '');
    $fakultasId   = (int)($_POST['fakultas_id'] ?? 0);
    $prodiId      = (int)($_POST['prodi_id'] ?? 0);
    $password     = $_POST['password'] ?? '';
    $passwordConf = $_POST['password_confirm'] ?? '';

    // Simpan old input
    $old = [
        'nama'        => $nama,
        'email_prefix'=> $emailPrefix,
        'no_hp'       => $noHp,
        'fakultas_id' => $fakultasId,
        'prodi_id'    => $prodiId,
    ];

    // ── Validasi ──

    // Nama
    if ($nama === '') {
        $errors['nama'] = 'Nama lengkap wajib diisi.';
    } elseif (mb_strlen($nama) > 100) {
        $errors['nama'] = 'Nama maksimal 100 karakter.';
    }

    // Email prefix
    if ($emailPrefix === '') {
        $errors['email_prefix'] = 'Email wajib diisi.';
    } elseif (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9._-]*$/', $emailPrefix)) {
        $errors['email_prefix'] = 'Format email tidak valid. Gunakan huruf, angka, titik, atau strip.';
    }

    $emailFull = strtolower($emailPrefix) . '@mhs.unsoed.ac.id';

    // Cek email unik
    if (!isset($errors['email_prefix'])) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $emailFull);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['email_prefix'] = 'Email ini sudah terdaftar.';
        }
        $stmt->close();
    }

    // No HP
    if ($noHp === '') {
        $errors['no_hp'] = 'Nomor HP/WhatsApp wajib diisi.';
    } elseif (!preg_match('/^08[0-9]{8,13}$/', $noHp)) {
        $errors['no_hp'] = 'Format nomor HP tidak valid. Contoh: 081234567890';
    }

    // Fakultas
    if ($fakultasId <= 0) {
        $errors['fakultas_id'] = 'Pilih fakultas.';
    }

    // Prodi
    if ($prodiId <= 0) {
        $errors['prodi_id'] = 'Pilih program studi.';
    } else {
        // Pastikan prodi sesuai fakultas
        $stmtP = $conn->prepare("SELECT id FROM prodi WHERE id = ? AND fakultas_id = ?");
        $stmtP->bind_param('ii', $prodiId, $fakultasId);
        $stmtP->execute();
        $stmtP->store_result();
        if ($stmtP->num_rows === 0) {
            $errors['prodi_id'] = 'Program studi tidak sesuai dengan fakultas.';
        }
        $stmtP->close();
    }

    // Password
    if ($password === '') {
        $errors['password'] = 'Password wajib diisi.';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password minimal 6 karakter.';
    }

    // Konfirmasi password
    if ($passwordConf === '') {
        $errors['password_confirm'] = 'Konfirmasi password wajib diisi.';
    } elseif ($password !== $passwordConf) {
        $errors['password_confirm'] = 'Konfirmasi password tidak cocok.';
    }

    // ── Simpan jika valid ──
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (nama, email, no_hp, fakultas_id, prodi_id, password, role, is_banned, created_at)
             VALUES (?, ?, ?, ?, ?, ?, 'user', 0, NOW())"
        );
        $stmt->bind_param('sssiis', $nama, $emailFull, $noHp, $fakultasId, $prodiId, $hashedPassword);

        if ($stmt->execute()) {
            // Redirect ke login dengan pesan sukses
            $_SESSION['flash_success'] = 'Pendaftaran berhasil! Silakan masuk dengan akun baru Anda.';
            header('Location: login.php');
            exit;
        } else {
            $errors['general'] = 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.';
        }
        $stmt->close();
    }
}

// Ambil prodi jika fakultas sudah dipilih (untuk pre-select saat validasi gagal)
$prodiList = [];
if (!empty($old['fakultas_id'])) {
    $stmtProdi = $conn->prepare("SELECT id, jenjang, nama_prodi FROM prodi WHERE fakultas_id = ? ORDER BY jenjang ASC, nama_prodi ASC");
    $stmtProdi->bind_param('i', $old['fakultas_id']);
    $stmtProdi->execute();
    $resProdi = $stmtProdi->get_result();
    while ($row = $resProdi->fetch_assoc()) {
        $prodiList[] = $row;
    }
    $stmtProdi->close();
}

// Mulai output HTML — session sudah dimulai di atas, jadi skip session_start di header
require_once 'includes/header.php';
require_once 'includes/navbar.php';
?>

<section class="section">
  <div class="container">
    <div class="auth-card">
      <div class="auth-card__header">
        <h1 class="auth-card__title">Daftar Akun</h1>
        <p class="auth-card__sub">Buat akun TitikNemu Unsoed untuk melaporkan dan mencari barang.</p>
      </div>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error"><?= htmlspecialchars($errors['general']) ?></div>
      <?php endif; ?>

      <form method="POST" action="register.php" class="auth-form" novalidate>

        <!-- Nama Lengkap -->
        <div class="form-group">
          <label for="nama" class="form-label">
            Nama Lengkap <span class="required" aria-label="wajib">*</span>
          </label>
          <input
            type="text" id="nama" name="nama"
            class="form-input <?= isset($errors['nama']) ? 'form-input--error' : '' ?>"
            value="<?= htmlspecialchars($old['nama'] ?? '') ?>"
            placeholder="Masukkan nama lengkap"
            required
          >
          <?php if (isset($errors['nama'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['nama']) ?></p>
          <?php endif; ?>
        </div>

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
          <?php else: ?>
            <p class="form-hint">Masukkan bagian depan email Unsoed Anda.</p>
          <?php endif; ?>
        </div>

        <!-- No HP -->
        <div class="form-group">
          <label for="no_hp" class="form-label">
            No. HP/WhatsApp <span class="required" aria-label="wajib">*</span>
          </label>
          <input
            type="tel" id="no_hp" name="no_hp"
            class="form-input <?= isset($errors['no_hp']) ? 'form-input--error' : '' ?>"
            value="<?= htmlspecialchars($old['no_hp'] ?? '') ?>"
            placeholder="08xxxxxxxxxx"
            required
          >
          <?php if (isset($errors['no_hp'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['no_hp']) ?></p>
          <?php else: ?>
            <p class="form-hint">Nomor ini akan digunakan untuk tombol "Hubungi via WhatsApp".</p>
          <?php endif; ?>
        </div>

        <!-- Fakultas -->
        <div class="form-group">
          <label for="fakultas_id" class="form-label">
            Fakultas <span class="required" aria-label="wajib">*</span>
          </label>
          <select
            id="fakultas_id" name="fakultas_id"
            class="form-select <?= isset($errors['fakultas_id']) ? 'form-input--error' : '' ?>"
            required
          >
            <option value="">Pilih Fakultas</option>
            <?php foreach ($fakultasList as $fak): ?>
              <option value="<?= $fak['id'] ?>"
                <?= ((int)($old['fakultas_id'] ?? 0) === (int)$fak['id']) ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($fak['nama_fakultas']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['fakultas_id'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['fakultas_id']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Program Studi -->
        <div class="form-group">
          <label for="prodi_id" class="form-label">
            Program Studi <span class="required" aria-label="wajib">*</span>
          </label>
          <select
            id="prodi_id" name="prodi_id"
            class="form-select <?= isset($errors['prodi_id']) ? 'form-input--error' : '' ?>"
            required
          >
            <option value="">Pilih Program Studi</option>
            <?php foreach ($prodiList as $prodi): ?>
              <option value="<?= $prodi['id'] ?>"
                <?= ((int)($old['prodi_id'] ?? 0) === (int)$prodi['id']) ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($prodi['jenjang'] . ' - ' . $prodi['nama_prodi']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['prodi_id'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['prodi_id']) ?></p>
          <?php endif; ?>
          <p class="form-hint prodi-hint" <?= empty($prodiList) && empty($errors['prodi_id']) ? '' : 'style="display:none;"' ?>>
            Pilih fakultas terlebih dahulu.
          </p>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label for="password" class="form-label">
            Password <span class="required" aria-label="wajib">*</span>
          </label>
          <input
            type="password" id="password" name="password"
            class="form-input <?= isset($errors['password']) ? 'form-input--error' : '' ?>"
            placeholder="Minimal 6 karakter"
            autocomplete="new-password"
            required
          >
          <?php if (isset($errors['password'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['password']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Konfirmasi Password -->
        <div class="form-group">
          <label for="password_confirm" class="form-label">
            Konfirmasi Password <span class="required" aria-label="wajib">*</span>
          </label>
          <input
            type="password" id="password_confirm" name="password_confirm"
            class="form-input <?= isset($errors['password_confirm']) ? 'form-input--error' : '' ?>"
            placeholder="Ulangi password"
            autocomplete="new-password"
            required
          >
          <?php if (isset($errors['password_confirm'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['password_confirm']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Submit -->
        <div class="form-group mt-2">
          <button type="submit" class="btn btn--primary btn--block btn--lg">Daftar</button>
        </div>

        <p class="auth-card__footer">
          Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </p>

      </form>
    </div>
  </div>
</section>

<script src="js/fakultas-prodi.js"></script>

<?php require_once 'includes/footer.php'; ?>
