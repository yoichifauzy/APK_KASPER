<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Definisikan halaman untuk setiap grup menu
$cash_management_pages = ['transaksi_list.php', 'pembayaran.php', 'kelola_kas_kategori.php'];
$cash_payment_pages = ['payment_list.php', 'kelola_kas.php', 'ranking.php'];
$member_management_pages = ['kelola_user.php'];
$announcement_pages = ['pengumuman.php', 'agenda.php'];
$report_pages = ['chart.php', 'export_transaksi.php', 'export_daftar_kas.php'];

?>
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="index.php?page=user_dashboard" class="logo">
                <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
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
            <ul class="nav nav-secondary">

                <li class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <a href="index.php?page=user_dashboard" class="collapsed" aria-expanded="false">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Components</h4>
                </li>

                <!-- Member Management -->
                <li class="nav-item <?php echo in_array($current_page, $member_management_pages) ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#member-management">
                        <i class="fas fa-drafting-compass"></i>
                        <p>Member Management</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo in_array($current_page, $member_management_pages) ? 'show' : ''; ?>" id="member-management">
                        <ul class="nav nav-collapse">
                        </ul>
                    </div>
                </li>

                <!-- Announcement Agenda -->
                <li class="nav-item <?php echo in_array($current_page, $announcement_pages) ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#announcement-agenda">
                        <i class="fas fa-balance-scale"></i>
                        <p>Announcement Agenda</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo in_array($current_page, $announcement_pages) ? 'show' : ''; ?>" id="announcement-agenda">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'pengumuman.php') ? 'active' : ''; ?>">
                                <a href="index.php?page=pengumuman">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Announcement</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'agenda.php') ? 'active' : ''; ?>">
                                <a href="index.php?page=agenda">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Activity Schedule</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Discussion -->
                <li class="nav-item <?php echo ($current_page == 'chat.php') ? 'active' : ''; ?>">
                    <a href="index.php?page=chat">
                        <i class="fas fa-sign-language"></i>
                        <p>Discussion</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#">
                        <i class="fas fa-cog"></i>
                        <p>Settings</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>