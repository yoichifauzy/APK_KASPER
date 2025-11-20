<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

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
    <title>OPERATOR KASPER - Transaksi List</title>
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
    <script>
        // Fallback sidebar toggler: if Kaiadmin toggles are not initialized for any reason,
        // ensure the sidebar can be toggled using the same buttons present in layout.
        // NOTE: This script relied on jQuery but it ran before jQuery was loaded which
        // caused an error and prevented the toggle from working. We remove the jQuery
        // dependent version from the head and add a small, dependency-free fallback
        // at the end of the page (after core scripts) so it always works.
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />

    <!-- DataTables + Buttons CSS (CDN for Buttons extension/pdfmake) -->
    <!-- Buttons extension removed (use server-side exports and Kaiadmin styling) -->

    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'layout_operator/sidebar.php'; ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
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
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <?php include 'layout_operator/navbar.php'; ?>
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    <main>
                        <div class="page-header mb-3">
                            <h3 class="fw-bold">Daftar Transaksi</h3>
                            <ul class="breadcrumbs mt-1">
                                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Cash Management</a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Transaction List</a></li>
                            </ul>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title">Transaction List</h4>
                                    <span class="text-muted small">Daftar transaksi lengkap dengan kategori dan dibuat oleh Operator</span>
                                </div>
                                <div id="cardActions" class="d-flex gap-2">
                                    <!-- Export buttons will be inserted here -->
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="transaksiTable" class="table table-striped table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>N0</th>
                                                <th>Nama</th>
                                                <th>Kategori</th>
                                                <!-- <th>Ket</th> -->
                                                <th>Jumlah</th>
                                                <th>Tanggal Bayar</th>
                                                <th>Status</th>
                                                <th>Bukti</th>
                                                <th>Ditambahkan Oleh</th>
                                                <th>Username</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1;
                                            while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($row['siswa_nama'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($row['kategori_nama'] ?? '-') ?></td>
                                                    <!-- <td><?= htmlspecialchars($row['kas_ket'] ?? '-') ?></td> -->
                                                    <td><?= number_format($row['jumlah'] ?? 0, 0, ',', '.') ?></td>
                                                    <td><?= htmlspecialchars($row['tanggal_bayar']) ?></td>
                                                    <td>
                                                        <?php if ($row['status'] === 'lunas'): ?>
                                                            <span class="badge bg-success">Lunas</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Telat</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($row['bukti']): ?>
                                                            <a href="upload/<?= urlencode($row['bukti']) ?>" target="_blank">Lihat</a>
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

            <!-- footer -->
            <?php include 'layout_operator/footer.php'; ?>
            <!-- footer -->
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- DataTables Buttons (CDN) -->
    <!-- Removed CDN Buttons/pdfmake/jszip - using local/server-side exports to keep Kaiadmin styling and offline usage -->

    <!-- Sweet Alert (already in assets) -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS (required for sidebar toggles and theme behaviors) -->
    <!-- jQuery Scrollbar plugin required by kaiadmin -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        // Dependency-free fallback sidebar toggler.
        // Attaches to the same button selectors used in Kaiadmin layout. This runs
        // after core scripts and DOM load so it won't fail due to missing jQuery.
        (function() {
            function onToggleClick(e) {
                e.preventDefault();
                document.body.classList.toggle('sidebar-collapse');
            }

            // Query both typical Kaiadmin toggle button classes
            var sel = '.btn-toggle.toggle-sidebar, .btn-toggle.sidenav-toggler, .btn-toggle.sidebar-toggler';

            function attach() {
                var els = document.querySelectorAll(sel);
                els.forEach(function(el) {
                    // Avoid attaching multiple times
                    if (!el.dataset.fallbackAttached) {
                        el.addEventListener('click', onToggleClick);
                        el.dataset.fallbackAttached = '1';
                    }
                });
            }

            // Attach on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', attach);
            } else {
                attach();
            }

            // Also observe for later additions to the layout (in case of dynamic content)
            var obs = new MutationObserver(function() {
                attach();
            });
            obs.observe(document.body, {
                childList: true,
                subtree: true
            });
        })();
    </script>

    <script>
        $(document).ready(function() {
            var table = $('#transaksiTable').DataTable({
                pageLength: 10,
                responsive: true
            });

            // Create local export buttons styled with Kaiadmin/Bootstrap classes.
            // These buttons call server-side export endpoints which generate CSV/Excel/Print.
            var cardActions = document.getElementById('cardActions');
            if (cardActions) {
                // CSV
                var btnCsv = document.createElement('a');
                btnCsv.className = 'btn btn-success btn-sm';
                btnCsv.href = 'export_transaksi.php?format=csv';
                btnCsv.innerHTML = '<i class="fa fa-file-csv me-1"></i>CSV';
                cardActions.appendChild(btnCsv);

                // Excel (served as xls via CSV-style for compatibility)
                var btnExcel = document.createElement('a');
                btnExcel.className = 'btn btn-success btn-sm';
                btnExcel.href = 'export_transaksi.php?format=excel';
                btnExcel.innerHTML = '<i class="fa fa-file-excel me-1"></i>Excel';
                cardActions.appendChild(btnExcel);

                // Print view
                var btnPrint = document.createElement('a');
                btnPrint.className = 'btn btn-secondary btn-sm';
                btnPrint.href = 'export_transaksi.php?format=print';
                btnPrint.target = '_blank';
                btnPrint.innerHTML = '<i class="fa fa-print me-1"></i>Print';
                cardActions.appendChild(btnPrint);
            }
        });
    </script>

</body>

</html>