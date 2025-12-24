<?php
require_once __DIR__ . '/../config/cek_login.php';
if (session_status() == PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_REQUEST['code']) || !is_string($_REQUEST['code'])) {
    http_response_code(400);
    echo json_encode(['error' => 'missing_code']);
    exit;
}
$code = $_REQUEST['code'];
if (!preg_match('/^PAY-\d+-[0-9a-fA-F]{6}$/', $code)) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_code_format']);
    exit;
}

$require_db = __DIR__ . '/../config/database.php';
require_once $require_db;

// determine which creator column exists so we can return a friendly operator display
$creator_join = '';
$creator_select = "'' AS ditambahkan_oleh_display";
$c1 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'ditambahkan_oleh'");
if ($c1 && $c1->num_rows > 0) {
    $colDita = 'ditambahkan_oleh';
} else {
    $c2 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'dibuat_oleh'");
    if ($c2 && $c2->num_rows > 0) $colDita = 'dibuat_oleh';
    else $colDita = null;
}
if ($colDita) {
    $creator_join = " LEFT JOIN user u_cre ON p." . $colDita . " = u_cre.id_user";
    $creator_select = "COALESCE(u_cre.username, CAST(p." . $colDita . " AS CHAR)) AS ditambahkan_oleh_display";
}

$sql = "SELECT p.id_pembayaran, p.id_user, u.nama_lengkap, COALESCE(p.jumlah, k.jumlah) AS jumlah, p.tanggal_bayar, p.status, p.bukti, p.id_kas, k.keterangan, " . $creator_select . " 
        FROM pembayaran p
        LEFT JOIN user u ON p.id_user = u.id_user
        LEFT JOIN kas k ON p.id_kas = k.id_kas" . $creator_join . "
        WHERE p.barcode = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $code);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}
$row = $res->fetch_assoc();
$stmt->close();

// permission: admin/operator can view any; regular user only their own
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$current_user = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : null;
if (!in_array($role, ['admin', 'operator'])) {
    if ($current_user === null || $current_user !== intval($row['id_user'])) {
        http_response_code(403);
        echo json_encode(['error' => 'forbidden']);
        exit;
    }
}

// write audit log: lookup
$log_stmt = $conn->prepare("INSERT INTO barcode_audit (barcode, id_user, action, ip, user_agent, extra) VALUES (?, ?, ?, ?, ?, ?)");
if ($log_stmt) {
    $action = 'lookup';
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
    $extra = null;
    $uid_for_log = $current_user !== null ? $current_user : null;
    $log_stmt->bind_param('sissss', $code, $uid_for_log, $action, $ip, $ua, $extra);
    $log_stmt->execute();
    $log_stmt->close();
}

// sanitize and return
unset($row['id_user']);
echo json_encode(['ok' => true, 'payment' => $row]);
exit;
