<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);
include '../config/database.php';

// Default date range: this month
$tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-01');
$tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-t');

// --- CALCULATIONS ---

// 1. Calculate Saldo Awal (Ending balance from the day before $tanggal_awal)
$saldo_awal = 0;
$sql_pemasukan_sebelum = "SELECT SUM(jumlah) as total FROM pembayaran WHERE DATE(tanggal_bayar) < ?";
$stmt = $conn->prepare($sql_pemasukan_sebelum);
$stmt->bind_param('s', $tanggal_awal);
$stmt->execute();
$pemasukan_sebelum = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

$sql_pengeluaran_sebelum = "SELECT SUM(jumlah) as total FROM kas WHERE jenis = 'pengeluaran' AND DATE(tanggal) < ?";
$stmt = $conn->prepare($sql_pengeluaran_sebelum);
$stmt->bind_param('s', $tanggal_awal);
$stmt->execute();
$pengeluaran_sebelum = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

$saldo_awal = $pemasukan_sebelum - $pengeluaran_sebelum;

// 2. Calculate Pemasukan in the selected period
$sql_pemasukan_periode = "SELECT SUM(jumlah) as total FROM pembayaran WHERE DATE(tanggal_bayar) BETWEEN ? AND ?";
$stmt = $conn->prepare($sql_pemasukan_periode);
$stmt->bind_param('ss', $tanggal_awal, $tanggal_akhir);
$stmt->execute();
$total_pemasukan = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// 3. Calculate Pengeluaran in the selected period
$sql_pengeluaran_periode = "SELECT SUM(jumlah) as total FROM kas WHERE jenis = 'pengeluaran' AND DATE(tanggal) BETWEEN ? AND ?";
$stmt = $conn->prepare($sql_pengeluaran_periode);
$stmt->bind_param('ss', $tanggal_awal, $tanggal_akhir);
$stmt->execute();
$total_pengeluaran = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// 4. Calculate Saldo Akhir
$saldo_akhir = $saldo_awal + $total_pemasukan - $total_pengeluaran;

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Balance Sheet - KASPER</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script> WebFont.load({ google: { families: ["Public Sans:300,400,500,600,700"] }, custom: { families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ["../assets/css/fonts.min.css"], }, active: function() { sessionStorage.fonts = true; }, }); </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
</head>
<body>
    <div class="wrapper">
        <?php include 'layout_operator/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="dashboard_operator.php" class="logo"><img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" /></a>
                        <div class="nav-toggle"><button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button><button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>
                <?php include 'layout_operator/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Laporan Neraca (Balance Sheet)</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Laporan Keuangan</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Neraca</a></li>
                        </ul>
                    </div>

                    <!-- Filter Card -->
                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Pilih Periode Laporan</h4></div>
                        <div class="card-body">
                            <form method="GET" action="">
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label>Dari Tanggal</label>
                                        <input type="date" name="tanggal_awal" class="form-control" value="<?= htmlspecialchars($tanggal_awal) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Sampai Tanggal</label>
                                        <input type="date" name="tanggal_akhir" class="form-control" value="<?= htmlspecialchars($tanggal_akhir) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Result Card -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Laporan Neraca Periode <?= date('d M Y', strtotime($tanggal_awal)) ?> s/d <?= date('d M Y', strtotime($tanggal_akhir)) ?></h4>
                                <div>
                                    <a href="export_pdf.php?<?= http_build_query($_GET) ?>&report=balance_sheet" target="_blank" class="btn btn-sm btn-danger"><i class="fa fa-file-pdf"></i> Export PDF</a>
                                    <a href="export_excel.php?<?= http_build_query($_GET) ?>&report=balance_sheet" class="btn btn-sm btn-success"><i class="fa fa-file-excel"></i> Export Excel (CSV)</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Saldo Awal -->
                                <div class="col-md-6 mb-4">
                                    <div class="card card-stats card-secondary card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon"><div class="icon-big text-center"><i class="fas fa-download"></i></div></div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers"><p class="card-category">Saldo Awal Periode</p><h4 class="card-title">Rp <?= number_format($saldo_awal, 0, ',', '.') ?></h4></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Total Pemasukan -->
                                <div class="col-md-6 mb-4">
                                    <div class="card card-stats card-success card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon"><div class="icon-big text-center"><i class="fas fa-arrow-alt-circle-down"></i></div></div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers"><p class="card-category">Total Pemasukan (Aset)</p><h4 class="card-title">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h4></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Total Pengeluaran -->
                                <div class="col-md-6 mb-4">
                                    <div class="card card-stats card-danger card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon"><div class="icon-big text-center"><i class="fas fa-arrow-alt-circle-up"></i></div></div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers"><p class="card-category">Total Pengeluaran (Kewajiban)</p><h4 class="card-title">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h4></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Saldo Akhir -->
                                <div class="col-md-6 mb-4">
                                    <div class="card card-stats card-primary card-round">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-icon"><div class="icon-big text-center"><i class="fas fa-wallet"></i></div></div>
                                                <div class="col col-stats ms-3 ms-sm-0">
                                                    <div class="numbers"><p class="card-category">Saldo Akhir Periode</p><h4 class="card-title">Rp <?= number_format($saldo_akhir, 0, ',', '.') ?></h4></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'layout_operator/footer.php'; ?>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
</body>
</html>
