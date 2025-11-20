<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';


// Filter bulan/tahun
$current_month = isset($_GET['month']) ? intval($_GET['month']) : date('m');
$current_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Ambil semua mahasiswa aktif
$students = [];
$res_students = $conn->query("SELECT id_user, nama_lengkap FROM user WHERE role = 'user' AND status='aktif' ORDER BY nama_lengkap");
while ($row = $res_students->fetch_assoc()) {
    $students[] = $row;
}

// Ambil semua tagihan kas (pemasukan) yang relevan untuk bulan dan tahun yang dipilih

// 1. Dapatkan id_kas dari tagihan yang dibuat di bulan/tahun yang dipilih
$kas_ids = [];
$stmt_kas = $conn->prepare("SELECT id_kas FROM kas WHERE jenis = 'pemasukan' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?");
$stmt_kas->bind_param("ii", $current_month, $current_year);
$stmt_kas->execute();
$result_kas = $stmt_kas->get_result();
while ($row = $result_kas->fetch_assoc()) {
    if (!in_array($row['id_kas'], $kas_ids)) {
        $kas_ids[] = $row['id_kas'];
    }
}
$stmt_kas->close();

// 3. Ambil detail tagihan berdasarkan ID unik tersebut


// 3. Ambil detail tagihan berdasarkan ID unik tersebut
$bills = [];
if (!empty($kas_ids)) {
    // Sort to maintain a consistent order
    sort($kas_ids);

    $placeholders = implode(',', array_fill(0, count($kas_ids), '?'));
    $types = str_repeat('i', count($kas_ids));

    $stmt_bills = $conn->prepare("SELECT id_kas, keterangan, jumlah FROM kas WHERE id_kas IN ($placeholders) ORDER BY tanggal ASC");
    $stmt_bills->bind_param($types, ...$kas_ids);
    $stmt_bills->execute();
    $res_bills = $stmt_bills->get_result();
    while ($row = $res_bills->fetch_assoc()) {
        $bills[] = $row;
    }
    $stmt_bills->close();
}
$ranking_data = [];

// 1. Ambil satu tagihan utama untuk bulan yang dipilih (jika ada)
$main_bill = null;
$stmt_bill = $conn->prepare("SELECT id_kas, keterangan, jumlah FROM kas WHERE jenis = 'pemasukan' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? ORDER BY id_kas ASC LIMIT 1");
$stmt_bill->bind_param("ii", $current_month, $current_year);
$stmt_bill->execute();
$result_bill = $stmt_bill->get_result();
if ($result_bill->num_rows > 0) {
    $main_bill = $result_bill->fetch_assoc();
}
$stmt_bill->close();

// 2. Loop melalui setiap mahasiswa dan tentukan status mereka
foreach ($students as $student) {
    $id_user = $student['id_user'];
    $status = 'belum_lunas';
    $total_paid = 0;
    $sisa_tagihan = 0;
    $keterangan_kas = 'Tidak ada tagihan bulan ini';
    $jumlah_tagihan = 0;

    if ($main_bill) {
        // --- LOGIKA JIKA ADA TAGIHAN UTAMA ---
        $id_kas = $main_bill['id_kas'];
        $required_amount = $main_bill['jumlah'];
        $keterangan_kas = $main_bill['keterangan'];
        $jumlah_tagihan = $required_amount;

        // Hitung total pembayaran mahasiswa untuk tagihan utama ini
        $stmt_paid = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND id_kas = ?");
        $stmt_paid->bind_param("ii", $id_user, $id_kas);
        $stmt_paid->execute();
        $stmt_paid->bind_result($sum_paid);
        if ($stmt_paid->fetch()) {
            $total_paid = $sum_paid ?? 0;
        }
        $stmt_paid->close();

        $sisa_tagihan = $required_amount - $total_paid;

        if ($total_paid >= $required_amount) {
            $is_late = false;
            $stmt_late = $conn->prepare("SELECT 1 FROM pembayaran WHERE id_user = ? AND id_kas = ? AND status = 'telat' LIMIT 1");
            $stmt_late->bind_param("ii", $id_user, $id_kas);
            $stmt_late->execute();
            if ($stmt_late->fetch()) {
                $is_late = true;
            }
            $stmt_late->close();
            $status = $is_late ? 'telat' : 'lunas';
        } elseif ($total_paid > 0) {
            $status = 'proses';
        } else {
            $status = 'belum_lunas';
        }
    } else {
        // --- LOGIKA JIKA TIDAK ADA TAGIHAN UTAMA ---
        // Cek pembayaran apa pun yang dilakukan mahasiswa di bulan ini
        $payments_in_month = [];
        $stmt_payments = $conn->prepare("SELECT jumlah, status FROM pembayaran WHERE id_user = ? AND MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ?");
        $stmt_payments->bind_param("iii", $id_user, $current_month, $current_year);
        $stmt_payments->execute();
        $result_payments = $stmt_payments->get_result();
        while ($row = $result_payments->fetch_assoc()) {
            $payments_in_month[] = $row;
        }
        $stmt_payments->close();

        if (!empty($payments_in_month)) {
            $is_late_payment_found = false;
            foreach ($payments_in_month as $payment) {
                $total_paid += $payment['jumlah'];
                if ($payment['status'] === 'telat') {
                    $is_late_payment_found = true;
                }
            }
            $status = $is_late_payment_found ? 'telat' : 'lunas';
            $keterangan_kas = 'Pembayaran Umum';
            $jumlah_tagihan = $total_paid; // Anggap tagihan = total bayar
        } else {
            $status = 'belum_lunas';
        }
    }

    $ranking_data[] = [
        'id_user' => $id_user,
        'nama_lengkap' => $student['nama_lengkap'],
        'id_kas' => $main_bill['id_kas'] ?? null,
        'keterangan_kas' => $keterangan_kas,
        'jumlah_tagihan' => $jumlah_tagihan,
        'total_dibayar' => $total_paid,
        'sisa_tagihan' => $sisa_tagihan,
        'status' => $status
    ];
}

// --- Data for Chart ---
$chart_data = [];
$lunas_payments_by_bill = []; // To store completion dates for each bill

foreach ($bills as $bill) {
    $bill_id = $bill['id_kas'];
    $required_amount = $bill['jumlah'];

    $lunas_payments_by_bill[$bill_id] = [];

    foreach ($students as $student) {
        $id_user = $student['id_user'];

        // Calculate total paid for this student and this bill
        $total_paid = 0;
        $stmt_paid = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND id_kas = ?");
        $stmt_paid->bind_param("ii", $id_user, $bill_id);
        $stmt_paid->execute();
        $stmt_paid->bind_result($sum_paid);
        if ($stmt_paid->fetch()) {
            $total_paid = $sum_paid ?? 0;
        }
        $stmt_paid->close();

        if ($total_paid >= $required_amount) {
            // Find the date when they became 'lunas' for this specific bill
            // This is the MAX(tanggal_bayar) of all payments for this bill
            $completion_date = null;
            $stmt_comp_date = $conn->prepare("SELECT MAX(tanggal_bayar) FROM pembayaran WHERE id_user = ? AND id_kas = ?");
            $stmt_comp_date->bind_param("ii", $id_user, $bill_id);
            $stmt_comp_date->execute();
            $stmt_comp_date->bind_result($max_date);
            if ($stmt_comp_date->fetch()) {
                $completion_date = $max_date;
            }
            $stmt_comp_date->close();

            if ($completion_date) {
                $lunas_payments_by_bill[$bill_id][] = [
                    'nama_lengkap' => $student['nama_lengkap'],
                    'completion_date' => $completion_date
                ];
            }
        }
    }
}

// Aggregate and sort for chart
$all_lunas_payers = [];
foreach ($lunas_payments_by_bill as $bill_id => $payers) {
    foreach ($payers as $payer) {
        $all_lunas_payers[] = $payer;
    }
}

// Sort by completion date
usort($all_lunas_payers, function ($a, $b) {
    return strtotime($a['completion_date']) - strtotime($b['completion_date']);
});

// Get top 3 fastest and bottom 3 slowest
$fastest_payers = array_slice($all_lunas_payers, 0, 3);
$slowest_payers = array_slice($all_lunas_payers, max(0, count($all_lunas_payers) - 3));



?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Ranking Pembayaran - KASPER</title>
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
        html,
        body {
            overflow-x: hidden;
        }

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
                            <h3 class="fw-bold">Ranking Pembayaran</h3>
                            <ul class="breadcrumbs mt-1">
                                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Cash Management</a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Ranking Pembayaran</a></li>
                            </ul>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title">Status Pembayaran Mahasiswa</h4>
                                <form method="get" class="d-flex align-items-center">
                                    <label class="me-2 mb-0">Bulan:</label>
                                    <select name="month" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?= $m ?>" <?= ($current_month == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <label class="me-2 mb-0">Tahun:</label>
                                    <select name="year" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                        <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                            <option value="<?= $y ?>" <?= ($current_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="rankingTable" class="table table-striped table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Tagihan</th>
                                                <th>Jumlah Tagihan</th>
                                                <th>Total Dibayar</th>
                                                <th>Sisa Tagihan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1;
                                            foreach ($ranking_data as $data): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($data['nama_lengkap']) ?></td>
                                                    <td><?= htmlspecialchars($data['keterangan_kas']) ?></td>
                                                    <td>Rp <?= number_format($data['jumlah_tagihan'], 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($data['total_dibayar'], 0, ',', '.') ?></td>
                                                    <td>Rp <?= number_format($data['sisa_tagihan'], 0, ',', '.') ?></td>
                                                    <td>
                                                        <?php if ($data['status'] == 'lunas'): ?>
                                                            <span class="badge bg-success">Lunas</span>
                                                        <?php elseif ($data['status'] == 'proses'): ?>
                                                            <span class="badge bg-warning">Proses</span>
                                                        <?php elseif ($data['status'] == 'telat'): ?>
                                                            <span class="badge bg-info">Telat</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Belum Lunas</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Peringkat Cepat & Lambat Section -->
                        <div class="row mt-4">
                            <!-- Tabel Pembayar Tercepat -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">3 Pembayar Tercepat (Bulan Ini)</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Mahasiswa</th>
                                                        <th>Tanggal Pelunasan (WIB)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($fastest_payers)): ?>
                                                        <?php $no = 1;
                                                        foreach ($fastest_payers as $payer): ?>
                                                            <tr>
                                                                <td><?= $no++ ?></td>
                                                                <td><?= htmlspecialchars($payer['nama_lengkap']) ?></td>
                                                                <td>
                                                                    <?php
                                                                    $date = new DateTime($payer['completion_date']);
                                                                    echo $date->format('d-m-Y H:i:s');
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center">Tidak ada data.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabel Pembayar Terlambat -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">3 Pembayar Terlambat (Bulan Ini)</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Mahasiswa</th>
                                                        <th>Tanggal Pelunasan (WIB)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($slowest_payers)): ?>
                                                        <?php $no = 1;
                                                        foreach (array_reverse($slowest_payers) as $payer): ?>
                                                            <tr>
                                                                <td><?= $no++ ?></td>
                                                                <td><?= htmlspecialchars($payer['nama_lengkap']) ?></td>
                                                                <td>
                                                                    <?php
                                                                    $date = new DateTime($payer['completion_date']);
                                                                    echo $date->format('d-m-Y H:i:s');
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center">Tidak ada data.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
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

    <!-- Chart JS -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#rankingTable').DataTable({
                pageLength: 10,
                responsive: true
            });
        });
    </script>



    <script>
        // Manual re-initialization or binding for sidebar toggle if Kaiadmin's default is not working
        $(document).ready(function() {
            // For the main sidebar toggle button
            $('.btn-toggle.toggle-sidebar').off('click').on('click', function() {
                $('body').toggleClass('sidebar_minimize');
                // Also toggle the class on the wrapper if needed, depending on Kaiadmin's implementation
                // $('.wrapper').toggleClass('sidebar_minimize');
            });

            // For the sidenav toggler (mobile/tablet view)
            $('.btn-toggle.sidenav-toggler').off('click').on('click', function() {
                $('html').toggleClass('sidenav-toggled');
            });

            // For the topbar toggler (mobile/tablet view)
            $('.topbar-toggler.more').off('click').on('click', function() {
                $('html').toggleClass('topbar-toggled');
            });
        });
    </script>

</body>

</html>