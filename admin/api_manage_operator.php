<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

// DataTables server-side processing
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
$orderCol = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 1;
$orderDir = isset($_GET['order'][0]['dir']) && in_array($_GET['order'][0]['dir'], ['asc', 'desc']) ? $_GET['order'][0]['dir'] : 'asc';

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$columns = [
    0 => 'id_user', // No (not used for ordering)
    1 => 'username',
    2 => 'nama_lengkap',
    3 => 'role',
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
$sql = "SELECT id_user, username, nama_lengkap, role, status, created_at FROM user $where ORDER BY $orderBy $orderDir LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $start, $length);
$stmt->execute();
$res = $stmt->get_result();
$no = $start + 1;
while ($r = $res->fetch_assoc()) {
    $actions = '<a href="edit_operator.php?id=' . urlencode($r['id_user']) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a> ';
    $actions .= '<a href="delete_operator.php?id=' . urlencode($r['id_user']) . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Hapus operator ini?\')"><i class="fa fa-trash"></i></a>';

    $statusLabel = ($r['status'] === 'aktif') ? '<span class="badge bg-success">aktif</span>' : '<span class="badge bg-secondary">' . htmlspecialchars($r['status']) . '</span>';

    $data[] = [
        $no++,
        htmlspecialchars($r['username']),
        htmlspecialchars($r['nama_lengkap']),
        htmlspecialchars($r['role']),
        $statusLabel,
        htmlspecialchars($r['created_at']),
        $actions
    ];
}

$out = [
    'draw' => $draw,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($out);
exit;
