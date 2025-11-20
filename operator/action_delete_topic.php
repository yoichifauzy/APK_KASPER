<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

if (isset($_GET['id'])) {
    $topic_id = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "DELETE FROM discussion_topics WHERE id = '$topic_id'";

    if (mysqli_query($conn, $query)) {
        header('Location: index_forum.php?status=success&message=Topic deleted successfully!');
    } else {
        header('Location: index_forum.php?status=error&message=' . mysqli_error($conn));
    }
    exit();
} else {
    header('Location: index_forum.php?status=error&message=No topic ID provided for deletion.');
    exit();
}
?>