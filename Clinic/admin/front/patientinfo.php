<?php
session_start(); // Start the session

// Check if the patient_id is passed
if (!isset($_GET['patient_id'])) {
    echo "<p class='error'>Access denied. No patient ID provided.</p>";
    exit;
}

$patient_id = intval($_GET['patient_id']); // Sanitize input to avoid SQL injection

// Establish database connection
$conn = new mysqli("localhost", "root", "", "blanche_db");
if ($conn->connect_error) {
    echo "<p class='error'>Database connection failed.</p>";
    exit;
}

// Fetch patient data
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
    $patient_data = json_encode(['success' => true, 'patient' => $row]);
} else {
    $patient_data = json_encode(['success' => false, 'message' => 'Patient not found']);
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Information</title>
    <link rel="stylesheet" href="../css/patientinfo.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fetch patient data using AJAX
            $.ajax({
                url: '../conn/getpatient.php', // Ensure this is the correct path to getpatient.php
                type: 'GET',
                data: { patient_id: <?php echo $patient_id; ?> }, // Pass the patient_id from the URL
                dataType: 'json', // Expecting JSON response
                success: function(response) {
                    if (response.success) {
                        const patient = response.patient;
                        $('#first_name').text(patient.first_name);
                        $('#middle_initial').text(patient.middle_initial);
                        $('#last_name').text(patient.last_name);
                        $('#occupation').text(patient.occupation);
                        $('#phone_number').text(patient.phone_number);
                        $('#email').text(patient.email);
                        $('#address').text(patient.present_address);
                        $('#gender').text(patient.gender);
                        $('#dob').text(patient.date_of_birth);
                        $('#age').text(patient.age + ' years old');
                    } else {
                        alert(response.message); // Show error message if patient data is not found
                    }
                },
                error: function() {
                    alert('Error fetching patient data. Please try again later.');
                }
            });
        });
    </script>
</head>

<body>
    <div class="patient-info-container">
        <h1 class="header">Patient Information</h1>
        <div class="info-section no-container">
            <div class="row">
                <div class="column">
                    <p><strong>First Name:</strong> <span id="first_name"></span></p>
                    <p><strong>Middle Initial:</strong> <span id="middle_initial"></span></p>
                    <p><strong>Last Name:</strong> <span id="last_name"></span></p>
                    <p><strong>Occupation:</strong> <span id="occupation"></span></p>
                </div>
                <div class="column">
                    <p><strong>Mobile Number:</strong> <span id="phone_number"></span></p>
                    <p><strong>Email:</strong> <span id="email"></span></p>
                    <p><strong>Address:</strong> <span id="address"></span></p>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <p><strong>Gender:</strong> <span id="gender"></span></p>
                    <p><strong>Date of Birth:</strong> <span id="dob"></span></p>
                    <p><strong>Age:</strong> <span id="age"></span></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
