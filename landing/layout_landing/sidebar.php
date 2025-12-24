<?php
// Ensure Font Awesome is loaded for landing sidebar icons (only once)
if (empty($GLOBALS['landing_fontawesome_loaded'])) {
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
    $GLOBALS['landing_fontawesome_loaded'] = true;
}
?>
<div class="sidebar sidebar-style-2" data-background-color="dark">
    <?php
    // Active state computation
    $current_page = basename($_SERVER['PHP_SELF']);

    $is_main_active = ($current_page === 'landingpage.php');
    $is_features_active = in_array($current_page, ['role_mahasiswa.php', 'role_operator.php', 'role_admin.php']);
    $is_pricing_active = in_array($current_page, ['cash_price.php']);
    $is_demo_active = ($current_page === 'demo.php');
    $is_label_active = ($current_page === 'label_cash.php');
    $is_docs_active = in_array($current_page, ['docs', 'documentation', 'documentation/index.html']);
    $is_faq_active = ($current_page === 'faq.php');
    $is_contact_active = ($current_page === 'contact.php');
    $is_programmer_active = in_array($current_page, ['structure.php', 'class_school.php', 'organization.php', 'picket.php', 'project.php']);
    $is_social_active = in_array($current_page, ['teknologiinfromasi.php', 'programming.php', 'socialmedia.php']);
    ?>
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="/APK_KAS/index.html" class="logo">
                <img
                    src="/APK_KAS/assets/img/kaiadmin/logo_light.svg"
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
            <ul class="nav nav-secondary">
                <li class="nav-item <?php echo ($is_main_active) ? 'active' : ''; ?>">
                    <a href="/APK_KAS/landingpage.php">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Components</h4>
                </li>
                <li class="nav-item <?php echo ($is_features_active) ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#base">
                        <i class="fas fa-layer-group"></i>
                        <p>Fitur Role</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo ($is_features_active) ? 'show' : ''; ?>" id="base">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'role_mahasiswa.php') ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/role_mahasiswa.php">
                                    <i class="fas fa-user-graduate"></i>
                                    <span>Role Mahasiswa</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'role_operator.php') ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/role_operator.php">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <span>Role Operator</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'role_admin.php') ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/role_admin.php">
                                    <i class="fas fa-user-shield"></i>
                                    <span>Role Admin</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item <?php echo ($is_demo_active || $is_pricing_active || $is_docs_active || $is_faq_active || $is_contact_active) ? 'active submenu' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#sidebarLayouts">
                        <i class="fas fa-th-list"></i>
                        <p>Cash Coding</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo ($is_demo_active || $is_pricing_active || $is_docs_active || $is_faq_active || $is_contact_active) ? 'show' : ''; ?>" id="sidebarLayouts">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo $is_pricing_active ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/cash_price.php">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>Cash Price</span>
                                </a>
                            </li>
                            <!-- <li class="<?php echo $is_demo_active ? 'active' : ''; ?>">
                                <a href="demo.php">
                                    <i class="fas fa-mobile-alt"></i>
                                    <span>Demo Application</span>
                                </a>
                            </li> -->
                            <li class="<?php echo $is_label_active ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/label_cash.php">
                                    <i class="fas fa-tags"></i>
                                    <span>Label Cash</span>
                                </a>
                            </li>
                            <!-- <li class="<?php echo $is_docs_active ? 'active' : ''; ?>">
                                <a href="docs/">
                                    <i class="fas fa-book"></i>
                                    <span>Documentation</span>
                                </a>
                            </li> -->
                            <li class="<?php echo $is_faq_active ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/faq.php">
                                    <i class="fas fa-question-circle"></i>
                                    <span>FAQ</span>
                                </a>
                            </li>
                            <li class="<?php echo $is_contact_active ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/contact.php">
                                    <i class="fas fa-envelope"></i>
                                    <span>Contact</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item <?php echo $is_programmer_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#forms">
                        <i class="fas fa-code"></i>
                        <p>Programmer</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_programmer_active ? 'show' : ''; ?>" id="forms">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo $is_class_active ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/class_school.php">
                                    <i class="fas fa-school"></i>
                                    <span>Class</span>
                                </a>
                            </li>
                            <li class="<?php echo $is_structure_active ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/structure.php">
                                    <i class="fas fa-table"></i>
                                    <span>Structure</span>
                                </a>
                            </li>
                            <li class="<?php echo $is_organization_active ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/organization.php">
                                    <i class="fas fa-sitemap"></i>
                                    <span>Organization</span>
                                </a>
                            </li>
                            <li class="<?php echo $is_picket_active ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/picket.php">
                                    <i class="fas fa-project-diagram"></i>
                                    <span>Picket</span>
                                </a>
                            </li>
                            <li class="<?php echo $is_project_active ? 'active' : ''; ?>">
                                <a href="#">
                                    <i class="fas fa-folder-open"></i>
                                    <span>Project</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item <?php echo $is_social_active ? 'active' : ''; ?>">
                    <a data-bs-toggle="collapse" href="#tables">
                        <i class="fas fa-users"></i>
                        <p>Social</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse <?php echo $is_social_active ? 'show' : ''; ?>" id="tables">
                        <ul class="nav nav-collapse">
                            <li class="<?php echo ($current_page == 'teknologiinformasi.php') ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/teknologiinformasi.php">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Information</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'programming.php') ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/programming.php">
                                    <i class="fas fa-laptop-code"></i>
                                    <span>Programming</span>
                                </a>
                            </li>
                            <li class="<?php echo ($current_page == 'socialmedia.php') ? 'active' : ''; ?>">
                                <a href="/APK_KAS/landing/socialmedia.php">
                                    <i class="fas fa-hashtag"></i>
                                    <span>Media Online</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>