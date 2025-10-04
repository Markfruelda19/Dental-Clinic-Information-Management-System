<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment History</title>
    <link rel="stylesheet" href="../css/persoapp.css">
</head>

<body>
    <?php
    require_once '../conn/db_connection.php';

    // Check if 'patient_id' exists in the URL and handle gracefully if it doesn't
    if (!isset($_GET['patient_id'])) {
        die('Error: Patient ID is required.');
    }

    $patient_id = $_GET['patient_id']; // Get patient_id from the URL
    $appointments = [];

    try {
        // Correct the SQL query by removing the trailing comma
        $sql_appointments = "
            SELECT 
                appointment_id, 
                patient_id, 
                service_type, 
                other_details, 
                expected_date, 
                expected_time, 
                medical_history, 
                allergies, 
                status 
            FROM appointments 
            WHERE patient_id = ? AND status IN ('completed', 'confirmed') 
            ORDER BY expected_date DESC, expected_time ASC
        ";
        $stmt_appointments = $conn->prepare($sql_appointments);
        if (!$stmt_appointments) {
            throw new Exception("SQL preparation failed: " . $conn->error);
        }
        $stmt_appointments->bind_param("i", $patient_id);
        $stmt_appointments->execute();
        $result_appointments = $stmt_appointments->get_result();

        // Process the result
        while ($row = $result_appointments->fetch_assoc()) {
            $date = new DateTime($row['expected_date']);
            $formatted_date = $date->format('F j, Y');
            $appointments[] = [
                'appointment_id' => $row['appointment_id'],
                'patient_id' => $row['patient_id'],
                'service_type' => $row['service_type'],
                'other_details' => $row['other_details'],
                'expected_date' => $formatted_date,
                'expected_time' => $row['expected_time'],
                'medical_history' => $row['medical_history'],
                'allergies' => $row['allergies'],
                'status' => $row['status'],
            ];
        }

        $stmt_appointments->close();
        $conn->close();
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
    ?>

    <div class="content">
        <h1>Appointment History</h1>
        <?php if (!empty($appointments)): ?>
            <div class="table-container">
                <table class="appointment-table">
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Patient ID</th>
                            <th>Service Type</th>
                            <th>Other Details</th>
                            <th>Expected Date</th>
                            <th>Expected Time</th>
                            <th>Medical History</th>
                            <th>Allergies</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?= htmlspecialchars($appointment['appointment_id']); ?></td>
                                <td><?= htmlspecialchars($appointment['patient_id']); ?></td>
                                <td><?= htmlspecialchars($appointment['service_type']); ?></td>
                                <td><?= htmlspecialchars($appointment['other_details']); ?></td>
                                <td><?= htmlspecialchars($appointment['expected_date']); ?></td>
                                <td><?= htmlspecialchars($appointment['expected_time']); ?></td>
                                <td><?= htmlspecialchars($appointment['medical_history']); ?></td>
                                <td><?= htmlspecialchars($appointment['allergies']); ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower(htmlspecialchars($appointment['status'])); ?>">
                                        <?= htmlspecialchars($appointment['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-data">No past appointments found.</p>
        <?php endif; ?>
    </div>
</body>

</html>
