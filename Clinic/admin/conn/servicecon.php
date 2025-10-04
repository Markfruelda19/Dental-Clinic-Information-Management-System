<?php
include '../conn/db_connection.php';

header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle adding a new service
        if (isset($_POST['../conn/add_service.php'])) {
            $service_name = trim($_POST['ServiceName']);
            $price_min = isset($_POST['PriceMin']) && is_numeric($_POST['PriceMin']) ? $_POST['PriceMin'] : null;
            $price_max = isset($_POST['PriceMax']) && is_numeric($_POST['PriceMax']) ? $_POST['PriceMax'] : null;
            $unit_based = isset($_POST['UnitBased']) ? (int)$_POST['UnitBased'] : 0;
            $description = trim($_POST['Description']);

            // Validate required fields
            if (empty($service_name) || empty($description)) {
                echo json_encode(['status' => 'error', 'message' => 'Service Name and Description are required.']);
                exit;
            }

            // Insert the service
            $query = "INSERT INTO services (service_name, price_min, price_max, unit_based, description) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sddis", $service_name, $price_min, $price_max, $unit_based, $description);

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Service added successfully!',
                    'new_service' => [
                        'service_id' => $stmt->insert_id,
                        'service_name' => $service_name,
                        'price_min' => $price_min,
                        'price_max' => $price_max,
                        'unit_based' => $unit_based,
                        'description' => $description
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to add service: ' . $stmt->error]);
            }
            exit;
        }

        // Handle editing an existing service
        if (isset($_POST['../conn/edit_service.php'])) {
            $service_id = isset($_POST['ServiceID']) ? (int)$_POST['ServiceID'] : null;
            $service_name = trim($_POST['ServiceName']);
            $price_min = isset($_POST['PriceMin']) && is_numeric($_POST['PriceMin']) ? $_POST['PriceMin'] : null;
            $price_max = isset($_POST['PriceMax']) && is_numeric($_POST['PriceMax']) ? $_POST['PriceMax'] : null;
            $unit_based = isset($_POST['UnitBased']) ? (int)$_POST['UnitBased'] : 0;
            $description = trim($_POST['Description']);

            // Validate required fields
            if (empty($service_id) || empty($service_name) || empty($description)) {
                echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
                exit;
            }

            // Update the service
            $query = "UPDATE services SET service_name = ?, price_min = ?, price_max = ?, unit_based = ?, description = ? WHERE service_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sddisi", $service_name, $price_min, $price_max, $unit_based, $description, $service_id);

            if ($stmt->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Service updated successfully!',
                    'updated_service' => [
                        'service_id' => $service_id,
                        'service_name' => $service_name,
                        'price_min' => $price_min,
                        'price_max' => $price_max,
                        'unit_based' => $unit_based,
                        'description' => $description
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update service: ' . $stmt->error]);
            }
            exit;
        }

        // Handle unknown action
        echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
        exit;
    }

    // Invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
    exit;
}
