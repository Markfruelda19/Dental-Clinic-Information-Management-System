<?php
session_start();
include '../connections/homecon.php';

// Check if there's a success message for appointment booking in the session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
if ($success_message) {
    unset($_SESSION['success_message']); // Clear the message after using it
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Clinic Appointment</title>
    <link rel="stylesheet" href="../styles/home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- SweetAlert for Appointment Booking Success -->
    <?php if ($success_message): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?php echo addslashes($success_message); ?>',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    <?php endif; ?>

    <div class="container">
        <div class="content">
            <div class="welcome-message">
                <h1>Welcome, <?php echo htmlspecialchars($first_name); ?>!</h1>
                <p>Using the sidebar, you may request a new consultation, view/edit your pending consultation requests,
                    and view/cancel/request a reschedule for your upcoming appointments.</p>
                <p class="second-sen">You can also see your next appointment below.</p>
            </div>

            <div class="appointment-section">
                <h2>Your next appointment</h2>
                <div class="appointment-details">
                    <div class="appointment-item">
                        <i class="fas fa-hashtag"></i>
                        <div class="appointment-info">
                            <span class="item-label">Appointment Reference No.</span>
                            <span class="item-value"><?php echo htmlspecialchars($appointment_id); ?></span>
                        </div>
                    </div>
                    <div class="appointment-item">
                        <i class="fas fa-info-circle"></i>
                        <div class="appointment-info">
                            <span class="item-label">Appointment Status</span>
                            <span class="item-value"><?php echo htmlspecialchars($status); ?></span>
                        </div>
                    </div>
                    <div class="appointment-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div class="appointment-info">
                            <span class="item-label">Consultation Date</span>
                            <span class="item-value">
                                <?php
                                if ($expected_date === "N/A" || empty($expected_date)) {
                                    echo "N/A";
                                } else {
                                    $date_obj = DateTime::createFromFormat('Y-m-d', $expected_date);
                                    echo $date_obj ? strtoupper($date_obj->format('F j, Y')) : "Invalid date";
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="appointment-item">
                        <i class="fas fa-clock"></i>
                        <div class="appointment-info">
                            <span class="item-label">Time Block</span>
                            <span class="item-value"><?php echo htmlspecialchars($expected_time); ?></span>
                        </div>
                    </div>
                    <div class="appointment-item">
                        <i class="fas fa-tooth"></i>
                        <div class="appointment-info">
                            <span class="item-label">Service Type</span>
                            <span class="item-value"><?php echo htmlspecialchars($service_type); ?></span>
                        </div>
                    </div>
                    <div class="appointment-item">
                        <i class="fas fa-notes-medical"></i>
                        <div class="appointment-info">
                            <span class="item-label">Details</span>
                            <span class="item-value"><?php echo htmlspecialchars($other_details); ?></span>
                        </div>
                    </div>
                </div>
                <?php if ($appointment_id === 'N/A'): ?>
                    <div class="no-appointment">
                        <p class="no-appointment-message">No Appointment</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
