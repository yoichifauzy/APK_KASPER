<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, trim($_POST['name'])) : '';
    if ($name === '') {
        header('Location: view_category.php?status=error&message=Name+required');
        exit();
    }

    $q = "INSERT INTO discussion_categories (name) VALUES ('" . $name . "')";
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
