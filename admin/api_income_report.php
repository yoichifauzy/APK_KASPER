<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

function safeDate($s)
{
    $s = trim($s);
    if (!$s) return null;
    $t = strtotime($s);
    return $t ? date('Y-m-d', $t) : null;
}

$from = safeDate($_GET['from'] ?? null) ?? date('Y-m-d', strtotime('-29 days'));
$to = safeDate($_GET['to'] ?? null) ?? date('Y-m-d');
$category = isset($_GET['category']) && $_GET['category'] !== '' ? $_GET['category'] : null;

if ($action === 'summary') {
    // summary KPIs and chart data
    $resp = ['ok' => true];

    // Use operator-style KPIs (overall totals like operator dashboard)
    // total income (all-time)
    $r_total = $conn->query("SELECT SUM(jumlah) AS total, COUNT(*) AS cnt, AVG(jumlah) AS avg FROM pembayaran");
    $r_total_row = $r_total ? $r_total->fetch_assoc() : ['total' => 0, 'cnt' => 0, 'avg' => 0];
    $resp['summary'] = ['total' => floatval($r_total_row['total'] ?: 0), 'avg' => floatval($r_total_row['avg'] ?: 0), 'count' => intval($r_total_row['cnt'] ?: 0)];

    // month (this month) - operator style: current month
    $mstart = date('Y-m-01');
    $mend = date('Y-m-t');
    $r_month = $conn->query("SELECT SUM(jumlah) AS total, COUNT(*) AS cnt, AVG(jumlah) AS avg FROM pembayaran WHERE DATE_FORMAT(tanggal_bayar, '%Y-%m') = '" . date('Y-m') . "'");
    $r_month_row = $r_month ? $r_month->fetch_assoc() : ['total' => 0, 'cnt' => 0, 'avg' => 0];
    $resp['summary']['month'] = floatval($r_month_row['total'] ?: 0);
    $resp['summary']['month_count'] = intval($r_month_row['cnt'] ?: 0);
    $resp['summary']['month_avg'] = floatval($r_month_row['avg'] ?: 0);
    $resp['summary']['month_label'] = date('F Y', strtotime($mstart));

    // today (current date)
    $today = date('Y-m-d');
    $r_today = $conn->query("SELECT SUM(jumlah) AS total FROM pembayaran WHERE DATE(tanggal_bayar) = '" . $today . "'");
    $r_today_row = $r_today ? $r_today->fetch_assoc() : ['total' => 0];
    $resp['summary']['today'] = floatval($r_today_row['total'] ?: 0);

    // chart: group by date in range (daily)
    // build daily labels between from..to
    $labels = [];
    $income_by_date = [];
    $expense_by_date = [];
    $period = new DatePeriod(new DateTime($from), new DateInterval('P1D'), (new DateTime($to))->modify('+1 day'));
    foreach ($period as $dt) {
        $d = $dt->format('Y-m-d');
        $labels[] = $d;
        $income_by_date[$d] = 0;
        $expense_by_date[$d] = 0;
    }

    // income per day
    // income per day: normalize to DATE(tanggal_bayar) so grouping matches daily labels
    $sqlc = "SELECT DATE(tanggal_bayar) AS ddate, SUM(jumlah) as total FROM pembayaran WHERE DATE(tanggal_bayar) BETWEEN ? AND ? AND status='lunas'";
    $pc = [$from, $to];
    if ($category) {
        $sqlc .= " AND id_kategori=?";
        $pc[] = $category;
    }
    $sqlc .= " GROUP BY DATE(tanggal_bayar) ORDER BY DATE(tanggal_bayar)";
    $stmtc = $conn->prepare($sqlc);
    $stmtc->bind_param(str_repeat('s', count($pc)), ...$pc);
    $stmtc->execute();
    $rc = $stmtc->get_result();
    while ($row = $rc->fetch_assoc()) {
        $k = $row['ddate'];
        if (isset($income_by_date[$k])) $income_by_date[$k] = floatval($row['total']);
    }

    // expense per day (from kas table, tanggal)
    $sqle = "SELECT DATE(tanggal) as ddate, SUM(jumlah) as total FROM kas WHERE DATE(tanggal) BETWEEN ? AND ? AND jenis='pengeluaran'";
    $pe = [$from, $to];
    if ($category) {
        $sqle .= " AND id_kategori=?";
        $pe[] = $category;
    }
    $sqle .= " GROUP BY DATE(tanggal) ORDER BY DATE(tanggal)";
    $stmte = $conn->prepare($sqle);
    $stmte->bind_param(str_repeat('s', count($pe)), ...$pe);
    $stmte->execute();
    $re = $stmte->get_result();
    while ($row = $re->fetch_assoc()) {
        $k = $row['ddate'];
        if (isset($expense_by_date[$k])) $expense_by_date[$k] = floatval($row['total']);
    }

    $resp['chart'] = [
        'labels' => array_values($labels),
        'income' => array_values($income_by_date),
        'expense' => array_values($expense_by_date)
    ];

    // range totals for doughnut and summary
    $stmtRangeInc = $conn->prepare("SELECT SUM(jumlah) AS total FROM pembayaran WHERE DATE(tanggal_bayar) BETWEEN ? AND ? AND status='lunas'");
    $stmtRangeInc->bind_param('ss', $from, $to);
    $stmtRangeInc->execute();
    $rri = $stmtRangeInc->get_result()->fetch_assoc();
    $resp['summary']['range_income'] = floatval($rri['total'] ?: 0);

    $stmtRangeExp = $conn->prepare("SELECT SUM(jumlah) AS total FROM kas WHERE DATE(tanggal) BETWEEN ? AND ? AND jenis='pengeluaran'");
    $stmtRangeExp->bind_param('ss', $from, $to);
    $stmtRangeExp->execute();
    $rre = $stmtRangeExp->get_result()->fetch_assoc();
    $resp['summary']['range_expense'] = floatval($rre['total'] ?: 0);

    // operator breakdown (sum of pembayaran per operator user with role 'operator')
    // detect creator column
    $op_col = null;
    $c1 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'ditambahkan_oleh'");
    if ($c1 && $c1->num_rows > 0) $op_col = 'ditambahkan_oleh';
    else {
        $c2 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'dibuat_oleh'");
        if ($c2 && $c2->num_rows > 0) $op_col = 'dibuat_oleh';
    }
    if ($op_col) {
        $sqlop = "SELECT COALESCE(u.username, CAST(p." . $op_col . " AS CHAR)) AS operator_name, SUM(p.jumlah) AS total FROM pembayaran p LEFT JOIN user u ON p." . $op_col . " = u.id_user WHERE p.tanggal_bayar BETWEEN ? AND ? GROUP BY operator_name ORDER BY total DESC LIMIT 20";
        $stmtop = $conn->prepare($sqlop);
        $stmtop->bind_param('ss', $from, $to);
        $stmtop->execute();
        $rop = $stmtop->get_result();
        $op_labels = [];
        $op_data = [];
        while ($r = $rop->fetch_assoc()) {
            $op_labels[] = $r['operator_name'] ?: '-';
            $op_data[] = floatval($r['total']);
        }
        $resp['operators'] = ['labels' => $op_labels, 'data' => $op_data];
    } else {
        $resp['operators'] = ['labels' => [], 'data' => []];
    }

    // category breakdown
    // category breakdown: pengeluaran per kategori (from kas table)
    $sqlcat = "SELECT kk.nama AS kategori, SUM(k.jumlah) AS total FROM kas k LEFT JOIN kas_kategori kk ON k.id_kategori = kk.id_kategori WHERE DATE(k.tanggal) BETWEEN ? AND ? AND k.jenis='pengeluaran'";
    $pcat = [$from, $to];
    if ($category) {
        $sqlcat .= " AND k.id_kategori=?";
        $pcat[] = $category;
    }
    $sqlcat .= " GROUP BY kk.nama ORDER BY total DESC LIMIT 10";
    $stmtcat = $conn->prepare($sqlcat);
    $stmtcat->bind_param(str_repeat('s', count($pcat)), ...$pcat);
    $stmtcat->execute();
    $rcat = $stmtcat->get_result();
    $labels = [];
    $cd = [];
    while ($r = $rcat->fetch_assoc()) {
        $labels[] = $r['kategori'] ?: 'Uncategorized';
        $cd[] = floatval($r['total']);
    }
    $resp['category'] = ['labels' => $labels, 'data' => $cd];

    echo json_encode($resp);
    exit;
}

if ($action === 'transactions') {
    // DataTables server-side processing
    $draw = intval($_GET['draw'] ?? 0);
    $start = intval($_GET['start'] ?? 0);
    $length = intval($_GET['length'] ?? 10);

    // base where
    $where = "WHERE p.tanggal_bayar BETWEEN ? AND ? AND p.status='lunas'";
    $params = [$from, $to];
    $types = 'ss';
    if ($category) {
        $where .= " AND p.id_kategori=?";
        $params[] = $category;
        $types .= 's';
    }

    // total records
    $countSql = "SELECT COUNT(*) as cnt FROM pembayaran p $where";
    $stmt = $conn->prepare($countSql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $tot = $stmt->get_result()->fetch_assoc()['cnt'];

    // data
    $dataSql = "SELECT p.id_pembayaran, p.tanggal_bayar, u.nama_lengkap, COALESCE(k.nama,'') as kategori, p.jumlah, p.metode, p.status, p.ditambahkan_oleh as operator, p.bukti FROM pembayaran p LEFT JOIN users u ON p.id_user=u.id_user LEFT JOIN kas_kategori k ON p.id_kategori=k.id_kategori $where ORDER BY p.tanggal_bayar DESC LIMIT ?, ?";
    $stmt2 = $conn->prepare($dataSql);
    // bind params + start,length
    $bindTypes = $types . 'ii';
    $bindParams = array_merge($params, [$start, $length]);
    $stmt2->bind_param($bindTypes, ...$bindParams);
    $stmt2->execute();
    $res = $stmt2->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = [
            'id_pembayaran' => $r['id_pembayaran'],
            'tanggal_bayar' => $r['tanggal_bayar'],
            'nama_lengkap' => $r['nama_lengkap'],
            'kategori' => $r['kategori'],
            'jumlah' => floatval($r['jumlah']),
            'metode' => $r['metode'],
            'status' => $r['status'],
            'operator' => $r['operator'],
            'bukti' => $r['bukti']
        ];
    }

    echo json_encode(['draw' => $draw, 'recordsTotal' => intval($tot), 'recordsFiltered' => intval($tot), 'data' => $rows]);
    exit;
}

// simple transactions list (for client-side DataTable) — reuse operator query
if ($action === 'transactions_simple') {
    // build creator/operator display similar to operator/pembayaran
    $creator_join = '';
    $creator_select = "'' AS ditambahkan_oleh_display";
    $colDita = null;
    $c1 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'ditambahkan_oleh'");
    if ($c1 && $c1->num_rows > 0) $colDita = 'ditambahkan_oleh';
    else {
        $c2 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'dibuat_oleh'");
        if ($c2 && $c2->num_rows > 0) $colDita = 'dibuat_oleh';
    }
    if ($colDita) {
        // left join user table alias for creator (try both user and users tables)
        // prefer 'user' table alias u_cre; if not present, COALESCE will fall back to raw column value
        $creator_join = " LEFT JOIN user u_cre ON p." . $colDita . " = u_cre.id_user";
        $creator_select = "COALESCE(u_cre.username, CAST(p." . $colDita . " AS CHAR)) AS ditambahkan_oleh_display";
    }

    $sql = "SELECT p.id_pembayaran, p.tanggal_bayar, p.status, COALESCE(p.jumlah, k.jumlah) AS jumlah, p.bukti,
               kk.nama AS kategori_nama,
               u_siswa.nama_lengkap AS siswa_nama,
               u_siswa.username AS siswa_username,
               k.keterangan AS kas_ket, " . $creator_select . "
            FROM pembayaran p
            LEFT JOIN user u_siswa ON p.id_user = u_siswa.id_user
            LEFT JOIN kas k ON p.id_kas = k.id_kas
            LEFT JOIN kas_kategori kk ON p.id_kategori = kk.id_kategori" . $creator_join . "
            WHERE p.tanggal_bayar BETWEEN ? AND ?
            ORDER BY p.tanggal_bayar ASC, p.id_pembayaran ASC";
    $params = [$from, $to];
    if ($category) {
        // filter by category if provided
        $sql = str_replace("WHERE p.tanggal_bayar BETWEEN ? AND ?", "WHERE p.tanggal_bayar BETWEEN ? AND ? AND p.id_kategori=?", $sql);
        $params[] = $category;
    }
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($params));
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = [
            'id_pembayaran' => $r['id_pembayaran'],
            'tanggal_bayar' => $r['tanggal_bayar'],
            'nama_lengkap' => $r['siswa_nama'],
            'kategori' => $r['kategori_nama'],
            'jumlah' => floatval($r['jumlah']),
            'status' => $r['status'],
            'operator' => $r['ditambahkan_oleh_display'] ?? ($r['dibuat_oleh'] ?? ''),
            'bukti' => $r['bukti']
        ];
    }
    echo json_encode(['ok' => true, 'data' => $rows]);
    exit;
}

// export csv or pdf (simple csv implementation)
if (isset($_GET['action']) && $_GET['action'] === 'export_csv') {
    $fname = 'income_report_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $fname . '"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Date', 'Name', 'Category', 'Amount', 'Method', 'Status', 'Operator']);
    $sql = "SELECT p.id_pembayaran, p.tanggal_bayar, u.nama_lengkap, COALESCE(k.nama,'') as kategori, p.jumlah, p.metode, p.status, p.ditambahkan_oleh as operator FROM pembayaran p LEFT JOIN users u ON p.id_user=u.id_user LEFT JOIN kas_kategori k ON p.id_kategori=k.id_kategori WHERE p.tanggal_bayar BETWEEN ? AND ? AND p.status='lunas'";
    $p = [$from, $to];
    if ($category) {
        $sql .= " AND p.id_kategori=?";
        $p[] = $category;
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($p)), ...$p);
    $stmt->execute();
    $r = $stmt->get_result();
    while ($row = $r->fetch_assoc()) {
        fputcsv($out, [$row['id_pembayaran'], $row['tanggal_bayar'], $row['nama_lengkap'], $row['kategori'], $row['jumlah'], $row['metode'], $row['status'], $row['operator']]);
    }
    fclose($out);
    exit;
}

// simple PDF link could be added using existing export_pdf.php in operator folder, redirect there with params
if (isset($_GET['action']) && $_GET['action'] === 'export_pdf') {
    // redirect to operator/export_pdf.php if exists, else return JSON error
    $target = '../operator/export_pdf.php?from=' . urlencode($from) . '&to=' . urlencode($to) . '&category=' . urlencode($category);
    header('Location: ' . $target);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'unknown action']);
exit;
