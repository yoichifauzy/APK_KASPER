<?php
include '../config/database.php';

// Log file path (optional, for debugging)
$log_file = __DIR__ . '/chat_auto_delete.log';

// Delete messages older than 3 hours
$query = "DELETE FROM chat WHERE waktu < NOW() - INTERVAL 3 HOUR";

if (mysqli_query($conn, $query)) {
    $affected_rows = mysqli_affected_rows($conn);
    $message = "SUCCESS: $affected_rows chat messages deleted older than 3 hours at " . date('Y-m-d H:i:s') . "\n";
    // file_put_contents($log_file, $message, FILE_APPEND); // Uncomment to enable logging
    echo $message;
} else {
    $error_message = "ERROR: Failed to delete chat messages: " . mysqli_error($conn) . " at " . date('Y-m-d H:i:s') . "\n";
    // file_put_contents($log_file, $error_message, FILE_APPEND); // Uncomment to enable logging
    echo $error_message;
}

mysqli_close($conn);
?>