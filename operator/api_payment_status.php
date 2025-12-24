<?php
// API: Return per-user per-month payment status for last N months
require_once '../config/cek_login.php';
// allow admin, operator and user to fetch this report for dashboard parity
otorisasi(['admin', 'operator', 'user']);
header('Content-Type: application/json; charset=utf-8');

include '../config/database.php';

$period = isset($_GET['period']) ? intval($_GET['period']) : 3;
if ($period !== 3 && $period !== 6) $period = 3;

// Optional start month/year: if provided, build months starting from that month forward
$start_month = isset($_GET['start_month']) ? intval($_GET['start_month']) : null;
$start_year = isset($_GET['start_year']) ? intval($_GET['start_year']) : null;
$months = [];
if ($start_month && $start_year && $start_month >= 1 && $start_month <= 12) {
    // Build months starting at provided month/year (inclusive) forward for $period months
    $dt = DateTime::createFromFormat('!Y-m', sprintf('%04d-%02d', $start_year, $start_month));
    if (!$dt) {
        $dt = new DateTime();
    }
    for ($i = 0; $i < $period; $i++) {
        $m = clone $dt;
        $m->modify("+{$i} months");
        $months[] = [
            'year' => intval($m->format('Y')),
            'month' => intval($m->format('m')),
            'label' => $m->format('M Y')
        ];
    }
} else {
    // default: last $period months ending this month (oldest -> newest)
    for ($i = $period - 1; $i >= 0; $i--) {
        $dt = new DateTime();
        $dt->modify("-{$i} months");
        $months[] = [
            'year' => intval($dt->format('Y')),
            'month' => intval($dt->format('m')),
            'label' => $dt->format('M Y')
        ];
    }
}

// Fetch users (active students)
$users = [];
$res = $conn->query("SELECT id_user, COALESCE(username, nama_lengkap, id_user) AS label FROM user WHERE role = 'user' AND status='aktif' ORDER BY username ASC");
while ($r = $res->fetch_assoc()) {
    $users[] = $r;
}

// color mapping (user-facing mapping: lunas=green, telat=blue, belum=red)
$colors = [
    'lunas' => '#2ecc71',
    'telat' => '#3498db',
    'belum' => '#e74c3c'
];

$datasets = [];
foreach ($months as $m) {
    $bgColors = [];
    $data = [];
    foreach ($users as $u) {
        // default status = belum
        $status = 'belum';
        $stmt = $conn->prepare("SELECT status FROM pembayaran WHERE id_user = ? AND MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ?");
        $stmt->bind_param('iii', $u['id_user'], $m['month'], $m['year']);
        $stmt->execute();
        $resS = $stmt->get_result();
        $foundStatuses = [];
        while ($row = $resS->fetch_assoc()) {
            $foundStatuses[] = $row['status'];
        }
        $stmt->close();

        if (!empty($foundStatuses)) {
            // precedence: telat > lunas > proses
            if (in_array('telat', $foundStatuses)) {
                $status = 'telat';
            } elseif (in_array('lunas', $foundStatuses)) {
                $status = 'lunas';
            } else {
                // treat 'proses' or others as 'belum'
                $status = 'belum';
            }
        }

        $bgColors[] = $colors[$status] ?? $colors['belum'];
        // each segment has value 1 so stacked height equals number of months
        $data[] = 1;
    }

    $datasets[] = [
        'label' => $m['label'],
        'data' => $data,
        'backgroundColor' => $bgColors,
        'borderColor' => array_fill(0, count($users), '#ffffff'),
        'borderWidth' => 1
    ];
}

$out = [
    'period' => $period,
    'months' => array_column($months, 'label'),
    'users' => array_column($users, 'label'),
    'datasets' => $datasets
];

echo json_encode($out);
exit;
