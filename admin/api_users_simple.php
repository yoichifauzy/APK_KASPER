<?php
require_once '../config/cek_login.php';
include '../config/database.php';
header('Content-Type: application/json; charset=utf-8');
$q = $conn->prepare("SELECT id_user, nama_lengkap, username FROM user WHERE status='aktif' ORDER BY nama_lengkap ASC");
$q->execute();
$res = $q->get_result();
$out = [];
while ($r = $res->fetch_assoc()) $out[] = $r;
echo json_encode(['success' => true, 'data' => $out]);
exit;
