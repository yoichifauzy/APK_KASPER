<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['admin']);

include_once __DIR__ . '/../config/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (isset($_GET['operator_id'])) {
    $operator_id = mysqli_real_escape_string($conn, $_GET['operator_id']);
    $admin_id = $_SESSION['id_user'];

    $query = "SELECT pc.id, pc.sender_id, pc.recipient_id, pc.message, pc.created_at, u.nama_lengkap as sender_name
              FROM private_chat pc
              JOIN user u ON pc.sender_id = u.id_user
              WHERE (pc.sender_id = '$admin_id' AND pc.recipient_id = '$operator_id')
                 OR (pc.sender_id = '$operator_id' AND pc.recipient_id = '$admin_id')
              ORDER BY pc.created_at ASC";

    $result = mysqli_query($conn, $query);
    if ($result) {
        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        $response = ['status' => 'success', 'messages' => $messages];
    } else {
        $response = ['status' => 'error', 'message' => 'DB error: ' . mysqli_error($conn)];
    }
}

echo json_encode($response);
