<?php
include '../conn/db_connection.php';
$response = ['success' => false, 'error' => null];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $ToolID = intval($_POST['ToolID']);
    $ToolName = $conn->real_escape_string($_POST['ToolName']);
    $Category = $conn->real_escape_string($_POST['Category']);
    $Quantity = intval($_POST['Quantity']);
    $CostPrice = floatval($_POST['CostPrice']);
    $PurchaseDate = $conn->real_escape_string($_POST['PurchaseDate']);
    $Supplier = $conn->real_escape_string($_POST['Supplier']);
    $UpdatedAt = date('Y-m-d H:i:s');

    // Debugging
    error_log("Updating Tool: ToolID = $ToolID, ToolName = $ToolName, Category = $Category, Quantity = $Quantity, CostPrice = $CostPrice, PurchaseDate = $PurchaseDate, Supplier = $Supplier");

    // Prepare the update query
    $query = "UPDATE tools SET 
                ToolName = ?, 
                Category = ?, 
                Quantity = ?, 
                CostPrice = ?, 
                PurchaseDate = ?, 
                Supplier = ?, 
                UpdatedAt = ? 
              WHERE ToolID = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        $response['error'] = "Error preparing the query: " . $conn->error;
        error_log($response['error']);
        echo json_encode($response);
        exit();
    }

    // Bind the parameters
    if ($stmt->bind_param(
        "ssidsssi",
        $ToolName,
        $Category,
        $Quantity,
        $CostPrice,
        $PurchaseDate,
        $Supplier,
        $UpdatedAt,
        $ToolID
    )) {
        error_log("Parameters bound successfully.");
    } else {
        $response['error'] = "Error binding parameters: " . $stmt->error;
        error_log($response['error']);
        echo json_encode($response);
        exit();
    }

    // Execute the query
    if ($stmt->execute()) {
        $response['success'] = true;
        error_log("Tool updated successfully with Supplier: $Supplier");
    } else {
        $response['error'] = "Execution Error: " . $stmt->error;
        error_log($response['error']);
    }

    // Close the statement
    $stmt->close();
} else {
    $response['error'] = "Invalid request method.";
    error_log($response['error']);
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
