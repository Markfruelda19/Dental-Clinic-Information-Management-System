<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Patient's Dashboard</title>
    <link rel="stylesheet" href="../styles/style.css" />
    <!-- Font Awesome Cdn Link -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Varela+Round&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <nav id="sidebar" class="collapsed">
            <!-- Toggle Button -->
            <button id="toggle-btn">
                <i class="fas fa-bars"></i>
            </button>
            <a href="#" class="logo">
                <img src="../pictures/blogo.jpg">
                <span class="nav-item">Admin</span>
            </a>
            <ul>
                <li><a href="../display/home.php" target="content-frame">
                    <i class="fas fa-house-user"></i>
                    <span class="nav-item">Home</span>
                </a></li>
                <li><a href="../display/profile.php" target="content-frame">
                    <i class="fas fa-user"></i>
                    <span class="nav-item">Profile</span>
                </a></li>
                <li><a href="../display/appointment.php" target="content-frame">
                    <i class="fas fa-calendar-check"></i>
                    <span class="nav-item">Appointment</span>
                </a></li>
                <li><a href="../connections/view_xray.php" target="content-frame">
                    <i class="fas fa-image"></i>
                    <span class="nav-item">X-Ray</span>
                </a></li>
                <li><a href="../display/view.php" target="content-frame">
                    <i class="fas fa-eye"></i>
                    <span class="nav-item">View <br> Appointments</span>
                </a></li>
                <li><a href="../display/billing.php" target="content-frame">
                    <i class="fas fa-money-bill"></i>
                    <span class="nav-item">Billing</span>
                </a></li>
            </ul>
            <!-- Log out button styled like other nav buttons but positioned at bottom -->
            <div class="logout">
                <a href="../display/login.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-item">Log out</span>
                </a>
            </div>
        </nav>
        
        <!-- Iframe to load pages -->
        <iframe src="../display/home.php" name="content-frame" style="width: 100%; height: 100vh; border: none;"></iframe>
    </div>

    <!-- SweetAlert Notification for Login Success -->
    <?php
    session_start();
    if (isset($_SESSION['welcome_message'])): ?>
        <script>
            window.onload = function() {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: '<?php echo $_SESSION['welcome_message']; ?>',
                    toast: true,
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
            };
        </script>
        <?php unset($_SESSION['welcome_message']); ?>
    <?php endif; ?>

    <!-- JavaScript to handle sidebar toggle and active state -->
    <script>
        document.getElementById('toggle-btn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('expanded');
            document.getElementById('sidebar').classList.toggle('collapsed');
        });

        // Function to manage active state on clicked menu items
        document.querySelectorAll("nav ul li a").forEach((link) => {
            link.addEventListener("click", function (event) {
                // Prevent default link behavior to load content in iframe
                event.preventDefault();

                // Remove "active" class from all links
                document.querySelectorAll("nav ul li a").forEach((item) => {
                    item.classList.remove("active");
                });

                // Add "active" class to clicked link
                this.classList.add("active");

                // Load content in iframe if link has href
                const target = this.getAttribute("href");
                if (target) {
                    document.querySelector("iframe[name='content-frame']").src = target;
                }
            });
        });
    </script>
</body>
</html>
