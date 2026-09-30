<?php
/**
 * config/database.php — Koneksi Database MySQL
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'titiknemu_unsoed');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#C83F45;">'
      . '<h2>Koneksi database gagal</h2>'
      . '<p>Pastikan MySQL sudah berjalan di Laragon dan database <code>titiknemu_unsoed</code> sudah dibuat.</p>'
      . '<p><small>' . htmlspecialchars($conn->connect_error) . '</small></p>'
      . '</div>');
}

$conn->set_charset('utf8mb4');
