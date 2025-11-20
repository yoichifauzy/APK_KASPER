<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

include '../config/database.php'; // pastikan koneksi ada: $conn (mysqli)

// Helper: ambil setting
function get_setting($conn, $key, $default = null)
{
    $stmt = $conn->prepare("SELECT `value` FROM settings WHERE `name` = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $value = null;
    $stmt->bind_result($value);
    if ($stmt->fetch()) {
        return $value;
    }
    return $default;
}

// Helper update ranking (tambahkan atau kurangi)
function adjust_ranking($conn, $id_user, $delta_rajin = 0, $delta_telat = 0, $delta_poin = 0)
{
    // cek existing
    $sql = "SELECT id_ranking, jumlah_rajinnya, jumlah_telatnya, poin FROM ranking WHERE id_user = ? LIMIT 1";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        // Define variables before binding by reference
        $id_r = 0;
        $jrajin = 0;
        $jtelat = 0;
        $poin = 0;
        $stmt->bind_result($id_r, $jrajin, $jtelat, $poin);
        if ($stmt->fetch()) {
            $stmt->close();
            $jrajin = $jrajin + $delta_rajin;
            $jtelat = $jtelat + $delta_telat;
            $poin = $poin + $delta_poin;
            if ($jrajin < 0) $jrajin = 0;
            if ($jtelat < 0) $jtelat = 0;
            $upd = "UPDATE ranking SET jumlah_rajinnya=?, jumlah_telatnya=?, poin=? WHERE id_user=?";
            $u = $conn->prepare($upd);
            $u->bind_param("iiii", $jrajin, $jtelat, $poin, $id_user);
            $u->execute();
            $u->close();
        } else {
            $stmt->close();
            // insert baru
            $ins = $conn->prepare("INSERT INTO ranking (id_user, jumlah_rajinnya, jumlah_telatnya, poin) VALUES (?, ?, ?, ?)");
            $init_rajin = max(0, $delta_rajin);
            $init_telat = max(0, $delta_telat);
            $init_poin = $delta_poin;
            $ins->bind_param("iiii", $id_user, $init_rajin, $init_telat, $init_poin);
            $ins->execute();
            $ins->close();
        }
    }
}

// upload handler
// $id_user optional: if provided the file will be named pembayaran_{idUser}_{YYYYmmdd_His}.{ext}
function handle_bukti_upload($file, $upload_dir = "upload/", $id_user = null, &$error = null)
{
    $error = null;
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Kode error upload: ' . $file['error'];
        return null;
    }
    // valid extension & size
    $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];
    $max_size = 2 * 1024 * 1024; // 2 MB
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext)) {
        $error = 'Ekstensi file tidak diperbolehkan. Hanya: ' . implode(', ', $allowed_ext);
        return null;
    }
    if ($file['size'] > $max_size) {
        $error = 'Ukuran file melebihi batas 2MB.';
        return null;
    }

    // ensure dir exists
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $error = 'Gagal membuat folder tujuan upload: ' . $upload_dir;
            return null;
        }
    }
    if (!is_writable($upload_dir)) {
        $error = 'Folder upload tidak bisa ditulis: ' . $upload_dir . '. Periksa permission.';
        return null;
    }

    if ($id_user) {
        $timestamp = date('Ymd_His');
        $safe_user = preg_replace('/[^0-9a-zA-Z_-]/', '', (string)$id_user);
        $newname = "pembayaran_{$safe_user}_{$timestamp}." . $ext;
    } else {
        $newname = time() . "_" . bin2hex(random_bytes(5)) . "." . $ext;
    }
    $target = rtrim($upload_dir, "/") . "/" . $newname;
    // quick checks on tmp file
    if (empty($file['tmp_name']) || !file_exists($file['tmp_name'])) {
        $error = 'File sementara upload tidak ditemukan (tmp_name kosong atau tidak ada).';
        return null;
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        $error = 'File bukan hasil upload HTTP (is_uploaded_file = false).';
        return null;
    }

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $target;
    }

    // fallback: try copy (useful on some Windows setups)
    if (@copy($file['tmp_name'], $target)) {
        // attempt to remove tmp file
        @unlink($file['tmp_name']);
        return $target;
    }

    $last = error_get_last();
    $extra = $last ? ('; system error: ' . ($last['message'] ?? 'unknown')) : '';
    $error = 'move_uploaded_file gagal dan fallback copy juga gagal — mungkin permission folder atau tmp tidak tersedia' . $extra;
    return null;
}

// Ambil setting default
$jatuh_tempo_hari = intval(get_setting($conn, 'jatuh_tempo_hari', 10));
$poin_rajinnya = intval(get_setting($conn, 'poin_rajinnya', 10));
$poin_telat = intval(get_setting($conn, 'poin_telat', -5));

// ======= HANDLE ACTIONS: ADD / EDIT / DELETE =======
$msg = "";
$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tambah pembayaran
    if (isset($_POST['add_payment'])) {
        $id_user = intval($_POST['id_user']);
        $id_kas = !empty($_POST['id_kas']) ? intval($_POST['id_kas']) : null;
        $id_kategori_post = !empty($_POST['id_kategori']) ? intval($_POST['id_kategori']) : null;
        // build tanggal_bayar from year/month/day selects if provided (order: year, month, day)
        if (isset($_POST['tanggal_bayar_year']) && isset($_POST['tanggal_bayar_month']) && isset($_POST['tanggal_bayar_day'])) {
            $y = intval($_POST['tanggal_bayar_year']);
            $m = intval($_POST['tanggal_bayar_month']);
            $d = intval($_POST['tanggal_bayar_day']);
            if (checkdate($m, $d, $y)) {
                $tanggal_bayar = sprintf('%04d-%02d-%02d', $y, $m, $d);
            } else {
                // fallback to today if invalid
                $tanggal_bayar = date('Y-m-d');
            }
        } else {
            $tanggal_bayar = !empty($_POST['tanggal_bayar']) ? $_POST['tanggal_bayar'] : date('Y-m-d');
        }
        $jumlah_input = isset($_POST['jumlah']) && $_POST['jumlah'] !== '' ? floatval($_POST['jumlah']) : null;

        // Tentukan due date: jika id_kas tersedia, gunakan bulan dari kas.tanggal
        $kas_tanggal = null;
        if ($id_kas) {
            $q = $conn->prepare("SELECT tanggal, jumlah FROM kas WHERE id_kas = ? LIMIT 1");
            $q->bind_param("i", $id_kas);
            $q->execute();
            $q->bind_result($ktgl, $kas_jumlah);
            if ($q->fetch()) {
                $kas_tanggal = $ktgl;
            }
            $q->close();
        }

        // Tentukan jumlah final: prioritas field jumlah input, else kas_jumlah (jika ada), else NULL
        $jumlah = $jumlah_input !== null ? $jumlah_input : ($kas_jumlah ?? null);

        // build due_date
        if ($kas_tanggal) {
            $y = date("Y", strtotime($kas_tanggal));
            $m = date("m", strtotime($kas_tanggal));
            $due_date = sprintf("%04d-%02d-%02d", $y, $m, $jatuh_tempo_hari);
        } else {
            // gunakan month dari tanggal bayar
            $y = date("Y", strtotime($tanggal_bayar));
            $m = date("m", strtotime($tanggal_bayar));
            $due_date = sprintf("%04d-%02d-%02d", $y, $m, $jatuh_tempo_hari);
        }

        // Tentukan status: 'proses', 'lunas', atau 'telat'
        // Dapatkan total yang sudah dibayar untuk kas ini oleh user ini
        $total_already_paid = 0;
        if ($id_kas) {
            $stmt_paid = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND id_kas = ?");
            $stmt_paid->bind_param("ii", $id_user, $id_kas);
            $stmt_paid->execute();
            $stmt_paid->bind_result($total_paid);
            if ($stmt_paid->fetch()) {
                $total_already_paid = $total_paid;
            }
            $stmt_paid->close();
        }
        $new_cumulative_total = $total_already_paid + $jumlah;

        $status = '';
        if ($id_kas && isset($kas_jumlah) && $new_cumulative_total < $kas_jumlah) {
            $status = 'proses';
        } else {
            // Jika pembayaran sudah mencapai atau melebihi total tagihan, atau jika tidak terikat kas tertentu
            $status = (strtotime($tanggal_bayar) <= strtotime($due_date)) ? 'lunas' : 'telat';
        }

        // upload bukti (store in upload/pembayaran with standardized name)
        $upload_dir = __DIR__ . '/../upload/pembayaran';
        $upload_err = null;
        $bukti_path = handle_bukti_upload($_FILES['bukti'] ?? null, $upload_dir, $id_user, $upload_err);
        // jika path berupa absolute (dari handle), ubah jadi relatif ke folder operator agar link bekerja
        if ($bukti_path) {
            // simpan nama file saja
            $bukti_db = basename($bukti_path);
        } else {
            $bukti_db = null;
        }

        // if user attempted to upload but file wasn't saved, surface an error and abort
        if ($upload_err) {
            // abort insert when upload attempted but failed
            $err = 'Upload bukti gagal: ' . $upload_err;
        }

        // Insert ke tabel pembayaran
        // include id_kategori if provided
        // detect whether pembayaran table has a 'ditambahkan_oleh' or legacy 'dibuat_oleh' column
        $has_ditambahkan_oleh = false;
        $has_dibuat_oleh = false;
        $colcheck = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'ditambahkan_oleh'");
        if ($colcheck && $colcheck->num_rows > 0) {
            $has_ditambahkan_oleh = true;
        }
        $colcheck2 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'dibuat_oleh'");
        if ($colcheck2 && $colcheck2->num_rows > 0) {
            $has_dibuat_oleh = true;
        }
        // prefer ditambahkan_oleh for payments; fall back to dibuat_oleh for compatibility
        $creator_column = null;
        if ($has_ditambahkan_oleh) $creator_column = 'ditambahkan_oleh';
        elseif ($has_dibuat_oleh) $creator_column = 'dibuat_oleh';
        $creator_val = isset($_SESSION['id_user']) ? intval($_SESSION['id_user']) : null;
        if ($id_kategori_post) {
            if ($creator_column) {
                $sql = "INSERT INTO pembayaran (id_user, id_kas, id_kategori, tanggal_bayar, status, jumlah, bukti, " . $creator_column . ") VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
            } else {
                $sql = "INSERT INTO pembayaran (id_user, id_kas, id_kategori, tanggal_bayar, status, jumlah, bukti) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
            }
        } else {
            if ($creator_column) {
                $sql = "INSERT INTO pembayaran (id_user, id_kas, tanggal_bayar, status, jumlah, bukti, " . $creator_column . ") VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
            } else {
                $sql = "INSERT INTO pembayaran (id_user, id_kas, tanggal_bayar, status, jumlah, bukti) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
            }
        }
        // jika id_kas null, bind param null harus di-handle: gunakan ssi... but simpler: cast id_kas to null or int
        if ($id_kategori_post) {
            if ($creator_column) {
                // params: i i i s s d s i
                $stmt->bind_param("iiissdsi", $id_user, $id_kas, $id_kategori_post, $tanggal_bayar, $status, $jumlah, $bukti_db, $creator_val);
            } else {
                // params: i i i s s d s
                $stmt->bind_param("iiissds", $id_user, $id_kas, $id_kategori_post, $tanggal_bayar, $status, $jumlah, $bukti_db);
            }
        } else {
            if ($creator_column) {
                $stmt->bind_param("iissdsi", $id_user, $id_kas, $tanggal_bayar, $status, $jumlah, $bukti_db, $creator_val);
            } else {
                $stmt->bind_param("iissds", $id_user, $id_kas, $tanggal_bayar, $status, $jumlah, $bukti_db);
            }
        }

        // To avoid issues with mixed types, do manual prepare / execute with mysqli_stmt::bind_param as above.
        // Execute and check
        if ($upload_err) {
            // do not attempt DB insert when upload failed
            // make sure to surface an error message to the user
            // $err already set above
        } elseif ($stmt->execute()) {
            // update ranking
            if ($status === 'lunas') {
                adjust_ranking($conn, $id_user, 1, 0, $poin_rajinnya);
            } else {
                adjust_ranking($conn, $id_user, 0, 1, $poin_telat);
            }
            $msg = "Pembayaran berhasil ditambahkan.";
            header("Location: pembayaran.php?msg=" . urlencode($msg));
            exit;
        } else {
            $err = "Gagal menambahkan pembayaran: " . $stmt->error;
        }
        $stmt->close();
    }

    // Edit pembayaran
    if (isset($_POST['edit_payment'])) {
        $id_pembayaran = intval($_POST['id_pembayaran']);
        $id_user = intval($_POST['id_user']);
        $tanggal_bayar = $_POST['tanggal_bayar'];
        $jumlah_input = isset($_POST['jumlah']) && $_POST['jumlah'] !== '' ? floatval($_POST['jumlah']) : null;
        $id_kas = !empty($_POST['id_kas']) ? intval($_POST['id_kas']) : null;
        $id_kategori_edit = !empty($_POST['id_kategori']) ? intval($_POST['id_kategori']) : null;

        // ambil record lama untuk menyesuaikan ranking jika status berubah
        $old = $conn->prepare("SELECT status FROM pembayaran WHERE id_pembayaran = ? LIMIT 1");
        $old->bind_param("i", $id_pembayaran);
        $old->execute();
        $old->bind_result($old_status);
        $old->fetch();
        $old->close();

        // hitung due date kembali
        $kas_tanggal = null;
        if ($id_kas) {
            $q = $conn->prepare("SELECT tanggal FROM kas WHERE id_kas = ? LIMIT 1");
            $q->bind_param("i", $id_kas);
            $q->execute();
            $q->bind_result($ktgl);
            if ($q->fetch()) $kas_tanggal = $ktgl;
            $q->close();
        }

        if ($kas_tanggal) {
            $y = date("Y", strtotime($kas_tanggal));
            $m = date("m", strtotime($kas_tanggal));
            $due_date = sprintf("%04d-%02d-%02d", $y, $m, $jatuh_tempo_hari);
        } else {
            $y = date("Y", strtotime($tanggal_bayar));
            $m = date("m", strtotime($tanggal_bayar));
            $due_date = sprintf("%04d-%02d-%02d", $y, $m, $jatuh_tempo_hari);
        }
        // Tentukan status baru: 'proses', 'lunas', atau 'telat'
        $kas_jumlah_edit = null;
        if ($id_kas) {
            $q_kas = $conn->prepare("SELECT jumlah FROM kas WHERE id_kas = ?");
            $q_kas->bind_param("i", $id_kas);
            $q_kas->execute();
            $q_kas->bind_result($kas_jumlah_edit);
            $q_kas->fetch();
            $q_kas->close();
        }

        // Dapatkan total yang sudah dibayar untuk kas ini oleh user ini, KECUALI pembayaran yang sedang diedit
        $total_already_paid_edit = 0;
        if ($id_kas) {
            $stmt_paid = $conn->prepare("SELECT SUM(jumlah) FROM pembayaran WHERE id_user = ? AND id_kas = ? AND id_pembayaran != ?");
            $stmt_paid->bind_param("iii", $id_user, $id_kas, $id_pembayaran);
            $stmt_paid->execute();
            $stmt_paid->bind_result($total_paid);
            if ($stmt_paid->fetch()) {
                $total_already_paid_edit = $total_paid;
            }
            $stmt_paid->close();
        }
        $new_cumulative_total_edit = $total_already_paid_edit + $jumlah_input;

        $status_new = '';
        if ($id_kas && isset($kas_jumlah_edit) && $new_cumulative_total_edit < $kas_jumlah_edit) {
            $status_new = 'proses';
        } else {
            $status_new = (strtotime($tanggal_bayar) <= strtotime($due_date)) ? 'lunas' : 'telat';
        }

        // handle bukti (jika upload baru replace)
        $upload_dir = __DIR__ . '/../upload/pembayaran';
        $upload_err = null;
        $bukti_path = handle_bukti_upload($_FILES['bukti'] ?? null, $upload_dir, $id_user, $upload_err);
        $bukti_db = $bukti_path ? basename($bukti_path) : null;
        // if replacing an existing bukti, delete the old file
        if ($bukti_db) {
            // get old filename
            $q = $conn->prepare("SELECT bukti FROM pembayaran WHERE id_pembayaran = ? LIMIT 1");
            $q->bind_param('i', $id_pembayaran);
            $q->execute();
            $q->bind_result($old_bukti);
            if ($q->fetch()) {
                if ($old_bukti) {
                    $old_path = __DIR__ . '/../upload/pembayaran/' . $old_bukti;
                    if (is_file($old_path)) @unlink($old_path);
                }
            }
            $q->close();
        }

        // if user uploaded but file wasn't saved, abort with error
        if ($upload_err) {
            $err = 'Upload bukti gagal: ' . $upload_err;
        }

        // update
        // include id_kategori in update
        $sql = "UPDATE pembayaran SET id_user = ?, id_kas = ?, id_kategori = ?, tanggal_bayar = ?, status = ?, jumlah = ?"
            . ($bukti_db ? ", bukti = ?" : "") . " WHERE id_pembayaran = ?";

        if ($stmt = $conn->prepare($sql)) {
            if ($bukti_db) {
                $stmt->bind_param("iiissdsi", $id_user, $id_kas, $id_kategori_edit, $tanggal_bayar, $status_new, $jumlah_input, $bukti_db, $id_pembayaran);
            } else {
                $stmt->bind_param("iiissdi", $id_user, $id_kas, $id_kategori_edit, $tanggal_bayar, $status_new, $jumlah_input, $id_pembayaran);
            }
            if ($upload_err) {
                // abort update when upload failed
            } elseif ($stmt->execute()) {
                // reconcile ranking: jika old_status != new_status, ubah ranking
                if ($old_status !== $status_new) {
                    if ($old_status === 'lunas') {
                        // sebelumnya rajin, sekarang diubah -> kurangi rajin dan poin
                        adjust_ranking($conn, $id_user, -1, 1, $poin_telat - $poin_rajinnya);
                    } else {
                        // sebelumnya telat, sekarang jadi lunas
                        adjust_ranking($conn, $id_user, 1, -1, $poin_rajinnya - $poin_telat);
                    }
                }
                $msg = "Pembayaran berhasil diperbarui.";
                header("Location: pembayaran.php?msg=" . urlencode($msg));
                exit;
            } else {
                $err = "Gagal update: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $err = "Gagal menyiapkan query update.";
        }
    }
}

// Hapus pembayaran
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // ambil data sebelum hapus untuk revert ranking
    $q = $conn->prepare("SELECT id_user, status FROM pembayaran WHERE id_pembayaran = ? LIMIT 1");
    $q->bind_param("i", $id);
    $q->execute();
    $q->bind_result($uid, $ustatus);
    if ($q->fetch()) {
        $q->close();
        // hapus
        $d = $conn->prepare("DELETE FROM pembayaran WHERE id_pembayaran = ? LIMIT 1");
        $d->bind_param("i", $id);
        if ($d->execute()) {
            // revert ranking: jika record yang dihapus berstatus 'lunas' -> kurangi jumlah_rajinnya & poin
            if ($ustatus === 'lunas') {
                adjust_ranking($conn, $uid, -1, 0, -$poin_rajinnya);
            } else {
                adjust_ranking($conn, $uid, 0, -1, -$poin_telat);
            }
            $msg = "Pembayaran berhasil dihapus.";
            header("Location: pembayaran.php?msg=" . urlencode($msg));
            exit;
        } else {
            $err = "Gagal menghapus: " . $d->error;
        }
    } else {
        $err = "Data pembayaran tidak ditemukan.";
    }
}

// ======= Ambil data untuk listing & form =======

// filter bulan/tahun
$filter_month = isset($_GET['month']) ? intval($_GET['month']) : 0;
$filter_year = isset($_GET['year']) ? intval($_GET['year']) : 0;

// Data users untuk dropdown
$users = [];
$res = $conn->query("SELECT id_user, nama_lengkap FROM user WHERE role = 'user' AND status='aktif' ORDER BY nama_lengkap");
while ($r = $res->fetch_assoc()) {
    $users[] = $r;
}

// Data kas untuk dropdown (opsional)
$kas_list = [];
// ambil daftar kategori terlebih dahulu
$kategori_list = [];
$rk = $conn->query("SELECT DISTINCT kk.id_kategori, kk.nama FROM kas_kategori kk JOIN kas k ON kk.id_kategori = k.id_kategori WHERE k.jenis = 'pemasukan' ORDER BY kk.nama");
if ($rk) {
    while ($rkk = $rk->fetch_assoc()) {
        $kategori_list[] = $rkk;
    }
}

$r2 = $conn->query("SELECT k.id_kas, k.id_kategori, k.tanggal, k.jenis, k.jumlah, k.keterangan, COALESCE(u.username, k.dibuat_oleh) AS dibuat_oleh FROM kas k LEFT JOIN user u ON k.dibuat_oleh = u.id_user WHERE k.jenis = 'pemasukan' ORDER BY k.tanggal DESC");
while ($rr = $r2->fetch_assoc()) {
    $kas_list[] = $rr;
}

// NOTE: if your pembayaran table doesn't have `id_kategori`, run this migration first:
// ALTER TABLE pembayaran ADD COLUMN id_kategori INT NULL AFTER id_kas;
// ALTER TABLE pembayaran ADD INDEX (id_kategori);

// build query daftar pembayaran (include kategori name if set)
// include a creator display column: prefer ditambahkan_oleh, fall back to dibuat_oleh if needed
$creator_join = '';
$creator_select = "'' AS ditambahkan_oleh_display";
// determine which column exists at runtime
$colDita = null;
$c1 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'ditambahkan_oleh'");
if ($c1 && $c1->num_rows > 0) $colDita = 'ditambahkan_oleh';
else {
    $c2 = $conn->query("SHOW COLUMNS FROM pembayaran LIKE 'dibuat_oleh'");
    if ($c2 && $c2->num_rows > 0) $colDita = 'dibuat_oleh';
}
if ($colDita) {
    // left join user table alias for creator
    $creator_join = " LEFT JOIN user u_cre ON p." . $colDita . " = u_cre.id_user";
    $creator_select = "COALESCE(u_cre.username, CAST(p." . $colDita . " AS CHAR)) AS ditambahkan_oleh_display";
}
$sql = "SELECT p.id_pembayaran, p.id_user, u.nama_lengkap, p.id_kas, p.id_kategori, kk.nama AS kategori_nama, k.keterangan AS kas_ket, COALESCE(p.jumlah, k.jumlah) AS jumlah, p.tanggal_bayar, p.status, p.bukti, " . $creator_select . " 
        FROM pembayaran p
        LEFT JOIN user u ON p.id_user = u.id_user
        LEFT JOIN kas k ON p.id_kas = k.id_kas
        LEFT JOIN kas_kategori kk ON p.id_kategori = kk.id_kategori" . $creator_join . "
        WHERE 1=1";

$params = [];
$types = "";

// apply filters
if ($filter_month && $filter_year) {
    $sql .= " AND MONTH(p.tanggal_bayar) = ? AND YEAR(p.tanggal_bayar) = ?";
    $types .= "ii";
    $params[] = $filter_month;
    $params[] = $filter_year;
} elseif ($filter_year) {
    $sql .= " AND YEAR(p.tanggal_bayar) = ?";
    $types .= "i";
    $params[] = $filter_year;
}

// order
$sql .= " ORDER BY p.tanggal_bayar DESC, p.id_pembayaran DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    // bind params dynamically
    $bind_names[] = $types;
    for ($i = 0; $i < count($params); $i++) {
        $bind_name = 'bind' . $i;
        $$bind_name = $params[$i];
        $bind_names[] = &$$bind_name;
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_names);
}
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>OPERATOR KASPER - Pembayaran</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["../assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />

    <!-- Demo CSS -->
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <style>
        /* make table horizontally scrollable on small screens with smooth touch scrolling */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* avoid wrapping table cells on small devices so horizontal scrollbar appears instead */
        @media (max-width: 768px) {

            #multi-filter-select th,
            #multi-filter-select td {
                white-space: nowrap;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'layout_operator/sidebar.php'; ?>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
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
                <?php include 'layout_operator/navbar.php'; ?>
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">

                    <main class="">
                        <div class="page-header">
                            <h3 class="fw-bold mb-3">Pembayaran</h3>
                            <ul class="breadcrumbs mb-3">
                                <li class="nav-home">
                                    <a href="#"><i class="icon-home"></i></a>
                                </li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Cash Management</a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Add Transaction</a></li>
                            </ul>
                        </div>

                        <?php if (isset($_GET['msg'])): ?>
                            <div class="alert alert-success mt-2"><?= htmlspecialchars($_GET['msg']) ?></div>
                        <?php endif; ?>
                        <?php if ($err): ?>
                            <div class="alert alert-danger mt-2"><?= htmlspecialchars($err) ?></div>
                        <?php endif; ?>

                        <?php if (isset($_GET['debug_upload']) && $_GET['debug_upload'] == '1'): ?>
                            <div class="alert alert-info mt-2">
                                <strong>Debug upload:</strong>
                                <ul>
                                    <li>upload_max_filesize = <?= ini_get('upload_max_filesize') ?></li>
                                    <li>post_max_size = <?= ini_get('post_max_size') ?></li>
                                    <li>max_file_uploads = <?= ini_get('max_file_uploads') ?></li>
                                    <li>file_uploads = <?= ini_get('file_uploads') ?></li>
                                    <li>upload_tmp_dir = <?= ini_get('upload_tmp_dir') ?: '(default / system temp)' ?></li>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="card-title">Student Payments</h4>
                                            <div class="d-flex align-items-center">
                                                <form method="get" class="d-flex align-items-center me-3">
                                                    <label class="me-2 mb-0">Bulan:</label>
                                                    <select name="month" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                                        <?php for ($m = 1; $m <= 12; $m++): ?>
                                                            <option value="<?= $m ?>" <?= ($filter_month == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                    <label class="me-2 mb-0">Tahun:</label>
                                                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                                            <option value="<?= $y ?>" <?= ($filter_year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </form>
                                                <div>
                                                    <a href="pembayaran.php" class="btn btn-sm btn-secondary">Refresh</a>
                                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah Pembayaran</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="multi-filter-select" class="display table table-striped table-hover" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Nama</th>
                                                        <th>Jumlah</th>
                                                        <th>Tanggal Bayar</th>
                                                        <th>Status</th>
                                                        <th>Operator</th>
                                                        <th>Bukti</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Nama</th>
                                                        <th>Jumlah</th>
                                                        <th>Tanggal Bayar</th>
                                                        <th>Status</th>
                                                        <th>Operator</th>
                                                        <th>Bukti</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <?php $no = 1;
                                                    while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                                            <td><?= number_format($row['jumlah'] ?? 0, 0, ',', '.') ?></td>
                                                            <td><?= htmlspecialchars($row['tanggal_bayar']) ?></td>
                                                            <td>
                                                                <?php if ($row['status'] == 'lunas'): ?>
                                                                    <span class="badge bg-success">Lunas</span>
                                                                <?php elseif ($row['status'] == 'proses'): ?>
                                                                    <span class="badge bg-warning">Proses</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger">Telat</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($row['ditambahkan_oleh_display'] ?? '-') ?></td>
                                                            <td>
                                                                <?php if ($row['bukti']): ?>
                                                                    <a href="../upload/pembayaran/<?= urlencode($row['bukti']) ?>" target="_blank">Lihat</a>
                                                                <?php else: ?>
                                                                    -
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0);" class="btn btn-sm btn-warning btn-edit-payment" data-payment='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') ?>'>Edit</a>
                                                                <a href="?action=delete&id=<?= $row['id_pembayaran'] ?>" class="btn btn-sm btn-danger btn-delete-payment">Hapus</a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </main>
                </div>
            </div>

            <!-- Modal Edit Pembayaran -->
            <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditTitle">Edit Pembayaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="edit_payment" value="1">
                                <input type="hidden" name="id_pembayaran" id="edit_id_pembayaran">
                                <input type="hidden" name="id_kas" id="edit_id_kas">
                                <div class="mb-3">
                                    <label>Nama</label>
                                    <select name="id_user" id="edit_id_user" class="form-select" required>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id_user'] ?>"><?= htmlspecialchars($u['nama_lengkap']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label>Tanggal Bayar</label>
                                    <input type="date" name="tanggal_bayar" id="edit_tanggal_bayar" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Jumlah (opsional)</label>
                                    <input type="number" step="0.01" name="jumlah" id="edit_jumlah" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label>Kategori Pembayaran (opsional)</label>
                                    <select id="edit_id_kategori" name="id_kategori" class="form-select">
                                        <option value="">--Pilih kategori--</option>
                                        <?php foreach ($kategori_list as $kat): ?>
                                            <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label>Bukti (unggah jika ingin mengganti)</label>
                                    <input type="file" name="bukti" class="form-control">
                                    <div id="edit_current_bukti"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Tambah Pembayaran -->
            <div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title">Tambah Pembayaran</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="add_payment" value="1">
                                <div class="mb-3">
                                    <label>Nama Mahasiswa</label>
                                    <select name="id_user" class="form-select" required>
                                        <option value="">--Pilih--</option>
                                        <?php foreach ($users as $u): ?>
                                            <option value="<?= $u['id_user'] ?>"><?= htmlspecialchars($u['nama_lengkap']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                                                 <div class="mb-3">
                                                                    <label>Kategori Pembayaran (opsional)</label>
                                                                    <select name="id_kategori" class="form-select">
                                                                        <option value="">--Pilih kategori--</option>
                                                                        <?php foreach ($kategori_list as $kat): ?>
                                                                            <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama']) ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                
                                                                <div class="mb-3">
                                                                    <label>Pilih Tagihan (opsional)</label>
                                                                    <select name="id_kas" class="form-select">
                                                                        <option value="">--Pilih Tagihan--</option>
                                                                        <?php foreach ($kas_list as $kas_item): ?>
                                                                            <option value="<?= $kas_item['id_kas'] ?>"><?= htmlspecialchars($kas_item['keterangan']) ?> (<?= date('d M Y', strtotime($kas_item['tanggal'])) ?>)</option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                <div class="mb-3">
                                    <label>Tanggal Bayar</label>
                                    <div class="d-flex gap-2">
                                        <select name="tanggal_bayar_year" id="tanggal_bayar_year" class="form-select" style="width: 33%" required></select>
                                        <select name="tanggal_bayar_month" id="tanggal_bayar_month" class="form-select" style="width: 33%" required></select>
                                        <select name="tanggal_bayar_day" id="tanggal_bayar_day" class="form-select" style="width: 34%" required></select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Jumlah</label>
                                    <input type="number" step="0.01" name="jumlah" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Bukti (jpg/png/pdf) max 2MB</label>
                                    <input type="file" name="bukti" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary">Simpan Pembayaran</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- footer -->
            <?php include 'layout_operator/footer.php'; ?>
            <!-- footer -->
        </div>

    </div>
    </div>
    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="../assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="../assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#multi-filter-select").DataTable({
                pageLength: 10,
                responsive: true,
                scrollX: true,
                initComplete: function() {
                    this.api().columns().every(function() {
                        var column = this;
                        var select = $(
                                '<select class="form-select form-select-sm"><option value=""></option></select>'
                            )
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            // strip HTML tags for cells that contain badges/links
                            var text = d.replace(/<[^>]*>?/gm, '');
                            select.append('<option value="' + $.trim(text) + '">' + $.trim(text) + '</option>');
                        });
                    });
                },
            });
        });
    </script>
    <script>
        // confirmation dialogs for edit and delete using SweetAlert
        $(function() {
            var editModal = new bootstrap.Modal(document.getElementById('modalEdit'));

            // Edit confirmation
            $(document).on('click', '.btn-edit-payment', function(e) {
                e.preventDefault();
                var paymentData = $(this).data('payment');

                swal({
                    title: 'Edit Pembayaran?',
                    text: 'Anda akan mengedit data pembayaran ini. Lanjutkan?',
                    icon: 'info',
                    buttons: {
                        cancel: {
                            text: 'Batal',
                            visible: true,
                            className: 'btn btn-secondary'
                        },
                        confirm: {
                            text: 'Edit',
                            visible: true,
                            className: 'btn btn-primary'
                        }
                    }
                }).then(function(ok) {
                    if (ok) {
                        // Populate the form fields
                        document.getElementById('modalEditTitle').textContent = 'Edit Pembayaran #' + paymentData.id_pembayaran;
                        document.getElementById('edit_id_pembayaran').value = paymentData.id_pembayaran;
                        document.getElementById('edit_id_user').value = paymentData.id_user;
                        document.getElementById('edit_tanggal_bayar').value = paymentData.tanggal_bayar;
                        document.getElementById('edit_jumlah').value = paymentData.jumlah;
                        document.getElementById('edit_id_kategori').value = paymentData.id_kategori;
                        document.getElementById('edit_id_kas').value = paymentData.id_kas;

                        // Handle current proof file
                        var currentBuktiDiv = document.getElementById('edit_current_bukti');
                        if (paymentData.bukti) {
                            currentBuktiDiv.innerHTML = '<small>File saat ini: ' + paymentData.bukti + '</small>';
                        } else {
                            currentBuktiDiv.innerHTML = '';
                        }

                        // Show the modal
                        editModal.show();
                    }
                });
            });

            // Delete confirmation
            $(document).on('click', '.btn-delete-payment', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                swal({
                    title: 'Hapus Pembayaran?',
                    text: 'Yakin ingin menghapus?',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'Batal',
                            visible: true,
                            className: 'btn btn-secondary'
                        },
                        confirm: {
                            text: 'Hapus',
                            visible: true,
                            className: 'btn btn-danger'
                        }
                    },
                    dangerMode: true
                }).then(function(willDelete) {
                    if (willDelete) {
                        window.location = url;
                    }
                });
            });
        });
    </script>
    <script>
        // filter kas options by selected kategori
        function filterKas(selectKategoriEl, selectKasEl) {
            var kat = $(selectKategoriEl).val();
            $(selectKasEl).find('option').each(function() {
                var dk = $(this).data('kategori');
                // the empty option should always be visible
                if (!$(this).val()) {
                    $(this).show();
                    return;
                }
                if (!kat || (dk && String(dk) === String(kat))) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            // if the currently selected kas is hidden, reset to empty
            var sel = $(selectKasEl).val();
            if (sel) {
                var opt = $(selectKasEl).find('option[value="' + sel + '"]');
                if (opt.length && opt.is(':hidden')) {
                    $(selectKasEl).val('');
                }
            }
        }

        $(function() {
            // Add modal
            $('#filter_kategori').on('change', function() {
                filterKas(this, '#select_kas_add');
            });

            // Edit form (if present)
            $('#edit_id_kategori').on('change', function() {
                filterKas(this, '#edit_id_kas');
            });

            // on page load, if edit form exists and has a selected kas, pre-select its category
            (function preselectEditCategory() {
                var selEdit = $('#select_kas_edit');
                if (selEdit.length) {
                    var selectedKas = selEdit.val();
                    if (selectedKas) {
                        var opt = selEdit.find('option[value="' + selectedKas + '"]');
                        if (opt.length) {
                            var dk = opt.data('kategori');
                            if (dk) $('#filter_kategori_edit').val(dk);
                        }
                    }
                    // apply filter initially
                    filterKas('#filter_kategori_edit', '#select_kas_edit');
                }
                // also filter add select on load (show all by default)
                filterKas('#filter_kategori', '#select_kas_add');
            })();
        });
    </script>
    <script>
        // Auto-dismiss the specific success alert for payment additions after 5 seconds
        $(function() {
            var selector = ".alert-success:contains('Pembayaran berhasil ditambahkan.')";
            var $alert = $(selector);
            if ($alert.length) {
                setTimeout(function() {
                    $alert.fadeOut(400, function() {
                        $(this).remove();
                    });
                }, 5000); // 5000ms = 5s
            }
        });
    </script>
    <script>
        // Populate year/month/day selects for add modal (order: year, month, day)
        (function() {
            var $y = $('#tanggal_bayar_year');
            var $m = $('#tanggal_bayar_month');
            var $d = $('#tanggal_bayar_day');

            if ($y.length && $m.length && $d.length) {
                var today = new Date();
                var curY = today.getFullYear();
                var curM = today.getMonth() + 1;
                var curD = today.getDate();

                // populate years: current year -5 .. current year +1 (adjust as needed)
                var startY = curY - 5;
                var endY = curY + 1;
                for (var yy = startY; yy <= endY; yy++) {
                    $y.append($('<option>').val(yy).text(yy));
                }

                // months 1..12
                for (var mm = 1; mm <= 12; mm++) {
                    var txt = ('0' + mm).slice(-2) + ' - ' + new Date(curY, mm - 1).toLocaleString(undefined, {
                        month: 'long'
                    });
                    $m.append($('<option>').val(mm).text(txt));
                }

                function daysInMonth(year, month) {
                    return new Date(year, month, 0).getDate();
                }

                function refreshDays() {
                    var selY = parseInt($y.val(), 10) || curY;
                    var selM = parseInt($m.val(), 10) || curM;
                    var dim = daysInMonth(selY, selM);
                    $d.empty();
                    for (var dd = 1; dd <= dim; dd++) {
                        $d.append($('<option>').val(dd).text(('0' + dd).slice(-2)));
                    }
                }

                // set defaults
                $y.val(curY);
                $m.val(curM);
                refreshDays();
                $d.val(curD);

                // update days when year/month changes
                $y.on('change', refreshDays);
                $m.on('change', refreshDays);
            }
        })();
    </script>
</body>

</html>