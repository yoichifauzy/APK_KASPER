<?php
require_once '../config/cek_login.php';
otorisasi(['user']);

include '../config/database.php';

// Fetch announcements from the database (read-only for users)
$announcements = [];
$sql = "SELECT id, tema, isi, pembuat, tanggal_posting, label FROM announcements ORDER BY tanggal_posting DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Pengumuman - APK_KAS</title>
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

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'layout_user/sidebar.php'; ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="dashboard_user.php" class="logo">
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

                <!-- Navbar Header -->
                <?php include 'layout_user/navbar.php'; ?>
                <!-- End Navbar -->
            </div>

            <div class="container-fluid">
                <div class="page-inner">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Pengumuman</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home">
                                <a href="#"><i class="icon-home"></i></a>
                            </li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Announcement Agenda</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Pengumuman</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <?php if (!empty($announcements)) : ?>
                            <?php foreach ($announcements as $announcement) : ?>
                                <div class="col-md-12">
                                    <div class="card card-post card-round">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <div class="avatar">
                                                    <img src="../assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle">
                                                </div>
                                                <div class="info-post ms-2">
                                                    <p class="username">Oleh: <?php echo htmlspecialchars($announcement['pembuat']); ?></p>
                                                    <p class="date text-muted">Diposting: <?php echo date('d M Y H:i', strtotime($announcement['tanggal_posting'])); ?></p>
                                                </div>
                                            </div>
                                            <div class="post-content mt-3">
                                                <h3 class="post-title text-primary"><?php echo htmlspecialchars($announcement['tema']); ?></h3>
                                                <?php if (!empty($announcement['label'])) : ?>
                                                    <span class="badge bg-danger mb-2"><?php echo htmlspecialchars($announcement['label']); ?></span>
                                                <?php endif; ?>
                                                <p><?php echo nl2br(htmlspecialchars($announcement['isi'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="col-md-12">
                                <div class="alert alert-info text-center" role="alert">
                                    Belum ada pengumuman saat ini.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php include 'layout_user/footer.php'; ?>
            <!-- end Footer -->
        </div>
    </div>
</body>

</html>