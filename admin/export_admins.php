<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=admins_export_' . date('Ymd') . '.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['id_user', 'username', 'nama_lengkap', 'role', 'status', 'created_at']);

$q = $conn->prepare("SELECT id_user, username, nama_lengkap, role, status, created_at FROM user WHERE role = 'admin' ORDER BY created_at DESC");
$q->execute();
$res = $q->get_result();
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [$r['id_user'], $r['username'], $r['nama_lengkap'], $r['role'], $r['status'], $r['created_at']]);
}
fclose($out);
exit;
