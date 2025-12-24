<?php
// User navbar
if (!isset($conn)) {
    @include_once __DIR__ . '/../../config/database.php';
}
date_default_timezone_set('Asia/Jakarta');

// Prepare date strings
$day_names = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
$month_names = ['Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Agu', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'];
$current_day_short = date('D');
$current_month_short = date('M');
$day = $day_names[$current_day_short] ?? date('D');
$month = $month_names[$current_month_short] ?? date('M');
$GLOBALS['formatted_date'] = $day . ' ' . date('d') . ' ' . $month . ' ' . date('Y');

// Notification counts (announcements + private messages)
$notif_count = 0;
$message_count = 0;
$recent_announcements = [];
$recent_messages = [];
if (isset($conn) && isset($_SESSION['id_user'])) {
    $uid = mysqli_real_escape_string($conn, $_SESSION['id_user']);
    $ra = mysqli_query($conn, "SELECT id, tema, tanggal_posting FROM announcements ORDER BY tanggal_posting DESC LIMIT 5");
    if ($ra) while ($r = mysqli_fetch_assoc($ra)) $recent_announcements[] = $r;
    $caq = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM announcements");
    if ($caq) {
        $c = mysqli_fetch_assoc($caq);
        $notif_count = intval($c['cnt']);
    }

    $rm = mysqli_query($conn, "SELECT pc.id, pc.sender_id, pc.message, pc.created_at, u.nama_lengkap AS sender_name FROM private_chat pc JOIN user u ON pc.sender_id = u.id_user WHERE pc.recipient_id = '$uid' ORDER BY pc.created_at DESC LIMIT 5");
    if ($rm) while ($r = mysqli_fetch_assoc($rm)) $recent_messages[] = $r;
    $cm = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM private_chat WHERE recipient_id = '$uid'");
    if ($cm) {
        $cc = mysqli_fetch_assoc($cm);
        $message_count = intval($cc['cnt']);
    }
}
?>

<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="submit" class="btn btn-search pe-1"><i class="fa fa-search search-icon"></i></button>
                </div>
                <input type="text" placeholder="Search ..." class="form-control" />
            </div>
        </nav>

        <div class="d-none d-lg-flex align-items-center bg-light border rounded-pill px-3 py-2 ms-3">
            <i class="fa fa-calendar-alt me-2 text-primary"></i>
            <span class="text-dark fw-bold" id="live-clock"><?php echo $GLOBALS['formatted_date'] . ' ' . date('H:i:s') . ' WIB'; ?></span>
        </div>

        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">




            <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <?php $profile_img = '../upload/profile/' . (isset($_SESSION['profile_picture']) && $_SESSION['profile_picture'] ? $_SESSION['profile_picture'] : 'default.png'); ?>
                    <img src="<?php echo htmlspecialchars($profile_img); ?>" alt="Profile" class="avatar-sm rounded-circle me-2" />
                    <span class="profile-username"><span class="op-7">Hi,</span> <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User'); ?></span></span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                            <div class="user-box">
                                <div class="avatar-lg">
                                    <img src="<?php echo htmlspecialchars($profile_img); ?>" alt="image profile" class="avatar-img rounded" />
                                </div>
                                <div class="u-text">
                                    <h4><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'User'); ?></h4>
                                    <p class="text-muted"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>

                                </div>
                            </div>
                        </li>

                    </div>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<script>
    function updateClock() {
        const clockElement = document.getElementById('live-clock');
        if (clockElement) {
            const now = new Date();
            const dateString = '<?php echo $GLOBALS['formatted_date']; ?>';
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockElement.textContent = `${dateString} ${hours}:${minutes}:${seconds} WIB`;
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>