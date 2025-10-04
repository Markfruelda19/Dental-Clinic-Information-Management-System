<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blanche_db";

// Create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted and session patient_id is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['patient_id'])) {
    $patient_id = $_SESSION['patient_id'];

    // Retrieve the updated data from the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $full_name = $_POST['full_name'];
    $middle_initial = $_POST['middle_initial'];
    $date_of_birth = $_POST['date_of_birth'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $occupation = $_POST['occupation'];
    $present_address = $_POST['present_address'];

    // Prepare an update statement
    $stmt = $conn->prepare("UPDATE patients SET first_name = ?, last_name = ?, full_name = ?, middle_initial = ?, date_of_birth = ?, phone_number = ?, email = ?, occupation = ?, present_address = ? WHERE patient_id = ?");
    if ($stmt === false) {
        $_SESSION['error_message'] = "Failed to prepare the statement.";
        $conn->close(); // Close the connection before exiting
        header("Location: ../display/profile.php");
        exit;
    }

    // Bind parameters
    $stmt->bind_param("sssssssssi", $first_name, $last_name, $full_name, $middle_initial, $date_of_birth, $phone_number, $email, $occupation, $present_address, $patient_id);

    // Execute the query
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Profile updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating profile: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();

    // Redirect back to profile page
    header("Location: ../display/profile.php");
    exit;
} else {
    $_SESSION['error_message'] = "Invalid request or not logged in.";
    $conn->close(); // Close the connection before exiting
    header("Location: ../display/profile.php");
    exit;
}
if (!$conn) {
    echo "Connection is already closed.";
} else {
    echo "Connection is open.";
}
// Close the database connection
$conn->close();
