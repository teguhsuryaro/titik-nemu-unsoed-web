<?php
/**
 * laporan/edit.php — Edit Laporan
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

$basePath  = '../';
$pageTitle = 'Edit Laporan — TitikNemu Unsoed';
$currentUserId = (int)$_SESSION['user_id'];

// Ambil ID dari GET (atau POST saat submit)
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($id <= 0) {
    header('Location: ../dashboard.php');
    exit;
}

// ── Cek Kepemilikan ──
$stmt = $conn->prepare("SELECT * FROM items WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$item) {
    $_SESSION['flash_msg'] = 'Laporan tidak ditemukan.';
    header('Location: ../dashboard.php');
    exit;
}

if ((int)$item['user_id'] !== $currentUserId) {
    $_SESSION['flash_msg'] = 'Akses ditolak. Anda hanya dapat mengedit laporan Anda sendiri.';
    header('Location: ../dashboard.php');
    exit;
}

// Ambil data referensi
$fakultasList = [];
$resFak = $conn->query("SELECT id, nama_fakultas FROM fakultas ORDER BY nama_fakultas ASC");
while ($row = $resFak->fetch_assoc()) {
    $fakultasList[] = $row;
}
$kategoriList = ['Kunci', 'Dompet/Tas', 'Elektronik', 'Dokumen', 'Pakaian/Aksesoris', 'Alat Tulis', 'Lainnya'];

$errors = [];
$old = [
    'nama_barang' => $item['nama_barang'],
    'kategori'    => $item['kategori'],
    'fakultas_id' => $item['fakultas_id'],
    'lokasi'      => $item['lokasi'],
    'tanggal'     => $item['tanggal'],
    'deskripsi'   => $item['deskripsi']
];

// ── Proses Submit ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaBarang = trim($_POST['nama_barang'] ?? '');
    $kategori   = $_POST['kategori'] ?? '';
    $fakultasId = (int)($_POST['fakultas_id'] ?? 0);
    $lokasi     = trim($_POST['lokasi'] ?? '');
    $tanggal    = $_POST['tanggal'] ?? '';
    $deskripsi  = trim($_POST['deskripsi'] ?? '');

    $old = [
        'nama_barang' => $namaBarang,
        'kategori'    => $kategori,
        'fakultas_id' => $fakultasId,
        'lokasi'      => $lokasi,
        'tanggal'     => $tanggal,
        'deskripsi'   => $deskripsi
    ];

    // Validasi
    if ($namaBarang === '') $errors['nama_barang'] = 'Nama barang wajib diisi.';
    if (!in_array($kategori, $kategoriList)) $errors['kategori'] = 'Pilih kategori yang valid.';
    if ($fakultasId <= 0) $errors['fakultas_id'] = 'Fakultas wajib dipilih.';
    if ($tanggal === '') $errors['tanggal'] = 'Tanggal wajib diisi.';

    // Foto
    $fotoName = $item['foto']; // Default foto lama
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['foto'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['foto'] = 'Terjadi kesalahan saat mengunggah foto.';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors['foto'] = 'Ukuran foto maksimal 2MB.';
        } else {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedTypes)) {
                $errors['foto'] = 'Format foto harus JPG, JPEG, atau PNG.';
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFotoName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $uploadPath = '../uploads/' . $newFotoName;
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Hapus foto lama jika ada
                    if (!empty($item['foto']) && file_exists('../uploads/' . $item['foto'])) {
                        unlink('../uploads/' . $item['foto']);
                    }
                    $fotoName = $newFotoName;
                } else {
                    $errors['foto'] = 'Gagal menyimpan foto ke server.';
                }
            }
        }
    }

    if (empty($errors)) {
        $stmtUpdate = $conn->prepare("
            UPDATE items 
            SET nama_barang=?, kategori=?, fakultas_id=?, lokasi=?, tanggal=?, deskripsi=?, foto=?
            WHERE id=?
        ");
        $stmtUpdate->bind_param('ssissssi', $namaBarang, $kategori, $fakultasId, $lokasi, $tanggal, $deskripsi, $fotoName, $id);
        
        if ($stmtUpdate->execute()) {
            $_SESSION['flash_msg'] = 'Laporan berhasil diperbarui.';
            header("Location: ../detail.php?id=" . $id);
            exit;
        } else {
            $errors['general'] = 'Gagal menyimpan pembaruan ke database.';
        }
        $stmtUpdate->close();
    }
}

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<section class="section">
  <div class="container">
    <div style="max-width: 600px; margin: 0 auto;">
      
      <div class="page-header">
        <a href="../detail.php?id=<?= $id ?>" class="btn btn--outline btn--sm mb-2" style="display: inline-flex;">&larr; Batal</a>
        <h1 class="page-header__title">Edit Laporan</h1>
        <p class="page-header__sub">Perbarui informasi mengenai barang <?= $item['type'] === 'lost' ? 'hilang' : 'ditemukan' ?> Anda.</p>
      </div>

      <?php if (!empty($errors['general'])): ?>
        <div class="alert alert--error mb-2"><?= htmlspecialchars($errors['general']) ?></div>
      <?php endif; ?>

      <form method="POST" action="edit.php?id=<?= $id ?>" enctype="multipart/form-data" class="card" style="padding: 1.5rem;">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="form-group">
          <label for="nama_barang" class="form-label">Nama Barang <span class="required">*</span></label>
          <input type="text" id="nama_barang" name="nama_barang" 
            class="form-input <?= isset($errors['nama_barang']) ? 'form-input--error' : '' ?>"
            value="<?= htmlspecialchars($old['nama_barang']) ?>" required>
          <?php if (isset($errors['nama_barang'])): ?><p class="form-error"><?= htmlspecialchars($errors['nama_barang']) ?></p><?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
          <div class="form-group" style="margin-bottom: 0;">
            <label for="kategori" class="form-label">Kategori <span class="required">*</span></label>
            <select id="kategori" name="kategori" class="form-select <?= isset($errors['kategori']) ? 'form-input--error' : '' ?>" required>
              <?php foreach ($kategoriList as $kat): ?>
                <option value="<?= $kat ?>" <?= $old['kategori'] === $kat ? 'selected' : '' ?>><?= $kat ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['kategori'])): ?><p class="form-error"><?= htmlspecialchars($errors['kategori']) ?></p><?php endif; ?>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label for="fakultas_id" class="form-label">Fakultas (Lokasi) <span class="required">*</span></label>
            <select id="fakultas_id" name="fakultas_id" class="form-select <?= isset($errors['fakultas_id']) ? 'form-input--error' : '' ?>" required>
              <?php foreach ($fakultasList as $fak): ?>
                <option value="<?= $fak['id'] ?>" <?= (int)$old['fakultas_id'] === (int)$fak['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($fak['nama_fakultas']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['fakultas_id'])): ?><p class="form-error"><?= htmlspecialchars($errors['fakultas_id']) ?></p><?php endif; ?>
          </div>
        </div>

        <div class="form-group mt-2">
          <label for="lokasi" class="form-label">Lokasi Detail</label>
          <input type="text" id="lokasi" name="lokasi" class="form-input" value="<?= htmlspecialchars($old['lokasi']) ?>">
        </div>

        <div class="form-group">
          <label for="tanggal" class="form-label">Tanggal Kejadian <span class="required">*</span></label>
          <input type="date" id="tanggal" name="tanggal" 
            class="form-input <?= isset($errors['tanggal']) ? 'form-input--error' : '' ?>"
            value="<?= htmlspecialchars($old['tanggal']) ?>" max="<?= date('Y-m-d') ?>" required>
          <?php if (isset($errors['tanggal'])): ?><p class="form-error"><?= htmlspecialchars($errors['tanggal']) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
          <label for="deskripsi" class="form-label">Deskripsi Tambahan</label>
          <textarea id="deskripsi" name="deskripsi" class="form-textarea"><?= htmlspecialchars($old['deskripsi']) ?></textarea>
        </div>

        <div class="form-group mb-4">
          <label for="foto" class="form-label">Ganti Foto Barang (Opsional)</label>
          <?php if (!empty($item['foto'])): ?>
            <div style="margin-bottom: .5rem; display: flex; align-items: center; gap: 1rem;">
              <img src="../uploads/<?= htmlspecialchars($item['foto']) ?>" style="width: 64px; height: 64px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
              <span class="text-muted" style="font-size: var(--font-size-xs);">Foto saat ini. Upload file baru untuk mengganti.</span>
            </div>
          <?php endif; ?>
          <input type="file" id="foto" name="foto" class="form-input" accept="image/jpeg,image/png,image/jpg" style="padding: .5rem .75rem; line-height: 1.5;">
          <p class="form-hint">Maksimal 2MB. Format: JPG, JPEG, PNG.</p>
          <?php if (isset($errors['foto'])): ?><p class="form-error"><?= htmlspecialchars($errors['foto']) ?></p><?php endif; ?>
        </div>

        <button type="submit" class="btn btn--primary btn--block btn--lg">Simpan Perubahan</button>
      </form>
    </div>
  </div>
</section>

<?php require_once '../includes/footer.php'; ?>
