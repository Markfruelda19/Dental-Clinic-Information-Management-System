<?php
include '../conn/db_connection.php';
header('Content-Type: application/json');

try {
    // Decode JSON payload
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['service_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Service ID is required.']);
        exit;
    }

    $service_id = (int)$data['service_id'];

    // Check if the service exists
    $checkQuery = "SELECT COUNT(*) as count FROM services WHERE service_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $service_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Service not found.']);
        exit;
    }

    // Delete the service
    $query = "DELETE FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $service_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Service deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete service: ' . $stmt->error]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
?>
