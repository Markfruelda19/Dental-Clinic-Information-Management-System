<?php
include '../conn/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $ToolName = $conn->real_escape_string($_POST['ToolName']);
    $Category = $conn->real_escape_string($_POST['Category']);
    $Quantity = intval($_POST['Quantity']);
    $CostPrice = floatval($_POST['CostPrice']);
    $PurchaseDate = $conn->real_escape_string($_POST['PurchaseDate']);
    $Supplier = $conn->real_escape_string($_POST['Supplier']);

    // Debugging - Log received values
    error_log("Received POST data: ToolName=$ToolName, Category=$Category, Quantity=$Quantity, CostPrice=$CostPrice, PurchaseDate=$PurchaseDate, Supplier=$Supplier");

    // Insert query
    $query = "INSERT INTO tools (ToolName, Category, Quantity, CostPrice, PurchaseDate, Supplier) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    // Check if prepare was successful
    if ($stmt === false) {
        error_log("Error preparing statement: " . $conn->error);
        echo json_encode(['success' => false, 'error' => "Error preparing statement: " . $conn->error]);
        exit();
    }

    // Bind parameters (correct type specifiers)
    if ($stmt->bind_param("ssidss", $ToolName, $Category, $Quantity, $CostPrice, $PurchaseDate, $Supplier)) {
        error_log("Parameters bound successfully.");
    } else {
        error_log("Error binding parameters: " . $stmt->error);
        echo json_encode(['success' => false, 'error' => "Error binding parameters: " . $stmt->error]);
        exit();
    }

    // Execute the query and check for success
    if ($stmt->execute()) {
        // Log success
        error_log("Tool added successfully: $ToolName");
        echo json_encode(['success' => true]);
    } else {
        // Log failure
        error_log("Error executing query: " . $stmt->error);
        echo json_encode(['success' => false, 'error' => "Error executing query: " . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
}
?>
