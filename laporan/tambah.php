<?php
/**
 * laporan/tambah.php — Form Buat Laporan Baru
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
  <div class="container">
    
    <div style="max-width: 600px; margin: 0 auto;">
      <div class="page-header">
        <a href="../index.php" class="btn btn--outline btn--sm mb-2" style="display: inline-flex;">&larr; Kembali</a>
        <h1 class="page-header__title">Buat Laporan</h1>
        <p class="page-header__sub">Isi form di bawah untuk melaporkan barang yang hilang atau ditemukan.</p>
      </div>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error mb-2"><?= htmlspecialchars($errors['general']) ?></div>
      <?php endif; ?>

      <form method="POST" action="tambah.php" enctype="multipart/form-data" class="card" style="padding: 1.5rem;">
        
        <!-- Jenis Laporan -->
        <div class="form-group mb-3">
          <label class="form-label" style="font-size: var(--font-size-lg); font-weight: 700; margin-bottom: 1rem;">
            Pilih Jenis Laporan <span class="required">*</span>
          </label>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            
            <label class="type-selector" style="cursor: pointer;">
              <input type="radio" name="type" value="lost" id="type_lost" style="display: none;" <?= $old['type'] === 'lost' ? 'checked' : '' ?>>
              <div class="type-card" style="border: 2px solid var(--border); border-radius: var(--radius-md); padding: 1rem; text-align: center; transition: all 0.2s;">
                <div style="color: var(--brand-yellow); margin-bottom: .5rem;">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <div style="font-weight: 600; color: var(--ink);">Barang Hilang</div>
              </div>
            </label>

            <label class="type-selector" style="cursor: pointer;">
              <input type="radio" name="type" value="found" id="type_found" style="display: none;" <?= $old['type'] === 'found' ? 'checked' : '' ?>>
              <div class="type-card" style="border: 2px solid var(--border); border-radius: var(--radius-md); padding: 1rem; text-align: center; transition: all 0.2s;">
                <div style="color: var(--brand-green); margin-bottom: .5rem;">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                </div>
                <div style="font-weight: 600; color: var(--ink);">Barang Ditemukan</div>
              </div>
            </label>

          </div>
        </div>

        <hr style="border: none; border-top: 1px solid var(--border); margin: 2rem 0;">

        <!-- Nama Barang -->
        <div class="form-group">
          <label for="nama_barang" class="form-label">
            Nama Barang <span class="required">*</span>
          </label>
          <input type="text" id="nama_barang" name="nama_barang" 
            class="form-input <?= isset($errors['nama_barang']) ? 'form-input--error' : '' ?>"
            value="<?= htmlspecialchars($old['nama_barang'] ?? '') ?>"
            placeholder="Contoh: Kunci Motor Honda, Dompet Hitam"
            required>
          <?php if (isset($errors['nama_barang'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['nama_barang']) ?></p>
          <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
          <!-- Kategori -->
          <div class="form-group" style="margin-bottom: 0;">
            <label for="kategori" class="form-label">
              Kategori <span class="required">*</span>
            </label>
            <select id="kategori" name="kategori" class="form-select <?= isset($errors['kategori']) ? 'form-input--error' : '' ?>" required>
              <option value="">Pilih Kategori</option>
              <?php foreach ($kategoriList as $kat): ?>
                <option value="<?= $kat ?>" <?= ($old['kategori'] ?? '') === $kat ? 'selected' : '' ?>><?= $kat ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['kategori'])): ?>
              <p class="form-error"><?= htmlspecialchars($errors['kategori']) ?></p>
            <?php endif; ?>
          </div>

          <!-- Fakultas -->
          <div class="form-group" style="margin-bottom: 0;">
            <label for="fakultas_id" class="form-label" id="label_fakultas">
              Fakultas (Lokasi) <span class="required">*</span>
            </label>
            <select id="fakultas_id" name="fakultas_id" class="form-select <?= isset($errors['fakultas_id']) ? 'form-input--error' : '' ?>" required>
              <option value="">Pilih Fakultas</option>
              <?php foreach ($fakultasList as $fak): ?>
                <option value="<?= $fak['id'] ?>" <?= ((int)($old['fakultas_id'] ?? 0) === (int)$fak['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($fak['nama_fakultas']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['fakultas_id'])): ?>
              <p class="form-error"><?= htmlspecialchars($errors['fakultas_id']) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-group mt-2">
          <label for="lokasi" class="form-label" id="label_lokasi">
            Lokasi Detail
          </label>
          <input type="text" id="lokasi" name="lokasi" 
            class="form-input"
            value="<?= htmlspecialchars($old['lokasi'] ?? '') ?>"
            placeholder="Contoh: Parkiran gedung F1, Depan kantin">
        </div>

        <div class="form-group">
          <label for="tanggal" class="form-label" id="label_tanggal">
            Tanggal Kejadian <span class="required">*</span>
          </label>
          <input type="date" id="tanggal" name="tanggal" 
            class="form-input <?= isset($errors['tanggal']) ? 'form-input--error' : '' ?>"
            value="<?= htmlspecialchars($old['tanggal'] ?? '') ?>"
            max="<?= date('Y-m-d') ?>"
            required>
          <?php if (isset($errors['tanggal'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['tanggal']) ?></p>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label for="deskripsi" class="form-label">Deskripsi Tambahan</label>
          <textarea id="deskripsi" name="deskripsi" class="form-textarea" 
            placeholder="Sebutkan ciri-ciri khusus barang..."><?= htmlspecialchars($old['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="form-group mb-4">
          <label for="foto" class="form-label">Foto Barang (Opsional)</label>
          <input type="file" id="foto" name="foto" class="form-input" accept="image/jpeg,image/png,image/jpg" style="padding: .5rem .75rem; line-height: 1.5;">
          <p class="form-hint">Maksimal 2MB. Format: JPG, JPEG, PNG.</p>
          <?php if (isset($errors['foto'])): ?>
            <p class="form-error"><?= htmlspecialchars($errors['foto']) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--primary btn--block btn--lg">Kirim Laporan</button>
      </form>

    </div>
  </div>
</section>

<!-- CSS Khusus Radio Button & Label Dinamis -->
<style>
  .type-selector input[type="radio"]:checked + .type-card {
    border-color: var(--brand-yellow);
    background-color: var(--brand-yellow-soft);
  }
  #type_found:checked + .type-card {
    border-color: var(--brand-green);
    background-color: var(--brand-green-soft);
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const radioLost = document.getElementById('type_lost');
  const radioFound = document.getElementById('type_found');
  
  const labelFakultas = document.getElementById('label_fakultas');
  const labelLokasi = document.getElementById('label_lokasi');
  const labelTanggal = document.getElementById('label_tanggal');
  
  function updateLabels() {
    if (radioLost.checked) {
      labelFakultas.innerHTML = 'Fakultas (Lokasi kehilangan) <span class="required">*</span>';
      labelLokasi.textContent = 'Lokasi Kehilangan Detail';
      labelTanggal.innerHTML = 'Tanggal Kehilangan <span class="required">*</span>';
    } else if (radioFound.checked) {
      labelFakultas.innerHTML = 'Fakultas (Lokasi ditemukan) <span class="required">*</span>';
      labelLokasi.textContent = 'Lokasi Penemuan Detail';
      labelTanggal.innerHTML = 'Tanggal Ditemukan <span class="required">*</span>';
    }
  }

  radioLost.addEventListener('change', updateLabels);
  radioFound.addEventListener('change', updateLabels);
  
  // Set inisial
  updateLabels();
});
</script>

<?php require_once '../includes/footer.php'; ?>
