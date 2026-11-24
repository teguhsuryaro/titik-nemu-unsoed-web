<?php
/**
 * search.php — Redirect ke index.php (pencarian sudah digabung di Beranda)
 */

$queryString = $_SERVER['QUERY_STRING'] ?? '';
$target = 'index.php' . ($queryString !== '' ? '?' . $queryString : '');

header("Location: {$target}", true, 301);
exit;
