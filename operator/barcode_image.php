<?php
require_once __DIR__ . '/../config/cek_login.php';
// ensure session is available
if (session_status() == PHP_SESSION_NONE) session_start();

// simple access model:
// - admins and operators can view any barcode
// - regular users can view only their own barcode

// simple GET endpoint: ?code=PAY-123-abcdef
if (!isset($_GET['code']) || !is_string($_GET['code'])) {
    http_response_code(400);
    echo 'Missing code';
    exit;
}
$code = $_GET['code'];

// validate expected format: PAY-{id}-{hex6}
if (!preg_match('/^PAY-\d+-[0-9a-fA-F]{6}$/', $code)) {
    http_response_code(400);
    echo 'Invalid code format';
    exit;
}

// verify code exists in DB and determine owner
require_once __DIR__ . '/../config/database.php';
$stmt = $conn->prepare("SELECT id_pembayaran, id_user FROM pembayaran WHERE barcode = ? LIMIT 1");
$stmt->bind_param('s', $code);
$stmt->execute();
$stmt->bind_result($idp, $owner_user_id);
if (!$stmt->fetch()) {
    $stmt->close();
    http_response_code(404);
    echo 'Barcode not found';
    exit;
}
$stmt->close();

// permission check: allow if current user is operator/admin OR owner
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$current_user = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : null;
if (!in_array($role, ['admin', 'operator'])) {
    if ($current_user === null || $current_user !== intval($owner_user_id)) {
        http_response_code(403);
        echo 'Access denied';
        exit;
    }
}

// try to include TCPDF 2D barcode helper (it's present in vendor/tcpdf)
$tcpdf_file = __DIR__ . '/../vendor/tcpdf/tcpdf_barcodes_2d.php';
if (!is_file($tcpdf_file)) {
    http_response_code(500);
    echo 'Barcode generator not available';
    exit;
}
require_once $tcpdf_file;

// record audit: view_image
$log_stmt = $conn->prepare("INSERT INTO barcode_audit (barcode, id_user, action, ip, user_agent, extra) VALUES (?, ?, ?, ?, ?, ?)");
if ($log_stmt) {
    $action = 'view_image';
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
    $extra = null;
    $uid_for_log = $current_user !== null ? $current_user : null;
    $log_stmt->bind_param('sissss', $code, $uid_for_log, $action, $ip, $ua, $extra);
    $log_stmt->execute();
    $log_stmt->close();
}

// create barcode and output PNG (use GD) with fallback to SVG
try {
    // create QR-code (2D) using TCPDF2DBarcode
    $qr = new TCPDF2DBarcode($code, 'QRCODE,H');
    // support size for thumbnails: ?size=thumb or ?size=small|full
    $size = isset($_GET['size']) ? $_GET['size'] : '';
    if ($size === 'thumb' || $size === 'small') {
        $module = 3; // small thumbnail
    } else {
        $module = 6; // default module size
    }
    $png = $qr->getBarcodePngData($module, $module, array(0, 0, 0));
    if ($png !== false && is_string($png)) {
        header('Content-Type: image/png');
        header('Cache-Control: public, max-age=3600');
        echo $png;
        exit;
    } else {
        // fallback to SVG
        header('Content-Type: image/svg+xml');
        header('Cache-Control: public, max-age=3600');
        echo $qr->getBarcodeSVGcode(3, 3, 'black');
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error generating barcode';
    exit;
}
