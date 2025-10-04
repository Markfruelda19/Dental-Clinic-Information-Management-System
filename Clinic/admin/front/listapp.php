<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Calendar</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Varela+Round&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/calendar.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- Main Content -->
<div class="main-content">
    <!-- Header Section -->
    <div class="header-container">
        <div class="header-title">
            <h1>Appointments</h1>
            <p>Here are the list of the appointments.</p>
        </div>
    </div>

    <!-- Calendar Container -->
    <div id="calendar-container">
        <div id="calendar"></div>
    </div>

    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>

            <div class="modal-header">
                <h3 class="modal-title">Appointment Details</h3>
                <div class="appointment-id">ID: <span id="appointmentID"></span></div>
                <div class="appointment-status" id="statusBadge">Pending</div>
            </div>

            <div class="patient-info">
                <div class="patient-avatar">CS</div>
                <div>
                    <h4 id="fullName"></h4>
                    <p id="occupation"></p>
                </div>
            </div>

            <div class="appointment-details">
                <div class="detail-item">
                    <label>Service Type</label>
                    <span id="serviceType"></span>
                </div>
                <div class="detail-item">
                    <label>Other Details</label>
                    <span id="otherDetails"></span>
                </div>
                <div class="detail-item">
                    <label>Date</label>
                    <span id="appointmentDate"></span>
                </div>
                <div class="detail-item">
                    <label>Time</label>
                    <span id="appointmentTime"></span>
                </div>
                <div class="detail-item">
                    <label>Medical History</label>
                    <span id="medicalHistory"></span>
                </div>
                <div class="detail-item">
                    <label>Allergies</label>
                    <span id="allergies"></span>
                </div>
            </div>

            <div class="general-info">
                <h4>General Info</h4>
                <div class="info-row">
                    <div class="info-item">
                        <label>Phone Number</label>
                        <span id="phoneNumber"></span>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <span id="patientEmail"></span>
                    </div>
                    <div class="info-item">
                        <label>Age</label>
                        <span id="age"></span>
                    </div>
                    <div class="info-item">
                        <label>Gender</label>
                        <span id="gender"></span>
                    </div>
                    <div class="info-item">
                        <label>Address</label>
                        <span id="address"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn confirm-btn">Confirm</button>
                <button class="btn decline-btn">Cancel</button>
                <button class="btn btn-finish">Finish</button>
            </div>
        </div>
    </div>

</div>

<!-- FullCalendar and Custom JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="../script/listofapp.js"></script> <!-- Link to external JavaScript file -->

</body>
</html>
