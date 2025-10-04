<?php
include '../conn/db_connection.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['treatment_id'];

    $stmt = $conn->prepare("DELETE FROM treatments WHERE treatment_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Treatment deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete treatment']);
    }

    $stmt->close();
}
