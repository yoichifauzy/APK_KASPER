<?php
header('Content-Type: application/json');
include '../config/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (isset($_GET['topic_id'])) {
    $topic_id = mysqli_real_escape_string($conn, $_GET['topic_id']);

    // Fetch messages for the last 3 hours for the specific topic
    $query = "SELECT
                c.pesan,
                c.waktu,
                c.id_user,
                c.id_chat,
                u.nama_lengkap AS sender_name,
                u.role AS sender_role
              FROM
                chat c
              JOIN
                user u ON c.id_user = u.id_user
              WHERE
                c.topic_id = '$topic_id' AND c.waktu >= NOW() - INTERVAL 3 HOUR
              ORDER BY
                c.waktu ASC";

    $result = mysqli_query($conn, $query);

    if ($result) {
        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = $row;
        }
        $response = ['status' => 'success', 'messages' => $messages];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to fetch messages: ' . mysqli_error($conn)];
    }
}

echo json_encode($response);
