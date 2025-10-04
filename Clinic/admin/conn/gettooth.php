<?php
// Clean output buffer to prevent any unintended output
if (ob_get_length()) ob_end_clean();

// Set content type to JSON
header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include '../conn/db_connection.php';

// Validate input
if (!isset($_GET['patient_id'])) {
    echo json_encode(['error' => 'Patient ID is required']);
    exit;
}

$patient_id = intval($_GET['patient_id']);

try {
    $stmt = $conn->prepare("
        SELECT 
            t.tooth_number,
            t.status,
            DATE_FORMAT(MAX(t.date), '%Y-%m-%d') as last_checked,
            GROUP_CONCAT(
                DISTINCT t.treatment 
                ORDER BY t.date DESC 
                SEPARATOR ', '
            ) as recent_treatment
        FROM treatments t
        WHERE t.patient_id = ?
        GROUP BY t.tooth_number, t.status
    ");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    // Bind parameters and execute
    $stmt->bind_param('i', $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize tooth data
    $toothData = [];
    for ($i = 1; $i <= 32; $i++) {
        $toothData[$i] = [
            'status' => 'Healthy',
            'last_checked' => null,
            'treatment' => []
        ];
    }

    // Populate tooth data
    while ($row = $result->fetch_assoc()) {
        $toothNumber = $row['tooth_number'];
        $toothData[$toothNumber] = [
            'status' => $row['status'],
            'last_checked' => $row['last_checked'],
            'treatment' => $row['recent_treatment'] ? explode(', ', $row['recent_treatment']) : []
        ];
    }

    // Return valid JSON
    echo json_encode([
        'success' => true,
        'tooth_data' => $toothData,
        'debug' => [
            'rows_found' => $result->num_rows,
            'patient_id' => $patient_id
        ]
    ]);

} catch (Exception $e) {
    error_log('Error in get_tooth.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
