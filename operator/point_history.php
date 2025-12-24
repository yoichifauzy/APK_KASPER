<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// Ambil data ranking bergabung dengan user
$sql = "SELECT r.id_ranking, r.id_user, r.jumlah_rajinnya, r.jumlah_telatnya, r.poin, u.nama_lengkap, u.username
        FROM ranking r
        LEFT JOIN user u ON r.id_user = u.id_user
        ORDER BY r.poin DESC, r.jumlah_rajinnya DESC";
$res = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Riwayat Poin - Operator</title>
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
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .badge-point {
            font-size: 0.9rem;
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
                        <a href="index.html" class="logo"><img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" /></a>
                        <div class="nav-toggle"><button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button><button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>

                <?php include 'layout_operator/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header mb-3">
                        <h3 class="fw-bold">Riwayat Poin Pengguna</h3>
                        <ul class="breadcrumbs mt-1">
                            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Member Management</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Riwayat Poin</a></li>
                        </ul>
                    </div>

                    <?php
                    // Attempt to read point rules from settings table if available
                    $poin_rajin = 10; // default: +10 per rajin
                    $poin_telat = -5;  // default: -5 per telat
                    $s = $conn->query("SELECT name, value FROM settings WHERE name IN ('poin_rajin','poin_telat')");
                    if ($s) {
                        while ($r = $s->fetch_assoc()) {
                            if ($r['name'] === 'poin_rajin' && is_numeric($r['value'])) $poin_rajin = (int)$r['value'];
                            if ($r['name'] === 'poin_telat' && is_numeric($r['value'])) $poin_telat = (int)$r['value'];
                        }
                    }
                    ?>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body ">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fas fa-thumbs-up"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Poin per Rajin</p>
                                                <h4 class="card-title">+<?= htmlspecialchars($poin_rajin) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body ">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                                <i class="fas fa-thumbs-down"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Poin per Telat</p>
                                                <h4 class="card-title"><?= htmlspecialchars($poin_telat) ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title">Daftar Poin</h4>
                                <span class="text-muted small">Menampilkan jumlah poin, rajin, dan telat per pengguna.</span>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <a href="export_point_pdf.php" class="btn btn-danger" title="Export PDF"><i class="fas fa-file-pdf"></i> PDF</a>
                                <a href="export_point_excel.php" class="btn btn-success" title="Export Excel"><i class="fas fa-file-excel"></i> Excel</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="poinTable" class="display table table-striped table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Username</th>
                                            <th>Rajin</th>
                                            <th>Telat</th>
                                            <th>Poin</th>
                                            <th>Info</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        if ($res) while ($row = $res->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($row['username'] ?? '-') ?></td>
                                                <td><?= intval($row['jumlah_rajinnya'] ?? 0) ?></td>
                                                <td><?= intval($row['jumlah_telatnya'] ?? 0) ?></td>
                                                <td><span class="badge bg-primary badge-point"><?= intval($row['poin'] ?? 0) ?></span></td>
                                                <td>
                                                    <?php
                                                    $raj = intval($row['jumlah_rajinnya'] ?? 0);
                                                    $tel = intval($row['jumlah_telatnya'] ?? 0);
                                                    $poin = intval($row['poin'] ?? 0);
                                                    $info = "Rajin: {$raj}, Telat: {$tel}, Total Poin: {$poin}";
                                                    echo htmlspecialchars($info);
                                                    ?>
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

            <?php include 'layout_operator/footer.php'; ?>
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
            $('#poinTable').DataTable({
                pageLength: 15,
                responsive: true
            });
        });
    </script>
</body>

</html>