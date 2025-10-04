<?php
header('Content-Type: application/json');

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blanche_db";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch appointments and patient details
$sql = "SELECT 
            a.appointment_id AS id, 
            a.service_type AS title, 
            a.other_details, 
            a.expected_date, 
            a.expected_time, 
            a.medical_history, 
            a.allergies, 
            a.status, 
            p.first_name, 
            p.last_name, 
            p.middle_initial, 
            p.date_of_birth, 
            p.gender, 
            p.phone_number, 
            p.email, 
            p.occupation, 
            p.present_address, 
            TIMESTAMPDIFF(YEAR, p.date_of_birth, CURDATE()) AS age
        FROM appointments a 
        JOIN patients p ON a.patient_id = p.patient_id";

$result = $conn->query($sql);

$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Format date and time as ISO 8601 for FullCalendar
        $startDateTime = date('Y-m-d\TH:i:s', strtotime($row['expected_date'] . ' ' . $row['expected_time']));

        $appointments[] = [
            'id' => $row['id'],
            'title' => $row['title'] ?? 'No Title',
            'start' => $startDateTime,
            'extendedProps' => [
                'other_details' => $row['other_details'] ?? 'N/A',
                'medical_history' => $row['medical_history'] ?? 'N/A',
                'allergies' => $row['allergies'] ?? 'N/A',
                'status' => $row['status'] ?? 'Unknown',
                'first_name' => $row['first_name'] ?? 'N/A',
                'last_name' => $row['last_name'] ?? 'N/A',
                'middle_initial' => $row['middle_initial'] ?? '',
                'email' => $row['email'] ?? 'N/A',
                'phone_number' => $row['phone_number'] ?? 'N/A',
                'age' => $row['age'] ?? 'N/A',
                'gender' => $row['gender'] ?? 'N/A',
                'address' => $row['present_address'] ?? 'N/A',
                'occupation' => $row['occupation'] ?? 'N/A'
            ]
        ];
    }
}

// Output the JSON response
echo json_encode($appointments, JSON_PRETTY_PRINT);

$conn->close();
?>
