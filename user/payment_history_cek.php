<?php
require_once '../config/cek_login.php';
otorisasi(['user']);

include '../config/database.php';

if (session_status() == PHP_SESSION_NONE) session_start();
$currentUserId = $_SESSION['id_user'] ?? null;
if (!$currentUserId) {
    header('Location: ../auth/login.php');
    exit;
}

// filters
$filter_month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$filter_year = isset($_GET['year']) ? intval($_GET['year']) : 0;

// determine creator display column similar to operator page
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
    $creator_join = " LEFT JOIN user u_cre ON p." . $colDita . " = u_cre.id_user";
    $creator_select = "COALESCE(u_cre.username, CAST(p." . $colDita . " AS CHAR)) AS ditambahkan_oleh_display";
}

$sql = "SELECT p.id_pembayaran, p.id_user, u.nama_lengkap, p.id_kas, p.id_kategori, kk.nama AS kategori_nama, k.keterangan AS kas_ket, COALESCE(p.jumlah, k.jumlah) AS jumlah, p.tanggal_bayar, p.status, p.bukti, p.barcode, p.barcode_image, " . $creator_select . " 
        FROM pembayaran p
        LEFT JOIN user u ON p.id_user = u.id_user
        LEFT JOIN kas k ON p.id_kas = k.id_kas
        LEFT JOIN kas_kategori kk ON p.id_kategori = kk.id_kategori" . $creator_join . "
        WHERE p.id_user = ?";

$params = [];
$types = 'i';
$params[] = $currentUserId;

if ($filter_month && $filter_year) {
    $sql .= " AND MONTH(p.tanggal_bayar) = ? AND YEAR(p.tanggal_bayar) = ?";
    $types .= 'ii';
    $params[] = $filter_month;
    $params[] = $filter_year;
} elseif ($filter_year) {
    $sql .= " AND YEAR(p.tanggal_bayar) = ?";
    $types .= 'i';
    $params[] = $filter_year;
}

$sql .= " ORDER BY p.tanggal_bayar DESC, p.id_pembayaran DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}
// bind params dynamically
$bind_names = [];
$bind_names[] = $types;
for ($i = 0; $i < count($params); $i++) {
    $bind_name = 'bind' . $i;
    $$bind_name = $params[$i];
    $bind_names[] = &$$bind_name;
}
if (count($params) > 0) call_user_func_array([$stmt, 'bind_param'], $bind_names);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Payment History - User</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["../assets/css/fonts.min.css"]
            },
            active: function() {
                sessionStorage.fonts = true;
            }
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

        @media (max-width:768px) {

            #multi-filter-select th,
            #multi-filter-select td {
                white-space: nowrap;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layout_user/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo"><img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" /></a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                            <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                        </div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>
                <?php include 'layout_user/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Student Payments</h3>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Student Payments</h4>
                            <form method="get" class="d-flex align-items-center">
                                <label class="me-2 mb-0">Bulan:</label>
                                <select name="month" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                    <option value="0">-- Semua --</option>
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= ($filter_month == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                                    <?php endfor; ?>
                                </select>
                                <label class="me-2 mb-0">Tahun:</label>
                                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="0">-- Semua --</option>
                                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                        <option value="<?= $y ?>" <?= ($filter_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                    <?php endfor; ?>
                                </select>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="multi-filter-select" class="display table table-striped table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nama</th>
                                            <th>Jumlah</th>
                                            <th>Tanggal Bayar</th>
                                            <th>Status</th>
                                            <th>Operator</th>
                                            <th>Bukti</th>
                                            <th>Bukti Barcode</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $no = 1;
                                        while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                                <td><?= number_format($row['jumlah'] ?? 0, 0, ',', '.') ?></td>
                                                <td><?= htmlspecialchars($row['tanggal_bayar']) ?></td>
                                                <td>
                                                    <?php if ($row['status'] == 'lunas'): ?>
                                                        <span class="badge bg-success">Lunas</span>
                                                    <?php elseif ($row['status'] == 'proses'): ?>
                                                        <span class="badge bg-warning">Proses</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Telat</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['ditambahkan_oleh_display'] ?? '-') ?></td>
                                                <td>
                                                    <?php if ($row['bukti']): ?>
                                                        <a href="../upload/pembayaran/<?= urlencode($row['bukti']) ?>" target="_blank">Lihat</a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($row['barcode_image'])): ?>
                                                        <a href="../upload/pembayaran/barcodes/<?= urlencode($row['barcode_image']) ?>" target="_blank" download>Download</a>
                                                        &nbsp;<img src="../upload/pembayaran/barcodes/<?= urlencode($row['barcode_image']) ?>" style="height:28px; vertical-align:middle;" alt="barcode">
                                                    <?php elseif (!empty($row['barcode'])): ?>
                                                        <a href="../operator/barcode_image.php?code=<?= urlencode($row['barcode']) ?>" target="_blank">Lihat</a>
                                                        &nbsp;<img src="../operator/barcode_image.php?code=<?= urlencode($row['barcode']) ?>&size=thumb" style="height:28px; vertical-align:middle;" alt="barcode">
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'layout_user/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#multi-filter-select").DataTable({
                pageLength: 10,
                responsive: true,
                scrollX: true
            });
        });
    </script>
</body>

</html>