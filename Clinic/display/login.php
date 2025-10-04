<?php
session_start(); // Start the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Varela+Round&display=swap" rel="stylesheet">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/login.css">
    <!-- Use SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <header>
        <img src="../pictures/noobg.png">
        </header>

        <!-- Success SweetAlert Notification -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <script>
                window.onload = function() {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: '<?php echo $_SESSION['success_message']; ?>',
                        toast: true,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        background: '#f0f9eb',
                        customClass: {
                            popup: 'colored-toast'
                        }
                    });
                };
            </script>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <!-- Error SweetAlert Notification -->
        <?php if (isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])): ?>
            <script>
                window.onload = function() {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'Login Error',
                        text: '<?php echo htmlspecialchars($_SESSION['error_message']); ?>',
                        toast: true,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        background: '#ffe6e6',
                        customClass: {
                            popup: 'colored-toast'
                        }
                    });
                };
            </script>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="login-box">
            <h2>Welcome!</h2>
            <p>Please login to access your account.</p>
            <form action="../connections/logincon.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Sign in</button>
                <div class="signup-link">Don’t have an account? <a href="../display/register.php">Signup</a></div>
            </form>
        </div>

        <footer>
            <p>&copy; 2024 | Privacy Policy</p>
        </footer>
    </div>
</body>
</html>
