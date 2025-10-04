<?php
session_start();
require '../connections/profilecon.php';

// Check if user is logged in by verifying `patient_id` in the session
if (!isset($_SESSION['patient_id'])) {
    echo "You are not logged in.";
    exit;
}

// Fetch user data based on the logged-in `patient_id`
$user = fetchUserData($conn);
if ($user === null) {
    echo "No user found in the database.";
    exit;
}

// Calculate age based on date of birth
function calculateAge($dateOfBirth) {
    $birthDate = new DateTime($dateOfBirth);
    $currentDate = new DateTime();
    $age = $birthDate->diff($currentDate)->y;
    return $age;
}

$age = calculateAge($user['date_of_birth']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile</title>
    <link rel="stylesheet" href="../styles/profile.css">
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Display SweetAlert for success or error -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <script>
            window.onload = function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: '<?php echo $_SESSION['success_message']; ?>',
                    toast: true,
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            };
        </script>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <script>
            window.onload = function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error',
                    text: '<?php echo $_SESSION['error_message']; ?>',
                    toast: true,
                    timer: 3000,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            };
        </script>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Independent Account Details Header -->
    <div class="account-details-header">
        <h2>Account Details</h2>
    </div>

    <!-- Main Profile Container -->
    <div class="profile-container">
        <!-- Profile Header Section with User Icon and Name -->
        <div class="profile-header">
            <div class="user-icon">👤</div>
            <div class="header-details">
                <h2 class="profile-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                <p class="occupation"><?= htmlspecialchars($user['occupation']); ?></p>
            </div>
        </div>
        <hr class="divider">

        <!-- Profile Information Section -->
        <form action="../connections/updateprofile.php" method="POST" class="profile-section">
            <div class="form-row">
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']); ?>">
                </div>
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']); ?>">
                </div>                
                <div class="form-group">
                    <label>Middle Initial</label>
                    <input type="text" name="middle_initial" value="<?= htmlspecialchars($user['middle_initial']); ?>">
                </div>
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth']); ?>">
                </div>
                <div class="form-group">
                    <label>Age</label>
                    <input type="text" value="<?= $age; ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Occupation</label>
                    <input type="text" name="occupation" value="<?= htmlspecialchars($user['occupation']); ?>">
                </div>
                <div class="form-group">
                    <label>Present Address</label>
                    <input type="text" name="present_address" value="<?= htmlspecialchars($user['present_address']); ?>">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']); ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>">
                </div>
            </div>
            <button type="submit" class="edit-button">Save Changes</button>
        </form>
    </div>
</body>
</html>
