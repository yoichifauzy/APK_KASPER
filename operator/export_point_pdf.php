<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// Load TCPDF library
require_once __DIR__ . '/../vendor/tcpdf/tcpdf.php';

// Fetch data
$sql = "SELECT r.id_ranking, r.id_user, r.jumlah_rajinnya, r.jumlah_telatnya, r.poin, u.nama_lengkap, u.username
        FROM ranking r
        LEFT JOIN user u ON r.id_user = u.id_user
        ORDER BY r.poin DESC, r.jumlah_rajinnya DESC";
$res = $conn->query($sql);

// Create PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator('KASPER');
$pdf->SetAuthor('KASPER');
$pdf->SetTitle('Riwayat Poin Pengguna');
$pdf->SetMargins(15, 15, 15);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->AddPage();
$pdf->SetFont('dejavusans', '', 10);

$html = '<h3>Riwayat Poin Pengguna</h3>';
$html .= '<table border="1" cellpadding="4" cellspacing="0" width="100%">';
$html .= '<thead><tr style="background-color:#f2f2f2;"><th width="5%">No</th><th width="30%">Nama</th><th width="20%">Username</th><th width="10%">Rajin</th><th width="10%">Telat</th><th width="15%">Poin</th></tr></thead>';
$html .= '<tbody>';

$no = 1;
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td align="center">' . $no++ . '</td>';
        $html .= '<td>' . htmlspecialchars($row['nama_lengkap'] ?? '-') . '</td>';
        $html .= '<td>' . htmlspecialchars($row['username'] ?? '-') . '</td>';
        $html .= '<td align="center">' . intval($row['jumlah_rajinnya'] ?? 0) . '</td>';
        $html .= '<td align="center">' . intval($row['jumlah_telatnya'] ?? 0) . '</td>';
        $html .= '<td align="center">' . intval($row['poin'] ?? 0) . '</td>';
        $html .= '</tr>';
    }
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('point_history.pdf', 'D');
exit;
