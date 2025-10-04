<?php
session_start(); // Start the session

// Check if the patient_id is passed
if (!isset($_GET['patient_id'])) {
    echo json_encode(['success' => false, 'message' => 'No patient ID provided']);
    exit;
}

$patient_id = intval($_GET['patient_id']); // Sanitize input

// Establish database connection
$conn = new mysqli("localhost", "root", "", "blanche_db");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Fetch patient information
$sql_patient = "SELECT 
    patient_id, 
    first_name, 
    middle_initial, 
    last_name, 
    gender, 
    date_of_birth, 
    phone_number, 
    email, 
    occupation, 
    present_address, 
    age 
    FROM patients 
    WHERE patient_id = ?";
$stmt = $conn->prepare($sql_patient);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'patient' => $row]);
} else {
    echo json_encode(['success' => false, 'message' => 'Patient not found']);
}

// Close connection
$stmt->close();
$conn->close();
?>
