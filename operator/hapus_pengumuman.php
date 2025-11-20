<?php
require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);

include '../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepare a delete statement
    $sql = "DELETE FROM announcements WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $id);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Records deleted successfully. Redirect to landing page
            $_SESSION['success'] = "Pengumuman berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Terjadi kesalahan. Gagal menghapus pengumuman.";
        }
    }

    // Close statement
    $stmt->close();
} else {
    // If ID is not set, redirect with an error
    $_SESSION['error'] = "ID pengumuman tidak ditemukan.";
}

// Close connection
$conn->close();

// Redirect back to the announcements page
header("location: pengumuman.php");
exit();
?>
