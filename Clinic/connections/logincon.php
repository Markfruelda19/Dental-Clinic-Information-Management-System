<?php
session_start();

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
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));

    // Check if the user is a staff
    $stmt = $conn->prepare("SELECT * FROM staffs WHERE username = ?");
    if ($stmt === false) {
        die("Failed to prepare statement: " . $conn->error);
    }

    // Bind the username to the query and execute it
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            // Check user role and redirect accordingly
            $_SESSION['staff_id'] = $user['staff_id'];
            $_SESSION['welcome_message'] = "Welcome, " . $user['username'] . "!";
            
            if ($user['role'] === 'Admin' || $user['role'] === 'Dentist') {
                header("Location: ../admin/front/admin.php"); // Redirect to admin dashboard
            } else {
                header("Location: ../display/navbar.php"); // Redirect to general staff dashboard
            }
            exit();
        } else {
            $_SESSION['error_message'] = "The password you entered is incorrect. Please try again.";
            header("Location: ../display/login.php");
            exit();
        }        
    } else {
        // Check patients table if no staff found
        $stmt = $conn->prepare("SELECT * FROM patients WHERE username = ?");
        if ($stmt === false) {
            die("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Redirect patients specifically
                $_SESSION['patient_id'] = $user['patient_id'];
                $_SESSION['welcome_message'] = "Welcome, " . $user['username'] . "!";
                header("Location: ../display/navbar.php"); // Redirect to patient dashboard
                exit();
            } else {
                $_SESSION['error_message'] = "The password you entered is incorrect. Please try again.";
                header("Location: ../display/login.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "No user found with that username. Please try again.";
            header("Location: ../display/login.php");
            exit();
        }
    }
}

// Close the statement and connection
$stmt->close();
$conn->close();
