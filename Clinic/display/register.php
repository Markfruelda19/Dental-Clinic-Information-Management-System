<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Patient Registration</title>
    <link rel="stylesheet" href="../styles/register.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Include SweetAlert -->
  </head>
  <body>
    <div class="container">
      <header>
        <h1>Blanche Dental Clinic</h1>
        <p>Register as a new patient</p>
      </header>

      <div class="form-container">
        <form action="../connections/regcon.php" method="POST" class="form">
          <!-- Full Name Section -->
          <div class="input-group">
            <div class="input-box">
              <label>First Name</label>
              <input type="text" name="first_name" placeholder="First Name" required />
            </div>
            <div class="input-box">
              <label>Middle Initial</label>
              <input type="text" name="middle_initial" placeholder="Middle Initial" maxlength="1" />
            </div>
            <div class="input-box">
              <label>Last Name</label>
              <input type="text" name="last_name" placeholder="Last Name" required />
            </div>
          </div>

          <!-- Other Personal Information -->
          <div class="input-group">
            <div class="input-box">
              <label>Date of Birth</label>
              <input type="date" name="date_of_birth" required />
            </div>
            <div class="input-box">
              <label>Age</label>
              <input type="number" name="age" placeholder="Age" required />
            </div>
            <div class="input-box">
              <label>Gender</label>
              <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
              </select>
            </div>
          </div>

          <!-- Contact Information -->
          <div class="input-group">
            <div class="input-box">
              <label>Phone Number</label>
              <input type="text" name="phone_number" placeholder="Phone Number" required />
            </div>
            <div class="input-box">
              <label>Email</label>
              <input type="email" name="email" placeholder="Email" required />
            </div>
          </div>

          <!-- Address and Occupation -->
          <div class="input-group">
            <div class="input-box">
              <label>Present Address</label>
              <input type="text" name="present_address" placeholder="Address" required />
            </div>
            <div class="input-box">
              <label>Occupation</label>
              <input type="text" name="occupation" placeholder="Your occupation" />
            </div>
          </div>

          <!-- Account Information -->
          <div class="input-group">
            <div class="input-box">
              <label>Username</label>
              <input type="text" name="username" placeholder="Choose a username" required />
            </div>
            <div class="input-box">
              <label>Password</label>
              <input type="password" name="password" placeholder="Choose a password" required />
            </div>
          </div>

          <button type="submit">Register</button>
        </form>
      </div>

      <footer>
        <p>Already registered? <a href="../display/login.php">Sign in here</a></p>
      </footer>
    </div>

    <!-- SweetAlert Notification for Success with Redirect -->
    <?php if (isset($_SESSION['success_message']) && isset($_SESSION['redirect_from_regcon'])): ?>
      <script>
        Swal.fire({
          position: "top-end",
          icon: "success",
          title: "<?php echo $_SESSION['success_message']; ?>",
          toast: true,
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true
        }).then(() => {
          window.location.href = "../display/login.php"; // Redirect to login page
        });
      </script>
      <?php 
      unset($_SESSION['success_message']);
      unset($_SESSION['redirect_from_regcon']); // Clear redirect flag
      ?>
    <?php endif; ?>

    <!-- SweetAlert Notification for Error -->
    <?php if (isset($_SESSION['error_message']) && isset($_SESSION['redirect_from_regcon'])): ?>
      <script>
        Swal.fire({
          position: "top-end",
          icon: "error",
          title: "<?php echo $_SESSION['error_message']; ?>",
          toast: true,
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true
        });
      </script>
      <?php 
      unset($_SESSION['error_message']);
      unset($_SESSION['redirect_from_regcon']); // Clear redirect flag
      ?>
    <?php endif; ?>
  </body>
</html>
