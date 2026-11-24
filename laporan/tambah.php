<?php
/**
 * laporan/tambah.php — Form Buat Laporan Baru
 * Refactored: Accessible Type Picker, Semantic Panel, No Inline Styles
 */

session_start();

// Harus login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Buat Laporan — TitikNemu Unsoed';

// Ambil parameter GET type sebagai default form
$defaultType = isset($_GET['type']) && $_GET['type'] === 'found' ? 'found' : 'lost';

// Ambil daftar fakultas
$fakultasList = [];
$resFak = $conn->query("SELECT id, nama_fakultas FROM fakultas ORDER BY nama_fakultas ASC");
if ($resFak) {
    while ($row = $resFak->fetch_assoc()) {
        $fakultasList[] = $row;
    }
}

// Daftar kategori
$kategoriList = ['Kunci', 'Dompet/Tas', 'Elektronik', 'Dokumen', 'Pakaian/Aksesoris', 'Alat Tulis', 'Lainnya'];

$errors = [];
$old    = ['type' => $defaultType];

// ── Proses Submit ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId     = (int)$_SESSION['user_id'];
    $type       = $_POST['type'] ?? 'lost';
    $namaBarang = trim($_POST['nama_barang'] ?? '');
    $kategori   = $_POST['kategori'] ?? '';
    $fakultasId = (int)($_POST['fakultas_id'] ?? 0);
    $lokasi     = trim($_POST['lokasi'] ?? '');
    $tanggal    = $_POST['tanggal'] ?? '';
    $deskripsi  = trim($_POST['deskripsi'] ?? '');

    $old = [
        'type'        => $type,
        'nama_barang' => $namaBarang,
        'kategori'    => $kategori,
        'fakultas_id' => $fakultasId,
        'lokasi'      => $lokasi,
        'tanggal'     => $tanggal,
        'deskripsi'   => $deskripsi
    ];

    // Validasi
    if (!in_array($type, ['lost', 'found'])) {
        $type = 'lost';
    }

    if ($namaBarang === '') {
        $errors['nama_barang'] = 'Nama barang wajib diisi.';
    } elseif (mb_strlen($namaBarang) > 150) {
        $errors['nama_barang'] = 'Nama barang terlalu panjang (maks 150 karakter).';
    }

    if (!in_array($kategori, $kategoriList)) {
        $errors['kategori'] = 'Pilih kategori yang valid.';
    }

    if ($fakultasId <= 0) {
        $errors['fakultas_id'] = 'Fakultas wajib dipilih.';
    }

    if ($tanggal === '') {
        $errors['tanggal'] = 'Tanggal wajib diisi.';
    }

    // Upload Foto (Opsional)
    $fotoName = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['foto'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['foto'] = 'Terjadi kesalahan saat mengunggah foto.';
        } else {
            $maxSize = 2 * 1024 * 1024; // 2MB
            if ($file['size'] > $maxSize) {
                $errors['foto'] = 'Ukuran foto maksimal 2MB.';
            } else {
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mime, $allowedTypes)) {
                    $errors['foto'] = 'Format foto harus JPG, JPEG, atau PNG.';
                } else {
                    // Generate nama file aman
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $fotoName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                    $uploadPath = '../uploads/' . $fotoName;
                    
                    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        $errors['foto'] = 'Gagal menyimpan foto ke server.';
                        $fotoName = null;
                    }
                }
            }
        }
    }

    // Simpan ke DB jika valid
    if (empty($errors)) {
        $status = ($type === 'lost') ? 'lost' : 'found';
        
        $stmt = $conn->prepare("
            INSERT INTO items (user_id, type, nama_barang, kategori, fakultas_id, lokasi, tanggal, deskripsi, foto, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param('isssisssss', $userId, $type, $namaBarang, $kategori, $fakultasId, $lokasi, $tanggal, $deskripsi, $fotoName, $status);
        
        if ($stmt->execute()) {
            $newId = $stmt->insert_id;
            $_SESSION['flash_msg'] = 'Laporan berhasil dibuat!';
            header("Location: ../detail.php?id=" . $newId);
            exit;
        } else {
            $errors['general'] = 'Gagal menyimpan data ke database. Silakan coba lagi.';
            // Hapus foto jika DB gagal
            if ($fotoName && file_exists('../uploads/' . $fotoName)) {
                unlink('../uploads/' . $fotoName);
            }
        }
        $stmt->close();
    }
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<section class="section">
  <div class="container form-wrapper">
    
    <div class="mb-3">
      <a href="../index.php" class="btn btn--secondary btn--sm">
        <svg class="icon icon--sm" aria-hidden="true"><use href="#i-arrow-left"/></svg>
        <span>Kembali ke Beranda</span>
      </a>
    </div>

    <div class="panel">
      <div class="mb-4">
        <h1 class="page-header__title">Buat Laporan Baru</h1>
        <p class="page-header__sub">Isi detail barang yang hilang atau ditemukan di kampus.</p>
      </div>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error" role="alert">
          <svg class="icon" aria-hidden="true"><use href="#i-alert"/></svg>
          <span><?= htmlspecialchars($errors['general']) ?></span>
        </div>
      <?php endif; ?>

      <form method="POST" action="tambah.php" enctype="multipart/form-data" novalidate>
        
        <!-- Pilihan Jenis Laporan (Accessible Radio Cards) -->
        <div class="form-group">
          <span class="form-label" id="label-jenis-laporan">
            <span>Jenis Laporan</span>
            <span class="form-label__req" aria-label="Wajib diisi">*</span>
          </span>

          <div class="type-picker" role="radiogroup" aria-labelledby="label-jenis-laporan">
            <div>
              <input 
                type="radio" 
                name="type" 
                value="lost" 
                id="type_lost" 
                class="type-picker__radio"
                <?= ($old['type'] ?? '') === 'lost' ? 'checked' : '' ?>
              >
              <label for="type_lost" class="type-picker__card type-picker__card--lost">
                <svg class="icon icon--lg text-warning" aria-hidden="true"><use href="#i-search"/></svg>
                <span class="type-picker__title">Barang Hilang</span>
              </label>
            </div>

            <div>
              <input 
                type="radio" 
                name="type" 
                value="found" 
                id="type_found" 
                class="type-picker__radio"
                <?= ($old['type'] ?? '') === 'found' ? 'checked' : '' ?>
              >
              <label for="type_found" class="type-picker__card type-picker__card--found">
                <svg class="icon icon--lg text-info" aria-hidden="true"><use href="#i-gift"/></svg>
                <span class="type-picker__title">Barang Temuan</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Nama Barang -->
        <div class="form-group">
          <label for="nama_barang" class="form-label">
            <span>Nama Barang</span>
            <span class="form-label__req" aria-label="Wajib diisi">*</span>
          </label>
          <input 
            type="text" 
            id="nama_barang" 
            name="nama_barang" 
            class="form-control <?= isset($errors['nama_barang']) ? 'form-control--error' : '' ?>"
            value="<?= htmlspecialchars($old['nama_barang'] ?? '') ?>"
            placeholder="Contoh: Kunci Motor Honda Vario, Dompet Cokelat"
            required
          >
          <?php if (isset($errors['nama_barang'])): ?>
            <p class="form-error" role="alert"><?= htmlspecialchars($errors['nama_barang']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-row-2">
          <!-- Kategori -->
          <div class="form-group">
            <label for="kategori" class="form-label">
              <span>Kategori</span>
              <span class="form-label__req" aria-label="Wajib diisi">*</span>
            </label>
            <select id="kategori" name="kategori" class="form-select <?= isset($errors['kategori']) ? 'form-select--error' : '' ?>" required>
              <option value="">Pilih Kategori</option>
              <?php foreach ($kategoriList as $kat): ?>
                <option value="<?= $kat ?>" <?= ($old['kategori'] ?? '') === $kat ? 'selected' : '' ?>><?= $kat ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['kategori'])): ?>
              <p class="form-error" role="alert"><?= htmlspecialchars($errors['kategori']) ?></p>
            <?php endif; ?>
          </div>

          <!-- Fakultas -->
          <div class="form-group">
            <label for="fakultas_id" class="form-label" id="label_fakultas">
              <span>Fakultas</span>
              <span class="form-label__req" aria-label="Wajib diisi">*</span>
            </label>
            <select id="fakultas_id" name="fakultas_id" class="form-select <?= isset($errors['fakultas_id']) ? 'form-select--error' : '' ?>" required>
              <option value="">Pilih Fakultas</option>
              <?php foreach ($fakultasList as $fak): ?>
                <option value="<?= $fak['id'] ?>" <?= ((int)($old['fakultas_id'] ?? 0) === (int)$fak['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($fak['nama_fakultas']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['fakultas_id'])): ?>
              <p class="form-error" role="alert"><?= htmlspecialchars($errors['fakultas_id']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Lokasi Detail -->
        <div class="form-group">
          <label for="lokasi" class="form-label" id="label_lokasi">
            <span>Lokasi Detail</span>
          </label>
          <input 
            type="text" 
            id="lokasi" 
            name="lokasi" 
            class="form-control"
            value="<?= htmlspecialchars($old['lokasi'] ?? '') ?>"
            placeholder="Contoh: Parkiran Gedung F, Gazebo depan perpus"
          >
        </div>

        <!-- Tanggal -->
        <div class="form-group">
          <label for="tanggal" class="form-label" id="label_tanggal">
            <span>Tanggal Kejadian</span>
            <span class="form-label__req" aria-label="Wajib diisi">*</span>
          </label>
          <input 
            type="date" 
            id="tanggal" 
            name="tanggal" 
            class="form-control <?= isset($errors['tanggal']) ? 'form-control--error' : '' ?>"
            value="<?= htmlspecialchars($old['tanggal'] ?? '') ?>"
            max="<?= date('Y-m-d') ?>"
            required
          >
          <?php if (isset($errors['tanggal'])): ?>
            <p class="form-error" role="alert"><?= htmlspecialchars($errors['tanggal']) ?></p>
          <?php endif; ?>
        </div>

        <!-- Deskripsi -->
        <div class="form-group">
          <label for="deskripsi" class="form-label">
            <span>Deskripsi &amp; Ciri Khusus</span>
          </label>
          <textarea 
            id="deskripsi" 
            name="deskripsi" 
            rows="3"
            class="form-textarea" 
            placeholder="Sebutkan ciri-ciri khusus, gantungan kunci, warna, atau detail lainnya..."
          ><?= htmlspecialchars($old['deskripsi'] ?? '') ?></textarea>
        </div>

        <!-- Upload Foto -->
        <div class="form-group mb-4">
          <label for="foto" class="form-label">
            <span>Foto Barang (Opsional)</span>
          </label>
          <input 
            type="file" 
            id="foto" 
            name="foto" 
            class="form-control" 
            accept="image/jpeg,image/png,image/jpg"
          >
          <p class="form-hint">Maksimal 2MB. Format file yang diterima: JPG, JPEG, PNG.</p>
          <?php if (isset($errors['foto'])): ?>
            <p class="form-error" role="alert"><?= htmlspecialchars($errors['foto']) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--primary btn--block btn--lg">
          <span>Kirim Laporan</span>
          <svg class="icon icon--sm" aria-hidden="true"><use href="#i-arrow-right"/></svg>
        </button>

      </form>
    </div>

  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  'use strict';
  var radioLost = document.getElementById('type_lost');
  var radioFound = document.getElementById('type_found');
  var labelFakultas = document.getElementById('label_fakultas');
  var labelLokasi = document.getElementById('label_lokasi');
  var labelTanggal = document.getElementById('label_tanggal');

  function updateLabels() {
    if (radioLost && radioLost.checked) {
      if (labelFakultas) labelFakultas.innerHTML = '<span>Fakultas (Kehilangan)</span><span class="form-label__req">*</span>';
      if (labelLokasi) labelLokasi.innerHTML = '<span>Lokasi Kehilangan Detail</span>';
      if (labelTanggal) labelTanggal.innerHTML = '<span>Tanggal Kehilangan</span><span class="form-label__req">*</span>';
    } else if (radioFound && radioFound.checked) {
      if (labelFakultas) labelFakultas.innerHTML = '<span>Fakultas (Lokasi Temuan)</span><span class="form-label__req">*</span>';
      if (labelLokasi) labelLokasi.innerHTML = '<span>Lokasi Penemuan Detail</span>';
      if (labelTanggal) labelTanggal.innerHTML = '<span>Tanggal Penemuan</span><span class="form-label__req">*</span>';
    }
  }

  if (radioLost) radioLost.addEventListener('change', updateLabels);
  if (radioFound) radioFound.addEventListener('change', updateLabels);
  updateLabels();
});
</script>

<?php require_once '../includes/footer.php'; ?>
