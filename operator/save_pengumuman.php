<?php
session_start();
require_once '../config/cek_login.php';
require_once '../config/database.php';

otorisasi(['admin', 'operator']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve form data
    $tema = filter_input(INPUT_POST, 'tema', FILTER_SANITIZE_STRING);
    $isi = filter_input(INPUT_POST, 'isi', FILTER_SANITIZE_STRING);
    $pembuat = filter_input(INPUT_POST, 'pembuat', FILTER_SANITIZE_STRING);
    $label = isset($_POST['label_penting']) ? filter_input(INPUT_POST, 'label_penting', FILTER_SANITIZE_STRING) : null;

    if ($tema && $isi && $pembuat) {
        $sql = "INSERT INTO announcements (tema, isi, pembuat, label) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param('ssss', $tema, $isi, $pembuat, $label);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Pengumuman berhasil ditambahkan.";
            } else {
                $_SESSION['error'] = "Gagal menyimpan pengumuman: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Gagal mempersiapkan statement: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Semua field harus diisi.";
    }

    $conn->close();
} else {
    $_SESSION['error'] = "Metode request tidak valid.";
}

header('Location: pengumuman.php');
exit;
?>
