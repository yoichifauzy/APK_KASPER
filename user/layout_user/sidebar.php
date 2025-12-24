<div class="sidebar sidebar-style-2" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="#" class="logo">
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

            $admin_forum_pages = ['index_forum.php', 'all_discussions.php', 'pengumuman.php'];
            $payment_pages = ['payment_history_cek.php', 'scan_barcode_cash.php'];

            $is_admin_forum_active = in_array($current_page, $admin_forum_pages);
            $is_payment_active = in_array($current_page, $payment_pages);
            ?>
            <ul class="nav nav-secondary">
                <li class="nav-item <?php echo ($current_page == 'dashboard_user.php') ? 'active' : ''; ?>">
                    <a href="dashboard_user.php" aria-expanded="false">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Management</h4>
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
                            <li class="<?php echo ($current_page == 'all_discussions.php') ? 'active' : ''; ?>"><a href="all_discussions.php"><i class="fas fa-list"></i><span>All Discussions</span></a></li>

                            <li class="<?php echo ($current_page == 'pengumuman.php') ? 'active' : ''; ?>"><a href="pengumuman.php"><i class="fas fa-bullhorn"></i><span>Announcements</span></a></li>

                        </ul>
                    </div>
                </li>

                <li class="nav-item <?php echo $is_payment_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#payment-management">
                        <i class="fas fa-credit-card"></i>
                        <p>Payment Management</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_payment_active ? 'show' : ''; ?>" id="payment-management">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'payment_history_cek.php') ? 'active' : ''; ?>"><a href="payment_history_cek.php"><i class="fas fa-history"></i><span>Payment History</span></a></li>
                            <li class="<?php echo ($current_page == 'scan_barcode_cash.php') ? 'active' : ''; ?>"><a href="scan_barcode_cash.php"><i class="fas fa-qrcode"></i><span>Scan Barcode Cash</span></a></li>
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
                    <a href="#" id="logout-user" data-logout-url="../auth/logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>


<!-- Sidebar related scripts moved to footer to ensure jQuery/bootstrap are loaded first -->
<script>
    // Fallback bindings to ensure sidebar toggle / collapse behavior matches admin/operator
    (function() {
        if (typeof jQuery === 'undefined') return;

        var $ = jQuery;

        $(document).ready(function() {
            // Ensure toggle buttons work even if theme script not loaded
            $('.btn-toggle.toggle-sidebar').off('click.fallback').on('click.fallback', function() {
                $('body').toggleClass('sidebar_minimize');
            });

            $('.btn-toggle.sidenav-toggler').off('click.fallback').on('click.fallback', function() {
                $('html').toggleClass('sidenav-toggled');
            });

            $('.topbar-toggler.more').off('click.fallback').on('click.fallback', function() {
                $('html').toggleClass('topbar-toggled');
            });

            // When clicking a sidebar nav link (non-collapse trigger), hide all collapse panels on small screens
            $('.sidebar .nav a').on('click.fallback', function(e) {
                var $t = $(this);
                // if this is a collapse toggle, ignore
                if ($t.attr('data-bs-toggle') === 'collapse' || $t.attr('href') === undefined) return;
                // hide all collapse groups (uses Bootstrap collapse API if available)
                try {
                    $('.sidebar .collapse').each(function() {
                        var $c = $(this);
                        if ($c.hasClass('show')) $c.collapse('hide');
                    });
                } catch (err) {
                    // fallback: remove show class
                    $('.sidebar .collapse.show').removeClass('show');
                }
                // on small screens also close sidenav classes
                if ($(window).width() < 992) {
                    $('html').removeClass('sidenav-toggled topbar-toggled');
                    $('body').removeClass('sidebar_minimize');
                }
            });

            // Initialize custom scrollbar if plugin available
            try {
                if ($.fn.scrollbar) {
                    $('.sidebar-wrapper.scrollbar, .sidebar-wrapper.scrollbar-inner').scrollbar();
                }
            } catch (e) {}
        });
    })();
</script>