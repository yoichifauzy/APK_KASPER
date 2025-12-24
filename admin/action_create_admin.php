<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create_admin.php');
    exit;
}

$nama = trim($_POST['nama_lengkap'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = 'admin';
$status = $_POST['status'] ?? 'aktif';

if ($nama === '' || $username === '' || $password === '') {
    header('Location: create_admin.php?error=missing');
    exit;
}

// check username uniqueness
$stmt = $conn->prepare('SELECT id_user FROM user WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    header('Location: create_admin.php?error=exists');
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$profile_filename = null;
if (!empty($_FILES['profile_picture']['name'])) {
    $up = $_FILES['profile_picture'];
    if ($up['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            header('Location: create_admin.php?error=badfile');
            exit;
        }
        $dir = __DIR__ . '/../upload/profile/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $profile_filename = 'profile_' . time() . '_' . preg_replace('/[^a-z0-9._-]/i', '', $up['name']);
        move_uploaded_file($up['tmp_name'], $dir . $profile_filename);
    }
}

$ins = $conn->prepare('INSERT INTO user (nama_lengkap, username, password, role, status, profile_picture) VALUES (?, ?, ?, ?, ?, ?)');
$ins->bind_param('ssssss', $nama, $username, $hash, $role, $status, $profile_filename);
if ($ins->execute()) {
    header('Location: manage_admin.php?msg=created');
    exit;
} else {
    header('Location: create_admin.php?error=insert');
    exit;
}
