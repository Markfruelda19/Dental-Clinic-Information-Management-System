<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients</title>
    <link rel="stylesheet" href="../css/patients.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="appointment-container">
        <?php
        // Establish database connection
        $conn = new mysqli("localhost", "root", "", "blanche_db");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        ?>

        <div class="appointment-content">
        <header class="patients-header">
    <div class="header-content">
        <h1>Patients Management</h1>
        <p>Easily manage patient records and history.</p>
    </div>
</header>
            <!-- Breadcrumb -->
            <div class="breadcrumb" id="breadcrumb">
                <span id="breadcrumb-list" onclick="showPatientList()">List of Patients</span>
                <span id="breadcrumb-detail" class="hidden"> > Patient Details</span>
            </div>
            <hr class="appointment-thin-line">

            <!-- Tabs Section (Initially Hidden) -->
            <div id="tabs-section" style="display: none;">
                <div class="tabs">
                    <span onclick="showTabContent('patient-info')" class="tab">Patient Information</span>
                    <span onclick="showTabContent('appointment-history')" class="tab">Appointment History</span>
                    <span onclick="showTabContent('xray-record')" class="tab">X-ray Record</span>
                    <span onclick="showTabContent('medical-record')" class="tab">Medical Record</span>
                </div>

                <!-- Tab Contents -->
                <div id="patient-info" class="tab-content" style="display: none;">
                    <iframe id="patient-info-frame" frameborder="0" style="width: 100%; height: 100vh;"></iframe>
                </div>

                <div id="appointment-history" class="tab-content" style="display: none;">
                    <iframe id="appointment-history-frame" frameborder="0" style="width: 100%; height: 100vh;"></iframe>
                </div>

                <div id="xray-record" class="tab-content" style="display: none;">
                    <iframe id="xray-record-frame" frameborder="0" style="width: 100%; height: 100vh;"></iframe>
                </div>

                <div id="medical-record" class="tab-content" style="display: none;">
                    <iframe id="odontogram-frame" frameborder="0" style="width: 100%; height: 100vh;"></iframe>
                </div>
            </div>

            <!-- Patient List Section -->
            <div id="patient-list-section">
                <?php
                $sql_patients = "SELECT patient_id, first_name, middle_initial, last_name, gender, date_of_birth, phone_number, email, occupation, present_address, age, role FROM patients";
                $result_patients = $conn->query($sql_patients);

                if ($result_patients->num_rows > 0) {
                    echo '<table class="appointment-table">
                            <thead>
                                <tr>
                                    <th>Patient ID</th>
                                    <th>First Name</th>
                                    <th>Middle Initial</th>
                                    <th>Last Name</th>
                                    <th>Gender</th>
                                    <th>Date of Birth</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Occupation</th>
                                    <th>Address</th>
                                    <th>Age</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>';
                    while ($row = $result_patients->fetch_assoc()) {
                        echo '<tr>
                                <td>' . htmlspecialchars($row['patient_id']) . '</td>
                                <td>' . htmlspecialchars($row['first_name']) . '</td>
                                <td>' . htmlspecialchars($row['middle_initial']) . '</td>
                                <td>' . htmlspecialchars($row['last_name']) . '</td>
                                <td>' . htmlspecialchars($row['gender']) . '</td>
                                <td>' . htmlspecialchars($row['date_of_birth']) . '</td>
                                <td>' . htmlspecialchars($row['phone_number']) . '</td>
                                <td>' . htmlspecialchars($row['email']) . '</td>
                                <td>' . htmlspecialchars($row['occupation']) . '</td>
                                <td>' . htmlspecialchars($row['present_address']) . '</td>
                                <td>' . htmlspecialchars($row['age']) . '</td>
                                <td>' . htmlspecialchars($row['role']) . '</td>
                                <td>
                                    <button class="view-btn" onclick="viewDetails(\'' . htmlspecialchars($row['patient_id']) . '\')">View Details</button>
                                    <button class="delete-btn" onclick="deleteRecord(\'' . htmlspecialchars($row['patient_id']) . '\')">Delete</button>
                                </td>
                              </tr>';
                    }
                    echo '</tbody>
                        </table>';
                } else {
                    echo '<p>No patients found.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <script>
     let selectedPatientId = null;

function viewDetails(patientId) {
    selectedPatientId = patientId;

    // Dynamically load content for all tabs
    document.getElementById('patient-info-frame').src = `../front/patientinfo.php?patient_id=${patientId}`;
    document.getElementById('appointment-history-frame').src = `../front/persoapp.php?patient_id=${patientId}`;
    document.getElementById('xray-record-frame').src = `../front/xray.php?patient_id=${patientId}`;
    document.getElementById('odontogram-frame').src = `../front/odontogram.php?patient_id=${patientId}`;

    // Show tabs section
    document.getElementById('patient-list-section').style.display = 'none';
    document.getElementById('tabs-section').style.display = 'block';

    // Default to Patient Information tab
    showTabContent('patient-info');

    // Update breadcrumb
    document.getElementById('breadcrumb-detail').classList.remove('hidden');
}

        function showTabContent(tabId) {
            const tabContents = document.querySelectorAll('.tab-content');
            const tabs = document.querySelectorAll('.tab');

            tabContents.forEach(content => content.style.display = 'none');
            tabs.forEach(tab => tab.classList.remove('active'));

            document.getElementById(tabId).style.display = 'block';
            const activeTab = Array.from(tabs).find(tab => tab.textContent.trim() === getTabName(tabId));
            if (activeTab) activeTab.classList.add('active');
        }

        function getTabName(tabId) {
            switch (tabId) {
                case 'patient-info': return 'Patient Information';
                case 'appointment-history': return 'Appointment History';
                case 'xray-record': return 'X-ray Record';
                case 'medical-record': return 'Medical Record';
                default: return '';
            }
        }

        function showPatientList() {
            document.getElementById('patient-list-section').style.display = 'block';
            document.getElementById('tabs-section').style.display = 'none';
            document.getElementById('breadcrumb-detail').classList.add('hidden');
        }

        function deleteRecord(patientId) {
            Swal.fire({
                title: 'Delete Patient Record?',
                text: "Are you sure you want to delete this record? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`../conn/deletepatient.php?patient_id=${patientId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Deleted!', 'The patient record has been successfully deleted.', 'success');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Swal.fire('Error!', 'An error occurred while deleting the record.', 'error');
                            }
                        })
                        .catch(() => Swal.fire('Error!', 'An unexpected error occurred.', 'error'));
                }
            });
        }
    </script>
</body>
</html>
