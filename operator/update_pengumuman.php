<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'], $_POST['tema'], $_POST['isi'])) {
        $id = $_POST['id'];
        $tema = $_POST['tema'];
        $isi = $_POST['isi'];
        $label = isset($_POST['label_penting']) ? $_POST['label_penting'] : '';

        // Prepare an update statement
        $sql = "UPDATE announcements SET tema = ?, isi = ?, label = ? WHERE id = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sssi", $tema, $isi, $label, $id);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Record updated successfully
                $_SESSION['success'] = "Pengumuman berhasil diperbarui.";
            } else {
                $_SESSION['error'] = "Terjadi kesalahan. Gagal memperbarui pengumuman.";
            }
        }
        
        // Close statement
        $stmt->close();
    } else {
        $_SESSION['error'] = "Data tidak lengkap untuk memperbarui pengumuman.";
    }
} else {
    $_SESSION['error'] = "Metode permintaan tidak valid.";
}

// Close connection
$conn->close();

// Redirect back to the announcements page
header("location: pengumuman.php");
exit();
?>
