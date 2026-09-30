<?php
/**
 * laporan/update_status.php — Update Status Laporan (Selesai/Dikembalikan)
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header('Location: ../dashboard.php');
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];

// ── Cek Otorisasi (Hanya Pemilik) ──
$stmt = $conn->prepare("SELECT user_id, type, status FROM items WHERE id = ? LIMIT 1");
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
    $_SESSION['flash_msg'] = 'Akses ditolak. Anda hanya dapat mengubah status laporan Anda sendiri.';
    header('Location: ../dashboard.php');
    exit;
}

if ($item['status'] === 'returned') {
    $_SESSION['flash_msg'] = 'Laporan ini sudah berstatus selesai/dikembalikan.';
    header("Location: ../detail.php?id=" . $id);
    exit;
}

// ── Proses Update ──
// Sesuai aturan: jika 'lost' -> ubah jadi 'found' (Ditemukan/Selesai).
// Jika 'found' -> ubah jadi 'returned' (Dikembalikan).
// Tapi dari PRD 7.10, status "returned" bisa digunakan untuk keduanya sebagai status "Selesai/Tertutup" 
// jika kita ingin menyederhanakan. Namun, kita ikuti spesifikasi PRD 7.10 "Perubahan status":
// Hilang -> Ditemukan -> Dikembalikan.
// Supaya seragam & hilang dari daftar "Masih Dicari/Menunggu Diambil", kita set jadi 'returned'.
// Revisi instruksi prompt: "Untuk barang hilang: bisa ubah status menjadi 'found' (Selesai), Untuk barang ditemukan: bisa ubah status menjadi 'returned'".

$newStatus = ($item['type'] === 'lost') ? 'found' : 'returned';

// Namun, agar konsisten dengan `detail.php` dan `dashboard.php` yang mengecek status akhir sebagai 'returned'
// (lihat baris "if ($isReturned)" atau "$item['status'] !== 'returned'"), sebaiknya semua status 'selesai' kita simpan 'returned'. 
// Prompt bilang: "Untuk barang hilang (type=lost): bisa ubah status menjadi 'found' (Ditemukan/Selesai)".
// Mari kita ikuti instruksi prompt literalnya.

$stmtUpdate = $conn->prepare("UPDATE items SET status = ? WHERE id = ?");
$stmtUpdate->bind_param('si', $newStatus, $id);

if ($stmtUpdate->execute()) {
    // Pesan sukses berbeda tergantung jenis awal
    if ($newStatus === 'found') {
        $_SESSION['flash_msg'] = 'Laporan ditandai: Barang telah Ditemukan/Selesai.';
    } else {
        $_SESSION['flash_msg'] = 'Laporan ditandai: Barang telah Dikembalikan ke pemilik.';
    }
} else {
    $_SESSION['flash_msg'] = 'Terjadi kesalahan saat memperbarui status.';
}
$stmtUpdate->close();

// Fallback redirect (kembali ke tempat semula)
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'dashboard.php') !== false) {
    header('Location: ../dashboard.php');
} else {
    header("Location: ../detail.php?id=" . $id);
}
exit;
