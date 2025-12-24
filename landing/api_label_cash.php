<?php
// landing/api_label_cash.php
// Read-only API returning aggregated payment list similar to operator/payment_list.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/database.php';

$validJenis = ['pemasukan', 'pengeluaran'];
$jenis = isset($_GET['jenis']) ? trim($_GET['jenis']) : 'pemasukan';
if (!in_array($jenis, $validJenis, true)) $jenis = 'pemasukan';

$result = ['ok' => false, 'data' => [], 'totals' => [], 'error' => null];

try {
    // totals
    $total_pemasukan = 0;
    $res_pemasukan = $conn->query("SELECT SUM(jumlah) AS total FROM pembayaran");
    if ($res_pemasukan) $total_pemasukan = $res_pemasukan->fetch_assoc()['total'] ?? 0;

    $total_pengeluaran = 0;
    $res_pengeluaran = $conn->query("SELECT SUM(jumlah) AS total FROM kas WHERE jenis = 'pengeluaran'");
    if ($res_pengeluaran) $total_pengeluaran = $res_pengeluaran->fetch_assoc()['total'] ?? 0;

    $sisa_uang = $total_pemasukan - $total_pengeluaran;

    if ($jenis === 'pengeluaran') {
        $sql = "SELECT 
                k.tanggal AS tanggal,
                kk.nama AS kategori_nama,
                1 AS payments_count,
                k.jumlah AS total_jumlah,
                k.keterangan AS keterangan_list
            FROM kas k
            LEFT JOIN kas_kategori kk ON k.id_kategori = kk.id_kategori
            WHERE k.jenis = ?
            ORDER BY k.tanggal DESC, k.id_kas DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $jenis);
    } else {
        $sql = "SELECT DATE(p.tanggal_bayar) AS tanggal,
                   p.id_kategori,
                   COALESCE(kk.nama, '-') AS kategori_nama,
                   COUNT(p.id_pembayaran) AS payments_count,
                   SUM(p.jumlah) AS total_jumlah,
                   GROUP_CONCAT(DISTINCT TRIM(COALESCE(k.keterangan, '')) SEPARATOR ' | ') AS keterangan_list
            FROM pembayaran p
            LEFT JOIN kas k ON p.id_kas = k.id_kas
            LEFT JOIN kas_kategori kk ON p.id_kategori = kk.id_kategori
            GROUP BY DATE(p.tanggal_bayar), p.id_kategori
            ORDER BY DATE(p.tanggal_bayar) DESC, kategori_nama ASC";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    $stmt->close();

    $result['ok'] = true;
    $result['data'] = $rows;
    $result['totals'] = [
        'total_pemasukan' => $total_pemasukan,
        'total_pengeluaran' => $total_pengeluaran,
        'sisa_uang' => $sisa_uang,
        'jenis' => $jenis
    ];
} catch (Exception $e) {
    $result['error'] = $e->getMessage();
}

echo json_encode($result);
