<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        header('Location: view_category.php?status=error&message=Invalid+id');
        exit();
    }

    // Optional: check for topics using this category before deleting
    $check = "SELECT COUNT(*) AS cnt FROM discussion_topics WHERE category_id=" . $id;
    $res = mysqli_query($conn, $check);
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        if ($row['cnt'] > 0) {
            header('Location: view_category.php?status=error&message=Category+in+use');
            exit();
        }
    }

    $q = "DELETE FROM discussion_categories WHERE id=" . $id;
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
