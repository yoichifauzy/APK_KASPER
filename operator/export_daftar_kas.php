<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// akses hanya admin/operator
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'operator'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// ambil semua kas
$res = $conn->query('SELECT k.id_kas, k.tanggal, k.jenis, k.jumlah, k.keterangan, kk.nama AS kategori_nama, u.username AS dibuat_oleh
    FROM kas k
    LEFT JOIN kas_kategori kk ON k.id_kategori = kk.id_kategori
    LEFT JOIN user u ON k.dibuat_oleh = u.id_user
    ORDER BY k.tanggal DESC');

$rows = [];
while ($r = $res->fetch_assoc()) $rows[] = $r;

if (empty($rows)) {
    echo json_encode(['error' => 'No data']);
    exit;
}

$now = date('Ymd_His');
$fname = "daftar_kas_{$now}.csv";
$path = __DIR__ . '/../upload/daftar_kas/' . $fname;

$fp = fopen($path, 'w');
if (!$fp) {
    echo json_encode(['error' => 'Cannot open file']);
    exit;
}

// header
fputcsv($fp, ['ID', 'Tanggal', 'Jenis', 'Jumlah', 'Keterangan', 'Kategori', 'Dibuat_Oleh']);
foreach ($rows as $r) {
    fputcsv($fp, [$r['id_kas'], $r['tanggal'], $r['jenis'], $r['jumlah'], $r['keterangan'], $r['kategori_nama'], $r['dibuat_oleh']]);
}

fclose($fp);

$url = dirname($_SERVER['SCRIPT_NAME']) . '/../upload/daftar_kas/' . $fname;
// normalize URL
$url = str_replace('\\', '/', $url);

echo json_encode(['file' => $url]);
