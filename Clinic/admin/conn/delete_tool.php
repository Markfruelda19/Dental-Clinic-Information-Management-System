<?php
include '../conn/db_connection.php';
if (isset($_GET['id'])) {
    $ToolID = $_GET['id'];
    $query = "DELETE FROM tools WHERE ToolID = $ToolID";

    if ($conn->query($query)) {
        header("Location: inventory.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
