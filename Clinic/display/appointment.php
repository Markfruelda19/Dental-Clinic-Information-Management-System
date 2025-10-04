<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments</title>
    <link rel="stylesheet" href="../styles/appointment.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Varela+Round&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet"> <!-- Select2 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script> <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="content-wrapper">
    <div class="main-content">
        <div class="appointment-header">
            <h2>Appointments</h2>
            <p>There is the latest update for the last 7 days. Check now</p>
        </div>

        <div class="calendar-toolbar">
            <button class="new-appointment-btn" onclick="submitAppointment()">+ New Appointment</button>
            <div class="search-and-filter">
                <input type="text" placeholder="Search anything here" class="search-input">
                <select class="view-select">
                    <option>Week</option>
                    <option>Month</option>
                    <option>Day</option>
                </select>
            </div>
        </div>

        <div class="calendar-time-wrapper">
            <div class="appointment-details">
                <h3>Appointment Details</h3>
                <label for="services">Services</label>
                <select id="services" multiple>
                    <option value="21">Dental Consultation</option>
                    <option value="22">Oral Prophylaxis</option>
                    <option value="23">Tooth Restoration</option>
                    <option value="24">Tooth Extraction</option>
                    <option value="25">Dentures</option>
                    <option value="26">Dental Braces Package</option>
                    <option value="27">Jacket Crown</option>
                    <option value="28">Fixed Bridge</option>
                    <option value="29">Veneers</option>
                    <option value="30">Oral Surgery</option>
                    <option value="32">Odontectomy (Surgery)</option>
                    <option value="34">Braces Adjustment</option>
                </select>
                <label for="complaint">Complaint</label>
                <textarea id="complaint" required></textarea>
                <label for="medical-history">Medical History</label>
                <textarea id="medical-history"></textarea>
                <label for="allergies">Allergies</label>
                <textarea id="allergies"></textarea>
                <label for="other-details">Other Details</label>
                <textarea id="other-details"></textarea>
                <input type="hidden" id="appointment-date">
                <input type="hidden" id="appointment-time">
            </div>

            <div class="calendar-container">
                <div id="calendar"></div>
            </div>

            <div class="time-slots">
                <h3>Available Time Slots</h3>
                <div id="timeSlotsList"></div>
            </div>
        </div>
    </div>
</div>

<script src="../JS/appointment.js"></script>
<script>
    // Initialize Select2 for the services dropdown
    $(document).ready(function () {
        $('#services').select2({
            placeholder: 'Select one or more services',
            allowClear: true
        });
    });

    // Check if a success message exists in the session and display it using SweetAlert
    <?php if (isset($_SESSION['success_message'])): ?>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'success',
                title: '<?php echo $_SESSION['success_message']; ?>',
                showConfirmButton: true,
                confirmButtonColor: '#28a745',
            });
        });
        <?php unset($_SESSION['success_message']); // Clear the message after displaying ?>
    <?php endif; ?>
</script>
</body>
</html>
