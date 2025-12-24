<?php
require_once __DIR__ . '/../config/cek_login.php';
if (function_exists('otorisasi')) {
    // allow only admin access similar to other admin pages
    otorisasi(['admin']);
}
include_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>WPS - KaiAdmin Editor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
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
                urls: ["../assets/css/fonts.min.css"]
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
    <!-- small local overrides for the WPS editor -->
    <link rel="stylesheet" href="../assets/css/kaiadmin.css" />

    <!-- Quill editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

    <style>
        /* editor-specific tweaks to match KaiAdmin look */
        #editor {
            min-height: 640px;
            background: #fff;
            padding: 28px;
            border-radius: 6px;
            border: 1px solid #e6e9ef;
        }

        .editor-actions {
            margin-bottom: 1rem;
        }

        .page-title {
            margin-bottom: .5rem;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'layout_admin/sidebar.php'; ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <!-- Header / logo area (kept minimal) -->
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
                <?php include 'layout_admin/navbar.php'; ?>
                <!-- End Navbar -->
            </div>

            <!-- main-content -->
            <div class="main-content">
                <div class="page-inner">
                    <div class="page-header">
                        <h4 class="page-title">WPS Editor</h4>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="editor-actions d-flex flex-wrap gap-2 align-items-center">
                                        <button class="btn btn-primary" id="btn-new">New</button>
                                        <label class="btn btn-secondary mb-0" for="file-open">Open</label>
                                        <input id="file-open" type="file" accept="text/html" style="display:none">
                                        <button class="btn btn-success" id="btn-export-docx">Export .docx</button>
                                        <button class="btn btn-danger" id="btn-export-pdf">Export PDF</button>
                                        <button class="btn btn-outline-dark" id="btn-print">Print</button>
                                        <div class="ms-auto d-flex gap-2">
                                            <a class="btn btn-outline-primary" id="btn-save-local">Save HTML</a>
                                        </div>
                                    </div>

                                    <div id="toolbar"></div>
                                    <div id="editor" class="mt-2"></div>
                                    <div class="mt-3 text-muted small">Tip: Gunakan toolbar untuk memformat teks. Anda dapat mengekspor ke DOCX/PDF.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End main-content -->

            <!-- Footer -->
            <?php include 'layout_admin/footer.php'; ?>
            <!-- end Footer -->
        </div>

        <!-- Custom template (if present) -->
    </div>

    <!-- Core JS Files -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- Plugins and KaiAdmin scripts -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>

    <!-- Quill and export libraries -->
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html-docx-js/0.4.1/html-docx.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="../assets/js/wps.js"></script>

    <!-- Demo/optional scripts -->
    <script src="../assets/js/setting-demo.js"></script>
    <script src="../assets/js/demo.js"></script>
</body>

</html>