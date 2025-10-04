<?php
include '../conn/db_connection.php';
header('Content-Type: application/json');

try {
    $service_id = isset($_POST['ServiceID']) ? (int)$_POST['ServiceID'] : null;
    $service_name = trim($_POST['ServiceName']);
    $price_min = isset($_POST['PriceMin']) ? (float)$_POST['PriceMin'] : null;
    $price_max = isset($_POST['PriceMax']) ? (float)$_POST['PriceMax'] : null;
    $unit_based = isset($_POST['UnitBased']) ? (int)$_POST['UnitBased'] : 0;
    $description = trim($_POST['Description']);

    // Validate required fields
    if (empty($service_id) || empty($service_name) || empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'Service ID, Service Name, and Description are required.']);
        exit;
    }

    // Prepare the query
    $query = "UPDATE services SET service_name = ?, price_min = ?, price_max = ?, unit_based = ?, description = ? WHERE service_id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }

    // Bind the parameters
    $stmt->bind_param("sddisi", $service_name, $price_min, $price_max, $unit_based, $description, $service_id);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Service updated successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update service: ' . $stmt->error]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
