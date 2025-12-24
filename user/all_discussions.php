<?php
require_once __DIR__ . '/../config/cek_login.php';
otorisasi(['user']);

include '../config/database.php';

// Fetch all topics for selection
$query_topics = "SELECT dt.id, dt.title, dt.created_at, u.nama_lengkap AS author_name, dc.name AS category_name
                 FROM discussion_topics dt
                 JOIN user u ON dt.user_id = u.id_user
                 JOIN discussion_categories dc ON dt.category_id = dc.id
                 ORDER BY dt.is_pinned DESC, dt.created_at DESC";
$result = mysqli_query($conn, $query_topics);
$topics = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) $topics[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>All Discussions - Admin</title>
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
    <style>
        .topic-row {
            cursor: pointer;
        }

        .topic-row:hover {
            background: #f8fafc;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'layout_user/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo"><img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" /></a>
                        <div class="nav-toggle"><button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button><button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button></div>
                        <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                    </div>
                </div>
                <?php include 'layout_user/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">All Discussions</h3>
                            <h6 class="op-7 mb-2">Pilih topik untuk membuka ruang diskusi (admin view)</h6>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <button id="openDiscussionBtn" class="btn btn-primary btn-round">Buka Diskusi</button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <?php if (empty($topics)): ?>
                                <p>Tidak ada topik diskusi.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Judul</th>
                                                <th>Kategori</th>
                                                <th>Penulis</th>
                                                <th>Dibuat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($topics as $t): ?>
                                                <tr class="topic-row" data-id="<?= htmlspecialchars($t['id']) ?>">
                                                    <td><input type="radio" name="selected_topic" value="<?= htmlspecialchars($t['id']) ?>"></td>
                                                    <td><?= htmlspecialchars($t['title']) ?></td>
                                                    <td><?= htmlspecialchars($t['category_name']) ?></td>
                                                    <td><?= htmlspecialchars($t['author_name']) ?></td>
                                                    <td><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'layout_user/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <!-- jQuery Scrollbar and KaiAdmin scripts (required for sidebar toggle/scroll) -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script src="../assets/js/setting-demo.js"></script>
    <script src="../assets/js/demo.js"></script>
    <script>
        // clicking a row selects the radio
        document.querySelectorAll('.topic-row').forEach(function(row) {
            row.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                this.querySelector('input[name="selected_topic"]').checked = true;
            });
        });

        document.getElementById('openDiscussionBtn').addEventListener('click', function() {
            const selected = document.querySelector('input[name="selected_topic"]:checked');
            if (!selected) {
                alert('Pilih topik terlebih dahulu.');
                return;
            }
            const topicId = selected.value;
            // redirect to admin my_discussion.php
            window.location.href = 'my_discussion.php?topic_id=' + encodeURIComponent(topicId);
        });
    </script>
</body>

</html>