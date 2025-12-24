<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['admin']);

include_once __DIR__ . '/../config/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['operator_id']) && isset($_POST['message'])) {
    $operator_id = mysqli_real_escape_string($conn, $_POST['operator_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $admin_id = $_SESSION['id_user'];

    if (empty($message)) {
        $response = ['status' => 'error', 'message' => 'Message cannot be empty'];
    } else {
        $query = "INSERT INTO private_chat (sender_id, recipient_id, message, created_at) VALUES ('$admin_id', '$operator_id', '$message', NOW())";
        if (mysqli_query($conn, $query)) {
            $inserted_id = mysqli_insert_id($conn);
            // fetch created_at for the inserted row
            $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT created_at FROM private_chat WHERE id = '$inserted_id' LIMIT 1"));
            $created_at = $row['created_at'] ?? date('Y-m-d H:i:s');
            $response = ['status' => 'success', 'message' => 'Message sent', 'id' => $inserted_id, 'created_at' => $created_at];
        } else {
            $response = ['status' => 'error', 'message' => 'DB error: ' . mysqli_error($conn)];
        }
    }
}

echo json_encode($response);
