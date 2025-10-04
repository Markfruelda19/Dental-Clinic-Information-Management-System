<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$patient_id = $_SESSION['patient_id'];
$data = json_decode(file_get_contents("php://input"), true);

$conn = new mysqli("localhost", "root", "", "blanche_db");

// Check database connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

if (empty($data['services']) || !is_array($data['services'])) {
    echo json_encode(['success' => false, 'message' => 'No services selected']);
    exit();
}

if (empty($data['expected_date']) || empty($data['expected_time'])) {
    echo json_encode(['success' => false, 'message' => 'Date and time are required']);
    exit();
}

try {
    // Validate that all service IDs exist in the services table
    $serviceIDs = array_map('intval', $data['services']);
    $placeholders = implode(',', array_fill(0, count($serviceIDs), '?'));
    $validateQuery = "SELECT service_id, service_name FROM services WHERE service_id IN ($placeholders)";

    $validateStmt = $conn->prepare($validateQuery);
    $validateStmt->bind_param(str_repeat('i', count($serviceIDs)), ...$serviceIDs);
    $validateStmt->execute();

    $existingServices = [];
    $serviceNames = [];
    $result = $validateStmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $existingServices[] = $row['service_id'];
        $serviceNames[] = $row['service_name'];
    }

    $missingServices = array_diff($serviceIDs, $existingServices);
    if (!empty($missingServices)) {
        throw new Exception("One or more selected services are invalid");
    }

    $service_type = implode(", ", $serviceNames); // Create a comma-separated list of service names

    // Start transaction
    $conn->begin_transaction();

    // Insert into appointments table
    $appointmentQuery = "
        INSERT INTO appointments 
        (patient_id, service_type, other_details, expected_date, expected_time, medical_history, allergies, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())
    ";
    $appointmentStmt = $conn->prepare($appointmentQuery);
    $appointmentStmt->bind_param(
        "issssss",
        $patient_id,
        $service_type,
        $data['other_details'],
        $data['expected_date'],
        $data['expected_time'],
        $data['medical_history'],
        $data['allergies']
    );

    if (!$appointmentStmt->execute()) {
        throw new Exception("Failed to add appointment: " . $appointmentStmt->error);
    }

    $appointment_id = $appointmentStmt->insert_id;

    // Insert into appointment_services table
    $serviceQuery = "INSERT INTO appointment_services (appointment_id, service_id) VALUES (?, ?)";
    $serviceStmt = $conn->prepare($serviceQuery);

    foreach ($serviceIDs as $service_id) {
        $serviceStmt->bind_param("ii", $appointment_id, $service_id);

        if (!$serviceStmt->execute()) {
            throw new Exception("Failed to add service to appointment: " . $serviceStmt->error);
        }
    }

    // Commit transaction
    $conn->commit();

    // Set success message for SweetAlert
    $_SESSION['success_message'] = 'Appointment successfully booked!';
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($appointmentStmt)) $appointmentStmt->close();
    if (isset($serviceStmt)) $serviceStmt->close();
    $conn->close();
}
