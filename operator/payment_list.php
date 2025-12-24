<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// Hitung total pemasukan (dari tabel pembayaran)
$total_pemasukan = 0;
$res_pemasukan = $conn->query("SELECT SUM(jumlah) AS total FROM pembayaran");
if ($res_pemasukan) {
    $total_pemasukan = $res_pemasukan->fetch_assoc()['total'] ?? 0;
}

// Hitung total pengeluaran (dari tabel kas)
$total_pengeluaran = 0;
$res_pengeluaran = $conn->query("SELECT SUM(jumlah) AS total FROM kas WHERE jenis = 'pengeluaran'");
if ($res_pengeluaran) {
    $total_pengeluaran = $res_pengeluaran->fetch_assoc()['total'] ?? 0;
}

// Hitung sisa uang
$sisa_uang = $total_pemasukan - $total_pengeluaran;

// validasi filter jenis
$validJenis = ['pemasukan', 'pengeluaran'];
$jenis = isset($_GET['jenis']) ? trim($_GET['jenis']) : 'pemasukan'; // Default to 'pemasukan'
if (!in_array($jenis, $validJenis, true)) {
    $jenis = 'pemasukan'; // fallback: jika tidak valid, tampilkan pemasukan
}

// Judul dinamis berdasarkan filter
$pageTitle = ($jenis === 'pengeluaran') ? 'Daftar Pengeluaran Kas' : 'Daftar Pemasukan Kas';

if ($jenis === 'pengeluaran') {
    // Jika filter 'pengeluaran', ambil data langsung dari tabel kas
    $sql = "SELECT 
                k.tanggal AS tanggal,
                kk.nama AS kategori_nama,
                1 AS payments_count, -- Anggap setiap entri kas sebagai 1 transaksi
                k.jumlah AS total_jumlah,
                k.keterangan AS keterangan_list
            FROM kas k
            LEFT JOIN kas_kategori kk ON k.id_kategori = kk.id_kategori
            WHERE k.jenis = ?
            ORDER BY k.tanggal DESC, k.id_kas DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $jenis);
} else {
    // Jika filter 'pemasukan' atau 'semua', agregat dari tabel pembayaran
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
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Payment List - KASPER</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- CSS Files -->
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
        <?php include 'layout_operator/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                            <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                        </div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>
                <?php include 'layout_operator/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <main>
                        <div class="page-header mb-3">
                            <h3 class="fw-bold">Payment List</h3>
                            <ul class="breadcrumbs mt-1">
                                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Cash Payment</a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Payment List</a></li>
                            </ul>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title"><?= $pageTitle ?></h4>
                                    <span class="text-muted small">Filter berdasarkan jenis transaksi</span>
                                </div>
                                <div>
                                    <form method="get" class="d-flex align-items-center">
                                        <label class="me-2 mb-0">Jenis</label>
                                        <select name="jenis" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                            <option value="pemasukan" <?= $jenis === 'pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                                            <option value="pengeluaran" <?= $jenis === 'pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
                                        </select>
                                        <noscript><button class="btn btn-primary btn-sm">Apply</button></noscript>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="paymentTable" class="table table-striped table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Kategori</th>
                                                <th><?= ($jenis === 'pengeluaran') ? 'Jumlah Transaksi' : 'Jumlah Pembayaran' ?></th>
                                                <th>Jumlah (Total)</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1;
                                            while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                                    <td><?= htmlspecialchars($row['kategori_nama'] ?? '-') ?></td>
                                                    <td><?= intval($row['payments_count'] ?? 0) ?></td>
                                                    <td><?= number_format($row['total_jumlah'] ?? 0, 0, ',', '.') ?></td>

                                                </tr>
                                            <?php endwhile;
                                            $stmt->close(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-sm-6 col-md-4">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                                    <i class="fas fa-arrow-down"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category">Total Pemasukan</p>
                                                    <h4 class="card-title">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-danger bubble-shadow-small">
                                                    <i class="fas fa-arrow-up"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category">Total Pengeluaran</p>
                                                    <h4 class="card-title">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                    <i class="fas fa-wallet"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category">Sisa Uang</p>
                                                    <h4 class="card-title">Rp <?= number_format($sisa_uang, 0, ',', '.') ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>

            <?php include 'layout_operator/footer.php'; ?>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- jQuery Scrollbar (ensure kaiadmin dependencies available) -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        $(document).ready(function() {
            // Remove the global settings panel on this page (prevents unwanted "Navbar Header" element)
            try {
                $('#customTemplate').remove();
            } catch (e) {
                /* ignore if not present */ }
            $('#paymentTable').DataTable({
                pageLength: 10,
                responsive: true
            });
        });
    </script>

</body>

</html>