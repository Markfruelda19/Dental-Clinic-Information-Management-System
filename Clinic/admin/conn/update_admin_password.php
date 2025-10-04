<?php
// Database connection credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blanche_db";

// Create the database connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// List of users with plain text passwords to hash and update
$usersToUpdate = [
    'mark' => 'mark', // Replace with each username => plain_text_password
    'drei' => '$2y$10$CHpykye35Q.Hu8WJfMR/xua402Hlznqb7Wo.AxNhiliw7XqermGsy',
    'johndoe' => '$2y$10$CHpykye35Q.Hu8WJfMR/xua402Hlznqb7Wo.AxNhiliw7XqermGsy',
    'chi' => '$2y$10$CHpykye35Q.Hu8WJfMR/xua402Hlznqb7Wo.AxNhiliw7XqermGsy'
];

foreach ($usersToUpdate as $username => $plainPassword) {
    // Hash the plain text password
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Update the hashed password in the database
    $stmt = $conn->prepare("UPDATE staffs SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $hashedPassword, $username);
    $stmt->execute();
    echo "Password for $username updated successfully.<br>";
}

// Close the statement and connection
$stmt->close();
$conn->close();

