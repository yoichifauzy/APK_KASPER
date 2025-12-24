<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['operator']);

include_once __DIR__ . '/../config/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id']) && isset($_POST['message_content'])) {
    $message_id = mysqli_real_escape_string($conn, $_POST['message_id']);
    $content = mysqli_real_escape_string($conn, $_POST['message_content']);
    $operator_id = $_SESSION['id_user'];

    // Ensure the message belongs to this operator
    $q = "SELECT sender_id FROM private_chat WHERE id = '$message_id' LIMIT 1";
    $res = mysqli_query($conn, $q);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if ($row['sender_id'] != $operator_id) {
            $response = ['status' => 'error', 'message' => 'Permission denied'];
            echo json_encode($response);
            exit;
        }
        $up = "UPDATE private_chat SET message = '$content', created_at = NOW() WHERE id = '$message_id'";
        if (mysqli_query($conn, $up)) {
            $response = ['status' => 'success', 'message' => 'Message updated'];
        } else {
            $response = ['status' => 'error', 'message' => 'DB error: ' . mysqli_error($conn)];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Message not found'];
    }
}

echo json_encode($response);
