<?php
include '../conn/db_connection.php';

// Fetch tools and calculate reorder recommendations
$query = "SELECT ToolID, ToolName, Quantity, AverageDailyUsage, LeadTime, SafetyStock 
          FROM tools";
$result = $conn->query($query);

$recommendations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Calculate Reorder Point
        $reorder_point = ($row['AverageDailyUsage'] * $row['LeadTime']) + $row['SafetyStock'];

        // If stock is below reorder point, recommend restocking
        if ($row['Quantity'] < $reorder_point) {
            $recommendations[] = [
                'ToolID' => $row['ToolID'],
                'ToolName' => $row['ToolName'],
                'Quantity' => $row['Quantity'],
                'ReorderPoint' => $reorder_point,
            ];
        }
    }
}

// Return recommendations as JSON
header('Content-Type: application/json');
echo json_encode($recommendations);

