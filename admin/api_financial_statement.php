<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$action = isset($_GET['action']) ? $_GET['action'] : '';

function safeDate($s)
{
    $s = trim($s);
    if (!$s) return null;
    $t = strtotime($s);
    return $t ? date('Y-m-d', $t) : null;
}

$from = safeDate($_GET['from'] ?? null) ?? date('Y-m-01');
$to = safeDate($_GET['to'] ?? null) ?? date('Y-m-t');

if ($action === 'summary') {
    $resp = ['ok' => true];

    // opening balance: sum of all income before from minus expenses before from
    $stmtIncBefore = $conn->prepare("SELECT COALESCE(SUM(jumlah),0) AS total FROM pembayaran WHERE DATE(tanggal_bayar) < ? AND status='lunas'");
    $stmtIncBefore->bind_param('s', $from);
    $stmtIncBefore->execute();
    $incBefore = floatval($stmtIncBefore->get_result()->fetch_assoc()['total'] ?? 0);

    $stmtKasIncBefore = $conn->prepare("SELECT COALESCE(SUM(jumlah),0) AS total FROM kas WHERE DATE(tanggal) < ? AND jenis='pemasukan'");
    $stmtKasIncBefore->bind_param('s', $from);
    $stmtKasIncBefore->execute();
    $kasIncBefore = floatval($stmtKasIncBefore->get_result()->fetch_assoc()['total'] ?? 0);

    $stmtExpBefore = $conn->prepare("SELECT COALESCE(SUM(jumlah),0) AS total FROM kas WHERE DATE(tanggal) < ? AND jenis='pengeluaran'");
    $stmtExpBefore->bind_param('s', $from);
    $stmtExpBefore->execute();
    $expBefore = floatval($stmtExpBefore->get_result()->fetch_assoc()['total'] ?? 0);

    $opening = $incBefore + $kasIncBefore - $expBefore;

    // range income and expense
    $stmtRangeInc = $conn->prepare("SELECT COALESCE(SUM(jumlah),0) AS total FROM pembayaran WHERE DATE(tanggal_bayar) BETWEEN ? AND ? AND status='lunas'");
    $stmtRangeInc->bind_param('ss', $from, $to);
    $stmtRangeInc->execute();
    $rangeInc = floatval($stmtRangeInc->get_result()->fetch_assoc()['total'] ?? 0);

    $stmtRangeKasInc = $conn->prepare("SELECT COALESCE(SUM(jumlah),0) AS total FROM kas WHERE DATE(tanggal) BETWEEN ? AND ? AND jenis='pemasukan'");
    $stmtRangeKasInc->bind_param('ss', $from, $to);
    $stmtRangeKasInc->execute();
    $rangeKasInc = floatval($stmtRangeKasInc->get_result()->fetch_assoc()['total'] ?? 0);

    $stmtRangeExp = $conn->prepare("SELECT COALESCE(SUM(jumlah),0) AS total FROM kas WHERE DATE(tanggal) BETWEEN ? AND ? AND jenis='pengeluaran'");
    $stmtRangeExp->bind_param('ss', $from, $to);
    $stmtRangeExp->execute();
    $rangeExp = floatval($stmtRangeExp->get_result()->fetch_assoc()['total'] ?? 0);

    $totalIncome = $rangeInc + $rangeKasInc;
    $net = $totalIncome - $rangeExp;
    $closing = $opening + $net;

    $resp['summary'] = [
        'opening_balance' => $opening,
        'range_income' => $totalIncome,
        'range_expense' => $rangeExp,
        'net_flow' => $net,
        'closing_balance' => $closing
    ];

    // build daily labels and series
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

    // payments income per day
    $sqlInc = "SELECT DATE(tanggal_bayar) AS ddate, SUM(jumlah) AS total FROM pembayaran WHERE DATE(tanggal_bayar) BETWEEN ? AND ? AND status='lunas' GROUP BY DATE(tanggal_bayar)";
    $stmt = $conn->prepare($sqlInc);
    $stmt->bind_param('ss', $from, $to);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $k = $r['ddate'];
        if (isset($income_by_date[$k])) $income_by_date[$k] += floatval($r['total']);
    }

    // kas pemasukan per day
    $sqlKasInc = "SELECT DATE(tanggal) AS ddate, SUM(jumlah) AS total FROM kas WHERE DATE(tanggal) BETWEEN ? AND ? AND jenis='pemasukan' GROUP BY DATE(tanggal)";
    $stmt2 = $conn->prepare($sqlKasInc);
    $stmt2->bind_param('ss', $from, $to);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($r = $res2->fetch_assoc()) {
        $k = $r['ddate'];
        if (isset($income_by_date[$k])) $income_by_date[$k] += floatval($r['total']);
    }

    // expenses per day
    $sqlExp = "SELECT DATE(tanggal) AS ddate, SUM(jumlah) AS total FROM kas WHERE DATE(tanggal) BETWEEN ? AND ? AND jenis='pengeluaran' GROUP BY DATE(tanggal)";
    $stmt3 = $conn->prepare($sqlExp);
    $stmt3->bind_param('ss', $from, $to);
    $stmt3->execute();
    $res3 = $stmt3->get_result();
    while ($r = $res3->fetch_assoc()) {
        $k = $r['ddate'];
        if (isset($expense_by_date[$k])) $expense_by_date[$k] = floatval($r['total']);
    }

    $resp['chart'] = [
        'labels' => array_values($labels),
        'income' => array_values($income_by_date),
        'expense' => array_values($expense_by_date)
    ];

    // category breakdown: expenses per category
    $sqlCat = "SELECT kk.nama AS kategori, SUM(k.jumlah) AS total FROM kas k LEFT JOIN kas_kategori kk ON k.id_kategori = kk.id_kategori WHERE DATE(k.tanggal) BETWEEN ? AND ? AND k.jenis='pengeluaran' GROUP BY kk.nama ORDER BY total DESC LIMIT 20";
    $stmtCat = $conn->prepare($sqlCat);
    $stmtCat->bind_param('ss', $from, $to);
    $stmtCat->execute();
    $rc = $stmtCat->get_result();
    $labelsCat = [];
    $dataCat = [];
    while ($r = $rc->fetch_assoc()) {
        $labelsCat[] = $r['kategori'] ?: 'Uncategorized';
        $dataCat[] = floatval($r['total']);
    }
    $resp['category'] = ['labels' => $labelsCat, 'data' => $dataCat];

    echo json_encode($resp);
    exit;
}

if ($action === 'transactions') {
    // return combined list: pembayaran (income) and kas (expense)
    $fromP = $from;
    $toP = $to;
    $rows = [];

    $sqlP = "SELECT p.id_pembayaran AS id, 'pembayaran' AS type, p.tanggal_bayar AS tanggal, COALESCE(u.nama_lengkap,'') AS nama, kk.nama AS kategori, COALESCE(p.jumlah,k.jumlah) AS jumlah, p.bukti, p.status, COALESCE(u_cre.username, p.ditambahkan_oleh) AS operator FROM pembayaran p LEFT JOIN user u ON p.id_user=u.id_user LEFT JOIN kas_kategori kk ON p.id_kategori=kk.id_kategori LEFT JOIN kas k ON p.id_kas=k.id_kas LEFT JOIN user u_cre ON p.ditambahkan_oleh = u_cre.id_user WHERE DATE(p.tanggal_bayar) BETWEEN ? AND ?";
    $stp = $conn->prepare($sqlP);
    $stp->bind_param('ss', $fromP, $toP);
    $stp->execute();
    $rp = $stp->get_result();
    while ($r = $rp->fetch_assoc()) {
        $r['jumlah'] = floatval($r['jumlah']);
        $rows[] = $r;
    }

    $sqlK = "SELECT k.id_kas AS id, 'kas' AS type, k.tanggal AS tanggal, '' AS nama, kk.nama AS kategori, k.jumlah AS jumlah, '' AS bukti, k.jenis AS status, COALESCE(u_cre.username, k.dibuat_oleh) AS operator FROM kas k LEFT JOIN kas_kategori kk ON k.id_kategori=kk.id_kategori LEFT JOIN user u_cre ON k.dibuat_oleh = u_cre.id_user WHERE DATE(k.tanggal) BETWEEN ? AND ?";
    $stk = $conn->prepare($sqlK);
    $stk->bind_param('ss', $fromP, $toP);
    $stk->execute();
    $rk = $stk->get_result();
    while ($r = $rk->fetch_assoc()) {
        $r['jumlah'] = floatval($r['jumlah']);
        $rows[] = $r;
    }

    // sort by tanggal asc
    usort($rows, function ($a, $b) {
        return strcmp($a['tanggal'] ?? '', $b['tanggal'] ?? '');
    });

    echo json_encode(['ok' => true, 'data' => $rows]);
    exit;
}

if ($action === 'export_csv') {
    $fromP = $from;
    $toP = $to;
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="financial_statement_' . date('Ymd_His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Tipe', 'Tanggal', 'Nama', 'Kategori', 'Keterangan', 'Jumlah', 'Operator', 'Status']);

    // reuse transactions code
    $sqlP = "SELECT 'pembayaran' AS type, p.tanggal_bayar AS tanggal, COALESCE(u.nama_lengkap,'') AS nama, kk.nama AS kategori, COALESCE(k.keterangan,'') AS keterangan, COALESCE(p.jumlah,k.jumlah) AS jumlah, COALESCE(u_cre.username, p.ditambahkan_oleh) AS operator, p.status FROM pembayaran p LEFT JOIN user u ON p.id_user=u.id_user LEFT JOIN kas_kategori kk ON p.id_kategori=kk.id_kategori LEFT JOIN kas k ON p.id_kas=k.id_kas LEFT JOIN user u_cre ON p.ditambahkan_oleh = u_cre.id_user WHERE DATE(p.tanggal_bayar) BETWEEN ? AND ?";
    $stp = $conn->prepare($sqlP);
    $stp->bind_param('ss', $fromP, $toP);
    $stp->execute();
    $rp = $stp->get_result();
    while ($r = $rp->fetch_assoc()) {
        fputcsv($out, [$r['type'], $r['tanggal'], $r['nama'], $r['kategori'], $r['keterangan'], $r['jumlah'], $r['operator'], $r['status']]);
    }

    $sqlK = "SELECT 'kas' AS type, k.tanggal AS tanggal, '' AS nama, kk.nama AS kategori, k.keterangan AS keterangan, k.jumlah AS jumlah, COALESCE(u_cre.username, k.dibuat_oleh) AS operator, k.jenis AS status FROM kas k LEFT JOIN kas_kategori kk ON k.id_kategori=kk.id_kategori LEFT JOIN user u_cre ON k.dibuat_oleh = u_cre.id_user WHERE DATE(k.tanggal) BETWEEN ? AND ?";
    $stk = $conn->prepare($sqlK);
    $stk->bind_param('ss', $fromP, $toP);
    $stk->execute();
    $rk = $stk->get_result();
    while ($r = $rk->fetch_assoc()) {
        fputcsv($out, [$r['type'], $r['tanggal'], $r['nama'], $r['kategori'], $r['keterangan'], $r['jumlah'], $r['operator'], $r['status']]);
    }
    fclose($out);
    exit;
}

if ($action === 'export_pdf') {
    // redirect to existing operator PDF exporter if available
    $target = '../operator/export_pdf.php?from=' . urlencode($from) . '&to=' . urlencode($to);
    header('Location: ' . $target);
    exit;
}

echo json_encode(['ok' => false, 'error' => 'unknown action']);
exit;
