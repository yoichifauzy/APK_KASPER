<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// Tambah Kas
$err = '';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_kas'])) {
    $tanggal = $_POST['tanggal'] ?: date('Y-m-d');
    $jumlah = isset($_POST['jumlah']) && $_POST['jumlah'] !== '' ? floatval($_POST['jumlah']) : 0;
    $keterangan = $_POST['keterangan'] ?: '';
    $jenis = in_array($_POST['jenis'] ?? '', ['pemasukan', 'pengeluaran']) ? $_POST['jenis'] : 'pemasukan';
    $id_kategori = !empty($_POST['id_kategori']) ? intval($_POST['id_kategori']) : null;

    $dibuat_oleh = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : 0;

    $sql = "INSERT INTO kas (tanggal, id_kategori, jenis, jumlah, keterangan, dibuat_oleh) VALUES (?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('sisdsi', $tanggal, $id_kategori, $jenis, $jumlah, $keterangan, $dibuat_oleh);
        if ($stmt->execute()) {
            $msg = 'Kas berhasil ditambahkan.';
            header('Location: kelola_kas.php?msg=' . urlencode($msg));
            exit;
        } else {
            $err = 'Gagal menambahkan kas: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        $err = 'Gagal menyiapkan query.';
    }
}

// Edit Kas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kas'])) {
    $id_edit = intval($_POST['id_kas']);
    $tanggal_e = $_POST['tanggal'] ?: date('Y-m-d');
    $jumlah_e = isset($_POST['jumlah']) && $_POST['jumlah'] !== '' ? floatval($_POST['jumlah']) : 0;
    $keterangan_e = $_POST['keterangan'] ?: '';
    $jenis_e = in_array($_POST['jenis'] ?? '', ['pemasukan', 'pengeluaran']) ? $_POST['jenis'] : 'pemasukan';
    $id_kategori_e = !empty($_POST['id_kategori']) ? intval($_POST['id_kategori']) : null;

    $u = $conn->prepare('UPDATE kas SET tanggal = ?, id_kategori = ?, jenis = ?, jumlah = ?, keterangan = ? WHERE id_kas = ?');
    if ($u) {
        $u->bind_param('sisdsi', $tanggal_e, $id_kategori_e, $jenis_e, $jumlah_e, $keterangan_e, $id_edit);
        if ($u->execute()) {
            $msg = 'Kas berhasil diperbarui.';
            header('Location: kelola_kas.php?msg=' . urlencode($msg));
            exit;
        } else {
            $err = 'Gagal update kas: ' . $u->error;
        }
        $u->close();
    } else {
        $err = 'Gagal menyiapkan query update.';
    }
}

// Hapus Kas
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $d = $conn->prepare('DELETE FROM kas WHERE id_kas = ?');
    $d->bind_param('i', $id);
    if ($d->execute()) {
        $msg = 'Kas dihapus.';
        header('Location: kelola_kas.php?msg=' . urlencode($msg));
        exit;
    } else {
        $err = 'Gagal menghapus: ' . $d->error;
    }
}

// Ambil daftar kategori untuk form
$kategori_list = [];
$rk = $conn->query('SELECT id_kategori, nama FROM kas_kategori ORDER BY nama');
if ($rk) {
    while ($rkk = $rk->fetch_assoc()) $kategori_list[] = $rkk;
}

// Ambil daftar kas
$kas_list = [];
$res = $conn->query('SELECT k.id_kas, k.id_kategori, kk.nama AS kategori_nama, k.tanggal, k.jenis, k.jumlah, k.keterangan, u.username AS dibuat_oleh, k.dibuat_oleh AS dibuat_oleh_id FROM kas k LEFT JOIN kas_kategori kk ON k.id_kategori = kk.id_kategori LEFT JOIN user u ON k.dibuat_oleh = u.id_user ORDER BY k.tanggal DESC, k.id_kas DESC');
while ($r = $res->fetch_assoc()) $kas_list[] = $r;

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kelola Kas - KASPER</title>
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
                        <h3 class="fw-bold">Kelola Kas</h3>
                        <ul class="breadcrumbs">
                            <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Cash Payment</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Add Payment</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">Daftar Kas</h4>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddKas">Tambah Kas</button>
                                </div>
                                <div class="card-body">
                                    <?php if ($err): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($_GET['msg'])): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
                                    <?php endif; ?>
                                    <div class="table-responsive">
                                        <table id="daftarKasTable" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Jenis</th>
                                                    <th>Kategori</th>
                                                    <th>Tanggal</th>
                                                    <th>Jumlah</th>
                                                    <th>Keterangan</th>
                                                    <th>Dibuat Oleh</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $no = 1;
                                                foreach ($kas_list as $k): ?>
                                                    <tr>
                                                        <td><?= $no++ ?></td>
                                                        <td><?= htmlspecialchars($k['jenis'] ?? '') ?></td>
                                                        <td><?= htmlspecialchars($k['kategori_nama'] ?? '-') ?></td>
                                                        <td><?= htmlspecialchars($k['tanggal']) ?></td>
                                                        <td><?= number_format($k['jumlah'], 0, ',', '.') ?></td>
                                                        <td><?= htmlspecialchars($k['keterangan']) ?></td>
                                                        <td><?= htmlspecialchars($k['dibuat_oleh'] ?? '') ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning btn-edit-kas" data-id="<?= $k['id_kas'] ?>" data-tanggal="<?= $k['tanggal'] ?>" data-jenis="<?= htmlspecialchars($k['jenis']) ?>" data-jumlah="<?= $k['jumlah'] ?>" data-keterangan="<?= htmlspecialchars($k['keterangan'], ENT_QUOTES) ?>" data-kategori="<?= htmlspecialchars($k['id_kategori'] ?? '') ?>">Edit</button>
                                                            <button class="btn btn-sm btn-danger btn-delete-kas" data-id="<?= $k['id_kas'] ?>">Hapus</button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Kas Modal -->
                    <div class="modal fade" id="modalAddKas" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tambah Kas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="add_kas" value="1">
                                        <div class="form-group mb-2">
                                            <label>Tanggal</label>
                                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Jumlah</label>
                                            <input type="number" step="0.01" name="jumlah" class="form-control" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Jenis</label>
                                            <select name="jenis" class="form-select">
                                                <option value="pemasukan">Pemasukan</option>
                                                <option value="pengeluaran">Pengeluaran</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Kategori (opsional)</label>
                                            <select name="id_kategori" class="form-select">
                                                <option value="">--Tidak ada--</option>
                                                <?php foreach ($kategori_list as $kat): ?>
                                                    <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Keterangan</label>
                                            <input type="text" name="keterangan" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary">Tambah Kas</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Kas Modal -->
                    <div class="modal fade" id="modalEditKas" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Kas</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="edit_kas" value="1">
                                        <input type="hidden" name="id_kas" id="edit_id_kas">
                                        <div class="form-group mb-2">
                                            <label>Tanggal</label>
                                            <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Jumlah</label>
                                            <input type="number" step="0.01" name="jumlah" id="edit_jumlah" class="form-control" required>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Jenis</label>
                                            <select name="jenis" id="edit_jenis" class="form-select">
                                                <option value="pemasukan">Pemasukan</option>
                                                <option value="pengeluaran">Pengeluaran</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Kategori (opsional)</label>
                                            <select name="id_kategori" id="edit_kategori" class="form-select">
                                                <option value="">--Tidak ada--</option>
                                                <?php foreach ($kategori_list as $kat): ?>
                                                    <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group mb-2">
                                            <label>Keterangan</label>
                                            <input type="text" name="keterangan" id="edit_keterangan" class="form-control">
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


                </div>
            </div>

            <?php include 'layout_operator/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script>
        $(function() {
            $('#daftarKasTable').DataTable();

            $('.btn-edit-kas').on('click', function() {
                var id = $(this).data('id');
                $('#edit_id_kas').val(id);
                $('#edit_tanggal').val($(this).data('tanggal'));
                $('#edit_jumlah').val($(this).data('jumlah'));
                $('#edit_jenis').val($(this).data('jenis'));
                $('#edit_keterangan').val($(this).data('keterangan'));
                $('#edit_kategori').val($(this).data('kategori'));
                var m = new bootstrap.Modal(document.getElementById('modalEditKas'));
                m.show();
            });

            $(document).on('click', '.btn-delete-kas', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                swal({
                    title: 'Hapus Kas?',
                    text: "Anda yakin ingin menghapus entri kas ini?",
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
</body>

</html>