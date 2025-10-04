
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment</title>
    <link rel="stylesheet" href="../styles/view.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Varela+Round&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="appointment-container">
        <?php
        session_start();
        if (!isset($_SESSION['patient_id'])) {
            header("Location: ../display/login.php?$error_message=Please login first");
        }

        // Get current user ID from session
        $loggedInUserId = $_SESSION['patient_id'];
        $conn = new mysqli("localhost", "root", "", "blanche_db");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check for tab parameter to control the active tab view
        $currentTab = isset($_GET['tab']) ? $_GET['tab'] : 'upcoming'; // Default to upcoming
        ?>

        <div class="appointment-content">
            <h1>APPOINTMENTS</h1>
            <hr class="appointment-thin-line">
            <!-- Tabs -->
            <div class="tabs">
                <div class="tab <?php echo $currentTab == 'upcoming' ? 'active' : ''; ?>" data-tab="upcoming"
                    onclick="showTab('upcoming')">Upcoming</div>
                <div class="tab <?php echo $currentTab == 'past' ? 'active' : ''; ?>" data-tab="past"
                    onclick="showTab('past')">Past</div>
                <div class="tab <?php echo $currentTab == 'cancel' ? 'active' : ''; ?>" data-tab="cancel"
                    onclick="showTab('cancel')">Cancelled Appointments</div>
            </div>

            <div class="tab-content">
                <!-- Upcoming Appointments -->
                <div id="upcoming" class="tab-pane"
                    style="<?php echo $currentTab == 'upcoming' ? 'display: block;' : 'display: none;'; ?>">
                    <?php
                    $sql_upcoming = "SELECT appointment_id, service_type, other_details, expected_date, expected_time, medical_history, allergies, status 
                    FROM appointments
                    WHERE patient_id = ? AND (expected_date >= CURDATE() OR expected_date IS NULL)
                    AND (status != 'Cancelled') 
                    ORDER BY appointment_id DESC";
                    $stmt_upcoming = $conn->prepare($sql_upcoming);
                    $stmt_upcoming->bind_param("i", $loggedInUserId);
                    $stmt_upcoming->execute();
                    $result_upcoming = $stmt_upcoming->get_result();

                    if ($result_upcoming->num_rows > 0) {
                        echo '<table class="appointment-table">
                                <thead>
                                    <tr>
                                        <th>Appointment ID</th>
                                        <th>Date</th>
                                        <th>Service</th>
                                        <th>Details</th>
                                        <th>Time</th>
                                        <th>Medical History</th>
                                        <th>Allergies</th>
                                        <th>Status</th>
                                        <th>Options</th>
                                    </tr>
                                </thead>
                                <tbody>';

                        while ($row = $result_upcoming->fetch_assoc()) {
                            $formattedDate = ($row['expected_date']) ? date('F d, Y', strtotime($row['expected_date'])) : 'TBD';
                            echo '<tr>
                                    <td>' . htmlspecialchars($row['appointment_id']) . '</td>
                                    <td>' . htmlspecialchars($formattedDate) . '</td>
                                    <td>' . htmlspecialchars($row['service_type']) . '</td>
                                    <td>' . htmlspecialchars($row['other_details']) . '</td>
                                    <td>' . htmlspecialchars($row['expected_time']) . '</td>
                                    <td>' . htmlspecialchars($row['medical_history']) . '</td>
                                    <td>' . htmlspecialchars($row['allergies']) . '</td>
                                    <td>' . htmlspecialchars($row['status']) . '</td>
                                    <td><button class="cancel-btn">CANCEL</button></td>
                                </tr>';
                        }
                        echo '  </tbody>
                            </table>';
                    } else {
                        echo '<p>No upcoming appointments found.</p>';
                    }
                    $stmt_upcoming->close();
                    ?>
                </div>

                <!-- Past Appointments -->
                <div id="past" class="tab-pane"
                    style="<?php echo $currentTab == 'past' ? 'display: block;' : 'display: none;'; ?>">
                    <?php
                    $sql_past = "SELECT appointment_id, service_type, other_details, expected_date, expected_time, medical_history, allergies, status 
                                 FROM appointments
                                 WHERE patient_id = ? AND (expected_date < CURDATE() OR status = 'Completed')
                                 ORDER BY appointment_id DESC";
                    $stmt_past = $conn->prepare($sql_past);
                    $stmt_past->bind_param("i", $loggedInUserId);
                    $stmt_past->execute();
                    $result_past = $stmt_past->get_result();

                    if ($result_past->num_rows > 0) {
                        echo '<table class="appointment-table">
                                <thead>
                                    <tr>
                                        <th>Appointment ID</th>
                                        <th>Date</th>
                                        <th>Service</th>
                                        <th>Details</th>
                                        <th>Time</th>
                                        <th>Medical History</th>
                                        <th>Allergies</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>';

                        while ($row = $result_past->fetch_assoc()) {
                            $formattedDate = date('F d, Y', strtotime($row['expected_date']));
                            echo '<tr>
                                    <td>' . htmlspecialchars($row['appointment_id']) . '</td>
                                    <td>' . htmlspecialchars($formattedDate) . '</td>
                                    <td>' . htmlspecialchars($row['service_type']) . '</td>
                                    <td>' . htmlspecialchars($row['other_details']) . '</td>
                                    <td>' . htmlspecialchars($row['expected_time']) . '</td>
                                    <td>' . htmlspecialchars($row['medical_history']) . '</td>
                                    <td>' . htmlspecialchars($row['allergies']) . '</td>
                                    <td>' . htmlspecialchars($row['status']) . '</td>
                                </tr>';
                        }
                        echo '  </tbody>
                            </table>';
                    } else {
                        echo '<p>No past appointments found.</p>';
                    }
                    $stmt_past->close();
                    ?>
                </div>

                <!-- Cancelled Appointments -->
                <div id="cancel" class="tab-pane"
                    style="<?php echo $currentTab == 'cancel' ? 'display: block;' : 'display: none;'; ?>">
                    <?php
                    $sql_cancelled = "SELECT appointment_id, service_type, other_details, expected_date, expected_time, medical_history, allergies, status 
                                      FROM appointments 
                                      WHERE patient_id = ? AND status = 'Cancelled'
                                      ORDER BY appointment_id DESC";
                    $stmt_cancelled = $conn->prepare($sql_cancelled);
                    $stmt_cancelled->bind_param("i", $loggedInUserId);
                    $stmt_cancelled->execute();
                    $result_cancelled = $stmt_cancelled->get_result();

                    if ($result_cancelled->num_rows > 0) {
                        echo '<table class="appointment-table">
                                <thead>
                                    <tr>
                                        <th>Appointment ID</th>
                                        <th>Date</th>
                                        <th>Service</th>
                                        <th>Details</th>
                                        <th>Time</th>
                                        <th>Medical History</th>
                                        <th>Allergies</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>';

                        while ($row = $result_cancelled->fetch_assoc()) {
                            $formattedDate = date('F d, Y', strtotime($row['expected_date']));
                            echo '<tr>
                                    <td>' . htmlspecialchars($row['appointment_id']) . '</td>
                                    <td>' . htmlspecialchars($formattedDate) . '</td>
                                    <td>' . htmlspecialchars($row['service_type']) . '</td>
                                    <td>' . htmlspecialchars($row['other_details']) . '</td>
                                    <td>' . htmlspecialchars($row['expected_time']) . '</td>
                                    <td>' . htmlspecialchars($row['medical_history']) . '</td>
                                    <td>' . htmlspecialchars($row['allergies']) . '</td>
                                    <td>' . htmlspecialchars($row['status']) . '</td>
                                </tr>';
                        }
                        echo '  </tbody>
                            </table>';
                    } else {
                        echo '<p>No cancelled appointments found.</p>';
                    }
                    $stmt_cancelled->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../JS/view.js"></script>
</body>

</html>
