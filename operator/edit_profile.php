<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

$flash = null;
if (session_status() == PHP_SESSION_NONE) session_start();

$currentUserId = $_SESSION['id_user'] ?? null;
if (!$currentUserId) {
    header('Location: ../auth/login.php');
    exit;
}

// Fetch current data
$stmt = $conn->prepare('SELECT id_user, nama_lengkap, username, profile_picture FROM user WHERE id_user = ?');
$stmt->bind_param('i', $currentUserId);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // basic validation
    if ($nama_lengkap === '' || $username === '') {
        $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Nama dan username harus diisi.'];
        header('Location: edit_profile.php');
        exit;
    }

    // check username uniqueness
    $stmt = $conn->prepare('SELECT id_user FROM user WHERE username = ? AND id_user != ?');
    $stmt->bind_param('si', $username, $currentUserId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Username sudah digunakan oleh akun lain.'];
        $stmt->close();
        header('Location: edit_profile.php');
        exit;
    }
    $stmt->close();

    // Handle profile picture upload
    $profile_picture_name = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $upload_dir = __DIR__ . '/../upload/profile/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $originalName = basename($_FILES['profile_picture']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_extensions, true)) {
            $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Ekstensi file tidak diperbolehkan.'];
            header('Location: edit_profile.php');
            exit;
        }
        $safeName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $target = $upload_dir . $safeName;
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target)) {
            $profile_picture_name = $safeName;
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Gagal mengunggah profile picture.'];
            header('Location: edit_profile.php');
            exit;
        }
    }

    // Build update query
    $sql_parts = ['nama_lengkap = ?', 'username = ?'];
    $types = 'ss';
    $params = [$nama_lengkap, $username];

    if (!empty($password)) {
        $sql_parts[] = 'password = ?';
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $types .= 's';
        $params[] = $hashed;
    }
    if ($profile_picture_name) {
        $sql_parts[] = 'profile_picture = ?';
        $types .= 's';
        $params[] = $profile_picture_name;
    }

    $types .= 'i';
    $params[] = $currentUserId;

    $sql = 'UPDATE user SET ' . implode(', ', $sql_parts) . ' WHERE id_user = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        // Update session display values
        $_SESSION['nama_lengkap'] = $nama_lengkap;
        $_SESSION['username'] = $username;
        if ($profile_picture_name) $_SESSION['profile_picture'] = $profile_picture_name;

        $_SESSION['flash'] = ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Profil berhasil diperbarui.'];
        $stmt->close();
        header('Location: edit_profile.php');
        exit;
    } else {
        $err = $stmt->error ?: $conn->error;
        $_SESSION['flash'] = ['type' => 'danger', 'title' => 'Gagal', 'message' => 'Gagal memperbarui profil. ' . ($err ? 'Error: ' . $err : '')];
        $stmt->close();
        header('Location: edit_profile.php');
        exit;
    }
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Edit Profile - KASPER</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />
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
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />
</head>

<body>
    <div class="wrapper">
        <?php include 'layout_operator/sidebar.php'; ?>
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo"><img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" /></a>
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
                        <h3 class="fw-bold mb-3">Edit Profile</h3>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">Profil Saya</div>
                                </div>
                                <div class="card-body">
                                    <?php if ($flash): ?>
                                        <div class="alert alert-<?php echo htmlspecialchars($flash['type'] ?? 'info'); ?>" role="alert">
                                            <strong><?php echo htmlspecialchars($flash['title'] ?? ''); ?></strong> <?php echo htmlspecialchars($flash['message'] ?? ''); ?>
                                        </div>
                                    <?php endif; ?>
                                    <form method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" name="nama_lengkap" class="form-control" required value="<?php echo htmlspecialchars($user['nama_lengkap'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password (kosongkan jika tidak ingin mengubah)</label>
                                            <input type="password" name="password" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Profile Picture</label>
                                            <input type="file" name="profile_picture" accept="image/*" class="form-control">
                                            <?php if (!empty($user['profile_picture'])): ?>
                                                <div class="mt-2"><img src="../upload/profile/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="avatar" style="width:80px;height:80px;object-fit:cover;border-radius:50%;"></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mt-2">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <a href="dashboard_operator.php" class="btn btn-secondary">Batal</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'layout_operator/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script>
        // Remove global settings panel on this page to avoid duplicate UI
        try {
            if (typeof $ !== 'undefined') {
                $('#customTemplate').remove();
            }
        } catch (e) {
            // ignore
        }
    </script>
</body>

</html>