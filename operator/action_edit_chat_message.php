<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator', 'user']);

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_id']) && isset($_POST['message_content'])) {
    $message_id = mysqli_real_escape_string($conn, $_POST['message_id']);
    $message_content = mysqli_real_escape_string($conn, $_POST['message_content']);
    $current_user_id = $_SESSION['id_user'];

    // Get topic_id for redirection
    $query_get_topic_id = "SELECT topic_id FROM chat WHERE id_chat = '$message_id' AND id_user = '$current_user_id'";
    $result_get_topic_id = mysqli_query($conn, $query_get_topic_id);
    $topic_id = null;
    if ($result_get_topic_id && mysqli_num_rows($result_get_topic_id) > 0) {
        $row = mysqli_fetch_assoc($result_get_topic_id);
        $topic_id = $row['topic_id'];
    }

    if ($topic_id) {
        $query = "UPDATE chat SET
                    pesan = '$message_content',
                    waktu = NOW()
                  WHERE id_chat = '$message_id' AND id_user = '$current_user_id'"; // Ensure only owner can edit

        if (mysqli_query($conn, $query)) {
            header('Location: my_discussion.php?topic_id=' . $topic_id . '&status=success&message=Message updated successfully!');
        } else {
            header('Location: my_discussion.php?topic_id=' . $topic_id . '&status=error&message=' . mysqli_error($conn));
        }
    } else {
        header('Location: index_forum.php?status=error&message=Message not found or you do not have permission to edit.');
    }
    exit();
} else {
    header('Location: index_forum.php?status=error&message=Invalid request for message edit.');
    exit();
}
?>