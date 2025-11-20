<?php
header('Content-Type: application/json');
require_once '../config/cek_login.php';
// All roles can send chat messages
otorisasi(['admin', 'operator', 'user']);

include '../config/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['topic_id']) && isset($_POST['message'])) {
    $topic_id = mysqli_real_escape_string($conn, $_POST['topic_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $user_id = $_SESSION['id_user']; // Assuming user ID is stored in session

    if (!empty($message)) {
        $query = "INSERT INTO chat (id_user, topic_id, pesan, waktu) VALUES ('$user_id', '$topic_id', '$message', NOW())";

        if (mysqli_query($conn, $query)) {
            $response = ['status' => 'success', 'message' => 'Message sent successfully!'];
        } else {
            $response = ['status' => 'error', 'message' => 'Failed to send message: ' . mysqli_error($conn)];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Message cannot be empty.'];
    }
}

echo json_encode($response);