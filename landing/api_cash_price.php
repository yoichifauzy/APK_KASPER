<?php
// landing/api_cash_price.php
// Read-only JSON API returning kas entries (used by landing/cash_price.php)
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/database.php';

$result = ['ok' => false, 'data' => [], 'error' => null];

try {
    $q = $conn->query("SELECT k.id_kas, k.id_kategori, kk.nama AS kategori_nama, k.tanggal, k.jenis, k.jumlah, k.keterangan, u.username AS dibuat_oleh FROM kas k LEFT JOIN kas_kategori kk ON k.id_kategori = kk.id_kategori LEFT JOIN user u ON k.dibuat_oleh = u.id_user ORDER BY k.tanggal DESC, k.id_kas DESC");
    if ($q) {
        $rows = [];
        while ($r = $q->fetch_assoc()) {
            $rows[] = $r;
        }
        $result['ok'] = true;
        $result['data'] = $rows;
    } else {
        $result['error'] = 'Query failed';
    }
} catch (Exception $e) {
    $result['error'] = $e->getMessage();
}

echo json_encode($result);
