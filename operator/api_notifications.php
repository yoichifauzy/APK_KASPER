<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['operator']);

include_once __DIR__ . '/../config/database.php';

$user_id = $_SESSION['id_user'];

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (isset($conn)) {
    // Private chats received by this operator
    $private_count = 0;
    $private_items = [];
    $qpc = "SELECT pc.id, pc.sender_id, pc.recipient_id, pc.message, pc.created_at, u.nama_lengkap AS sender_name
            FROM private_chat pc
            JOIN user u ON pc.sender_id = u.id_user
            WHERE pc.recipient_id = '" . mysqli_real_escape_string($conn, $user_id) . "'
            ORDER BY pc.created_at DESC LIMIT 5";
    $rpc = mysqli_query($conn, $qpc);
    if ($rpc) {
        while ($r = mysqli_fetch_assoc($rpc)) $private_items[] = $r;
    }
    $rpcnt = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM private_chat WHERE recipient_id = '" . mysqli_real_escape_string($conn, $user_id) . "'");
    if ($rpcnt) {
        $row = mysqli_fetch_assoc($rpcnt);
        $private_count = intval($row['cnt']);
    }

    // Forum chat replies on topics owned by this user
    $forum_count = 0;
    $forum_items = [];
    $qforum = "SELECT c.id_chat AS id, c.topic_id, dt.title AS topic_title, c.pesan AS message, c.waktu AS created_at, u.nama_lengkap AS sender_name
               FROM chat c
               JOIN user u ON c.id_user = u.id_user
               JOIN discussion_topics dt ON c.topic_id = dt.id
               WHERE dt.user_id = '" . mysqli_real_escape_string($conn, $user_id) . "'
               ORDER BY c.waktu DESC LIMIT 5";
    $rforum = mysqli_query($conn, $qforum);
    if ($rforum) {
        while ($r = mysqli_fetch_assoc($rforum)) $forum_items[] = $r;
    }
    $rforumcnt = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM chat c JOIN discussion_topics dt ON c.topic_id = dt.id WHERE dt.user_id = '" . mysqli_real_escape_string($conn, $user_id) . "'");
    if ($rforumcnt) {
        $row = mysqli_fetch_assoc($rforumcnt);
        $forum_count = intval($row['cnt']);
    }

    $total = $private_count + $forum_count;

    $response = [
        'status' => 'success',
        'counts' => [
            'private' => $private_count,
            'forum' => $forum_count,
            'total' => $total
        ],
        'items' => [
            'private' => $private_items,
            'forum' => $forum_items
        ]
    ];
}

echo json_encode($response);
