<?php
include '../display/dbconnect.php';

$billing_id = $_GET['billing_id'] ?? null;

if (!$billing_id) {
    echo "Invalid Billing ID.";
    exit;
}

// Fetch billing details for the selected billing ID
$query = "
    SELECT 
        b.billing_id,
        b.total_amount,
        b.created_at AS billing_date,
        GROUP_CONCAT(CONCAT(s.service_name, ' (₱', bi.price, ')')) AS services,
        SUM(bi.subtotal) AS subtotal
    FROM billing b
    JOIN billing_items bi ON b.billing_id = bi.billing_id
    JOIN services s ON bi.service_id = s.service_id
    WHERE b.billing_id = ?
    GROUP BY b.billing_id
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $billing_id);
$stmt->execute();
$result = $stmt->get_result();
$billing = $result->fetch_assoc();

if (!$billing) {
    echo "Billing record not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/invoice.css">
    <title>Invoice</title>
</head>
<body>
    <div class="invoice">
        <h1>Invoice</h1>
        <p><strong>Billing ID:</strong> <?= $billing['billing_id'] ?></p>
        <p><strong>Date:</strong> <?= date("F d, Y", strtotime($billing['billing_date'])) ?></p>
        <p><strong>Total Amount:</strong> ₱<?= number_format($billing['total_amount'], 2) ?></p>
        <h2>Services</h2>
        <ul>
            <?php
            $services = explode(',', $billing['services']);
            foreach ($services as $service) {
                echo "<li>" . htmlspecialchars($service) . "</li>";
            }
            ?>
        </ul>
        <div class="invoice-footer">
            <button onclick="window.print()">Print</button>
        </div>
    </div>
</body>
</html>
