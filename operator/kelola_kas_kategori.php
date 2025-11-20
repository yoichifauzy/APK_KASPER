<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

$err = '';
$msg = '';

// tambah kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_kategori'])) {
    $nama = trim($_POST['nama'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    // store user id (integer) to maintain consistency across tables
    $dibuat_oleh = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : null;

    if ($nama === '') {
        $err = 'Nama kategori diperlukan.';
    } else {
        $ins = $conn->prepare('INSERT INTO kas_kategori (nama, keterangan, dibuat_oleh) VALUES (?, ?, ?)');
        // dibuat_oleh stored as integer user id
        $ins->bind_param('ssi', $nama, $keterangan, $dibuat_oleh);
        if ($ins->execute()) {
            $msg = 'Kategori berhasil ditambahkan.';
            header('Location: kelola_kas_kategori.php?msg=' . urlencode($msg));
            exit;
        } else {
            $err = 'Gagal menambahkan kategori: ' . $ins->error;
        }
        $ins->close();
    }
}

// edit kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kategori'])) {
    $id = intval($_POST['id_kategori']);
    $nama = trim($_POST['nama'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $created_at = trim($_POST['created_at'] ?? '');

    if ($nama === '') {
        $err = 'Nama kategori diperlukan.';
    } elseif (empty($created_at)) {
        $err = 'Waktu tidak boleh kosong.';
    } else {
        // Convert datetime-local string back to database format
        $db_timestamp = date('Y-m-d H:i:s', strtotime($created_at));

        $u = $conn->prepare('UPDATE kas_kategori SET nama = ?, keterangan = ?, created_at = ? WHERE id_kategori = ?');
        $u->bind_param('sssi', $nama, $keterangan, $db_timestamp, $id);
        if ($u->execute()) {
            $msg = 'Kategori berhasil diperbarui.';
            header('Location: kelola_kas_kategori.php?msg=' . urlencode($msg));
            exit;
        } else {
            $err = 'Gagal update: ' . $u->error;
        }
        $u->close();
    }
}

// hapus kategori
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // cek dependensi kas
    $chk = $conn->prepare('SELECT COUNT(*) FROM kas WHERE id_kategori = ?');
    $chk->bind_param('i', $id);
    $chk->execute();
    $chk->bind_result($cnt);
    $chk->fetch();
    $chk->close();
    // Update related 'kas' entries to set id_kategori to NULL
    $update_kas = $conn->prepare('UPDATE kas SET id_kategori = NULL WHERE id_kategori = ?');
    $update_kas->bind_param('i', $id);
    $update_kas->execute();
    $update_kas->close();

    // Now delete the category
    $d = $conn->prepare('DELETE FROM kas_kategori WHERE id_kategori = ?');
    $d->bind_param('i', $id);
    if ($d->execute()) {
        $msg = 'Kategori dihapus.';
        header('Location: kelola_kas_kategori.php?msg=' . urlencode($msg));
        exit;
    } else {
        $err = 'Gagal menghapus: ' . $d->error;
    }
}

// ambil daftar kategori (tampilkan username pembuat jika tersedia)
$kats = [];
$res = $conn->query('SELECT kk.id_kategori, kk.nama, kk.keterangan, kk.dibuat_oleh, kk.created_at, COALESCE(u.username, kk.dibuat_oleh) AS dibuat_oleh_display FROM kas_kategori kk LEFT JOIN user u ON kk.dibuat_oleh = u.id_user ORDER BY kk.created_at DESC');
while ($r = $res->fetch_assoc()) $kats[] = $r;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kelola Kas Kategori - KASPER</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons (load early so sidebar icons render) -->
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
    <!-- Demo / page tweaks -->
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
                        <h3 class="fw-bold">Kelola Kategori Kas</h3>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Cash Management</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Add Categories</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Daftar Kategori</h4>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddKategori">Tambah Kategori</button>
                                </div>
                                <div class="card-body">
                                    <?php if ($err): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($_GET['msg'])): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
                                    <?php endif; ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nama</th>
                                                    <th>Keterangan</th>
                                                    <th>Dibuat Oleh</th>
                                                    <th>Waktu</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($kats as $k): ?>
                                                    <tr>
                                                        <td><?= $k['id_kategori'] ?></td>
                                                        <td><?= htmlspecialchars($k['nama']) ?></td>
                                                        <td><?= htmlspecialchars($k['keterangan']) ?></td>
                                                        <td><?= htmlspecialchars($k['dibuat_oleh_display'] ?? $k['dibuat_oleh'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($k['created_at']) ?></td>
                                                                                                                 <td>
                                                                                                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $k['id_kategori'] ?>" data-nama="<?= htmlspecialchars($k['nama'], ENT_QUOTES) ?>" data-ket="<?= htmlspecialchars($k['keterangan'], ENT_QUOTES) ?>" data-waktu="<?= htmlspecialchars($k['created_at'], ENT_QUOTES) ?>">Edit</button>
                                                                                                                    <button class="btn btn-sm btn-danger btn-delete-kategori" data-id="<?= $k['id_kategori'] ?>" data-nama="<?= htmlspecialchars($k['nama']) ?>">Hapus</button>
                                                                                                                </td>                                                    </tr>
                                                <?php endforeach; ?>
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

    <!-- Add Modal -->
    <div class="modal fade" id="modalAddKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="add_kategori" value="1">
                        <div class="form-group mb-2">
                            <label>Nama Kategori</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Tambah</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="edit_kategori" value="1">
                        <input type="hidden" name="id_kategori" id="edit_id">
                        <div class="form-group mb-2">
                            <label>Nama</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="form-group mb-2">
                            <label>Keterangan</label>
                            <textarea name="keterangan" id="edit_ket" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label>Waktu</label>
                            <input type="datetime-local" name="created_at" id="edit_waktu" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <!-- jQuery Scrollbar plugin required by kaiadmin -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script>
        $(function() {
            $('.btn-edit').on('click', function() {
                $('#edit_id').val($(this).data('id'));
                $('#edit_nama').val($(this).data('nama'));
                $('#edit_ket').val($(this).data('ket'));
                
                var dbTime = $(this).data('waktu'); // e.g., "2023-10-27 05:10:05"
                if (dbTime && dbTime.length >= 16) {
                    // Truncate seconds and format for datetime-local input, e.g., "2023-10-27T05:10"
                    var inputTime = dbTime.substring(0, 16).replace(' ', 'T');
                    $('#edit_waktu').val(inputTime);
                }

                var m = new bootstrap.Modal(document.getElementById('modalEdit'));
                m.show();
            });

            // SweetAlert for delete confirmation
            $(document).on('click', '.btn-delete-kategori', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                swal({
                    title: 'Hapus Kategori?',
                    text: 'Anda yakin ingin menghapus kategori "' + nama + '"?',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Batal',
                            visible: true,
                            className: 'btn btn-secondary'
                        },
                        confirm: {
                            text: 'Ya, Hapus',
                            visible: true,
                            className: 'btn btn-danger'
                        }
                    },
                    dangerMode: true
                }).then(function(willDelete) {
                    if (willDelete) {
                        window.location.href = '?delete=' + id;
                    }
                });
            });
        });
    </script>
    <script>
        // Dependency-free fallback sidebar toggler (ensures burger works even if Kaiadmin/init missed it)
        (function() {
            function onToggleClick(e) {
                e.preventDefault();
                document.body.classList.toggle('sidebar-collapse');
            }
            var sel = '.btn-toggle.toggle-sidebar, .btn-toggle.sidenav-toggler, .btn-toggle.sidebar-toggler';

            function attach() {
                var els = document.querySelectorAll(sel);
                els.forEach(function(el) {
                    if (!el.dataset.fallbackAttached) {
                        el.addEventListener('click', onToggleClick);
                        el.dataset.fallbackAttached = '1';
                    }
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', attach);
            } else {
                attach();
            }
            var obs = new MutationObserver(function() {
                attach();
            });
            obs.observe(document.body, {
                childList: true,
                subtree: true
            });
        })();
    </script>
</body>

</html>