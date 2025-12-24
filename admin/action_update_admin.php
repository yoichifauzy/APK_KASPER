<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_admin.php');
    exit;
}

$id = intval($_POST['id_user'] ?? 0);
$nama = trim($_POST['nama_lengkap'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'admin';
$status = $_POST['status'] ?? 'aktif';

if ($id <= 0 || $nama === '' || $username === '') {
    header('Location: edit_admin.php?id=' . $id . '&error=missing');
    exit;
}

// check username uniqueness (exclude current)
$stmt = $conn->prepare('SELECT id_user FROM user WHERE username = ? AND id_user <> ? LIMIT 1');
$stmt->bind_param('si', $username, $id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    header('Location: edit_admin.php?id=' . $id . '&error=exists');
    exit;
}

$profile_filename = null;
if (!empty($_FILES['profile_picture']['name'])) {
    $up = $_FILES['profile_picture'];
    if ($up['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            header('Location: edit_admin.php?id=' . $id . '&error=badfile');
            exit;
        }
        $dir = __DIR__ . '/../upload/profile/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $profile_filename = 'profile_' . time() . '_' . preg_replace('/[^a-z0-9._-]/i', '', $up['name']);
        move_uploaded_file($up['tmp_name'], $dir . $profile_filename);
        // remove old file if exists
        $old = $conn->query('SELECT profile_picture FROM user WHERE id_user = ' . $id)->fetch_assoc()['profile_picture'] ?? null;
        if ($old) {
            @unlink($dir . $old);
        }
    }
}

if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($profile_filename) {
        $upd = $conn->prepare('UPDATE user SET nama_lengkap=?, username=?, password=?, role=?, status=?, profile_picture=? WHERE id_user=?');
        $upd->bind_param('ssssssi', $nama, $username, $hash, $role, $status, $profile_filename, $id);
    } else {
        $upd = $conn->prepare('UPDATE user SET nama_lengkap=?, username=?, password=?, role=?, status=? WHERE id_user=?');
        $upd->bind_param('sssssi', $nama, $username, $hash, $role, $status, $id);
    }
} else {
    if ($profile_filename) {
        $upd = $conn->prepare('UPDATE user SET nama_lengkap=?, username=?, role=?, status=?, profile_picture=? WHERE id_user=?');
        $upd->bind_param('sssssi', $nama, $username, $role, $status, $profile_filename, $id);
    } else {
        $upd = $conn->prepare('UPDATE user SET nama_lengkap=?, username=?, role=?, status=? WHERE id_user=?');
        $upd->bind_param('ssssi', $nama, $username, $role, $status, $id);
    }
}

if ($upd->execute()) {
    header('Location: manage_admin.php?msg=updated');
    exit;
} else {
    header('Location: edit_admin.php?id=' . $id . '&error=update');
    exit;
}
