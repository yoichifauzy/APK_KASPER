<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Tambah Operator</title>
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
</head>

<body>
    <div class="wrapper">
        <?php include 'layout_admin/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <?php include 'layout_admin/navbar.php'; ?>
            </div>
            <div class="container">
                <div class="page-inner">
                    <main>
                        <div class="page-header mb-3">
                            <h3 class="fw-bold">Tambah Operator</h3>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <form action="action_create_operator.php" method="post" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="aktif" selected>aktif</option>
                                            <option value="nonaktif">nonaktif</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Foto Profil (opsional)</label>
                                        <input type="file" name="profile_picture" class="form-control">
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary">Simpan</button>
                                        <a href="manage_operator.php" class="btn btn-secondary">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
            <?php include 'layout_admin/footer.php'; ?>
        </div>
    </div>
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
</body>

</html>