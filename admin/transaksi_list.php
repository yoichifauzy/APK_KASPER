<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);

include '../config/database.php';

// Ambil daftar pembayaran lengkap dengan kategori dan dibuat_oleh (operator pembuat kas)
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

// prepare dropdown data: distinct names, categories, statuses, operators, usernames
$names = [];
$r = $conn->query("SELECT DISTINCT u_siswa.nama_lengkap AS nama FROM pembayaran p LEFT JOIN user u_siswa ON p.id_user = u_siswa.id_user WHERE u_siswa.nama_lengkap IS NOT NULL ORDER BY nama ASC");
if ($r) {
    while ($rr = $r->fetch_assoc()) $names[] = $rr['nama'];
}
$categories = [];
$rc = $conn->query("SELECT id_kategori, nama FROM kas_kategori ORDER BY nama ASC");
if ($rc) {
    while ($rr = $rc->fetch_assoc()) $categories[] = $rr;
}
$statuses = [];
$rs = $conn->query("SELECT DISTINCT status FROM pembayaran ORDER BY status ASC");
if ($rs) {
    while ($rr = $rs->fetch_assoc()) $statuses[] = $rr['status'];
}
$operators = [];
$ro = $conn->query("SELECT DISTINCT COALESCE(u_cre.username, p." . ($colDita ?: 'ditambahkan_oleh') . ") AS operator_name FROM pembayaran p LEFT JOIN user u_cre ON p." . ($colDita ?: 'ditambahkan_oleh') . " = u_cre.id_user ORDER BY operator_name ASC");
if ($ro) {
    while ($rr = $ro->fetch_assoc()) $operators[] = $rr['operator_name'];
}
$usernames = [];
$ru = $conn->query("SELECT DISTINCT u_siswa.username FROM pembayaran p LEFT JOIN user u_siswa ON p.id_user = u_siswa.id_user WHERE u_siswa.username IS NOT NULL ORDER BY u_siswa.username ASC");
if ($ru) {
    while ($rr = $ru->fetch_assoc()) $usernames[] = $rr['username'];
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
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Admin - Daftar Transaksi</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />

    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layout_admin/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.php" class="logo">
                            <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                </div>

                <?php include 'layout_admin/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <main>
                        <div class="page-header mb-3">
                            <h3 class="fw-bold">Daftar Transaksi</h3>
                            <ul class="breadcrumbs mt-1">
                                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Financial Reports</a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Transaction List</a></li>
                            </ul>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Transaction List</h4>
                                    <span class="text-muted small">Daftar transaksi pembayaran</span>
                                </div>
                                <div id="cardActions" class="d-flex gap-2"></div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="transaksiTable" class="table table-striped table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Kategori</th>
                                                <th>Jumlah</th>
                                                <th>Tanggal Bayar</th>
                                                <th>Status</th>
                                                <th>Bukti</th>
                                                <th>Operator</th>
                                                <th>Username</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th>
                                                    <select id="filterNama" class="form-select form-select-sm">
                                                        <option value="">Semua Nama</option>
                                                        <?php foreach ($names as $n): ?>
                                                            <option value="<?= htmlspecialchars($n) ?>"><?= htmlspecialchars($n) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </th>
                                                <th>
                                                    <select id="filterKategori" class="form-select form-select-sm">
                                                        <option value="">Semua Kategori</option>
                                                        <?php foreach ($categories as $c): ?>
                                                            <option value="<?= htmlspecialchars($c['nama']) ?>"><?= htmlspecialchars($c['nama']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </th>
                                                <th>
                                                    <input type="text" id="filterJumlah" class="form-control form-control-sm" placeholder="Jumlah">
                                                </th>
                                                <th>
                                                    <input type="text" id="filterTanggal" class="form-control form-control-sm" placeholder="Tanggal (YYYY-MM-DD)">
                                                </th>
                                                <th>
                                                    <select id="filterStatus" class="form-select form-select-sm">
                                                        <option value="">Semua Status</option>
                                                        <?php foreach ($statuses as $s): ?>
                                                            <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </th>
                                                <th></th>
                                                <th>
                                                    <select id="filterOperator" class="form-select form-select-sm">
                                                        <option value="">Semua Operator</option>
                                                        <?php foreach ($operators as $op): ?>
                                                            <option value="<?= htmlspecialchars($op) ?>"><?= htmlspecialchars($op) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </th>
                                                <th>
                                                    <select id="filterUsername" class="form-select form-select-sm">
                                                        <option value="">Semua Username</option>
                                                        <?php foreach ($usernames as $u): ?>
                                                            <option value="<?= htmlspecialchars($u) ?>"><?= htmlspecialchars($u) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php $no = 1;
                                            while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($row['siswa_nama'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($row['kategori_nama'] ?? '-') ?></td>
                                                    <td><?= number_format($row['jumlah'] ?? 0, 0, ',', '.') ?></td>
                                                    <td><?= htmlspecialchars($row['tanggal_bayar']) ?></td>
                                                    <td>
                                                        <?php if ($row['status'] === 'lunas'): ?>
                                                            <span class="badge bg-success">Lunas</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger"><?= htmlspecialchars($row['status']) ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($row['bukti']): ?>
                                                            <a href="../upload/pembayaran/<?= urlencode($row['bukti']) ?>" target="_blank">Lihat</a>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($row['ditambahkan_oleh_display'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($row['siswa_username'] ?? '-') ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>

            <?php include 'layout_admin/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#transaksiTable').DataTable({
                pageLength: 10,
                responsive: true
            });

            // bind dropdown/text filters to DataTable columns
            $('#filterNama').on('change', function() {
                table.column(1).search(this.value).draw();
            });
            $('#filterKategori').on('change', function() {
                table.column(2).search(this.value).draw();
            });
            $('#filterJumlah').on('keyup change', function() {
                table.column(3).search(this.value).draw();
            });
            $('#filterTanggal').on('keyup change', function() {
                table.column(4).search(this.value).draw();
            });
            $('#filterStatus').on('change', function() {
                table.column(5).search(this.value).draw();
            });
            $('#filterOperator').on('change', function() {
                table.column(7).search(this.value).draw();
            });
            $('#filterUsername').on('change', function() {
                table.column(8).search(this.value).draw();
            });

            var cardActions = document.getElementById('cardActions');
            if (cardActions) {
                var btnCsv = document.createElement('a');
                btnCsv.className = 'btn btn-success btn-sm';
                btnCsv.href = '../operator/export_transaksi.php?format=csv';
                btnCsv.innerHTML = '<i class="fa fa-file-csv me-1"></i>CSV';
                cardActions.appendChild(btnCsv);

                var btnExcel = document.createElement('a');
                btnExcel.className = 'btn btn-success btn-sm';
                btnExcel.href = '../operator/export_transaksi.php?format=excel';
                btnExcel.innerHTML = '<i class="fa fa-file-excel me-1"></i>Excel';
                cardActions.appendChild(btnExcel);

                var btnPrint = document.createElement('a');
                btnPrint.className = 'btn btn-secondary btn-sm';
                btnPrint.href = '../operator/export_transaksi.php?format=print';
                btnPrint.target = '_blank';
                btnPrint.innerHTML = '<i class="fa fa-print me-1"></i>Print';
                cardActions.appendChild(btnPrint);
            }
        });
    </script>
</body>

</html>