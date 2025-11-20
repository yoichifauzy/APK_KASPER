<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);
include '../config/database.php';

// Ambil semua kategori untuk filter
$kategori_list = [];
$result_kategori = $conn->query("SELECT id_kategori, nama FROM kas_kategori ORDER BY nama ASC");
if ($result_kategori) {
    while ($row = $result_kategori->fetch_assoc()) {
        $kategori_list[] = $row;
    }
}

// Default values
$periode = $_GET['periode'] ?? 'bulanan';
$filter_kategori = $_GET['kategori'] ?? [];

// Data untuk laporan
$transactions = [];
$total_pemasukan = 0;
$total_pengeluaran = 0;

// Logika untuk memproses filter dan mengambil data
if (isset($_GET['tampilkan'])) {
    $where_clauses = [];
    $params = [];
    $types = '';

    // Filter berdasarkan periode
    switch ($periode) {
        case 'harian':
            $tanggal = $_GET['tanggal'] ?? date('Y-m-d');
            $where_clauses[] = "DATE(tanggal) = ?";
            $params[] = $tanggal;
            $types .= 's';
            break;
        case 'mingguan':
            $tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-d', strtotime('monday this week'));
            $tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d', strtotime('sunday this week'));
            $where_clauses[] = "DATE(tanggal) BETWEEN ? AND ?";
            $params[] = $tanggal_awal;
            $params[] = $tanggal_akhir;
            $types .= 'ss';
            break;
        case 'tahunan':
            $tahun = $_GET['tahun'] ?? date('Y');
            $where_clauses[] = "YEAR(tanggal) = ?";
            $params[] = $tahun;
            $types .= 's';
            break;
        case 'bulanan':
        default:
            $bulan = $_GET['bulan'] ?? date('m');
            $tahun = $_GET['tahun_bulan'] ?? date('Y');
            $where_clauses[] = "MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
            $params[] = $bulan;
            $params[] = $tahun;
            $types .= 'ss';
            break;
    }

    // Filter berdasarkan kategori
    if (!empty($filter_kategori) && is_array($filter_kategori)) {
        $in_placeholder = implode(',', array_fill(0, count($filter_kategori), '?'));
        $where_clauses[] = "id_kategori IN ($in_placeholder)";
        foreach ($filter_kategori as $cat_id) {
            $params[] = $cat_id;
            $types .= 'i';
        }
    }

    // Membuat klausa WHERE spesifik untuk setiap tabel
    $where_clauses_pembayaran = [];
    foreach ($where_clauses as $clause) {
        $where_clauses_pembayaran[] = str_replace('tanggal', 'tanggal_bayar', $clause);
    }

    $where_sql_pembayaran = 'WHERE ' . implode(' AND ', $where_clauses_pembayaran);
    $where_sql_kas = 'WHERE jenis = \'pengeluaran\' AND ' . implode(' AND ', $where_clauses);

    $sql = "
        SELECT tanggal_bayar as tanggal, 'Iuran Kas' as keterangan, id_kategori, jumlah as pemasukan, 0 as pengeluaran FROM pembayaran {$where_sql_pembayaran}
        UNION ALL
        SELECT tanggal, keterangan, id_kategori, 0 as pemasukan, jumlah as pengeluaran FROM kas {$where_sql_kas}
        ORDER BY tanggal ASC
    ";

    $union_params = array_merge($params, $params);
    $union_types = $types . $types;

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param($union_types, ...$union_params);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $transactions[] = $row;
                $total_pemasukan += $row['pemasukan'];
                $total_pengeluaran += $row['pengeluaran'];
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Cash Flow Report - KASPER</title>
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
                        <h3 class="fw-bold mb-3">Laporan Arus Kas (Cash Flow)</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Laporan Keuangan</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Arus Kas</a></li>
                        </ul>
                    </div>

                    <div class="card">
                        <div class="card-header"><h4 class="card-title">Filter Laporan</h4></div>
                        <div class="card-body">
                            <form method="GET" action="">
                                <div class="row">
                                    <!-- Kolom Filter Periode -->
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="periode" class="fw-bold">Periode</label>
                                            <select name="periode" id="periode" class="form-select">
                                                <option value="harian" <?= $periode == 'harian' ? 'selected' : '' ?>>Harian</option>
                                                <option value="mingguan" <?= $periode == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
                                                <option value="bulanan" <?= $periode == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                                                <option value="tahunan" <?= $periode == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
                                            </select>
                                        </div>
                                        <div id="filter-harian" class="filter-dynamic mt-2">
                                            <label>Tanggal</label>
                                            <input type="date" name="tanggal" class="form-control" value="<?= $_GET['tanggal'] ?? date('Y-m-d') ?>">
                                        </div>
                                        <div id="filter-mingguan" class="filter-dynamic mt-2">
                                            <div class="row">
                                                <div class="col-6"><label>Dari</label><input type="date" name="tanggal_awal" class="form-control" value="<?= $_GET['tanggal_awal'] ?? date('Y-m-d', strtotime('monday this week')) ?>"></div>
                                                <div class="col-6"><label>Sampai</label><input type="date" name="tanggal_akhir" class="form-control" value="<?= $_GET['tanggal_akhir'] ?? date('Y-m-d', strtotime('sunday this week')) ?>"></div>
                                            </div>
                                        </div>
                                        <div id="filter-bulanan" class="filter-dynamic mt-2">
                                            <div class="row">
                                                <div class="col-6"><label>Bulan</label><select name="bulan" class="form-select"><?php for ($m=1; $m<=12; $m++): ?><option value="<?= $m ?>" <?= ($m == ($_GET['bulan'] ?? date('m'))) ? 'selected' : '' ?>><?= date('F', mktime(0,0,0,$m, 1, date('Y'))) ?></option><?php endfor; ?></select></div>
                                                <div class="col-6"><label>Tahun</label><input type="number" name="tahun_bulan" class="form-control" value="<?= $_GET['tahun_bulan'] ?? date('Y') ?>"></div>
                                            </div>
                                        </div>
                                        <div id="filter-tahunan" class="filter-dynamic mt-2">
                                            <label>Tahun</label>
                                            <input type="number" name="tahun" class="form-control" value="<?= $_GET['tahun'] ?? date('Y') ?>">
                                        </div>
                                    </div>
                                    <!-- Kolom Filter Kategori -->
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label class="fw-bold">Kategori</label>
                                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                                <?php foreach ($kategori_list as $kat): ?>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="kategori[]" value="<?= $kat['id_kategori'] ?>" id="kat_<?= $kat['id_kategori'] ?>" <?= in_array($kat['id_kategori'], $filter_kategori) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="kat_<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama']) ?></label>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Kolom Tombol -->
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" name="tampilkan" value="1" class="btn btn-primary w-100">Tampilkan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if (isset($_GET['tampilkan'])): ?>
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="card-title fw-bold">Hasil Laporan</h4>
                                <div>
                                    <a href="export_pdf.php?<?= http_build_query($_GET) ?>&report=cash_flow" target="_blank" class="btn btn-sm btn-danger"><i class="fa fa-file-pdf"></i> Export PDF</a>
                                    <a href="export_excel.php?<?= http_build_query($_GET) ?>&report=cash_flow" class="btn btn-sm btn-success"><i class="fa fa-file-excel"></i> Export Excel (CSV)</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Kategori</th>
                                            <th>Keterangan</th>
                                            <th class="text-end">Pemasukan</th>
                                            <th class="text-end">Pengeluaran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($transactions)): ?>
                                            <tr><td colspan="5" class="text-center">Tidak ada data untuk periode atau filter yang dipilih.</td></tr>
                                        <?php else: ?>
                                            <?php foreach ($transactions as $trx): ?>
                                            <tr>
                                                <td><?= date('d M Y', strtotime($trx['tanggal'])) ?></td>
                                                <td><?= htmlspecialchars($kategori_list[array_search($trx['id_kategori'], array_column($kategori_list, 'id_kategori'))]['nama'] ?? 'Tidak Diketahui') ?></td>
                                                <td><?= htmlspecialchars($trx['keterangan']) ?></td>
                                                <td class="text-end"><?= $trx['pemasukan'] > 0 ? 'Rp ' . number_format($trx['pemasukan'], 0, ',', '.') : '-' ?></td>
                                                <td class="text-end"><?= $trx['pengeluaran'] > 0 ? 'Rp ' . number_format($trx['pengeluaran'], 0, ',', '.') : '-' ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    </table>
                                    <hr>
                                    <table class="table">
                                    <tfoot class="fw-bold">
                                        <tr>
                                            <td colspan="3" class="text-end border-0">Total Pemasukan</td>
                                            <td class="text-end border-0">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></td>
                                            <td class="border-0"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end border-0">Total Pengeluaran</td>
                                            <td class="border-0"></td>
                                            <td class="text-end border-0">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
                                        </tr>
                                        <tr class="table-light fs-5">
                                            <td colspan="3" class="text-end border-0">Arus Kas Bersih</td>
                                            <td colspan="2" class="text-center border-0"><?= ($total_pemasukan - $total_pengeluaran) >= 0 ? 'Rp ' . number_format($total_pemasukan - $total_pengeluaran, 0, ',', '.') : '(Rp ' . number_format(abs($total_pemasukan - $total_pengeluaran), 0, ',', '.') . ')' ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
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

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script>
        $(document).ready(function() {
            function toggleFilterInputs() {
                var selected = $('#periode').val();
                $('.filter-dynamic').hide();
                $('#filter-' + selected).show();
            }
            toggleFilterInputs(); // Initial call
            $('#periode').on('change', toggleFilterInputs);
        });
    </script>
</body>
</html>