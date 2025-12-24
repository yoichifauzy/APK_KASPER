<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// Query data
$sql = "SELECT r.id_ranking, r.id_user, r.jumlah_rajinnya, r.jumlah_telatnya, r.poin, u.nama_lengkap, u.username
        FROM ranking r
        LEFT JOIN user u ON r.id_user = u.id_user
        ORDER BY r.poin DESC, r.jumlah_rajinnya DESC";
$res = $conn->query($sql);

// Send headers to force download as CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=point_history.csv');

$out = fopen('php://output', 'w');
// UTF-8 BOM for Excel compatibility
fwrite($out, "\xEF\xBB\xBF");
fputcsv($out, ['No', 'Nama', 'Username', 'Rajin', 'Telat', 'Poin']);

$no = 1;
if ($res) {
    while ($row = $res->fetch_assoc()) {
        fputcsv($out, [
            $no++,
            $row['nama_lengkap'] ?? '-',
            $row['username'] ?? '-',
            intval($row['jumlah_rajinnya'] ?? 0),
            intval($row['jumlah_telatnya'] ?? 0),
            intval($row['poin'] ?? 0),
        ]);
    }
}

fclose($out);
exit;
