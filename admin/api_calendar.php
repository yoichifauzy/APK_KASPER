<?php
require_once '../config/cek_login.php';
include '../config/database.php';
header('Content-Type: application/json; charset=utf-8');

// only admin permitted to access calendar API
otorisasi(['admin']);

$method = $_SERVER['REQUEST_METHOD'];

function json_body()
{
    $raw = file_get_contents('php://input');
    $d = json_decode($raw, true);
    return is_array($d) ? $d : [];
}

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare('SELECT * FROM calendar_event WHERE id = ? LIMIT 1');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $row]);
            exit;
        }
        // optional range filter
        $start = isset($_GET['start']) ? $_GET['start'] : null;
        $end = isset($_GET['end']) ? $_GET['end'] : null;
        if ($start && $end) {
            $stmt = $conn->prepare('SELECT * FROM calendar_event WHERE NOT (end_datetime < ? OR start_datetime > ?) ORDER BY start_datetime ASC');
            $stmt->bind_param('ss', $start, $end);
        } else {
            $stmt = $conn->prepare('SELECT * FROM calendar_event ORDER BY start_datetime ASC');
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($r = $res->fetch_assoc()) $out[] = $r;
        echo json_encode(['success' => true, 'data' => $out]);
        exit;
    }

    if ($method === 'POST') {
        $data = json_body();
        $title = trim($data['title'] ?? '');
        if (!$title) {
            echo json_encode(['success' => false, 'message' => 'Title required']);
            exit;
        }
        $desc = $data['description'] ?? null;
        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;
        $type = $data['type'] ?? 'other';
        $owner = isset($data['owner_id']) ? intval($data['owner_id']) : (isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : null);
        $participants = isset($data['participants']) ? $data['participants'] : null; // store CSV or JSON
        $created_by = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : null;
        $bg_color = isset($data['bg_color']) ? $data['bg_color'] : null;
        $text_color = isset($data['text_color']) ? $data['text_color'] : null;

        $stmt = $conn->prepare('INSERT INTO calendar_event (title,description,start_datetime,end_datetime,type,owner_id,participants,created_by,bg_color,text_color) VALUES (?,?,?,?,?,?,?,?,?,?)');
        // types: title(s), desc(s), start(s), end(s), type(s), owner(i), participants(s), created_by(i), bg_color(s), text_color(s)
        $stmt->bind_param('sssssisiss', $title, $desc, $start, $end, $type, $owner, $participants, $created_by, $bg_color, $text_color);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'DB error: ' . $stmt->error]);
            exit;
        }
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
        // fetch existing owner
        $orig = $conn->query('SELECT owner_id FROM calendar_event WHERE id = ' . $id)->fetch_assoc();
        $orig_owner = $orig['owner_id'] ?? null;
        // only admin or owner can update (but otorisasi already restricts to admin); keep owner check for safety
        if (!in_array($_SESSION['role'], ['admin']) && intval($_SESSION['id_user']) !== intval($orig_owner)) {
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            exit;
        }

        $fields = [];
        $params = [];
        $types = '';
        if (isset($data['title'])) {
            $fields[] = 'title=?';
            $params[] = $data['title'];
            $types .= 's';
        }
        if (array_key_exists('description', $data)) {
            $fields[] = 'description=?';
            $params[] = $data['description'];
            $types .= 's';
        }
        if (isset($data['start'])) {
            $fields[] = 'start_datetime=?';
            $params[] = $data['start'];
            $types .= 's';
        }
        if (isset($data['end'])) {
            $fields[] = 'end_datetime=?';
            $params[] = $data['end'];
            $types .= 's';
        }
        if (isset($data['type'])) {
            $fields[] = 'type=?';
            $params[] = $data['type'];
            $types .= 's';
        }
        if (isset($data['owner_id'])) {
            $fields[] = 'owner_id=?';
            $params[] = intval($data['owner_id']);
            $types .= 'i';
        }
        if (isset($data['participants'])) {
            $fields[] = 'participants=?';
            $params[] = $data['participants'];
            $types .= 's';
        }
        if (isset($data['bg_color'])) {
            $fields[] = 'bg_color=?';
            $params[] = $data['bg_color'];
            $types .= 's';
        }
        if (isset($data['text_color'])) {
            $fields[] = 'text_color=?';
            $params[] = $data['text_color'];
            $types .= 's';
        }
        if (count($fields) === 0) {
            echo json_encode(['success' => false, 'message' => 'No fields']);
            exit;
        }
        $sql = 'UPDATE calendar_event SET ' . implode(',', $fields) . ' WHERE id=?';
        $params[] = $id;
        $types .= 'i';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'DB error: ' . $stmt->error]);
            exit;
        }
        echo json_encode(['success' => true]);
        exit;
    }

    if ($method === 'DELETE') {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing id']);
            exit;
        }
        $orig = $conn->query('SELECT owner_id FROM calendar_event WHERE id = ' . $id)->fetch_assoc();
        $orig_owner = $orig['owner_id'] ?? null;
        if (!in_array($_SESSION['role'], ['admin']) && intval($_SESSION['id_user']) !== intval($orig_owner)) {
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            exit;
        }
        $stmt = $conn->prepare('DELETE FROM calendar_event WHERE id = ?');
        $stmt->bind_param('i', $id);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'DB error: ' . $stmt->error]);
            exit;
        }
        echo json_encode(['success' => true]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
} catch (Exception $ex) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $ex->getMessage()]);
    exit;
}
