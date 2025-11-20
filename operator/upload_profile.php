<?php

include '../config/database.php';

function uploadFile($file, $target_dir, $allowed_extensions)
{
    $file_name = basename($file["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ['success' => false, 'message' => "File is not an image."];
    }

    // Allow certain file formats
    if (!in_array($imageFileType, $allowed_extensions)) {
        return ['success' => false, 'message' => "Sorry, only " . implode(", ", $allowed_extensions) . " files are allowed."];
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'file_name' => $file_name];
    } else {
        return ['success' => false, 'message' => "Sorry, there was an error uploading your file."];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $profile_picture_name = null;
    $background_picture_name = null;

    $allowed_extensions = ["jpg", "png", "jpeg", "gif"];

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_upload_dir = "../upload/profile/";
        $profile_upload_result = uploadFile($_FILES['profile_picture'], $profile_upload_dir, $allowed_extensions);
        if ($profile_upload_result['success']) {
            $profile_picture_name = $profile_upload_result['file_name'];
        } else {
            echo $profile_upload_result['message'];
            exit();
        }
    }

    // Handle background picture upload
    if (isset($_FILES['background_picture']) && $_FILES['background_picture']['error'] == 0) {
        $background_upload_dir = "../upload/background/"; // Assuming a new directory for background images
        // Create the directory if it doesn't exist
        if (!is_dir($background_upload_dir)) {
            mkdir($background_upload_dir, 0777, true);
        }
        $background_upload_result = uploadFile($_FILES['background_picture'], $background_upload_dir, $allowed_extensions);
        if ($background_upload_result['success']) {
            $background_picture_name = $background_upload_result['file_name'];
        } else {
            echo $background_upload_result['message'];
            exit();
        }
    }

    // Update database
    if ($profile_picture_name || $background_picture_name) {
        $sql_parts = [];
        $bind_types = '';
        $bind_params = [];

        if ($profile_picture_name) {
            $sql_parts[] = 'profile_picture = ?';
            $bind_types .= 's';
            $bind_params[] = $profile_picture_name;
        }
        if ($background_picture_name) {
            $sql_parts[] = 'background_picture = ?';
            $bind_types .= 's';
            $bind_params[] = $background_picture_name;
        }

        $sql = "UPDATE user SET " . implode(', ', $sql_parts) . " WHERE id_user = ?";
        $bind_types .= 'i';
        $bind_params[] = $user_id;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($bind_types, ...$bind_params);

        if ($stmt->execute()) {
            header("Location: cardmember.php");
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        header("Location: cardmember.php"); // Redirect even if no files were uploaded
    }
}
