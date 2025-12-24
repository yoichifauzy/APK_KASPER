<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

// DataTables server-side processing
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 15;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
$orderCol = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 2;
$orderDir = isset($_GET['order'][0]['dir']) && in_array($_GET['order'][0]['dir'], ['asc', 'desc']) ? $_GET['order'][0]['dir'] : 'asc';

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$columns = [
    0 => 'id_user',
    1 => 'profile_picture',
    2 => 'username',
    3 => 'nama_lengkap',
    4 => 'status',
    5 => 'created_at'
];

$orderBy = isset($columns[$orderCol]) ? $columns[$orderCol] : 'created_at';

$where = "WHERE role = 'operator'";
if ($statusFilter !== '') {
    $s = $conn->real_escape_string($statusFilter);
    $where .= " AND status='" . $s . "'";
}
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $where .= " AND (username LIKE '%" . $s . "%' OR nama_lengkap LIKE '%" . $s . "%')";
}

// total records
$totalRes = $conn->query("SELECT COUNT(*) AS cnt FROM user WHERE role = 'operator'");
$totalRow = $totalRes->fetch_assoc();
$recordsTotal = intval($totalRow['cnt']);

// filtered records
$filteredRes = $conn->query("SELECT COUNT(*) AS cnt FROM user $where");
$filteredRow = $filteredRes->fetch_assoc();
$recordsFiltered = intval($filteredRow['cnt']);

$data = [];
$sql = "SELECT id_user, username, nama_lengkap, status, created_at, COALESCE(profile_picture, '') AS profile_picture FROM user $where ORDER BY $orderBy $orderDir LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $start, $length);
$stmt->execute();
$res = $stmt->get_result();
$no = $start + 1;
while ($r = $res->fetch_assoc()) {
    $img = '<img src="../upload/profile/' . ($r['profile_picture'] ? htmlspecialchars($r['profile_picture']) : 'default.png') . '" alt="pp" style="width:48px;height:48px;object-fit:cover;border-radius:50%;" onerror="this.onerror=null;this.src=\'../assets/img/profile.jpg\'">';
    $statusLabel = ($r['status'] === 'aktif') ? '<span class="badge bg-success">aktif</span>' : '<span class="badge bg-secondary">' . htmlspecialchars($r['status']) . '</span>';

    $data[] = [
        $no++,
        $img,
        htmlspecialchars($r['username']),
        htmlspecialchars($r['nama_lengkap']),
        $statusLabel,
        htmlspecialchars($r['created_at'])
    ];
}

$out = ['draw' => $draw, 'recordsTotal' => $recordsTotal, 'recordsFiltered' => $recordsFiltered, 'data' => $data];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($out);
exit;
