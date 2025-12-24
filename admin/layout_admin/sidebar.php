<div class="sidebar sidebar-style-2" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="index.html" class="logo">
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
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            // define groups for admin pages (inferred filenames)
            $admin_users_pages = ['manage_admin.php', 'manage_operator.php', 'students.php', 'operators.php'];
            $admin_financial_pages = ['income_report.php', 'transaksi_list.php'];
            $admin_tech_pages = ['analysis_todo.php', 'analysis_roadmap.php'];
            $admin_app_pages = ['calendar.php', 'wps.php'];
            $admin_forum_pages = ['index_forum.php', 'all_discussions.php', 'discussions_operator.php'];

            $is_admin_users_active = in_array($current_page, $admin_users_pages);
            $is_admin_financial_active = in_array($current_page, $admin_financial_pages);
            $is_admin_tech_active = in_array($current_page, $admin_tech_pages);
            $is_admin_app_active = in_array($current_page, $admin_app_pages);
            $is_admin_forum_active = in_array($current_page, $admin_forum_pages);
            ?>
            <ul class="nav nav-secondary">
                <li class="nav-item <?php echo ($current_page == 'dashboard_admin.php') ? 'active' : ''; ?>">
                    <a href="dashboard_admin.php" aria-expanded="false">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Management</h4>
                </li>

                <li class="nav-item <?php echo $is_admin_financial_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#admin-financial">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <p>Financial Reports</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_admin_financial_active ? 'show' : ''; ?>" id="admin-financial">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'income_report.php') ? 'active' : ''; ?>">
                                <a href="income_report.php"><i class="fas fa-file-invoice-dollar"></i><span>Income Report</span></a>
                            </li>
                            <li class="<?php echo ($current_page == 'transaksi_list.php') ? 'active' : ''; ?>">
                                <a href="transaksi_list.php"><i class="fas fa-receipt"></i><span>Transaction List</span></a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item <?php echo $is_admin_users_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#admin-users">
                        <i class="fas fa-users-cog"></i>
                        <p>Users Management</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_admin_users_active ? 'show' : ''; ?>" id="admin-users">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'manage_admin.php') ? 'active' : ''; ?>">
                                <a href="manage_admin.php">
                                    <i class="fas fa-user"></i>
                                    <span>CRUD Admin</span></a>
                            </li>
                            <li class="<?php echo ($current_page == 'manage_operator.php') ? 'active' : ''; ?>">
                                <a href="manage_operator.php"><i class="fas fa-user-cog"></i><span>CRUD Operator</span></a>
                            </li>
                            <li class="<?php echo ($current_page == 'students.php') ? 'active' : ''; ?>">
                                <a href="students.php"><i class="fas fa-user-graduate"></i><span>Students Data</span></a>
                            </li>
                            <li class="<?php echo ($current_page == 'operators.php') ? 'active' : ''; ?>">
                                <a href="operators.php"><i class="fas fa-user-tie"></i><span>Operators Data</span></a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item <?php echo $is_admin_tech_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#admin-tech">
                        <i class="fas fa-lightbulb"></i>
                        <p>Technical Analysis</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_admin_tech_active ? 'show' : ''; ?>" id="admin-tech">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'analysis_todo.php') ? 'active' : ''; ?>"><a href="analysis_todo.php"><i class="fas fa-check-square"></i><span>Analysis To Do</span></a></li>
                            <li class="<?php echo ($current_page == 'analysis_roadmap.php') ? 'active' : ''; ?>"><a href="analysis_roadmap.php"><i class="fas fa-road"></i><span>Analysis Roadmap</span></a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item <?php echo $is_admin_app_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#admin-app">
                        <i class="fas fa-table"></i>
                        <p>Application Data</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_admin_app_active ? 'show' : ''; ?>" id="admin-app">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'calendar.php') ? 'active' : ''; ?>"><a href="calendar.php"><i class="fas fa-calendar-alt"></i><span>Calendar</span></a></li>
                            <li class="<?php echo ($current_page == 'wps.php') ? 'active' : ''; ?>"><a href="wps.php">
                                    <i class="fas fa-database"></i>
                                    <span>WPS Data</span></a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item <?php echo $is_admin_forum_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#admin-forum">
                        <i class="fas fa-comments"></i>
                        <p>Discussion Center</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_admin_forum_active ? 'show' : ''; ?>" id="admin-forum">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'index_forum.php') ? 'active' : ''; ?>"><a href="index_forum.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard Forums</span></a></li>
                            <li class="<?php echo ($current_page == 'all_discussions.php') ? 'active' : ''; ?>"><a href="all_discussions.php"><i class="fas fa-comments"></i><span>All Discussions</span></a></li>
                            <li class="<?php echo ($current_page == 'discussions_operator.php') ? 'active' : ''; ?>"><a href="discussions_operator.php"><i class="fas fa-comment-dots"></i><span>Operator Discussions</span></a></li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="edit_profile.php" class="<?php echo ($current_page == 'edit_profile.php') ? 'active' : ''; ?>">
                        <i class="fas fa-user-edit"></i>
                        <p>Edit Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a id="logout-link" data-logout-url="../auth/logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>