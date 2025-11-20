<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

// Fetch announcements from the database
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
    <title>Pengumuman - Kaiadmin</title>
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
                    <?php if (isset($_SESSION['success'])) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['info'])) : ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['info']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['info']); ?>
                    <?php endif; ?>
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Pengumuman</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home">
                                <a href="#"><i class="icon-home"></i></a>
                            </li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Announcement Agenda</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Announcement</a></li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h6 class="op-7 mb-2">Kelola dan buat pengumuman untuk mahasiswa</h6>
                        </div>
                        <div class="ms-md-auto py-2 py-md-0">
                            <button type="button" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                                <i class="fa fa-plus"></i> Tambah Pengumuman
                            </button>
                        </div>
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
                                                <div class="ms-auto">
                                                    <button class="btn btn-icon btn-sm btn-warning btn-round" data-bs-toggle="modal" data-bs-target="#editAnnouncementModal" data-id="<?php echo $announcement['id']; ?>" data-tema="<?php echo htmlspecialchars($announcement['tema'], ENT_QUOTES, 'UTF-8'); ?>" data-isi="<?php echo htmlspecialchars($announcement['isi'], ENT_QUOTES, 'UTF-8'); ?>" data-label="<?php echo htmlspecialchars($announcement['label']); ?>">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-icon btn-sm btn-danger btn-round delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-href="hapus_pengumuman.php?id=<?php echo $announcement['id']; ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="post-content">
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
            <?php include 'layout_operator/footer.php'; ?>
            <!-- end Footer -->
        </div>
    </div>

    <!-- Add Announcement Modal -->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAnnouncementModalLabel">Tambah Pengumuman Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="save_pengumuman.php" method="post">
                        <div class="mb-3">
                            <label for="tema" class="form-label">Tema Pengumuman</label>
                            <input type="text" class="form-control" id="tema" name="tema" required>
                        </div>
                        <div class="mb-3">
                            <label for="isi" class="form-label">Isi Pengumuman</label>
                            <textarea class="form-control" id="isi" name="isi" rows="5" required></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="label_penting" name="label_penting" value="PENTING">
                            <label class="form-check-label" for="label_penting">Tandai sebagai PENTING</label>
                        </div>
                        <input type="hidden" name="pembuat" value="<?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Operator'); ?>">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Pengumuman</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Announcement Modal -->
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAnnouncementModalLabel">Edit Pengumuman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editAnnouncementForm" action="update_pengumuman.php" method="post">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_tema" class="form-label">Tema Pengumuman</label>
                            <input type="text" class="form-control" id="edit_tema" name="tema" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_isi" class="form-label">Isi Pengumuman</label>
                            <textarea class="form-control" id="edit_isi" name="isi" rows="5" required></textarea>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="edit_label_penting" name="label_penting" value="PENTING">
                            <label class="form-check-label" for="edit_label_penting">Tandai sebagai PENTING</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus pengumuman ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a id="delete-confirm-btn" class="btn btn-danger" href="#">Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../assets/js/setting-demo.js"></script>
    <script src="../assets/js/demo.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function htmlDecode(input) {
                var doc = new DOMParser().parseFromString(input, "text/html");
                return doc.documentElement.textContent;
            }

            // Edit Modal
            var editAnnouncementModal = document.getElementById('editAnnouncementModal');
            editAnnouncementModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var tema = button.getAttribute('data-tema');
                var isi = button.getAttribute('data-isi');
                var label = button.getAttribute('data-label');

                var modalBodyInputId = editAnnouncementModal.querySelector('#edit_id');
                var modalBodyInputTema = editAnnouncementModal.querySelector('#edit_tema');
                var modalBodyInputIsi = editAnnouncementModal.querySelector('#edit_isi');
                var modalBodyInputLabel = editAnnouncementModal.querySelector('#edit_label_penting');

                modalBodyInputId.value = id;
                modalBodyInputTema.value = htmlDecode(tema);
                modalBodyInputIsi.value = htmlDecode(isi);
                modalBodyInputLabel.checked = (label === 'PENTING');
            });

            var editForm = document.getElementById('editAnnouncementForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default form submission
                    var form = this;
                    swal({
                        title: 'Simpan perubahan?',
                        text: 'Pengumuman akan diperbarui.',
                        icon: 'info',
                        buttons: {
                            cancel: {
                                text: 'Batal',
                                visible: true,
                                className: 'btn btn-secondary',
                                closeModal: true
                            },
                            confirm: {
                                text: 'Ya, Simpan',
                                className: 'btn btn-primary',
                                closeModal: true
                            }
                        }
                    }).then((willSave) => {
                        if (willSave) {
                            form.submit(); // Submit the form if confirmed
                        }
                    });
                });
            }

            // Delete Confirmation Modal
            var deleteConfirmModal = document.getElementById('deleteConfirmModal');
            deleteConfirmModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var href = button.getAttribute('data-href');
                var deleteBtn = deleteConfirmModal.querySelector('#delete-confirm-btn');
                deleteBtn.href = href;
            });
        });
    </script>

</body>

</html>