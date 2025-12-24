<?php
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['admin', 'operator', 'user']);

include '../config/database.php';

$topic = null;
if (isset($_GET['id'])) {
    $topic_id = mysqli_real_escape_string($conn, $_GET['id']);
    $query_topic = "SELECT
                        dt.id,
                        dt.title,
                        dt.content,
                        dt.is_pinned,
                        dt.created_at,
                        dt.updated_at,
                        u.nama_lengkap AS author_name,
                        dc.name AS category_name
                     FROM
                        discussion_topics dt
                     JOIN
                        user u ON dt.user_id = u.id_user
                     JOIN
                        discussion_categories dc ON dt.category_id = dc.id
                     WHERE
                        dt.id = '$topic_id'";
    $result_topic = mysqli_query($conn, $query_topic);
    if ($result_topic && mysqli_num_rows($result_topic) > 0) {
        $topic = mysqli_fetch_assoc($result_topic);
    }
}

if (!$topic) {
    // Redirect or show an error if topic not found
    header('Location: index_forum.php?status=error&message=Topic not found');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?= htmlspecialchars($topic['title']) ?> - Forum Diskusi</title>
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

                <!-- Navbar Header -->
                <?php include 'layout_user/navbar.php'; ?>
                <!-- End Navbar -->
            </div>

            <!-- main-content -->
            <div class="container-fluid">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">Forum Diskusi</h3>
                            <h6 class="op-7 mb-2">Detail Topik Diskusi</h6>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <a href="index_forum.php" class="btn btn-primary btn-round">Kembali ke Forum</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        <?php if ($topic['is_pinned']) : ?>
                                            <span class="badge bg-info me-2">Pinned</span>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($topic['title']) ?>
                                    </div>
                                    <div class="card-category">
                                        Oleh: <?= htmlspecialchars($topic['author_name']) ?> | Kategori: <?= htmlspecialchars($topic['category_name']) ?> | Dibuat: <?= date('d M Y H:i', strtotime($topic['created_at'])) ?> | Terakhir Diperbarui: <?= date('d M Y H:i', strtotime($topic['updated_at'])) ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p><?= nl2br(htmlspecialchars($topic['content'])) ?></p>
                                </div>
                                <div class="card-footer">
                                    <a href="index_forum.php" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Footer -->
            <?php include 'layout_user/footer.php'; ?>
            <!-- end Footer -->
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
</body>

</html>