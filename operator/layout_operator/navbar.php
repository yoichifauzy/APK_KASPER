<nav
    class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
        <nav
            class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="submit" class="btn btn-search pe-1">
                        <i class="fa fa-search search-icon"></i>
                    </button>
                </div>
                <input
                    type="text"
                    placeholder="Search ..."
                    class="form-control" />
            </div>
        </nav>

        <?php

        date_default_timezone_set('Asia/Jakarta');



        $day_names = [

            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',

            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu'

        ];

        $month_names = [

            'Jan' => 'Jan',
            'Feb' => 'Feb',
            'Mar' => 'Mar',
            'Apr' => 'Apr',

            'May' => 'Mei',
            'Jun' => 'Jun',
            'Jul' => 'Jul',
            'Aug' => 'Agu',

            'Sep' => 'Sep',
            'Oct' => 'Okt',
            'Nov' => 'Nov',
            'Dec' => 'Des'

        ];



        $current_day_short = date('D');

        $current_month_short = date('M');



        $day = $day_names[$current_day_short];

        $month = $month_names[$current_month_short];

        $GLOBALS['formatted_date'] = $day . ' ' . date('d') . ' ' . $month . ' ' . date('Y');

        ?>



        <div class="d-none d-lg-flex align-items-center bg-light border rounded-pill px-3 py-2 ms-3">

            <i class="fa fa-calendar-alt me-2 text-primary"></i>

            <span class="text-dark fw-bold" id="live-clock"><?php echo $GLOBALS['formatted_date'] . ' ' . date('H:i:s') . ' WIB'; ?></span>

        </div>
        <?php
        // Ensure we have a live DB connection. Some pages may have closed $conn earlier.
        if (!isset($conn) || (isset($conn) && !@mysqli_ping($conn))) {
            @include_once __DIR__ . '/../../config/database.php';
        }

        // Fetch recent announcements for notification dropdown
        $notif_count = 0;
        $recent_announcements = [];
        if (isset($conn) && @mysqli_ping($conn)) {
            $res_notif = mysqli_query($conn, "SELECT id, tema, tanggal_posting FROM announcements ORDER BY tanggal_posting DESC LIMIT 5");
            if ($res_notif) {
                $recent_announcements = [];
                while ($r = mysqli_fetch_assoc($res_notif)) {
                    $recent_announcements[] = $r;
                }
            }
            $res_count = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM announcements");
            if ($res_count) {
                $rowc = mysqli_fetch_assoc($res_count);
                $notif_count = intval($rowc['cnt']);
            }
        } else {
            // Leave defaults (0, empty list) if DB unavailable
            $notif_count = 0;
            $recent_announcements = [];
        }

        ?>

        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
            <li
                class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                <a
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="#"
                    role="button"
                    aria-expanded="false"
                    aria-haspopup="true">
                    <i class="fa fa-search"></i>
                </a>
                <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                        <div class="input-group">
                            <input
                                type="text"
                                placeholder="Search ..."
                                class="form-control" />
                        </div>
                    </form>
                </ul>
            </li>
            <li class="nav-item topbar-icon dropdown hidden-caret">
                <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="messageDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="fa fa-envelope"></i>
                    <?php
                    // compute aggregated message counts (private chats + forum replies to user's topics)
                    $message_private_count = 0;
                    $message_forum_count = 0;
                    $message_total = 0;
                    $recent_private = [];
                    $recent_forum = [];
                    if (isset($conn) && isset($_SESSION['id_user'])) {
                        $uid = mysqli_real_escape_string($conn, $_SESSION['id_user']);
                        $rc1 = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM private_chat WHERE recipient_id = '$uid'");
                        if ($rc1) {
                            $r = mysqli_fetch_assoc($rc1);
                            $message_private_count = intval($r['cnt']);
                        }
                        $rp = mysqli_query($conn, "SELECT pc.id, pc.sender_id, u.nama_lengkap AS sender_name, pc.message, pc.created_at FROM private_chat pc JOIN user u ON pc.sender_id = u.id_user WHERE pc.recipient_id = '$uid' ORDER BY pc.created_at DESC LIMIT 5");
                        if ($rp) {
                            while ($row = mysqli_fetch_assoc($rp)) $recent_private[] = $row;
                        }

                        $rc2 = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM chat c JOIN discussion_topics dt ON c.topic_id = dt.id WHERE dt.user_id = '$uid'");
                        if ($rc2) {
                            $r = mysqli_fetch_assoc($rc2);
                            $message_forum_count = intval($r['cnt']);
                        }
                        $rf = mysqli_query($conn, "SELECT c.id_chat AS id, c.topic_id, dt.title AS topic_title, c.pesan AS message, c.waktu AS created_at, u.nama_lengkap AS sender_name FROM chat c JOIN user u ON c.id_user = u.id_user JOIN discussion_topics dt ON c.topic_id = dt.id WHERE dt.user_id = '$uid' ORDER BY c.waktu DESC LIMIT 5");
                        if ($rf) {
                            while ($row = mysqli_fetch_assoc($rf)) $recent_forum[] = $row;
                        }

                        $message_total = $message_private_count + $message_forum_count;
                    }
                    ?>
                    <span class="notification" id="messageCountBadge"><?php echo $message_total; ?></span>
                </a>
                <ul class="dropdown-menu messages-notif-box animated fadeIn" aria-labelledby="messageDropdown">
                    <li>
                        <div class="dropdown-title d-flex justify-content-between align-items-center">
                            Messages
                            <a href="#" class="small">Mark all as read</a>
                        </div>
                    </li>
                    <li>
                        <div class="message-notif-scroll scrollbar-outer">
                            <div class="notif-center">
                                <?php if ($message_total <= 0): ?>
                                    <div class="text-center p-3 text-muted">Tidak ada pesan baru</div>
                                <?php else: ?>
                                    <?php if (!empty($recent_private)): ?>
                                        <div class="px-3 py-2"><strong>Private (<?php echo $message_private_count; ?>)</strong></div>
                                        <?php foreach ($recent_private as $it): ?>
                                            <a href="discussion_admin.php" class="py-2 px-3 d-block" onclick="event.preventDefault(); window.location='discussion_admin.php#user-'+<?php echo intval($it['sender_id']); ?>;">
                                                <div class="notif-content">
                                                    <span class="subject"><?php echo htmlspecialchars($it['sender_name']); ?></span>
                                                    <span class="block"><?php echo htmlspecialchars(mb_strimwidth($it['message'], 0, 80, '...')); ?></span>
                                                    <span class="time small text-muted"><?php echo date('d M Y H:i', strtotime($it['created_at'])); ?></span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($recent_forum)): ?>
                                        <div class="px-3 py-2"><strong>Forum Replies (<?php echo $message_forum_count; ?>)</strong></div>
                                        <?php foreach ($recent_forum as $it): ?>
                                            <a href="view_topic.php?id=<?php echo intval($it['topic_id']); ?>" class="py-2 px-3 d-block">
                                                <div class="notif-content">
                                                    <span class="subject"><?php echo htmlspecialchars($it['topic_title']); ?></span>
                                                    <span class="block"><?php echo htmlspecialchars(mb_strimwidth($it['message'], 0, 80, '...')); ?></span>
                                                    <span class="time small text-muted"><?php echo date('d M Y H:i', strtotime($it['created_at'])); ?></span>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a class="see-all" href="discussion_admin.php">Lihat semua pesan<i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item topbar-icon dropdown hidden-caret">
                <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="notifDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                    <i class="fa fa-bell"></i>
                    <span class="notification"><?php echo $notif_count; ?></span>
                </a>
                <ul
                    class="dropdown-menu notif-box animated fadeIn"
                    aria-labelledby="notifDropdown">
                    <li>
                        <div class="dropdown-title">
                            Pengumuman (<?php echo $notif_count; ?>)
                        </div>
                    </li>
                    <li>
                        <div class="notif-scroll scrollbar-outer">
                            <div class="notif-center">
                                <?php if (empty($recent_announcements)): ?>
                                    <div class="text-center p-3 text-muted">Tidak ada pengumuman</div>
                                <?php else: ?>
                                    <?php foreach ($recent_announcements as $ann): ?>
                                        <a href="#" class="py-2 px-3 d-block">
                                            <div class="notif-content">
                                                <span class="block"><?php echo htmlspecialchars($ann['tema']); ?></span>
                                                <span class="time small text-muted"><?php echo date('d M Y', strtotime($ann['tanggal_posting'])); ?></span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <li>
                        <a class="see-all" href="pengumuman.php">Lihat semua pengumuman<i class="fa fa-angle-right"></i>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- <li class="nav-item topbar-icon dropdown hidden-caret">
                <a
                    class="nav-link"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false">
                    <i class="fas fa-layer-group"></i>
                </a>
                <div class="dropdown-menu quick-actions animated fadeIn">
                    <div class="quick-actions-header">
                        <span class="title mb-1">Quick Actions</span>
                        <span class="subtitle op-7">Shortcuts</span>
                    </div>
                    <div class="quick-actions-scroll scrollbar-outer">
                        <div class="quick-actions-items">
                            <div class="row m-0">
                                <a class="col-6 col-md-4 p-0" href="#">
                                    <div class="quick-actions-item">
                                        <div class="avatar-item bg-danger rounded-circle">
                                            <i class="far fa-calendar-alt"></i>
                                        </div>
                                        <span class="text">Calendar</span>
                                    </div>
                                </a>
                                <a class="col-6 col-md-4 p-0" href="#">
                                    <div class="quick-actions-item">
                                        <div
                                            class="avatar-item bg-warning rounded-circle">
                                            <i class="fas fa-map"></i>
                                        </div>
                                        <span class="text">Maps</span>
                                    </div>
                                </a>
                                <a class="col-6 col-md-4 p-0" href="#">
                                    <div class="quick-actions-item">
                                        <div class="avatar-item bg-info rounded-circle">
                                            <i class="fas fa-file-excel"></i>
                                        </div>
                                        <span class="text">Reports</span>
                                    </div>
                                </a>
                                <a class="col-6 col-md-4 p-0" href="#">
                                    <div class="quick-actions-item">
                                        <div
                                            class="avatar-item bg-success rounded-circle">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <span class="text">Emails</span>
                                    </div>
                                </a>
                                <a class="col-6 col-md-4 p-0" href="#">
                                    <div class="quick-actions-item">
                                        <div
                                            class="avatar-item bg-primary rounded-circle">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </div>
                                        <span class="text">Invoice</span>
                                    </div>
                                </a>
                                <a class="col-6 col-md-4 p-0" href="#">
                                    <div class="quick-actions-item">
                                        <div
                                            class="avatar-item bg-secondary rounded-circle">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <span class="text">Payments</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </li> -->

            <li class="nav-item topbar-user dropdown hidden-caret">
                <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false">

                    <?php
                    $profile_img = '../upload/profile/' . (isset($_SESSION['profile_picture']) && $_SESSION['profile_picture'] ? $_SESSION['profile_picture'] : 'default.png');
                    ?>
                    <img src="<?php echo htmlspecialchars($profile_img); ?>" alt="Profile" class="avatar-sm rounded-circle me-2" />
                    <span class="profile-username">
                        <span class="op-7">Hi,</span>
                        <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Operator'); ?></span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                        <!-- <li>
                            <div class="user-box">
                                <div class="avatar-lg">
                                    <img
                                        src="../upload/profile/<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.png'); ?>"
                                        alt="image profile"
                                        class="avatar-img rounded" />
                                </div>
                                <div class="u-text">
                                    <h4><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Operator'); ?></h4>
                                    <p class="text-muted"><?php echo htmlspecialchars($_SESSION['email'] ?? 'operator@example.com'); ?></p>
                                    <a
                                        href="edit_user.php"
                                        class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                </div>
                            </div>
                        </li> -->
                        <li>
                            <!-- <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">My Profile</a>
                            <a class="dropdown-item" href="#">My Balance</a>
                            <a class="dropdown-item" href="#">Inbox</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Account Setting</a>
                            <div class="dropdown-divider"></div> -->
                            <a class="dropdown-item" href="../auth/logout.php">Logout</a>
                        </li>
                    </div>
                </ul>
            </li>
</nav>

<script>
    function updateClock() {
        const clockElement = document.getElementById('live-clock');
        if (clockElement) {
            const now = new Date();
            const dateString = '<?php echo $GLOBALS["formatted_date"]; ?>';

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            clockElement.textContent = `${dateString} ${hours}:${minutes}:${seconds} WIB`;
        }
    }

    // Update the clock every second
    setInterval(updateClock, 1000);

    // Initial call to display clock immediately without waiting for 1 second
    updateClock();
</script>