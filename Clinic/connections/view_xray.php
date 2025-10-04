<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your X-ray Records</title>
    <link rel="stylesheet" href="../styles/viewxray.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    require_once '../display/dbconnect.php';

    // Start the session and check if the patient is logged in
    session_start();
    if (!isset($_SESSION['patient_id'])) {
        die('Error: You must be logged in to view your X-rays.');
    }

    $patient_id = $_SESSION['patient_id']; // Get patient ID from session

    // Fetch X-ray records for the patient from the database
    $stmt = $conn->prepare("SELECT * FROM xray_images WHERE patient_id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $xrays = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    ?>

    <div class="content">
        <h1>Your X-ray Records</h1>

        <!-- Display Uploaded X-rays -->
        <?php if (!empty($xrays)): ?>
            <div class="table-container">
                <table class="appointment-table">
                    <thead>
                        <tr>
                            <th>X-ray ID</th>
                            <th>File Name</th>
                            <th>Description</th>
                            <th>X-ray Date</th>
                            <th>Uploaded On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($xrays as $xray): ?>
                            <tr>
                                <td><?= htmlspecialchars($xray['xray_id']); ?></td>
                                <td><?= htmlspecialchars($xray['xray_name']); ?></td>
                                <td><?= htmlspecialchars($xray['description']); ?></td>
                                <td><?= htmlspecialchars($xray['xray_date']); ?></td>
                                <td>
                                    <?php 
                                    // Use the file URL, not the local path
                                    $file_url = '/Clinic/admin/uploads/xrays/' . $xray['xray_name'];
                                    
                                    // You could also check if the file exists on the server
                                    $file_path = $_SERVER['DOCUMENT_ROOT'] . $file_url;
                                    if (file_exists($file_path)) {
                                        echo htmlspecialchars(date('Y-m-d', filemtime($file_path)));
                                    } else {
                                        echo "File not found.";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= htmlspecialchars($file_url); ?>" download class="download-btn">Download</a>
                                    <a href="#" class="view-btn" onclick="viewXray('<?= htmlspecialchars($file_url); ?>')">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-data">No X-rays found for you.</p>
        <?php endif; ?>
    </div>

    <script>
        // Function to view the X-ray image in a modal
        function viewXray(imagePath) {
            Swal.fire({
                title: 'X-ray Preview',
                imageUrl: imagePath,
                imageWidth: 600,
                imageAlt: 'X-ray Image',
                confirmButtonText: 'Close',
            });
        }
    </script>
</body>

</html>
