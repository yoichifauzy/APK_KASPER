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
            $cash_management_pages = ['transaksi_list.php', 'pembayaran.php', 'kelola_kas_kategori.php', 'scan_barcode_cash.php'];
            $cash_payment_pages = ['payment_list.php', 'kelola_kas.php', 'ranking.php'];
            $member_management_pages = ['cardmember.php', 'kelola_user.php', 'point_history.php'];
            $announcement_agenda_pages = ['pengumuman.php', 'jadwal.php'];
            $financial_report_pages = ['cash_flow_report.php', 'balance_sheet.php'];
            $discussion_forum_pages = ['index_forum.php', 'create_topic.php', 'my_discussion.php', 'discussion_admin.php', 'view_category.php'];

            $is_cash_management_active = in_array($current_page, $cash_management_pages);
            $is_cash_payment_active = in_array($current_page, $cash_payment_pages);
            $is_member_management_active = in_array($current_page, $member_management_pages);
            $is_announcement_agenda_active = in_array($current_page, $announcement_agenda_pages);
            $is_financial_reports_active = in_array($current_page, $financial_report_pages);
            $is_discussion_forum_active = in_array($current_page, $discussion_forum_pages);
            ?>
            <ul class="nav nav-secondary">
                <li class="nav-item <?php echo ($current_page == 'dashboard_operator.php') ? 'active' : ''; ?>">
                    <a href="dashboard_operator.php" aria-expanded="false">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Master</h4>
                </li>
                <li class="nav-item <?php echo $is_cash_management_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#cash-management">
                        <i class="fas fa-money-check"></i>
                        <p>Cash Management</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_cash_management_active ? 'show' : ''; ?>" id="cash-management">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'transaksi_list.php') ? 'active' : ''; ?>">
                                <a href="transaksi_list.php">
                                    <i class="fas fa-money-bill"></i>
                                    <span>Transaction List</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'pembayaran.php') ? 'active' : ''; ?>">
                                <a href="pembayaran.php">
                                    <i class="fas fa-money-check-alt"></i>
                                    <span>Add Transaction</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'kelola_kas_kategori.php') ? 'active' : ''; ?>">
                                <a href="kelola_kas_kategori.php">
                                    <i class="fas fa-tags"></i>
                                    <span>Add Categories</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'scan_barcode_cash.php') ? 'active' : ''; ?>">
                                <a href="scan_barcode_cash.php">
                                    <i class="fas fa-barcode"></i>
                                    <span>Scan Barcode Cash</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item <?php echo $is_cash_payment_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#sidebarLayouts">
                        <i class="fas fa-th-list"></i>
                        <p>Cash Payment</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_cash_payment_active ? 'show' : ''; ?>" id="sidebarLayouts">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'payment_list.php') ? 'active' : ''; ?>">
                                <a href="payment_list.php">
                                    <i class="fas fa-money-check-alt"></i>
                                    <span>Payment List</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'kelola_kas.php') ? 'active' : ''; ?>">
                                <a href="kelola_kas.php">
                                    <i class="fas fa-money-bill-wave-alt"></i>
                                    <span>Add Payment</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'ranking.php') ? 'active' : ''; ?>">
                                <a href="ranking.php">
                                    <i class="fas fa-map"></i>
                                    <span>Payment Ranking</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item <?php echo $is_member_management_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#forms">
                        <i class="fas fa-drafting-compass"></i>
                        <p>Member Management </p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_member_management_active ? 'show' : ''; ?>" id="forms">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'cardmember.php') ? 'active' : ''; ?>">
                                <a href="cardmember.php">
                                    <i class="fas fa-id-card"></i>
                                    <span>Card Member</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'point_history.php') ? 'active' : ''; ?>">
                                <a href="point_history.php">
                                    <i class="fas fa-history"></i>
                                    <span>Point History</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'kelola_user.php') ? 'active' : ''; ?>">
                                <a href="kelola_user.php">
                                    <i class="fas fa-users-cog"></i>
                                    <span>CRUD Data</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item <?php echo $is_announcement_agenda_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#tables">
                        <i class="fas fa-table"></i>
                        <p>Announcement Agenda</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_announcement_agenda_active ? 'show' : ''; ?>" id="tables">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'pengumuman.php') ? 'active' : ''; ?>">
                                <a href="pengumuman.php">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Announcement</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'jadwal.php') ? 'active' : ''; ?>">
                                <a href="jadwal.php">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Activity Schedule</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item <?php echo $is_financial_reports_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#maps">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <p>Financial Reports</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_financial_reports_active ? 'show' : ''; ?>" id="maps">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'cash_flow_report.php') ? 'active' : ''; ?>">
                                <a href="cash_flow_report.php">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                    <span>Cash Flow Report</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'balance_sheet.php') ? 'active' : ''; ?>">
                                <a href="balance_sheet.php">
                                    <i class="fas fa-balance-scale"></i>
                                    <span>Balance Sheet</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item <?php echo $is_discussion_forum_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#charts">
                        <i class="fas fa-comments"></i>
                        <p>Discussion Center</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_discussion_forum_active ? 'show' : ''; ?>" id="charts">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'index_forum.php') ? 'active' : ''; ?>">
                                <a href="index_forum.php">
                                    <i class="fas fa-tachometer-alt"></i>
                                    <span>Dashboard Forums</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'create_topic.php') ? 'active' : ''; ?>">
                                <a href="create_topic.php">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>New Topic</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'my_discussion.php') ? 'active' : ''; ?>">
                                <a href="my_discussion.php">
                                    <i class="fas fa-comments"></i>
                                    <span>Topic Chats</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'discussion_admin.php') ? 'active' : ''; ?>">
                                <a href="discussion_admin.php">
                                    <i class="fas fa-user-shield"></i>
                                    <span>Admin Discussion</span>
                                </a>
                            <li class="<?php echo ($current_page == 'view_category.php') ? 'active' : ''; ?>">
                                <a href="view_category.php">
                                    <i class="fas fa-folder-open"></i>
                                    <span>Discussion Category</span>
                                </a>
                            </li>

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