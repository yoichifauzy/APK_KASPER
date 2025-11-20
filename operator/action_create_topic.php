<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['id_user']; // Assuming user ID is stored in session
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $topic_title = mysqli_real_escape_string($conn, $_POST['topic_title']);
    $topic_content = mysqli_real_escape_string($conn, $_POST['topic_content']);
    $is_pinned = isset($_POST['is_pinned']) ? 1 : 0; // Default to 0 if not set
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    $query = "INSERT INTO discussion_topics (user_id, category_id, title, content, is_pinned, created_at, updated_at) VALUES ('$user_id', '$category_id', '$topic_title', '$topic_content', '$is_pinned', '$created_at', '$updated_at')";

    if (mysqli_query($conn, $query)) {
        header('Location: index_forum.php?status=success');
    } else {
        header('Location: index_forum.php?status=error&message=' . mysqli_error($conn));
    }
    exit();
} else {
    header('Location: create_topic.php');
    exit();
}
?>