<?php
/**
 * ajax/get_prodi.php — Endpoint AJAX untuk Dropdown Program Studi
 */

require_once '../config/database.php';

// Set header JSON
header('Content-Type: application/json; charset=utf-8');

// Ambil parameter
$fakultasId = isset($_GET['fakultas_id']) ? (int)$_GET['fakultas_id'] : 0;

if ($fakultasId <= 0) {
    echo json_encode([]);
    exit;
}

// Query ke database
$stmt = $conn->prepare("SELECT id, jenjang, nama_prodi FROM prodi WHERE fakultas_id = ? ORDER BY jenjang ASC, nama_prodi ASC");
$stmt->bind_param('i', $fakultasId);
$stmt->execute();
$result = $stmt->get_result();

$prodi = [];
while ($row = $result->fetch_assoc()) {
    $prodi[] = [
        'id'         => (int)$row['id'],
        'jenjang'    => $row['jenjang'],
        'nama_prodi' => $row['nama_prodi']
    ];
}

$stmt->close();
$conn->close();

// Return JSON
echo json_encode($prodi);
