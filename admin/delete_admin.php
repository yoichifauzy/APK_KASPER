<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: manage_admin.php');
    exit;
}

$row = $conn->query('SELECT profile_picture FROM user WHERE id_user = ' . $id)->fetch_assoc();
$profile = $row['profile_picture'] ?? null;

$del = $conn->prepare('DELETE FROM user WHERE id_user = ?');
$del->bind_param('i', $id);
if ($del->execute()) {
    if ($profile) {
        @unlink(__DIR__ . '/../upload/profile/' . $profile);
    }
    header('Location: manage_admin.php?msg=deleted');
    exit;
} else {
    header('Location: manage_admin.php?error=delete');
    exit;
}
