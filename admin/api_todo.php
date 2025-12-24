<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

// Helper to read JSON body
function body_json()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

if ($method === 'GET') {
    // list optionally filtered by status
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    if ($status && !in_array($status, ['todo', 'inprogress', 'done'])) $status = null;
    if ($status) {
        $stmt = $conn->prepare('SELECT * FROM analysis_todo WHERE status = ? ORDER BY position ASC, id ASC');
        $stmt->bind_param('s', $status);
    } else {
        $stmt = $conn->prepare('SELECT * FROM analysis_todo ORDER BY FIELD(status, "todo","inprogress","done"), position ASC, id ASC');
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $out = [];
    while ($r = $res->fetch_assoc()) {
        $out[] = $r;
    }
    echo json_encode(['success' => true, 'data' => $out]);
    exit;
}

if ($method === 'POST') {
    $data = body_json();
    // support action=reorder too
    if (isset($data['action']) && $data['action'] === 'reorder' && isset($data['items']) && is_array($data['items'])) {
        // items = [{id:.., status:.., position:..}, ...]
        $stmt = $conn->prepare('UPDATE analysis_todo SET status = ?, position = ? WHERE id = ?');
        foreach ($data['items'] as $it) {
            $id = intval($it['id']);
            $status = in_array($it['status'], ['todo', 'inprogress', 'done']) ? $it['status'] : 'todo';
            $pos = intval($it['position']);
            $stmt->bind_param('sii', $status, $pos, $id);
            $stmt->execute();
        }
        echo json_encode(['success' => true]);
        exit;
    }

    // create new
    $title = isset($data['title']) ? trim($data['title']) : '';
    $description = isset($data['description']) ? trim($data['description']) : null;
    $due = isset($data['due_date']) && $data['due_date'] !== '' ? $data['due_date'] : null;
    $status = isset($data['status']) && in_array($data['status'], ['todo', 'inprogress', 'done']) ? $data['status'] : 'todo';

    if ($title === '') {
        echo json_encode(['success' => false, 'message' => 'Title required']);
        exit;
    }

    // compute max position in that column
    $stmtp = $conn->prepare('SELECT COALESCE(MAX(position),0) as maxpos FROM analysis_todo WHERE status = ?');
    $stmtp->bind_param('s', $status);
    $stmtp->execute();
    $mp = $stmtp->get_result()->fetch_assoc()['maxpos'];
    $pos = intval($mp) + 1;

    $stmt = $conn->prepare('INSERT INTO analysis_todo (title,description,status,due_date,position,created_by) VALUES (?,?,?,?,?,?)');
    $uid = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : null;
    $stmt->bind_param('ssssii', $title, $description, $status, $due, $pos, $uid);
    $stmt->execute();
    $id = $conn->insert_id;
    echo json_encode(['success' => true, 'id' => $id]);
    exit;
}

if ($method === 'PUT') {
    $data = body_json();
    $id = isset($data['id']) ? intval($data['id']) : 0;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing id']);
        exit;
    }
    $fields = [];
    $params = [];
    $types = '';
    if (isset($data['title'])) {
        $fields[] = 'title = ?';
        $params[] = $data['title'];
        $types .= 's';
    }
    if (array_key_exists('description', $data)) {
        $fields[] = 'description = ?';
        $params[] = $data['description'];
        $types .= 's';
    }
    if (isset($data['due_date'])) {
        $fields[] = 'due_date = ?';
        $params[] = $data['due_date'];
        $types .= 's';
    }
    if (isset($data['status']) && in_array($data['status'], ['todo', 'inprogress', 'done'])) {
        $fields[] = 'status = ?';
        $params[] = $data['status'];
        $types .= 's';
    }
    if (isset($data['position'])) {
        $fields[] = 'position = ?';
        $params[] = intval($data['position']);
        $types .= 'i';
    }

    if (count($fields) === 0) {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        exit;
    }
    $sql = 'UPDATE analysis_todo SET ' . implode(', ', $fields) . ' WHERE id = ?';
    $params[] = $id;
    $types .= 'i';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit;
}

if ($method === 'DELETE') {
    // expected query ?id=
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing id']);
        exit;
    }
    $stmt = $conn->prepare('DELETE FROM analysis_todo WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit;
}

// method not allowed
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
exit;
