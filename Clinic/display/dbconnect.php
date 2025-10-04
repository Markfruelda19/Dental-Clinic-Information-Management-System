<?php
$last_name = $first_name = $full_name = $middle_initial = $birthdate = $gender = $occupation = $age = $phone_number = $email = $present_address = $username = $password = "";
// Connect to the database and fetch appointments data (already included in your PHP code)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blanche_db";

$conn = new mysqli($servername, $username, $password, $dbname);

   if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
}
