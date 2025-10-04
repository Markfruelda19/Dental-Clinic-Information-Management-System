<?php
include '../display/dbconnect.php';

session_start();
$patient_id = $_SESSION['patient_id'] ?? null; // Replace with actual session variable for the patient ID

if (!$patient_id) {
    echo "Unauthorized access!";
    exit;
}

$billings = []; // Initialize $billings to an empty array

// Fetch billing details for the logged-in patient
$query = "
    SELECT 
        b.billing_id,
        b.total_amount,
        b.created_at AS billing_date,
        GROUP_CONCAT(
            CASE 
                WHEN bi.service_id IS NULL THEN CONCAT('Additional Fee (₱', bi.price, ')') 
                ELSE CONCAT(s.service_name, ' (₱', bi.price, ')') 
            END
        ) AS services,
        SUM(bi.subtotal) AS subtotal
    FROM billing b
    JOIN billing_items bi ON b.billing_id = bi.billing_id
    LEFT JOIN services s ON bi.service_id = s.service_id
    WHERE b.patient_id = ?
    GROUP BY b.billing_id
    ORDER BY b.created_at DESC
";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $billings = $result->fetch_all(MYSQLI_ASSOC); // Assign fetched data to $billings
} else {
    echo "Database error: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bills</title>
    <link rel="stylesheet" href="../styles/billing.css"> <!-- Use a similar style as bills.php -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
</head>
<body>
<header class="billing-header">
    <div class="header-content">
        <h1>Your Bills</h1>
        <p>View all your billing details here.</p>
    </div>
</header>

<div class="billing-container">
    <?php if (count($billings) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Billing Date</th>
                    <th>Services</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($billings as $index => $billing): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars(date("F d, Y", strtotime($billing['billing_date']))) ?></td>
                        <td>
                            <ul class="services-list">
                                <?php 
                                    $services = explode(',', $billing['services']);
                                    foreach ($services as $service): 
                                ?>
                                    <li><?= htmlspecialchars($service) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>₱<?= number_format($billing['total_amount'], 2) ?></td>
                        <td>
                            <a href="../connections/generate_invoice.php?billing_id=<?= $billing['billing_id'] ?>" target="_blank">
                                <button>Download</button>
                            </a>
                            <button onclick="window.print()">Print</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-bills">
            <p>You have no bills at the moment.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
