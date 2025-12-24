<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

// read JSON body for POST/PUT
function json_body()
{
    $raw = file_get_contents('php://input');
    $d = json_decode($raw, true);
    return is_array($d) ? $d : [];
}

if ($method === 'GET') {
    // list or get by id; optional start/end filters
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare('SELECT * FROM roadmap_item WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $res]);
        exit;
    }
    $start = isset($_GET['start']) ? $_GET['start'] : null;
    $end = isset($_GET['end']) ? $_GET['end'] : null;
    $sql = 'SELECT * FROM roadmap_item';
    $params = [];
    if ($start && $end) {
        $sql .= ' WHERE NOT (end_date < ? OR start_date > ?)';
        $params = [$start, $end];
    }
    $sql .= ' ORDER BY start_date IS NULL, start_date ASC, id ASC';
    $stmt = $conn->prepare($sql);
    if ($start && $end) $stmt->bind_param('ss', $params[0], $params[1]);
    $stmt->execute();
    $r = $stmt->get_result();
    $out = [];
    while ($row = $r->fetch_assoc()) $out[] = $row;
    echo json_encode(['success' => true, 'data' => $out]);
    exit;
}

if ($method === 'POST') {
    $data = json_body();
    $title = isset($data['title']) ? $data['title'] : '';
    if (!$title) {
        echo json_encode(['success' => false, 'message' => 'Title required']);
        exit;
    }
    $description = isset($data['description']) ? $data['description'] : null;
    $owner = isset($data['owner_id']) ? intval($data['owner_id']) : null;
    $start = isset($data['start_date']) ? $data['start_date'] : null;
    $end = isset($data['end_date']) ? $data['end_date'] : null;
    $status = isset($data['status']) ? $data['status'] : 'planned';
    $progress = isset($data['progress']) ? intval($data['progress']) : 0;
    $tags = isset($data['tags']) ? $data['tags'] : null;
    $created_by = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : null;

    $stmt = $conn->prepare('INSERT INTO roadmap_item (title,description,owner_id,start_date,end_date,status,progress,tags,created_by) VALUES (?,?,?,?,?,?,?,?,?)');
    $stmt->bind_param('ssisssisi', $title, $description, $owner, $start, $end, $status, $progress, $tags, $created_by);
    $stmt->execute();
    echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    exit;
}

if ($method === 'PUT') {
    $data = json_body();
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
    if (isset($data['owner_id'])) {
        $fields[] = 'owner_id = ?';
        $params[] = intval($data['owner_id']);
        $types .= 'i';
    }
    if (isset($data['start_date'])) {
        $fields[] = 'start_date = ?';
        $params[] = $data['start_date'];
        $types .= 's';
    }
    if (isset($data['end_date'])) {
        $fields[] = 'end_date = ?';
        $params[] = $data['end_date'];
        $types .= 's';
    }
    if (isset($data['status'])) {
        $fields[] = 'status = ?';
        $params[] = $data['status'];
        $types .= 's';
    }
    if (isset($data['progress'])) {
        $fields[] = 'progress = ?';
        $params[] = intval($data['progress']);
        $types .= 'i';
    }
    if (isset($data['tags'])) {
        $fields[] = 'tags = ?';
        $params[] = $data['tags'];
        $types .= 's';
    }
    if (count($fields) === 0) {
        echo json_encode(['success' => false, 'message' => 'No fields']);
        exit;
    }
    $sql = 'UPDATE roadmap_item SET ' . implode(', ', $fields) . ' WHERE id = ?';
    $params[] = $id;
    $types .= 'i';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit;
}

if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing id']);
        exit;
    }
    $stmt = $conn->prepare('DELETE FROM roadmap_item WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
exit;
