<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blanche_db";

// Create a new MySQLi connection

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total number of patients
$sqlTotalPatients = "SELECT COUNT(patient_id) AS totalPatients FROM patients";
$resultTotalPatients = $conn->query($sqlTotalPatients);
$totalPatients = $resultTotalPatients->fetch_assoc()['totalPatients'];

// Fetch count of today's confirmed appointments
$today = date('Y-m-d');
$sqlAppointmentsTodayConfirmed = "SELECT COUNT(appointment_id) AS totalTodayAppointments 
                                  FROM appointments 
                                  WHERE expected_date = ? AND status = 'confirmed'";
$stmt = $conn->prepare($sqlAppointmentsTodayConfirmed);
$stmt->bind_param("s", $today);
$stmt->execute();
$resultAppointmentsToday = $stmt->get_result();
$totalTodayAppointments = $resultAppointmentsToday->fetch_assoc()['totalTodayAppointments'];

// Fetch patient demographics
$sqlPatientDemographics = "SELECT 
    COUNT(CASE WHEN gender = 'male' THEN 1 END) AS malePatients,
    COUNT(CASE WHEN gender = 'female' THEN 1 END) AS femalePatients
FROM patients;";
$resultDemographics = $conn->query($sqlPatientDemographics);
$rowDemographics = $resultDemographics->fetch_assoc();
$malePatients = $rowDemographics['malePatients'];
$femalePatients = $rowDemographics['femalePatients'];

// Fetch account creation by day
$sqlAccountsByDay = "SELECT DATE(created_at) AS creation_date, COUNT(patient_id) AS accounts_created 
                     FROM patients
                     GROUP BY creation_date 
                     ORDER BY creation_date";
$resultAccountsByDay = $conn->query($sqlAccountsByDay);

$creationDates = [];
$accountsCreated = [];

while ($rowAccountsByDay = $resultAccountsByDay->fetch_assoc()) {
    $creationDates[] = $rowAccountsByDay['creation_date'];
    $accountsCreated[] = $rowAccountsByDay['accounts_created'];
}

// Prepare data for JavaScript
$creationDatesJson = json_encode($creationDates);
$accountsCreatedJson = json_encode($accountsCreated);

// Fetch most availed services
$sqlServiceTypes = "SELECT appointments.service_type
                    FROM appointments 
                    WHERE status = 'completed'";
$resultServiceTypes = $conn->query($sqlServiceTypes);

$serviceTypeCounts = array();

// Process each appointment's services
while ($row = $resultServiceTypes->fetch_assoc()) {
    // Split the services by comma and handle various delimiters
    $services = preg_split('/\s*,\s*/', $row['service_type']); // Only split by commas with optional spaces

    // Process each service
    foreach ($services as $service) {
        // Clean and normalize the service name
        $service = trim($service);
        // Remove any remaining special characters and normalize case
        $service = preg_replace('/[^a-zA-Z0-9\s]/', '', $service); // Remove special chars except spaces and alphanumeric
        // Convert to Title Case for consistent display
        $service = ucwords(strtolower($service));

        // Skip empty strings and common words that aren't services
        if (!empty($service) && $service !== 'Other') {
            if (!isset($serviceTypeCounts[$service])) {
                $serviceTypeCounts[$service] = 1;
            } else {
                $serviceTypeCounts[$service]++;
            }
        }
    }
}

// To display the confirmed appointments count
$confirmedAppointmentsSql = "SELECT COUNT(*) AS confirmed_count FROM appointments WHERE status = 'confirmed'";
$confirmedResult = $conn->query($confirmedAppointmentsSql);
$confirmedAppointments = $confirmedResult->fetch_assoc()['confirmed_count'];

// Sort by count in descending order
arsort($serviceTypeCounts);

// Fetch pending requests
$sqlPendingRequests = "SELECT COUNT(appointment_id) AS pendingRequests FROM appointments WHERE status = 'Pending'";
$resultPendingRequests = $conn->query($sqlPendingRequests);
$pendingRequests = $resultPendingRequests->fetch_assoc()['pendingRequests'];

// Daily revenue calculation (for paid status only)
$sqlDailyRevenue = "SELECT DATE(created_at) AS date, SUM(total_amount) AS dailyRevenue
                    FROM billing 
                    WHERE status = 'paid'
                    GROUP BY DATE(created_at)
                    ORDER BY DATE(created_at)";
$resultDailyRevenue = $conn->query($sqlDailyRevenue);

$dailyRevenueDates = [];
$dailyRevenueAmounts = [];

while ($row = $resultDailyRevenue->fetch_assoc()) {
    $dailyRevenueDates[] = $row['date'];
    $dailyRevenueAmounts[] = (float)$row['dailyRevenue'];
}

// Encode the data for JavaScript
$dailyRevenueDatesJSON = json_encode($dailyRevenueDates);
$dailyRevenueAmountsJSON = json_encode($dailyRevenueAmounts);

// Total revenue calculation (for paid status only)
$sqlTotalRevenue = "SELECT SUM(total_amount) AS totalRevenue
                    FROM billing 
                    WHERE status = 'paid'";
$resultTotalRevenue = $conn->query($sqlTotalRevenue);

// Use a default value of 0.00 if no revenue is found
$totalRevenue = $resultTotalRevenue->fetch_assoc()['totalRevenue'] ?? 0.00;

// Fetch cancelled appointments
$sqlCancelledAppointments = "SELECT COUNT(appointment_id) AS cancelledAppointments FROM appointments WHERE status = 'cancelled'";
$resultCancelledAppointments = $conn->query($sqlCancelledAppointments);
$cancelledAppointments = $resultCancelledAppointments->fetch_assoc()['cancelledAppointments'];

// Prepare arrays for the chart
$serviceTypes = array_keys($serviceTypeCounts);
$serviceCounts = array_values($serviceTypeCounts);


// Fetch appointments created per month
$sqlAppointmentsByMonth = "SELECT DATE_FORMAT(expected_date, '%Y-%m') AS month, COUNT(appointment_id) AS appointments_created 
                         FROM appointments
                         GROUP BY month 
                         ORDER BY month";
$resultAppointmentsByMonth = $conn->query($sqlAppointmentsByMonth);

$appointmentMonths = [];
$appointmentsCreatedByMonth = [];

while ($rowAppointmentsByMonth = $resultAppointmentsByMonth->fetch_assoc()) {
    $appointmentMonths[] = $rowAppointmentsByMonth['month'];
    $appointmentsCreatedByMonth[] = $rowAppointmentsByMonth['appointments_created'];
}

// Prepare data for JavaScript (monthly appointments)
$appointmentMonthsJson = json_encode($appointmentMonths);
$appointmentsCreatedByMonthJson = json_encode($appointmentsCreatedByMonth);

$conn->close();
