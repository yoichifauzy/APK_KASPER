<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);
include '../config/database.php';

$report_type = $_GET['report'] ?? 'cash_flow';

// Set headers to force download
header('Content-Type: text/csv; charset=utf-ax');
header('Content-Disposition: attachment; filename="laporan_' . $report_type . '_' . date('Y-m-d') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// --- LOGIC BASED ON REPORT TYPE ---

if ($report_type === 'cash_flow') {
    // --- Re-use logic from cash_flow_report.php ---
    $kategori_list = [];
    $result_kategori = $conn->query("SELECT id_kategori, nama FROM kas_kategori");
    if ($result_kategori) {
        while ($row = $result_kategori->fetch_assoc()) { $kategori_list[$row['id_kategori']] = $row['nama']; }
    }

    $periode = $_GET['periode'] ?? 'bulanan';
    $filter_kategori = $_GET['kategori'] ?? [];
    $where_clauses = [];
    $params = [];
    $types = '';

    switch ($periode) {
        case 'harian':
            $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
            $where_clauses[] = "DATE(tanggal) = ?";
            $params[] = $tanggal; $types .= 's';
            break;
        case 'mingguan':
            $tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-d', strtotime('monday this week'));
            $tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d', strtotime('sunday this week'));
            $where_clauses[] = "DATE(tanggal) BETWEEN ? AND ?";
            $params[] = $tanggal_awal; $params[] = $tanggal_akhir; $types .= 'ss';
            break;
        case 'tahunan':
            $tahun = $_GET['tahun'] ?? date('Y');
            $where_clauses[] = "YEAR(tanggal) = ?";
            $params[] = $tahun; $types .= 's';
            break;
        default:
            $bulan = $_GET['bulan'] ?? date('m');
            $tahun = $_GET['tahun_bulan'] ?? date('Y');
            $where_clauses[] = "MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
            $params[] = $bulan; $params[] = $tahun; $types .= 'ss';
            break;
    }

    if (!empty($filter_kategori) && is_array($filter_kategori)) {
        $in_placeholder = implode(',', array_fill(0, count($filter_kategori), '?'));
        $where_clauses[] = "id_kategori IN ($in_placeholder)";
        foreach ($filter_kategori as $cat_id) { $params[] = $cat_id; $types .= 'i'; }
    }

    $where_clauses_pembayaran = [];
    foreach ($where_clauses as $clause) {
        $where_clauses_pembayaran[] = str_replace('tanggal', 'tanggal_bayar', $clause);
    }

    $where_sql_pembayaran = 'WHERE ' . implode(' AND ', $where_clauses_pembayaran);
    $where_sql_kas = 'WHERE jenis = \'pengeluaran\' AND ' . implode(' AND ', $where_clauses);

    $sql = "SELECT tanggal_bayar as tanggal, 'Iuran Kas' as keterangan, id_kategori, jumlah as pemasukan, 0 as pengeluaran FROM pembayaran {$where_sql_pembayaran} UNION ALL SELECT tanggal, keterangan, id_kategori, 0 as pemasukan, jumlah as pengeluaran FROM kas {$where_sql_kas} ORDER BY tanggal ASC";
    
    $union_params = array_merge($params, $params);
    $union_types = $types . $types;
    $stmt = $conn->prepare($sql);

    // Write header
    fputcsv($output, ['Tanggal', 'Kategori', 'Keterangan', 'Pemasukan', 'Pengeluaran']);
    
    if ($stmt) {
        $stmt->bind_param($union_types, ...$union_params);
        $stmt->execute();
        $result = $stmt->get_result();
        $total_pemasukan = 0;
        $total_pengeluaran = 0;
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                date('Y-m-d', strtotime($row['tanggal'])),
                $kategori_list[$row['id_kategori']] ?? 'Tidak Diketahui',
                $row['keterangan'],
                $row['pemasukan'],
                $row['pengeluaran']
            ]);
            $total_pemasukan += $row['pemasukan'];
            $total_pengeluaran += $row['pengeluaran'];
        }
        // Write totals
        fputcsv($output, []); // empty line
        fputcsv($output, ['Total Pemasukan', $total_pemasukan]);
        fputcsv($output, ['Total Pengeluaran', $total_pengeluaran]);
        fputcsv($output, ['Arus Kas Bersih', $total_pemasukan - $total_pengeluaran]);
        $stmt->close();
    }

} elseif ($report_type === 'balance_sheet') {
    // --- Re-use logic from balance_sheet.php ---
    $tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-01');
    $tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-t');

    $sql_pemasukan_sebelum = "SELECT SUM(jumlah) as total FROM pembayaran WHERE DATE(tanggal_bayar) < ?";
    $stmt1 = $conn->prepare($sql_pemasukan_sebelum);
    $stmt1->bind_param('s', $tanggal_awal);
    $stmt1->execute();
    $pemasukan_sebelum = $stmt1->get_result()->fetch_assoc()['total'] ?? 0;

    $sql_pengeluaran_sebelum = "SELECT SUM(jumlah) as total FROM kas WHERE jenis = 'pengeluaran' AND DATE(tanggal) < ?";
    $stmt2 = $conn->prepare($sql_pengeluaran_sebelum);
    $stmt2->bind_param('s', $tanggal_awal);
    $stmt2->execute();
    $pengeluaran_sebelum = $stmt2->get_result()->fetch_assoc()['total'] ?? 0;
    $saldo_awal = $pemasukan_sebelum - $pengeluaran_sebelum;

    $sql_pemasukan_periode = "SELECT SUM(jumlah) as total FROM pembayaran WHERE DATE(tanggal_bayar) BETWEEN ? AND ?";
    $stmt3 = $conn->prepare($sql_pemasukan_periode);
    $stmt3->bind_param('ss', $tanggal_awal, $tanggal_akhir);
    $stmt3->execute();
    $total_pemasukan = $stmt3->get_result()->fetch_assoc()['total'] ?? 0;

    $sql_pengeluaran_periode = "SELECT SUM(jumlah) as total FROM kas WHERE jenis = 'pengeluaran' AND DATE(tanggal) BETWEEN ? AND ?";
    $stmt4 = $conn->prepare($sql_pengeluaran_periode);
    $stmt4->bind_param('ss', $tanggal_awal, $tanggal_akhir);
    $stmt4->execute();
    $total_pengeluaran = $stmt4->get_result()->fetch_assoc()['total'] ?? 0;
    $saldo_akhir = $saldo_awal + $total_pemasukan - $total_pengeluaran;

    // Write to CSV
    fputcsv($output, ['Laporan Neraca Periode', date('d M Y', strtotime($tanggal_awal)) . ' - ' . date('d M Y', strtotime($tanggal_akhir))]);
    fputcsv($output, []); // empty line
    fputcsv($output, ['Item', 'Jumlah']);
    fputcsv($output, ['Saldo Awal Periode', $saldo_awal]);
    fputcsv($output, ['Total Pemasukan (Aset)', $total_pemasukan]);
    fputcsv($output, ['Total Pengeluaran (Kewajiban)', $total_pengeluaran]);
    fputcsv($output, ['Saldo Akhir Periode', $saldo_akhir]);
    
    $stmt1->close();
    $stmt2->close();
    $stmt3->close();
    $stmt4->close();
}

fclose($output);
$conn->close();
exit();
?>
