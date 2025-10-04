<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$patient_id = $_SESSION['patient_id'];

// Database connection
$mysqli = new mysqli("localhost", "root", "", "blanche_db");
if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit();
}

// Updated query based on your appointments table structure
$query = "SELECT appointment_id, service_type, other_details, expected_time AS appointment_time, expected_date AS appointment_date, status, medical_history, allergies 
          FROM appointments 
          WHERE patient_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize an array to store events
$events = [];
while ($row = $result->fetch_assoc()) {
    // Combine date and time for FullCalendar's `start` field
    $startDateTime = $row['appointment_date'] . 'T' . $row['appointment_time'];
    
    $events[] = [
        'id' => $row['appointment_id'],
        'title' => $row['service_type'],
        'start' => $startDateTime,
        'extendedProps' => [
            'other_details' => $row['other_details'],
            'status' => $row['status'],
            'medical_history' => $row['medical_history'],
            'allergies' => $row['allergies']
        ]
    ];
}

// Output the events as a JSON response
echo json_encode(['success' => true, 'events' => $events]);

// Close the statement and connection
$stmt->close();
$mysqli->close();

