<?php
include '../conn/db_connection.php';
header('Content-Type: application/json');

try {
    $service_name = trim($_POST['ServiceName']);
    $price_min = isset($_POST['PriceMin']) ? (float)$_POST['PriceMin'] : null;
    $price_max = isset($_POST['PriceMax']) ? (float)$_POST['PriceMax'] : null;
    $unit_based = isset($_POST['UnitBased']) ? (int)$_POST['UnitBased'] : 0;
    $description = trim($_POST['Description']);

    // Validate required fields
    if (empty($service_name) || empty($description)) {
        echo json_encode(['status' => 'error', 'message' => 'Service Name and Description are required.']);
        exit;
    }

    if (is_null($price_min) && is_null($price_max)) {
        echo json_encode(['status' => 'error', 'message' => 'At least one price value (min or max) is required.']);
        exit;
    }

    // Calculate service_price (average if both min and max are provided)
    $service_price = 0;
    if (!is_null($price_min) && !is_null($price_max)) {
        $service_price = ($price_min + $price_max) / 2;
    } elseif (!is_null($price_min)) {
        $service_price = $price_min;
    } elseif (!is_null($price_max)) {
        $service_price = $price_max;
    }

    // Insert the service into the database
    $query = "INSERT INTO services (service_name, service_price, price_min, price_max, unit_based, description, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement: ' . $conn->error]);
        exit;
    }

    // Correct the number of bind parameters and their types: s (string), d (double), i (integer)
    $stmt->bind_param("sddiss", $service_name, $service_price, $price_min, $price_max, $unit_based, $description);

    if ($stmt->execute()) {
        $new_service_id = $stmt->insert_id; // Get the last inserted ID
        echo json_encode([
            'status' => 'success',
            'message' => 'Service added successfully!',
            'new_service' => [
                'service_id' => $new_service_id,
                'service_name' => $service_name,
                'price_min' => $price_min,
                'price_max' => $price_max,
                'service_price' => $service_price,
                'unit_based' => $unit_based,
                'description' => $description
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to execute statement: ' . $stmt->error]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
?>
