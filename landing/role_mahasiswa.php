<?php
// landing/role_mahasiswa.php
// Halaman ini menampilkan fitur-fitur yang tersedia untuk role Mahasiswa/User
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Role Mahasiswa - Kaiadmin</title>
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
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <style>
        .card-kaidmin {
            border-left: .35rem solid #4e73df;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include_once __DIR__ . '/layout_landing/sidebar.php'; ?>

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="../index.php" class="logo">
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

                <?php include_once __DIR__ . '/layout_landing/navbar.php'; ?>
            </div>


            <div class="container">
                <div class="page-inner">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h2 class="card-title">Role: Mahasiswa / User</h2>
                                    <p class="text-muted">Hak akses dan fitur yang tersedia untuk pengguna bertipe Mahasiswa di aplikasi KAS.</p>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <div class="icon-big text-center icon-primary bubble-shadow-small me-3">
                                                                    <i class="fas fa-tachometer-alt fa-2x"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="mb-1">Dashboard Pribadi</h5>
                                                                    <p class="text-muted mb-0 small">Ringkasan saldo, notifikasi, dan aktivitas terbaru.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <div class="icon-big text-center icon-success bubble-shadow-small me-3">
                                                                    <i class="fas fa-credit-card fa-2x"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="mb-1">Transaksi & Pembayaran</h5>
                                                                    <p class="text-muted mb-0 small">Bayar tagihan, lihat riwayat, unduh bukti pembayaran.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <div class="icon-big text-center icon-warning bubble-shadow-small me-3">
                                                                    <i class="fas fa-comments fa-2x"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="mb-1">Forum & Diskusi</h5>
                                                                    <p class="text-muted mb-0 small">Buat topik, balas, dan ikuti diskusi kelas.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <div class="icon-big text-center icon-info bubble-shadow-small me-3">
                                                                    <i class="fas fa-paper-plane fa-2x"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="mb-1">Chat & Pesan Pribadi</h5>
                                                                    <p class="text-muted mb-0 small">Kirim pesan privat dan lihat riwayat chat.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <div class="icon-big text-center icon-secondary bubble-shadow-small me-3">
                                                                    <i class="fas fa-file-export fa-2x"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="mb-1">Laporan & Export</h5>
                                                                    <p class="text-muted mb-0 small">Lihat laporan pemasukan, unduh CSV/XLS.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card card-stats card-round">
                                                        <div class="card-body">
                                                            <div class="d-flex align-items-center">
                                                                <div class="icon-big text-center icon-dark bubble-shadow-small me-3">
                                                                    <i class="fas fa-user-cog fa-2x"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="mb-1">Profil & Pengaturan</h5>
                                                                    <p class="text-muted mb-0 small">Edit profil, ganti password, kelola notifikasi.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="card card-round">
                                                <div class="card-header">
                                                    <div class="card-title">Quick Actions</div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-grid gap-2">
                                                        <a class="btn btn-label-primary btn-round"> <i class="fa fa-tachometer-alt me-2"></i> Buka Dashboard</a>
                                                        <a class="btn btn-label-success btn-round"> <i class="fa fa-credit-card me-2"></i> Pembayaran</a>
                                                        <a class="btn btn-label-warning btn-round"> <i class="fa fa-comments me-2"></i> Forum & Diskusi</a>
                                                        <a class="btn btn-label-info btn-round"> <i class="fa fa-paper-plane me-2"></i> Chat</a>
                                                    </div>

                                                    <hr />

                                                    <div class="mb-3">
                                                        <h6 class="mb-1">Ringkasan</h6>
                                                        <div class="small text-muted">Saldo Tersedia</div>
                                                        <h3 class="fw-bold">Plizz Bayar</h3>
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
            </div>

            <footer class="footer">
                <?php include_once __DIR__ . '/layout_landing/footer.php'; ?>
            </footer>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/chart-circle/circles.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/demo.js"></script>
</body>

</html>