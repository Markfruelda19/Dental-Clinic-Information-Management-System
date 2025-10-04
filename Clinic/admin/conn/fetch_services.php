<?php
include '../conn/db_connection.php';
header('Content-Type: application/json');

try {
    $query = "SELECT service_id, service_name, price_min, price_max, unit_based, description FROM services ORDER BY service_name ASC";
    $result = $conn->query($query);

    $services = [];
    while ($row = $result->fetch_assoc()) {
        $services[] = [
            'service_id' => (int) $row['service_id'],
            'service_name' => $row['service_name'],
            'price_min' => $row['price_min'],
            'price_max' => $row['price_max'],
            'unit_based' => (int) $row['unit_based'], // Ensure it's an integer
            'description' => $row['description']
        ];
    }

    echo json_encode(['services' => $services]);
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred while fetching services: ' . $e->getMessage()]);
}
?>
