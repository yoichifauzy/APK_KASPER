<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// Fetch users and their rankings
$sql = "SELECT u.id_user, u.nama_lengkap, u.username, u.profile_picture, u.background_picture, u.status, r.poin, r.jumlah_rajinnya, r.jumlah_telatnya
FROM user u
LEFT JOIN ranking r ON u.id_user = r.id_user
WHERE u.role = 'user'
ORDER BY u.nama_lengkap ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Card Member - Kaiadmin</title>
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
        <?php include 'layout_operator/sidebar.php'; ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="dashboard_operator.php" class="logo">
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
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Card Member</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home">
                                <a href="#"><i class="icon-home"></i></a>
                            </li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Member</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Card Member</a></li>
                        </ul>
                    </div>
                    <div class="row">
                        <?php if ($result->num_rows > 0) : ?>
                            <?php while ($row = $result->fetch_assoc()) : ?>
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                    <div class="card card-profile">
                                        <div class="card-header" style="background-image: url('../upload/background/<?php echo $row['background_picture'] ?? 'blogpost.jpg'; ?>')">
                                            <div class="profile-picture">
                                                <img src="../upload/profile/<?php echo $row['profile_picture'] ?? 'default.png'; ?>" alt="..." class="avatar-img rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="user-profile text-center">
                                                <div class="name"><?php echo htmlspecialchars($row['nama_lengkap']); ?></div>
                                                <div class="job"><?php echo htmlspecialchars($row['username']); ?></div>
                                                <div class="desc">Status: <?php echo htmlspecialchars($row['status']); ?></div>

                                                <div class="social-media">
                                                    <a class="btn btn-info btn-twitter btn-sm btn-link" href="#">
                                                        <span><i class="fab fa-twitter"></i></span>
                                                    </a>
                                                    <a class="btn btn-danger btn-sm btn-link" href="#" rel="publisher">
                                                        <span><i class="fab fa-google-plus-g"></i></span>
                                                    </a>
                                                    <a class="btn btn-primary btn-sm btn-link" href="#" rel="publisher">
                                                        <span><i class="fab fa-facebook-f"></i></span>
                                                    </a>
                                                    <a class="btn btn-danger btn-sm btn-link" href="#" rel="publisher">
                                                        <span><i class="fab fa-pinterest-p"></i></span>
                                                    </a>
                                                </div>
                                                <div class="view-profile">
                                                    <a href="#" class="btn btn-secondary btn-block" data-bs-toggle="modal" data-bs-target="#editProfileModal_<?php echo $row['id_user']; ?>">Edit Profile</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="row user-stats">
                                                <div class="col">
                                                    <div class="number"><?php echo $row['poin'] ?? 0; ?></div>
                                                    <div class="title">Poin</div>
                                                </div>
                                                <div class="col">
                                                    <div class="number"><?php echo $row['jumlah_rajinnya'] ?? 0; ?></div>
                                                    <div class="title">Rajin</div>
                                                </div>
                                                <div class="col">
                                                    <div class="number"><?php echo $row['jumlah_telatnya'] ?? 0; ?></div>
                                                    <div class="title">Telat</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="col-12">
                                <p class="text-center">No members found.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Modal for Editing Own Profile -->
            <?php include 'edit_user.php'; ?>

            <!-- Footer -->
            <?php include 'layout_operator/footer.php'; ?>
            <!-- end Footer -->
        </div>
    </div>

    <!-- Modals -->
    <?php
    if ($result->num_rows > 0) {
        $result->data_seek(0); // Reset the result pointer
        while ($row = $result->fetch_assoc()) : ?>
            <div class="modal fade" id="editProfileModal_<?php echo $row['id_user']; ?>" tabindex="-1" aria-labelledby="editProfileModalLabel_<?php echo $row['id_user']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProfileModalLabel_<?php echo $row['id_user']; ?>">Edit Profile for <?php echo htmlspecialchars($row['nama_lengkap']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="upload_profile.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="user_id" value="<?php echo $row['id_user']; ?>">
                                <div class="mb-3">
                                    <label for="profile_picture_<?php echo $row['id_user']; ?>" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control" id="profile_picture_<?php echo $row['id_user']; ?>" name="profile_picture">
                                </div>
                                <div class="mb-3">
                                    <label for="background_picture_<?php echo $row['id_user']; ?>" class="form-label">Background Picture</label>
                                    <input type="file" class="form-control" id="background_picture_<?php echo $row['id_user']; ?>" name="background_picture">
                                </div>
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
    <?php endwhile;
    }
    ?>
    <!-- End Modals -->


    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../assets/js/setting-demo.js"></script>
    <script src="../assets/js/demo.js"></script>

</body>

</html>
