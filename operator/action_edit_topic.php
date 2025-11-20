<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic_id = mysqli_real_escape_string($conn, $_POST['topic_id']);
    $topic_title = mysqli_real_escape_string($conn, $_POST['topic_title']);
    $topic_content = mysqli_real_escape_string($conn, $_POST['topic_content']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $is_pinned = isset($_POST['is_pinned']) ? 1 : 0;
    $updated_at = date('Y-m-d H:i:s');

    $query = "UPDATE discussion_topics SET
                title = '$topic_title',
                content = '$topic_content',
                category_id = '$category_id',
                is_pinned = '$is_pinned',
                updated_at = '$updated_at'
              WHERE id = '$topic_id'";

    if (mysqli_query($conn, $query)) {
        header('Location: index_forum.php?status=success&message=Topic updated successfully!');
    } else {
        header('Location: index_forum.php?status=error&message=' . mysqli_error($conn));
    }
    exit();
} else {
    header('Location: index_forum.php');
    exit();
}
?>