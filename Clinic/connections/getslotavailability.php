<?php
header('Content-Type: application/json');

if (!isset($_GET['date'])) {
    echo json_encode(['success' => false, 'message' => 'Date not specified']);
    exit();
}

$date = $_GET['date'];
$conn = new mysqli("localhost", "root", "", "blanche_db");

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

$query = "SELECT expected_time, COUNT(*) as count FROM appointments WHERE expected_date = ? GROUP BY expected_time";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    // Format time as "h:i A" (e.g., "11:00 AM")
    $formattedTime = date("g:i A", strtotime($row['expected_time']));
    $slots[$formattedTime] = (int)$row['count'];
}

// Debug output to ensure we’re returning correctly formatted data
echo json_encode([
    'success' => true,
    'slots' => $slots,
    'debug' => [
        'requested_date' => $date,
        'raw_times' => array_column($result->fetch_all(MYSQLI_ASSOC), 'expected_time'),
        'formatted_times' => array_keys($slots)
    ]
]);

$stmt->close();
$conn->close();
?>
