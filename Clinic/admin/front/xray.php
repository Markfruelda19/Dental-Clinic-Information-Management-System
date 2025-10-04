<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X-ray Record</title>
    <link rel="stylesheet" href="../css/xray.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    require_once '../conn/db_connection.php';

    // Check if 'patient_id' exists in the URL
    if (!isset($_GET['patient_id'])) {
        die('Error: Patient ID is required.');
    }

    $patient_id = $_GET['patient_id'];

    // Handle file upload
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['xray_file'])) {
        $upload_dir = '../uploads/xrays/';

        // Ensure the upload directory exists
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate a unique file name to avoid overwriting
        $file_name = time() . '_' . basename($_FILES['xray_file']['name']);
        $file_path = $upload_dir . $file_name;
        $description = $_POST['description'];
        $xray_date = $_POST['xray_date'];
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

        // Check file type (optional)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array(strtolower($file_extension), $allowed_extensions)) {
            echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and PDF are allowed.');</script>";
        } else {
            // Move the uploaded file
            if (move_uploaded_file($_FILES['xray_file']['tmp_name'], $file_path)) {
                // Insert file information into the database
                $stmt = $conn->prepare("INSERT INTO xray_images (patient_id, xray_name, image_path, description, xray_date, file_hash) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $patient_id, $file_name, $file_path, $description, $xray_date, $file_extension);
                $stmt->execute();
                $stmt->close();
                echo "<script>alert('X-ray uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Failed to upload X-ray.');</script>";
            }
        }
    }

    // Fetch X-ray records for the patient
    $stmt = $conn->prepare("SELECT * FROM xray_images WHERE patient_id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $xrays = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    ?>

    <div class="content">
        <h1>X-ray Record</h1>

        <!-- Upload X-ray Form -->
        <form action="xray.php?patient_id=<?= htmlspecialchars($patient_id); ?>" method="POST" enctype="multipart/form-data" class="upload-form">
            <!-- Upload X-ray -->
            <label for="xray_file">Upload X-ray:</label>
            <input type="file" name="xray_file" id="xray_file" accept=".jpg,.jpeg,.png,.pdf" required>
            
            <!-- Description -->
            <label for="description">Description:</label>
            <input type="text" name="description" id="description" placeholder="Enter description..." required>
            
            <!-- X-ray Date -->
            <label for="xray_date">X-ray Date:</label>
            <input type="date" name="xray_date" id="xray_date" required>
            
            <!-- Upload Button -->
            <button type="submit">Upload</button>
        </form>

        <hr>

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
                                <td><?= htmlspecialchars(date('Y-m-d', filemtime($xray['image_path']))); ?></td>
                                <td>
                                    <a href="#" class="view-btn" onclick="viewXray('<?= htmlspecialchars($xray['image_path']); ?>')">View</a>
                                    <a href="<?= htmlspecialchars($xray['image_path']); ?>" download class="download-btn">Download</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-data">No X-rays found for this patient.</p>
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
