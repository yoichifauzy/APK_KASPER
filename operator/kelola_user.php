<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// Tambah User
if (isset($_POST['tambah'])) {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = strtolower(trim($_POST['role']));
    $allowedRoles = ['admin', 'operator', 'user'];
    if (!in_array($role, $allowedRoles, true)) $role = 'user';
    // Prevent non-admin users from creating admin accounts
    if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin' && $role === 'admin') {
        $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Anda tidak diizinkan membuat akun dengan role admin.'];
        header("Location: kelola_user.php");
        exit;
    }
    $status = 'aktif';

    $sql = "INSERT INTO user (nama_lengkap, username, password, role, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nama_lengkap, $username, $password, $role, $status);
    if ($stmt->execute()) {
        $_SESSION['flash'] = ['type' => 'success', 'title' => 'Berhasil', 'message' => 'User berhasil ditambahkan.'];
    } else {
        $err = $stmt->error ?: $conn->error;
        $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Gagal menambahkan user. ' . ($err ? 'Error: ' . $err : '')];
    }
    header("Location: kelola_user.php");
    exit;
}

// Edit User
if (isset($_POST['edit'])) {
    $id = $_POST['id_user'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $role = strtolower(trim($_POST['role']));
    $allowedRoles = ['admin', 'operator', 'user'];
    if (!in_array($role, $allowedRoles, true)) $role = 'user';
    // Prevent non-admin users from assigning admin role
    if (isset($_SESSION['role']) && $_SESSION['role'] !== 'admin' && $role === 'admin') {
        $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Anda tidak diizinkan mengubah role menjadi admin.'];
        header("Location: kelola_user.php");
        exit;
    }

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE user SET nama_lengkap=?, username=?, password=?, role=? WHERE id_user=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nama_lengkap, $username, $password, $role, $id);
    } else {
        $sql = "UPDATE user SET nama_lengkap=?, username=?, role=? WHERE id_user=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nama_lengkap, $username, $role, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['flash'] = ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Data user berhasil diperbarui.'];
    } else {
        $err = $stmt->error ?: $conn->error;
        $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Gagal memperbarui data user. ' . ($err ? 'Error: ' . $err : '')];
    }
    header("Location: kelola_user.php");
    exit;
}

// Hapus User
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sql = "DELETE FROM user WHERE id_user=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['flash'] = ['type' => 'success', 'title' => 'Berhasil', 'message' => 'User berhasil dihapus.'];
    } else {
        $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Gagal menghapus user.'];
    }
    header("Location: kelola_user.php");
    exit;
}

// Ambil Data User
$result = $conn->query("SELECT id_user, nama_lengkap, username, role FROM user ORDER BY id_user DESC");
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// get counts per role for mini dashboard
$counts = ['admin' => 0, 'operator' => 0, 'user' => 0];
$res_counts = $conn->query("SELECT role, COUNT(*) AS cnt FROM user GROUP BY role");
if ($res_counts) {
    while ($r = $res_counts->fetch_assoc()) {
        $counts[$r['role']] = (int) ($r['cnt'] ?? 0);
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kelola User - KASPER</title>
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
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
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
        <?php include 'layout_operator/sidebar.php'; ?>

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
                <?php include 'layout_operator/navbar.php'; ?>
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Kelola User</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Manajemen User</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Tambah User</div>
                                </div>
                                <div class="card-body">
                                    <form method="post" id="form-tambah">
                                        <input type="hidden" name="tambah" value="1">
                                        <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" required></div>
                                        <div class="form-group"><label>Username</label><input type="text" name="username" class="form-control" placeholder="Username" required></div>
                                        <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                                        <div class="form-group"><label>Role</label><select name="role" class="form-select">
                                                <option value="user">User</option>
                                                <option value="operator">Operator</option>
                                            </select></div>
                                        <div class="mt-2"><button type="submit" class="btn btn-primary">Tambah</button></div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Daftar User</div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="kelola-user-table" class="display table table-striped table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nama</th>
                                                    <th>Username</th>
                                                    <th>Role</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = $result->fetch_assoc()) { ?>
                                                    <tr>
                                                        <td><?= $row['id_user']; ?></td>
                                                        <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                                        <td><?= htmlspecialchars($row['username']); ?></td>
                                                        <td><?= htmlspecialchars($row['role']); ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning edit-user-btn"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editUserModal"
                                                                data-id="<?= $row['id_user']; ?>"
                                                                data-nama="<?= htmlspecialchars($row['nama_lengkap']); ?>"
                                                                data-username="<?= htmlspecialchars($row['username']); ?>"
                                                                data-role="<?= htmlspecialchars($row['role']); ?>">
                                                                Edit
                                                            </button>
                                                            <a href="kelola_user.php?hapus=<?= $row['id_user']; ?>" class="btn btn-sm btn-danger delete-user-btn">Hapus</a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include 'layout_operator/footer.php'; ?>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="form-edit">
                    <div class="modal-body">
                        <input type="hidden" name="edit" value="1">
                        <input type="hidden" id="edit_id_user" name="id_user">
                        <div class="form-group">
                            <label for="edit_nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="edit_nama_lengkap" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_username">Username</label>
                            <input type="text" id="edit_username" name="username" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_password">Password</label>
                            <input type="password" id="edit_password" name="password" class="form-control">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>
                        <div class="form-group">
                            <label for="edit_role">Role</label>
                            <select id="edit_role" name="role" class="form-select">
                                <option value="user">User</option>
                                <option value="operator">Operator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#kelola-user-table").DataTable({
                pageLength: 10,
                responsive: true
            });

            // Flash message notification
            var flash = <?php echo json_encode($flash ?? null); ?>;
            if (flash) {
                $.notify({
                    title: '<strong>' + (flash.title || '') + '</strong><br>',
                    message: flash.message || ''
                }, {
                    type: flash.type || 'info',
                    placement: {
                        from: 'top',
                        align: 'right'
                    },
                    delay: 4000,
                    animate: {
                        enter: 'animated fadeInDown',
                        exit: 'animated fadeOutUp'
                    }
                });
            }

            // Populate Edit Modal
            $('#editUserModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var nama = button.data('nama');
                var username = button.data('username');
                var role = button.data('role');

                var modal = $(this);
                modal.find('#edit_id_user').val(id);
                modal.find('#edit_nama_lengkap').val(nama);
                modal.find('#edit_username').val(username);
                modal.find('#edit_role').val(role);
                modal.find('#edit_password').val(''); // Clear password field
            });

            // SweetAlert confirmations
            $('#form-tambah').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                swal({
                    title: 'Tambah user?',
                    text: 'Pastikan data yang diinput sudah benar.',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Batal',
                            visible: true
                        },
                        confirm: {
                            text: 'Ya, Tambah'
                        }
                    }
                }).then(willAdd => {
                    if (willAdd) form.submit();
                });
            });

            $('#form-edit').on('submit', function(e) {
                e.preventDefault();
                var form = this;
                swal({
                    title: 'Simpan perubahan?',
                    text: 'Data user akan diperbarui.',
                    icon: 'info',
                    buttons: {
                        cancel: {
                            text: 'Batal',
                            visible: true
                        },
                        confirm: {
                            text: 'Ya, Simpan'
                        }
                    }
                }).then(willSave => {
                    if (willSave) form.submit();
                });
            });

            $(document).on('click', '.delete-user-btn', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                swal({
                    title: 'Hapus user?',
                    text: 'Data yang dihapus tidak bisa dikembalikan.',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Batal',
                            visible: true
                        },
                        confirm: {
                            text: 'Ya, Hapus'
                        }
                    },
                    dangerMode: true
                }).then(willDelete => {
                    if (willDelete) window.location.href = href;
                });
            });
        });
    </script>
</body>

</html>