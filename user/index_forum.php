<?php
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['user']);

include '../config/database.php';

// Fetch discussion topics
$query_topics = "SELECT
                    dt.id,
                    dt.title,
                    dt.content,
                    dt.is_pinned,
                    dt.created_at,
                    dt.updated_at,
                    u.nama_lengkap AS author_name,
                    dc.name AS category_name,
                    dc.id AS category_id
                 FROM
                    discussion_topics dt
                 JOIN
                    user u ON dt.user_id = u.id_user
                 JOIN
                    discussion_categories dc ON dt.category_id = dc.id
                 ORDER BY
                    dt.is_pinned DESC, dt.created_at DESC";
$result_topics = mysqli_query($conn, $query_topics);
$topics = [];
if ($result_topics) {
    while ($row = mysqli_fetch_assoc($result_topics)) {
        $topics[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Forum Diskusi - Pengguna</title>
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
        .card-post .post-text {
            color: #374151;
        }
    </style>
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
                            <h6 class="op-7 mb-2">Dashboard Forum Diskusi untuk pengguna</h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Daftar Topik Diskusi</div>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($topics)) : ?>
                                        <p>Belum ada topik diskusi.</p>
                                    <?php else : ?>
                                        <?php foreach ($topics as $topic) : ?>
                                            <div class="card card-post card-round mb-3">
                                                <div class="card-body">
                                                    <div class="d-flex">
                                                        <div class="avatar avatar-sm">
                                                            <img src="../assets/img/profile.jpg" alt="..." class="avatar-img rounded-circle">
                                                        </div>
                                                        <div class="info-post ms-2">
                                                            <p class="username"><?= htmlspecialchars($topic['author_name']) ?></p>
                                                            <p class="date text-muted"><?= date('d M Y H:i', strtotime($topic['created_at'])) ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="post-content">
                                                        <h3 class="post-title">
                                                            <?php if ($topic['is_pinned']) : ?>
                                                                <span class="badge bg-info me-2"><i class="fa fa-thumbtack"></i> Pinned</span>
                                                            <?php endif; ?>
                                                            <a href="view_topic.php?id=<?= htmlspecialchars($topic['id']) ?>"><?= htmlspecialchars($topic['title']) ?></a>
                                                        </h3>
                                                        <p class="post-category">Kategori: <span class="badge bg-primary"><?= htmlspecialchars($topic['category_name']) ?></span></p>
                                                        <p class="post-text"><?= substr(htmlspecialchars($topic['content']), 0, 150) ?>...</p>
                                                        <div class="d-flex justify-content-end mt-3">
                                                            <a href="view_topic.php?id=<?= htmlspecialchars($topic['id']) ?>" class="btn btn-primary btn-sm me-2">Baca Selengkapnya</a>
                                                            <a href="my_discussion.php?topic_id=<?= htmlspecialchars($topic['id']) ?>" class="btn btn-info btn-sm">Chat</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <?php
                            // Recent announcements
                            $recent_announcements = [];
                            $ra = mysqli_query($conn, "SELECT id, tema, tanggal_posting, label FROM announcements ORDER BY tanggal_posting DESC LIMIT 5");
                            if ($ra) while ($r = mysqli_fetch_assoc($ra)) $recent_announcements[] = $r;

                            // Recent messages for this user
                            $recent_messages = [];
                            if (isset($_SESSION['id_user'])) {
                                $uid = mysqli_real_escape_string($conn, $_SESSION['id_user']);
                                $rm = mysqli_query($conn, "SELECT pc.id, pc.sender_id, pc.message, pc.created_at, u.nama_lengkap AS sender_name FROM private_chat pc JOIN user u ON pc.sender_id = u.id_user WHERE pc.recipient_id = '$uid' ORDER BY pc.created_at DESC LIMIT 5");
                                if ($rm) while ($r = mysqli_fetch_assoc($rm)) $recent_messages[] = $r;
                            }
                            ?>

                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Pengumuman Terbaru</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <?php if (empty($recent_announcements)): ?>
                                            <div class="list-group-item text-center text-muted">Tidak ada pengumuman</div>
                                        <?php else: ?>
                                            <?php foreach ($recent_announcements as $ann): ?>
                                                <a href="pengumuman.php" class="list-group-item list-group-item-action">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($ann['tema']); ?></h6>
                                                        <small class="text-muted"><?php echo date('d M Y', strtotime($ann['tanggal_posting'])); ?></small>
                                                    </div>
                                                    <?php if (!empty($ann['label'])): ?>
                                                        <small class="badge bg-danger"><?php echo htmlspecialchars($ann['label']); ?></small>
                                                    <?php endif; ?>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Pesan Masuk</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        <?php if (empty($recent_messages)): ?>
                                            <div class="list-group-item text-center text-muted">Tidak ada pesan</div>
                                        <?php else: ?>
                                            <?php foreach ($recent_messages as $m): ?>
                                                <a href="discussion_admin.php" class="list-group-item list-group-item-action">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($m['sender_name']); ?></h6>
                                                        <small class="text-muted"><?php echo date('d M Y H:i', strtotime($m['created_at'])); ?></small>
                                                    </div>
                                                    <p class="mb-1 text-truncate"><?php echo htmlspecialchars($m['message']); ?></p>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
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

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

</body>

</html>