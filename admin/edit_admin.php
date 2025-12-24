<?php
require_once '../config/cek_login.php';
otorisasi(['admin']);
include '../config/database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: manage_admin.php');
    exit;
}

$stmt = $conn->prepare('SELECT id_user, nama_lengkap, username, role, status, profile_picture FROM user WHERE id_user = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
if (!$user) {
    header('Location: manage_admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Edit Admin</title>
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
                            <h3 class="fw-bold">Edit Admin</h3>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <form action="action_update_admin.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id_user" value="<?= intval($user['id_user']) ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password (biarkan kosong untuk tidak mengubah)</label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role" class="form-select">
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                                            <option value="operator" <?= $user['role'] === 'operator' ? 'selected' : '' ?>>operator</option>
                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="aktif" <?= $user['status'] === 'aktif' ? 'selected' : '' ?>>aktif</option>
                                            <option value="nonaktif" <?= $user['status'] === 'nonaktif' ? 'selected' : '' ?>>nonaktif</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Foto Profil (ganti jika ingin)</label>
                                        <?php if ($user['profile_picture']): ?>
                                            <div class="mb-2"><img src="../upload/profile/<?= htmlspecialchars($user['profile_picture']) ?>" alt="profile" style="max-height:80px"></div>
                                        <?php endif; ?>
                                        <input type="file" name="profile_picture" class="form-control">
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary">Simpan</button>
                                        <a href="manage_admin.php" class="btn btn-secondary">Batal</a>
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