<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, trim($_POST['name'])) : '';

    if ($id <= 0 || $name === '') {
        header('Location: view_category.php?status=error&message=Invalid+input');
        exit();
    }

    $q = "UPDATE discussion_categories SET name='" . $name . "' WHERE id=" . $id;
    if (mysqli_query($conn, $q)) {
        header('Location: view_category.php?status=success');
    } else {
        header('Location: view_category.php?status=error&message=' . urlencode(mysqli_error($conn)));
    }
    exit();
} else {
    header('Location: view_category.php');
    exit();
}
