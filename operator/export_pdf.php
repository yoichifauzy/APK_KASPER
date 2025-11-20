<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);
require_once '../config/database.php';
require_once '../vendor/tcpdf/tcpdf.php';

// Custom PDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 15, 'Laporan Keuangan - KASPER', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 15, 'Dibuat pada: ' . date('d/m/Y H:i'), 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Line(15, 20, $this->getPageWidth() - 15, 20);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('KASPER System');
$pdf->SetTitle('Laporan Keuangan');
$pdf->SetSubject('Laporan Keuangan KASPER');

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// --- LOGIC BASED ON REPORT TYPE ---
$report_type = $_GET['report'] ?? 'cash_flow';

if ($report_type === 'cash_flow') {
    // --- Re-use logic from cash_flow_report.php ---
    $kategori_list = [];
    $result_kategori = $conn->query("SELECT id_kategori, nama FROM kas_kategori");
    if ($result_kategori) { while ($row = $result_kategori->fetch_assoc()) { $kategori_list[$row['id_kategori']] = $row['nama']; } }

    $periode = $_GET['periode'] ?? 'bulanan';
    // ... (rest of the filtering logic is identical)
    $filter_kategori = $_GET['kategori'] ?? [];
    $where_clauses = []; $params = []; $types = '';
    switch ($periode) {
        case 'harian': $tanggal = $_GET['tanggal'] ?? date('Y-m-d'); $where_clauses[] = "DATE(tanggal) = ?"; $params[] = $tanggal; $types .= 's'; break;
        case 'mingguan': $tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-d', strtotime('monday this week')); $tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d', strtotime('sunday this week')); $where_clauses[] = "DATE(tanggal) BETWEEN ? AND ?"; $params[] = $tanggal_awal; $params[] = $tanggal_akhir; $types .= 'ss'; break;
        case 'tahunan': $tahun = $_GET['tahun'] ?? date('Y'); $where_clauses[] = "YEAR(tanggal) = ?"; $params[] = $tahun; $types .= 's'; break;
        default: $bulan = $_GET['bulan'] ?? date('m'); $tahun = $_GET['tahun_bulan'] ?? date('Y'); $where_clauses[] = "MONTH(tanggal) = ? AND YEAR(tanggal) = ?"; $params[] = $bulan; $params[] = $tahun; $types .= 'ss'; break;
    }
    if (!empty($filter_kategori) && is_array($filter_kategori)) { $in_placeholder = implode(',', array_fill(0, count($filter_kategori), '?')); $where_clauses[] = "id_kategori IN ($in_placeholder)"; foreach ($filter_kategori as $cat_id) { $params[] = $cat_id; $types .= 'i'; } }
    
    $where_clauses_pembayaran = [];
    foreach ($where_clauses as $clause) {
        $where_clauses_pembayaran[] = str_replace('tanggal', 'tanggal_bayar', $clause);
    }

    $where_sql_pembayaran = 'WHERE ' . implode(' AND ', $where_clauses_pembayaran);
    $where_sql_kas = 'WHERE jenis = \'pengeluaran\' AND ' . implode(' AND ', $where_clauses);

    $sql = "SELECT tanggal_bayar as tanggal, 'Iuran Kas' as keterangan, id_kategori, jumlah as pemasukan, 0 as pengeluaran FROM pembayaran {$where_sql_pembayaran} UNION ALL SELECT tanggal, keterangan, id_kategori, 0 as pemasukan, jumlah as pengeluaran FROM kas {$where_sql_kas} ORDER BY tanggal ASC";
    $union_params = array_merge($params, $params); $union_types = $types . $types;
    $stmt = $conn->prepare($sql);

    // Build HTML for PDF
    $html = '<h3>Laporan Arus Kas</h3>';
    $html .= '<p>Periode Laporan: ' . htmlspecialchars($periode) . '</p>';
    $html .= '<table border="1" cellpadding="4">
                <thead><tr style="background-color:#f2f2f2;font-weight:bold;"><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th>Pemasukan</th><th>Pengeluaran</th></tr></thead>
                <tbody>';

    if ($stmt) {
        $stmt->bind_param($union_types, ...$union_params);
        $stmt->execute();
        $result = $stmt->get_result();
        $total_pemasukan = 0;
        $total_pengeluaran = 0;
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $html .= '<tr>
                            <td>' . date('d/m/Y', strtotime($row['tanggal'])) . '</td>
                            <td>' . htmlspecialchars($kategori_list[$row['id_kategori']] ?? '-') . '</td>
                            <td>' . htmlspecialchars($row['keterangan']) . '</td>
                            <td align="right">' . ($row['pemasukan'] > 0 ? number_format($row['pemasukan'], 0, ',', '.') : '-') . '</td>
                            <td align="right">' . ($row['pengeluaran'] > 0 ? number_format($row['pengeluaran'], 0, ',', '.') : '-') . '</td>
                          </tr>';
                $total_pemasukan += $row['pemasukan'];
                $total_pengeluaran += $row['pengeluaran'];
            }
        } else {
            $html .= '<tr><td colspan="5" align="center">Tidak ada data.</td></tr>';
        }
        $html .= '</tbody><tfoot>
                    <tr style="font-weight:bold;">
                        <td colspan="3" align="right">Total</td>
                        <td align="right">' . number_format($total_pemasukan, 0, ',', '.') . '</td>
                        <td align="right">' . number_format($total_pengeluaran, 0, ',', '.') . '</td>
                    </tr>
                    <tr style="font-weight:bold;background-color:#f2f2f2;">
                        <td colspan="3" align="right">Arus Kas Bersih</td>
                        <td colspan="2" align="center">' . number_format($total_pemasukan - $total_pengeluaran, 0, ',', '.') . '</td>
                    </tr>
                  </tfoot></table>';
        $stmt->close();
    }
    $pdf->writeHTML($html, true, false, true, false, '');

} elseif ($report_type === 'balance_sheet') {
    // --- Re-use logic from balance_sheet.php ---
    $tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-01');
    $tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-t');

    $sql_pemasukan_sebelum = "SELECT SUM(jumlah) as total FROM pembayaran WHERE DATE(tanggal_bayar) < ?";
    $stmt1 = $conn->prepare($sql_pemasukan_sebelum); $stmt1->bind_param('s', $tanggal_awal); $stmt1->execute();
    $pemasukan_sebelum = $stmt1->get_result()->fetch_assoc()['total'] ?? 0;

    $sql_pengeluaran_sebelum = "SELECT SUM(jumlah) as total FROM kas WHERE jenis = 'pengeluaran' AND DATE(tanggal) < ?";
    $stmt2 = $conn->prepare($sql_pengeluaran_sebelum); $stmt2->bind_param('s', $tanggal_awal); $stmt2->execute();
    $pengeluaran_sebelum = $stmt2->get_result()->fetch_assoc()['total'] ?? 0;
    $saldo_awal = $pemasukan_sebelum - $pengeluaran_sebelum;

    $sql_pemasukan_periode = "SELECT SUM(jumlah) as total FROM pembayaran WHERE DATE(tanggal_bayar) BETWEEN ? AND ?";
    $stmt3 = $conn->prepare($sql_pemasukan_periode); $stmt3->bind_param('ss', $tanggal_awal, $tanggal_akhir); $stmt3->execute();
    $total_pemasukan = $stmt3->get_result()->fetch_assoc()['total'] ?? 0;

    $sql_pengeluaran_periode = "SELECT SUM(jumlah) as total FROM kas WHERE jenis = 'pengeluaran' AND DATE(tanggal) BETWEEN ? AND ?";
    $stmt4 = $conn->prepare($sql_pengeluaran_periode); $stmt4->bind_param('ss', $tanggal_awal, $tanggal_akhir); $stmt4->execute();
    $total_pengeluaran = $stmt4->get_result()->fetch_assoc()['total'] ?? 0;
    $saldo_akhir = $saldo_awal + $total_pemasukan - $total_pengeluaran;

    // Build HTML for PDF
    $html = '<h3>Laporan Neraca</h3>';
    $html .= '<p>Periode Laporan: ' . date('d M Y', strtotime($tanggal_awal)) . ' s/d ' . date('d M Y', strtotime($tanggal_akhir)) . '</p>';
    $html .= '<table border="1" cellpadding="5">
                <tr><td width="200"><b>Saldo Awal Periode</b></td><td width="300" align="right">Rp ' . number_format($saldo_awal, 0, ',', '.') . '</td></tr>
                <tr><td width="200"><b>Total Pemasukan (Aset)</b></td><td width="300" align="right">Rp ' . number_format($total_pemasukan, 0, ',', '.') . '</td></tr>
                <tr><td width="200"><b>Total Pengeluaran (Kewajiban)</b></td><td width="300" align="right">Rp ' . number_format($total_pengeluaran, 0, ',', '.') . '</td></tr>
                <tr style="background-color:#f2f2f2;font-weight:bold;"><td width="200"><b>Saldo Akhir Periode</b></td><td width="300" align="right">Rp ' . number_format($saldo_akhir, 0, ',', '.') . '</td></tr>
              </table>';
    
    $pdf->writeHTML($html, true, false, true, false, '');
    $stmt1->close(); $stmt2->close(); $stmt3->close(); $stmt4->close();
}

$conn->close();
// Close and output PDF document
$pdf->Output('laporan_' . $report_type . '_' . date('Y-m-d') . '.pdf', 'I');
exit();
?>
