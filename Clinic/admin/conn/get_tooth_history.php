<?php
// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure JSON response
header('Content-Type: application/json');

// Include the database connection
include '../conn/db_connection.php';

// Validate input parameters
if (!isset($_GET['patient_id']) || !isset($_GET['tooth_number'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters: patient_id and/or tooth_number',
    ]);
    exit;
}

$patient_id = intval($_GET['patient_id']);
$tooth_number = intval($_GET['tooth_number']);

try {
    // Check database connection
    if (!$conn) {
        throw new Exception('Database connection failed');
    }

    // Query to fetch tooth history data
    $stmt = $conn->prepare(
        "SELECT status, last_checked, treatments FROM tooth_data WHERE patient_id = ? AND tooth_number = ?"
    );
    $stmt->bind_param("ii", $patient_id, $tooth_number);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch and return data if available
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Parse treatments if stored as a serialized or JSON string
        if (isset($data['treatments']) && is_string($data['treatments'])) {
            $data['treatments'] = json_decode($data['treatments'], true) ?? [];
        }

        echo json_encode([
            'success' => true,
            'data' => $data,
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No history found for this tooth',
        ]);
    }

    $stmt->close();
} catch (Exception $e) {
    // Log the error
    error_log('Error in get_tooth_history.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching tooth history: ' . $e->getMessage(),
    ]);
} finally {
    // Close the database connection
    if ($conn) {
        $conn->close();
    }
}
