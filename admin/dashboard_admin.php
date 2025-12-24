<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// Data untuk Grafik Arus Kas (7 Hari Terakhir)
$chart_labels = [];
$chart_inflow = array_fill(0, 7, 0);
$chart_outflow = array_fill(0, 7, 0);

for ($i = 6; $i >= 0; $i--) {
    $date = (new DateTime())->modify("-$i days");
    $chart_labels[] = $date->format('d M');
    $date_sql = $date->format('Y-m-d');

    // Query untuk kas masuk (pemasukan) dari tabel pembayaran
    $stmt_inflow = $conn->prepare("SELECT SUM(jumlah) as total FROM pembayaran WHERE tanggal_bayar = ?");
    $stmt_inflow->bind_param("s", $date_sql);
    $stmt_inflow->execute();
    $inflow_result = $stmt_inflow->get_result()->fetch_assoc();
    if ($inflow_result && $inflow_result['total']) {
        $chart_inflow[6 - $i] = (float)$inflow_result['total'];
    }
    $stmt_inflow->close();

    // Query untuk kas keluar (pengeluaran) dari tabel kas
    $stmt_outflow = $conn->prepare("SELECT SUM(jumlah) as total FROM kas WHERE jenis = 'pengeluaran' AND tanggal = ?");
    $stmt_outflow->bind_param("s", $date_sql);
    $stmt_outflow->execute();
    $outflow_result = $stmt_outflow->get_result()->fetch_assoc();
    if ($outflow_result && $outflow_result['total']) {
        $chart_outflow[6 - $i] = (float)$outflow_result['total'];
    }
    $stmt_outflow->close();
}

// Data untuk Grafik Pie Status Pembayaran
$pie_month = $_GET['pie_month'] ?? date('m');
$pie_year = $_GET['pie_year'] ?? date('Y');

$pie_chart_data = ['Lunas' => 0, 'Telat' => 0, 'Belum Lunas' => 0];

// 1. Ambil semua mahasiswa aktif
$students = [];
$res_students = $conn->query("SELECT id_user FROM user WHERE role = 'user' AND status='aktif'");
while ($row = $res_students->fetch_assoc()) {
    $students[] = $row['id_user'];
}
$total_students = count($students);

// 2. Ambil satu tagihan utama untuk bulan yang dipilih
$main_bill = null;
$stmt_bill = $conn->prepare("SELECT id_kas, jumlah FROM kas WHERE jenis = 'pemasukan' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? ORDER BY id_kas ASC LIMIT 1");
$stmt_bill->bind_param("ii", $pie_month, $pie_year);
$stmt_bill->execute();
$result_bill = $stmt_bill->get_result();
if ($result_bill->num_rows > 0) {
    $main_bill = $result_bill->fetch_assoc();
}
$stmt_bill->close();

if ($total_students > 0) {
    $lunas_count = 0;
    $telat_count = 0;
    $paid_students = []; // Keep track of students who have paid

    if ($main_bill) {
        // --- LOGIKA JIKA ADA TAGIHAN UTAMA ---
        $id_kas = $main_bill['id_kas'];
        $required_amount = $main_bill['jumlah'];

        foreach ($students as $id_user) {
            $total_paid = 0;
            $stmt_paid = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND id_kas = ?");
            $stmt_paid->bind_param("ii", $id_user, $id_kas);
            $stmt_paid->execute();
            $stmt_paid->bind_result($sum_paid);
            if ($stmt_paid->fetch()) {
                $total_paid = $sum_paid ?? 0;
            }
            $stmt_paid->close();

            if ($total_paid >= $required_amount) {
                $paid_students[] = $id_user;
                $is_late = false;
                $stmt_late = $conn->prepare("SELECT 1 FROM pembayaran WHERE id_user = ? AND id_kas = ? AND status = 'telat' LIMIT 1");
                $stmt_late->bind_param("ii", $id_user, $id_kas);
                $stmt_late->execute();
                if ($stmt_late->fetch()) {
                    $is_late = true;
                }
                $stmt_late->close();

                if ($is_late) {
                    $telat_count++;
                } else {
                    $lunas_count++;
                }
            }
        }
    } else {
        // --- LOGIKA JIKA TIDAK ADA TAGIHAN UTAMA ---
        foreach ($students as $id_user) {
            $payments_in_month = [];
            $stmt_payments = $conn->prepare("SELECT status FROM pembayaran WHERE id_user = ? AND MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ?");
            $stmt_payments->bind_param("iii", $id_user, $pie_month, $pie_year);
            $stmt_payments->execute();
            $result_payments = $stmt_payments->get_result();
            while ($row = $result_payments->fetch_assoc()) {
                $payments_in_month[] = $row;
            }
            $stmt_payments->close();

            if (!empty($payments_in_month)) {
                $paid_students[] = $id_user;
                $is_late_payment_found = false;
                foreach ($payments_in_month as $payment) {
                    if ($payment['status'] === 'telat') {
                        $is_late_payment_found = true;
                        break;
                    }
                }
                if ($is_late_payment_found) {
                    $telat_count++;
                } else {
                    $lunas_count++;
                }
            }
        }
    }

    $pie_chart_data['Lunas'] = $lunas_count;
    $pie_chart_data['Telat'] = $telat_count;
    $pie_chart_data['Belum Lunas'] = $total_students - count(array_unique($paid_students));
}


// Data untuk Grafik Batang Status Pembayaran per User
$bar_chart_month = $_GET['bar_chart_month'] ?? date('m');
$bar_chart_year = $_GET['bar_chart_year'] ?? date('Y');

$user_payment_summary_data = [
    'labels' => [],
    'datasets' => [
        ['label' => 'Lunas', 'data' => [], 'backgroundColor' => '#2ecc71'],
        ['label' => 'Telat', 'data' => [], 'backgroundColor' => '#3498db'],
        ['label' => 'Belum Lunas', 'data' => [], 'backgroundColor' => '#e74c3c']
    ]
];

// Ambil data siswa dengan detail untuk grafik batang
$students_for_bar_chart = [];
$res_students_bar = $conn->query("SELECT id_user, username FROM user WHERE role = 'user' AND status='aktif' ORDER BY username ASC");
while ($row = $res_students_bar->fetch_assoc()) {
    $students_for_bar_chart[] = $row;
}

// Ambil satu tagihan utama khusus untuk filter bar chart
$main_bill_bar_chart = null;
$stmt_bill_bar = $conn->prepare("SELECT id_kas, jumlah FROM kas WHERE jenis = 'pemasukan' AND MONTH(tanggal) = ? AND YEAR(tanggal) = ? ORDER BY id_kas ASC LIMIT 1");
$stmt_bill_bar->bind_param("ii", $bar_chart_month, $bar_chart_year);
$stmt_bill_bar->execute();
$result_bill_bar = $stmt_bill_bar->get_result();
if ($result_bill_bar->num_rows > 0) {
    $main_bill_bar_chart = $result_bill_bar->fetch_assoc();
}
$stmt_bill_bar->close();

// Hitung status untuk setiap user berdasarkan single main_bill_bar_chart
foreach ($students_for_bar_chart as $student) {
    $id_user = $student['id_user'];
    $current_lunas = 0;
    $current_telat = 0;
    $current_belum_lunas = 100; // Default

    if ($main_bill_bar_chart) {
        $id_kas_current_bill = $main_bill_bar_chart['id_kas'];
        $required_amount_current_bill = $main_bill_bar_chart['jumlah'];

        $total_paid_current_bill = 0;
        $stmt_paid_current_bill = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND id_kas = ?");
        $stmt_paid_current_bill->bind_param("ii", $id_user, $id_kas_current_bill);
        $stmt_paid_current_bill->execute();
        $stmt_paid_current_bill->bind_result($sum_paid_current_bill);
        if ($stmt_paid_current_bill->fetch()) {
            $total_paid_current_bill = $sum_paid_current_bill ?? 0;
        }
        $stmt_paid_current_bill->close();

        if ($total_paid_current_bill >= $required_amount_current_bill) {
            $is_late_current_bill = false;
            $stmt_late_current_bill = $conn->prepare("SELECT 1 FROM pembayaran WHERE id_user = ? AND id_kas = ? AND status = 'telat' LIMIT 1");
            $stmt_late_current_bill->bind_param("ii", $id_user, $id_kas_current_bill);
            $stmt_late_current_bill->execute();
            if ($stmt_late_current_bill->fetch()) {
                $is_late_current_bill = true;
            }
            $stmt_late_current_bill->close();

            if ($is_late_current_bill) {
                $current_telat = 100;
                $current_lunas = 0;
                $current_belum_lunas = 0;
            } else {
                $current_lunas = 100;
                $current_telat = 0;
                $current_belum_lunas = 0;
            }
        } else {
            $current_belum_lunas = 100;
            $current_lunas = 0;
            $current_telat = 0;
        }
    } else {
        // Logika jika tidak ada tagihan utama
        $payments_in_month_current_user = [];
        $stmt_payments_current_user = $conn->prepare("SELECT status FROM pembayaran WHERE id_user = ? AND MONTH(tanggal_bayar) = ? AND YEAR(tanggal_bayar) = ?");
        $stmt_payments_current_user->bind_param("iii", $id_user, $bar_chart_month, $bar_chart_year);
        $stmt_payments_current_user->execute();
        $result_payments_current_user = $stmt_payments_current_user->get_result();
        while ($row_payment = $result_payments_current_user->fetch_assoc()) {
            $payments_in_month_current_user[] = $row_payment;
        }
        $stmt_payments_current_user->close();

        if (!empty($payments_in_month_current_user)) {
            $is_late_payment_found_current_user = false;
            foreach ($payments_in_month_current_user as $payment_entry) {
                if ($payment_entry['status'] === 'telat') {
                    $is_late_payment_found_current_user = true;
                    break;
                }
            }
            if ($is_late_payment_found_current_user) {
                $current_telat = 100;
                $current_lunas = 0;
                $current_belum_lunas = 0;
            } else {
                $current_lunas = 100;
                $current_telat = 0;
                $current_belum_lunas = 0;
            }
        } else {
            $current_belum_lunas = 100;
            $current_lunas = 0;
            $current_telat = 0;
        }
    }

    $user_payment_summary_data['labels'][] = $student['username'];
    $user_payment_summary_data['datasets'][0]['data'][] = $current_lunas;
    $user_payment_summary_data['datasets'][1]['data'][] = $current_telat;
    $user_payment_summary_data['datasets'][2]['data'][] = $current_belum_lunas;
}

// Data untuk Grafik Radar (Jumlah Pengumuman & Agenda)
$total_pengumuman = 0;
$stmt_pengumuman = $conn->prepare("SELECT COUNT(*) as total FROM announcements"); // Corrected table name based on user feedback
$stmt_pengumuman->execute();
$result_pengumuman = $stmt_pengumuman->get_result()->fetch_assoc();
if ($result_pengumuman && $result_pengumuman['total']) {
    $total_pengumuman = (int)$result_pengumuman['total'];
}
$stmt_pengumuman->close();

$total_agenda = 0;
$stmt_agenda = $conn->prepare("SELECT COUNT(*) as total FROM jadwal_kegiatan"); // Corrected table name
$stmt_agenda->execute();
$result_agenda = $stmt_agenda->get_result()->fetch_assoc();
if ($result_agenda && $result_agenda['total']) {
    $total_agenda = (int)$result_agenda['total'];
}
$stmt_agenda->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kaiadmin - Bootstrap 5 Admin Dashboard</title>
    <meta
        content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
        name="viewport" />
    <link
        rel="icon"
        href="../assets/img/kaiadmin/favicon.ico"
        type="image/x-icon" />

    <!-- Fonts and icons -->
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

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="../assets/css/demo.css" />
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'layout_admin/sidebar.php'; ?>
        <!-- End Sidebar -->


        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img
                                src="../assets/img/kaiadmin/logo_light.svg"
                                alt="navbar brand"
                                class="navbar-brand"
                                height="20" />
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
                    <!-- End Logo Header -->
                </div>


                <!-- Navbar Header -->
                <?php include 'layout_admin/navbar.php'; ?>
                <!-- End Navbar -->


            </div>

            <!-- main-content -->
            <?php include 'layout_admin/main-content.php'; ?>


            <!-- Footer -->
            <?php include 'layout_admin/footer.php'; ?>
            <!-- end Footer -->
        </div>

        <!-- Custom template | don't include it in your project! -->
        <div class="custom-template">
            <div class="title">Settings</div>
            <div class="custom-content">
                <div class="switcher">
                    <div class="switch-block">
                        <h4>Logo Header</h4>
                        <div class="btnSwitch">
                            <button
                                type="button"
                                class="selected changeLogoHeaderColor"
                                data-color="dark"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="blue"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="purple"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="light-blue"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="green"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="orange"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="red"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="white"></button>
                            <br />
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="dark2"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="blue2"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="purple2"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="light-blue2"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="green2"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="orange2"></button>
                            <button
                                type="button"
                                class="changeLogoHeaderColor"
                                data-color="red2"></button>
                        </div>
                    </div>
                    <div class="switch-block">
                        <h4>Navbar Header</h4>
                        <div class="btnSwitch">
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="dark"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="blue"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="purple"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="light-blue"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="green"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="orange"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="red"></button>
                            <button
                                type="button"
                                class="selected changeTopBarColor"
                                data-color="white"></button>
                            <br />
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="dark2"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="blue2"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="purple2"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="light-blue2"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="green2"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="orange2"></button>
                            <button
                                type="button"
                                class="changeTopBarColor"
                                data-color="red2"></button>
                        </div>
                    </div>
                    <div class="switch-block">
                        <h4>Sidebar</h4>
                        <div class="btnSwitch">
                            <button
                                type="button"
                                class="changeSideBarColor"
                                data-color="white"></button>
                            <button
                                type="button"
                                class="selected changeSideBarColor"
                                data-color="dark"></button>
                            <button
                                type="button"
                                class="changeSideBarColor"
                                data-color="dark2"></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom-toggle">
                <i class="icon-settings"></i>
            </div>
        </div>
        <!-- End Custom template -->
    </div>
    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="../assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="../assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../assets/js/setting-demo.js"></script>
    <script src="../assets/js/demo.js"></script>
    <script>
        $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#177dff",
            fillColor: "rgba(23, 125, 255, 0.14)",
        });

        $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#f3545d",
            fillColor: "rgba(243, 84, 93, .14)",
        });

        $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#ffa534",
            fillColor: "rgba(255, 165, 52, .14)",
        });
    </script>
    <script>
        // Logout confirmation
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();
            var logoutUrl = this.getAttribute('data-logout-url');
            swal({
                title: 'Apakah Anda yakin?',
                text: "Anda akan keluar dari sesi ini!",
                type: 'warning',
                buttons: {
                    confirm: {
                        text: 'Ya, Logout!',
                        className: 'btn btn-success'
                    },
                    cancel: {
                        visible: true,
                        text: 'Tidak',
                        className: 'btn btn-danger'
                    }
                }
            }).then((willLogout) => {
                if (willLogout) {
                    // Redirect to logout page
                    window.location.href = logoutUrl;
                }
            });
        });
    </script>
</body>

</html>