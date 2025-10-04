<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blanche_db";

// Create the database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $middle_initial = mysqli_real_escape_string($conn, trim($_POST['middle_initial']));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $date_of_birth = mysqli_real_escape_string($conn, trim($_POST['date_of_birth']));
    $age = (int)mysqli_real_escape_string($conn, trim($_POST['age']));
    $gender = mysqli_real_escape_string($conn, trim($_POST['gender']));
    $phone_number = mysqli_real_escape_string($conn, trim($_POST['phone_number']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $occupation = mysqli_real_escape_string($conn, trim($_POST['occupation']));
    $present_address = mysqli_real_escape_string($conn, trim($_POST['present_address']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Concatenate first name, middle initial, and last name
    $full_name = $first_name . ' ' . $middle_initial . '. ' . $last_name;

    // Insert the data into the `patients` table
    $sql = "INSERT INTO patients (first_name, middle_initial, last_name, full_name, date_of_birth, age, gender, phone_number, email, occupation, present_address, username, password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind the parameters to the SQL query
    $stmt->bind_param("ssssissssssss", $first_name, $middle_initial, $last_name, $full_name, $date_of_birth, $age, $gender, $phone_number, $email, $occupation, $present_address, $username, $hashed_password);
    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        // Registration successful, set success message
        $_SESSION['success_message'] = "Your registration was successful!";
        header("Location: ../display/login.php"); // Redirect directly to login.php
        exit();
    } else {
        // Error during registration
        $_SESSION['error_message'] = "Registration failed. Please try again.";
        header("Location: ../display/register.php");
        exit();
    }

    // Only attempt to close $stmt if it was successfully prepared
    if ($stmt) {
        $stmt->close();
    }
    $conn->close();
} else {
    echo "Invalid request method.";
}
