<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['admin']);

include_once __DIR__ . '/../config/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (isset($_GET['id'])) {
    $message_id = mysqli_real_escape_string($conn, $_GET['id']);
    $admin_id = $_SESSION['id_user'];

    // Ensure owner
    $q = "SELECT sender_id FROM private_chat WHERE id = '$message_id' LIMIT 1";
    $res = mysqli_query($conn, $q);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if ($row['sender_id'] != $admin_id) {
            $response = ['status' => 'error', 'message' => 'Permission denied'];
            echo json_encode($response);
            exit;
        }
        $del = "DELETE FROM private_chat WHERE id = '$message_id'";
        if (mysqli_query($conn, $del)) {
            $response = ['status' => 'success', 'message' => 'Message deleted'];
        } else {
            $response = ['status' => 'error', 'message' => 'DB error: ' . mysqli_error($conn)];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Message not found'];
    }
}

echo json_encode($response);
