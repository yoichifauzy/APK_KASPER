<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

$categories = [];
$q = "SELECT id, name FROM discussion_categories ORDER BY id";
$res = mysqli_query($conn, $q);
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $categories[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Discussion Categories - Kaiadmin</title>
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
                        <h3 class="fw-bold mb-3">Discussion Categories</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home">
                                <a href="#"><i class="icon-home"></i></a>
                            </li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Forum</a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="#">Categories</a></li>
                        </ul>
                    </div>

                    <?php if (isset($_GET['status'])): ?>
                        <?php $status = $_GET['status'];
                        $message = isset($_GET['message']) ? urldecode($_GET['message']) : ''; ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert <?= ($status === 'success') ? 'alert-success' : 'alert-danger' ?> alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($message ?: ($status === 'success' ? 'Operation successful' : 'An error occurred')) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="card-title">Category List</div>
                                    <div>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="categoriesTable" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($categories as $cat) : ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($cat['id']) ?></td>
                                                        <td><?= htmlspecialchars($cat['name']) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-info btn-edit" data-id="<?= htmlspecialchars($cat['id']) ?>" data-name="<?= htmlspecialchars($cat['name']) ?>">Edit</button>
                                                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="<?= htmlspecialchars($cat['id']) ?>" data-name="<?= htmlspecialchars($cat['name']) ?>">Delete</button>
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
                </div>
            </div>

            <?php include 'layout_operator/footer.php'; ?>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="action_create_category.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editCategoryForm" action="action_edit_category.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editCategoryId">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="editCategoryName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteCategoryForm" action="action_delete_category.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Delete Category</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete the category <strong id="deleteCategoryName"></strong> ? This action cannot be undone.</p>
                        <input type="hidden" name="id" id="deleteCategoryId">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#categoriesTable').DataTable({});

            $('.btn-edit').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#editCategoryId').val(id);
                $('#editCategoryName').val(name);
                var editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                editModal.show();
            });

            $('.btn-delete').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#deleteCategoryId').val(id);
                $('#deleteCategoryName').text(name);
                var delModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
                delModal.show();
            });
        });
    </script>
</body>

</html>