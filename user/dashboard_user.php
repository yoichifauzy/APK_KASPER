<?php
require_once '../config/cek_login.php';
otorisasi(['user']);

include '../config/database.php';
?>
<div class="wrapper">
        <!-- Sidebar -->
        <?php include 'layout/sidebar.php'; ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.php?page=user_dashboard" class="logo">
                            <img
                                src="../assets/img/kaiadmin/logo_light.svg"
                                alt="navbar brand"
                                class="navbar-brand"
                                height="20" />
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
                <?php include 'layout/navbar.php'; ?>
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    <div
                        class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-3">User Dashboard</h3>
                            <h6 class="op-7 mb-2">Welcome to your personalized dashboard!</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-body">
                                    <p>This is the user-specific content area. You can add your charts, tables, and other information here.</p>
                                    <p>For example, you can display cash info, charts/diagrams, options to change password, diligent user ratings, and chat/discussion features as per your application logic.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- footer -->
            <?php include 'layout/footer.php'; ?>
            <!-- footer -->
        </div>

        </div>
    </div>