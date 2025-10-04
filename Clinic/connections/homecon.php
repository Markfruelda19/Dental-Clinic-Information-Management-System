<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../display/dbconnect.php'; // Ensure this file contains the correct DB credentials

// Check if the login success flag is set in the session
$login_success = isset($_SESSION['login_success']) && $_SESSION['login_success'] === true;

// Unset the login success flag to prevent showing the toast on page refresh
unset($_SESSION['login_success']);

// Fetch patient details (assuming patient details are stored in the 'patients' table)
$patient_id = $_SESSION['patient_id'] ?? null;
$first_name = $last_name = $full_name = $middle_initial = $date_of_birth = $gender = $occupation = $age = $phone_number = $email = $present_address = $username = "N/A";
$appointment_id = $status = $expected_date = $expected_time = $service_type = $other_details = $medical_history = $allergies = "N/A";

if ($patient_id) {
    // Prepare patient details query
    $sql_patient = "SELECT last_name, first_name, full_name, middle_initial, date_of_birth, gender, occupation, age, phone_number, email, present_address, username FROM patients WHERE patient_id = ?";
    $stmt_patient = $conn->prepare($sql_patient);

    if ($stmt_patient) {
        $stmt_patient->bind_param("i", $patient_id);
        $stmt_patient->execute();
        $stmt_patient->bind_result($last_name, $first_name, $full_name, $middle_initial, $date_of_birth, $gender, $occupation, $age, $phone_number, $email, $present_address, $username);
        $stmt_patient->fetch();
        $stmt_patient->close();
    }

    // Prepare appointment details query
    $sql_appointment = "SELECT appointment_id, status, expected_date, expected_time, service_type, other_details, medical_history, allergies
                        FROM appointments
                        WHERE patient_id = ? AND expected_date >= CURDATE() AND status = 'Confirmed'
                        ORDER BY expected_date ASC LIMIT 1";
    $stmt_appointment = $conn->prepare($sql_appointment);

    if ($stmt_appointment) {
        $stmt_appointment->bind_param("i", $patient_id);
        $stmt_appointment->execute();
        $stmt_appointment->bind_result($appointment_id, $status, $expected_date, $expected_time, $service_type, $other_details, $medical_history, $allergies);
        $stmt_appointment->fetch();
        $stmt_appointment->close();
    }
}

// Close the database connection
$conn->close();
