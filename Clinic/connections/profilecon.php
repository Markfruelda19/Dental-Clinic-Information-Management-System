<?php
// Start the session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Function to fetch user data based on the logged-in patient_id
function fetchUserData($conn) {
    // Check if the user is logged in with patient_id
    if (!isset($_SESSION['patient_id'])) {
        return null; // Not logged in
    }

    // Get the logged-in user's patient_id from the session
    $loggedInPatientId = $_SESSION['patient_id'];

    // Prepare a SQL statement to fetch user data by patient_id
    $stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
    if ($stmt === false) {
        die("Failed to prepare statement: " . $conn->error);
    }

    // Bind the patient_id parameter and execute the query
    $stmt->bind_param("i", $loggedInPatientId);
    $stmt->execute();
    
    // Get the result of the query
    $result = $stmt->get_result();
    if ($result === false) {
        die("Error executing query: " . $stmt->error);
    }

    // Fetch the user data if it exists
    $userData = null; // Default to null
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc(); // Fetch user data
    }

    // Close the statement
    $stmt->close();
    
    return $userData; // Return the fetched user data
}

// Note: Keep the connection open if it is needed later in other scripts.
// You can manually close it in the relevant scripts after the data is processed.
// Uncomment the line below if you want to close it here.
// $conn->close();
?>
