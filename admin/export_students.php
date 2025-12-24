<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=students_export_' . date('Ymd') . '.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['id_user', 'username', 'nama_lengkap', 'status', 'created_at', 'profile_picture']);

$q = $conn->prepare("SELECT id_user, username, nama_lengkap, status, created_at, profile_picture FROM user WHERE role = 'user' ORDER BY created_at DESC");
$q->execute();
$res = $q->get_result();
while ($r = $res->fetch_assoc()) {
    fputcsv($out, [$r['id_user'], $r['username'], $r['nama_lengkap'], $r['status'], $r['created_at'], $r['profile_picture']]);
}
fclose($out);
exit;
