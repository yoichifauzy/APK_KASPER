<?php
header('Content-Type: application/json');

require_once '../config/cek_login.php';
otorisasi(['admin', 'operator']);
include '../config/database.php';

$response = ['status' => 'error', 'message' => 'Invalid request.'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT id, title, description, start_datetime, end_datetime, category FROM jadwal_kegiatan";
    $result = $conn->query($sql);
    $events = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $events[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'start' => $row['start_datetime'],
                'end' => $row['end_datetime'],
                'description' => $row['description'],
                'category' => $row['category']
            ];
        }
    }
    echo json_encode($events);
    exit;
}

if ($method === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            if (isset($_POST['title'], $_POST['start_datetime'], $_POST['end_datetime'], $_POST['category'])) {
                $title = $_POST['title'];
                $description = $_POST['description'] ?? null;
                $start_datetime = $_POST['start_datetime'];
                $end_datetime = $_POST['end_datetime'];
                $category = $_POST['category'];
                $created_by = $_SESSION['nama_lengkap'] ?? 'Operator';

                $sql = "INSERT INTO jadwal_kegiatan (title, description, start_datetime, end_datetime, category, created_by) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $title, $description, $start_datetime, $end_datetime, $category, $created_by);
                
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Agenda berhasil ditambahkan.'];
                } else {
                    $response['message'] = 'Gagal menyimpan agenda: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['message'] = 'Data tidak lengkap.';
            }
            break;

        case 'update':
            if (isset($_POST['id'], $_POST['title'], $_POST['start_datetime'], $_POST['end_datetime'], $_POST['category'])) {
                $id = $_POST['id'];
                $title = $_POST['title'];
                $description = $_POST['description'] ?? null;
                $start_datetime = $_POST['start_datetime'];
                $end_datetime = $_POST['end_datetime'];
                $category = $_POST['category'];

                $sql = "UPDATE jadwal_kegiatan SET title=?, description=?, start_datetime=?, end_datetime=?, category=? WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssi", $title, $description, $start_datetime, $end_datetime, $category, $id);

                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Agenda berhasil diperbarui.'];
                } else {
                    $response['message'] = 'Gagal memperbarui agenda: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['message'] = 'Data tidak lengkap untuk pembaruan.';
            }
            break;

        case 'delete':
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                $sql = "DELETE FROM jadwal_kegiatan WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);

                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Agenda berhasil dihapus.'];
                } else {
                    $response['message'] = 'Gagal menghapus agenda: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $response['message'] = 'ID agenda tidak ditemukan.';
            }
            break;

        default:
            $response['message'] = 'Aksi tidak valid.';
            break;
    }
}

$conn->close();
echo json_encode($response);
?>
