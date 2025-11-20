<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';


$format = isset($_GET['format']) ? strtolower($_GET['format']) : 'csv';

$colDita = null;
$c1 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'ditambahkan_oleh'");
if ($c1 && $c1->num_rows > 0) $colDita = 'ditambahkan_oleh';
else {
    $c2 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'dibuat_oleh'");
    if ($c2 && $c2->num_rows > 0) $colDita = 'dibuat_oleh';
}
$creator_join = '';
$creator_select = "'' AS ditambahkan_oleh_display";
if ($colDita) {
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
        WHERE 1=1
        ORDER BY p.tanggal_bayar DESC, p.id_pembayaran DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$res = $stmt->get_result();

if ($format === 'print') {
    // Simple print-friendly HTML
?>
    <!doctype html>
    <html>

    <head>
        <meta charset="utf-8">
        <title>Transaksi - Print</title>
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <style>
            table {
                width: 100%;
                border-collapse: collapse
            }

            td,
            th {
                border: 1px solid #ccc;
                padding: 6px
            }
        </style>
    </head>

    <body>
        <h3>Daftar Transaksi</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Kategori</th>
                    <th>Ket</th>
                    <th>Jumlah</th>
                    <th>Tanggal Bayar</th>
                    <th>Status</th>
                    <th>Ditambahkan Oleh</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                while ($row = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['siswa_nama'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['siswa_username'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['kategori_nama'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['kas_ket'] ?? '-') ?></td>
                        <td><?= number_format($row['jumlah'] ?? 0, 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($row['tanggal_bayar']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['ditambahkan_oleh_display'] ?? '-') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <script>
            window.print()
        </script>
    </body>

    </html>
<?php
    exit;
}

// For CSV/Excel
// Prepare filename
$ts = date('Y-m-d');
if ($format === 'excel') {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="transaksi_' . $ts . '.xls"');
} else {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="transaksi_' . $ts . '.csv"');
}

$out = fopen('php://output', 'w');
// BOM for Excel to recognize UTF-8
fputs($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

// header row
fputcsv($out, ['No', 'Nama', 'Username', 'Kategori', 'Keterangan', 'Jumlah', 'Tanggal Bayar', 'Status', 'Ditambahkan Oleh']);

$no = 1;
while ($row = $res->fetch_assoc()) {
    fputcsv($out, [
        $no++,
        $row['siswa_nama'] ?? '-',
        $row['siswa_username'] ?? '-',
        $row['kategori_nama'] ?? '-',
        $row['kas_ket'] ?? '-',
        $row['jumlah'] ?? 0,
        $row['tanggal_bayar'],
        $row['status'],
        $row['ditambahkan_oleh_display'] ?? '-'
    ]);
}

fclose($out);
exit;
