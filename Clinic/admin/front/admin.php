<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    // Redirect to login if not logged in
    header("Location: ../display/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
    <!-- Font Awesome Cdn Link -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Varela+Round&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
                <img src="../../pictures/blogo.jpg" alt="Logo">
                <span class="nav-item">Admin</span>
            </a>
            <ul>
                <li><a href="../front/dashboard.php" target="content-frame">
                    <i class="fas fa-house-user"></i>
                    <span class="nav-item">Dashboard</span>
                </a></li>
                <li><a href="../front/patients.php" target="content-frame">
                    <i class="fas fa-user-circle"></i>
                    <span class="nav-item">Patients</span>
                </a></li>
                <li><a href="../front/listapp.php" target="content-frame">
                    <i class="fas fa-eye"></i>
                    <span class="nav-item">Manage Appointments</span>
                </a></li>
                <li><a href="../front/inventory.php" target="content-frame">
                    <i class="fas fa-archive"></i>
                    <span class="nav-item">Inventory</span>
                </a></li>
                <li><a href="../front/services.php" target="content-frame">
                    <i class="fas fa-tooth"></i>
                    <span class="nav-item">Services</span>
                </a></li>
                <li><a href="../front/bills.php" target="content-frame">
                    <i class="fas fa-money-bill"></i>
                    <span class="nav-item">Billing</span>
                </a></li>
            </ul>
            <!-- Log out button styled like other nav buttons but positioned at bottom -->
            <div class="logout">
                <a href="../../display/login.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-item">Log out</span>
                </a>
            </div>
        </nav>
        
        <!-- Iframe to load pages -->
        <iframe name="content-frame" style="width: 100%; height: 100vh; border: none;"></iframe>
    </div>

    <!-- SweetAlert Notification for Login Success -->
    <?php if (isset($_SESSION['welcome_message'])): ?>
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
        // Sidebar toggle functionality
        document.getElementById('toggle-btn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('expanded');
            document.getElementById('sidebar').classList.toggle('collapsed');
        });

        // Manage active state on sidebar menu items
        document.querySelectorAll("nav ul li a").forEach((link) => {
            link.addEventListener("click", function(event) {
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
