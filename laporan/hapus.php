<?php
/**
 * laporan/hapus.php — Hapus Laporan
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Tolak GET, redirect ke dashboard
    header('Location: ../dashboard.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header('Location: ../dashboard.php');
    exit;
}

$currentUserId = (int)$_SESSION['user_id'];
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// ── Cek Otorisasi (Pemilik atau Admin) ──
$stmt = $conn->prepare("SELECT user_id, type, status, foto FROM items WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    $_SESSION['flash_msg'] = 'Laporan tidak ditemukan.';
    $_SESSION['flash_type'] = 'error';
    header('Location: ../dashboard.php');
    exit;
}

if ((int)$item['user_id'] !== $currentUserId && !$isAdmin) {
    $_SESSION['flash_msg'] = 'Akses ditolak. Anda tidak berhak menghapus laporan ini.';
    $_SESSION['flash_type'] = 'error';
    header('Location: ../dashboard.php');
    exit;
}

$isSelesai = ($item['status'] === 'returned' || ($item['type'] === 'lost' && $item['status'] === 'found'));
if ($isSelesai && !$isAdmin) {
    $_SESSION['flash_msg'] = 'Laporan yang sudah selesai diarsipkan dan tidak dapat dihapus.';
    $_SESSION['flash_type'] = 'info';
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'detail.php') !== false) {
        header('Location: ../detail.php?id=' . $id);
    } else {
        header('Location: ../dashboard.php');
    }
    exit;
}

// ── Proses Hapus ──
$fotoPath = '../uploads/' . $item['foto'];
if (!empty($item['foto']) && file_exists($fotoPath)) {
    unlink($fotoPath); // Hapus foto fisik
}

$stmtDel = $conn->prepare("DELETE FROM items WHERE id = ?");
$stmtDel->bind_param('i', $id);

if ($stmtDel->execute()) {
    $_SESSION['flash_msg'] = 'Laporan berhasil dihapus secara permanen.';
} else {
    $_SESSION['flash_msg'] = 'Terjadi kesalahan saat menghapus laporan.';
}
$stmtDel->close();

// Redirect ke dashboard atau admin panel tergantung role (fallback ke dashboard jika bingung)
if ($isAdmin && strpos($_SERVER['HTTP_REFERER'], 'admin/laporan.php') !== false) {
    header('Location: ../admin/laporan.php');
} else {
    header('Location: ../dashboard.php');
}
exit;
