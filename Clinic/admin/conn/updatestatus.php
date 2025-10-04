<?php
header('Content-Type: application/json');

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blanche_db";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Parse input data
$data = json_decode(file_get_contents("php://input"), true);
$appointmentId = $data['id'] ?? null;
$status = $data['status'] ?? null;

// Validate input
if (!$appointmentId || !$status) {
    echo json_encode(["success" => false, "message" => "Invalid input data."]);
    exit;
}

// Update the status in the database
$sql = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("si", $status, $appointmentId);
    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Appointment status updated successfully.",
            "updatedStatus" => $status,
            "appointmentId" => $appointmentId
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update appointment status."]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Failed to prepare the database statement."]);
}

$conn->close();
