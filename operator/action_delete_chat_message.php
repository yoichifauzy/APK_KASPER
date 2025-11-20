<?php
header('Content-Type: application/json');
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator', 'user']);

include '../config/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (isset($_GET['id']) && isset($_GET['topic_id'])) {
    $message_id = mysqli_real_escape_string($conn, $_GET['id']);
    $topic_id = mysqli_real_escape_string($conn, $_GET['topic_id']);
    $current_user_id = $_SESSION['id_user'];

    $query = "DELETE FROM chat WHERE id_chat = '$message_id' AND id_user = '$current_user_id'"; // Ensure only owner can delete

    if (mysqli_query($conn, $query)) {
        $response = ['status' => 'success', 'message' => 'Message deleted successfully!'];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to delete message: ' . mysqli_error($conn)];
    }
} else {
    $response = ['status' => 'error', 'message' => 'No message ID or topic ID provided for deletion.'];
}

echo json_encode($response);