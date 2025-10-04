<?php

require_once '../conn/dashboardcon.php';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-material-ui/material-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="top-bar"></div>
    <div class="container">
        <div class="home_content">
            <div class="content">
                <div class="dashboard-container">
                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card bg-blue">
                            <i class="fa-solid fa-users"></i>
                            <div class="stat-content">
                                <h3>Total Patients</h3>
                                <p><?php echo $totalPatients; ?></p>
                            </div>
                        </div>

                        <div class="stat-card bg-teal">
                            <i class="fa-solid fa-check-circle"></i>
                            <div class="stat-content">
                                <h3>Confirmed</h3>
                                <p><?php echo $confirmedAppointments; ?></p>
                            </div>
                        </div>

                        <div class="stat-card bg-orange">
                            <i class="fa-solid fa-clock"></i>
                            <div class="stat-content">
                                <h3>Pending</h3>
                                <p><?php echo $pendingRequests; ?></p>
                            </div>
                        </div>
                        <div class="stat-card bg-purple">
                            <i class="fa-solid fa-peso-sign"></i>
                            <div class="stat-content">
                                <h3>Total Revenue</h3>
                                <p><?php echo '₱' . number_format($totalRevenue, 2); ?></p>
                            </div>
                        </div>


                        <div class="stat-card bg-red">
                            <i class="fa-solid fa-ban"></i>
                            <div class="stat-content">
                                <h3>Cancelled</h3>
                                <p><?php echo $cancelledAppointments; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Grid -->
                    <div class="charts-grid">
                        <div class="chart-container services">
                            <h3>Most Availed Services</h3>
                            <canvas id="serviceTypeChart"></canvas>
                        </div>

                        <div class="chart-container demographics">
                            <h3>Patient Demographics</h3>
                            <canvas id="demographicsChart"></canvas>
                        </div>
                        <div class="chart-container revenue">
                            <h3>Daily Revenue</h3>
                            <canvas id="revenueChart"></canvas>
                        </div>
                        <div class="chart-container accounts">
                            <h3>New Accounts</h3>
                            <canvas id="accountsChart"></canvas>
                        </div>

                        <div class="chart-container appointments">
                            <h3>Appointments Overview</h3>
                            <canvas id="monthlyAppointmentsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Ensure that PHP variables are passed correctly as JSON data to JavaScript
        const malePatients = <?php echo json_encode($malePatients); ?>;
        const femalePatients = <?php echo json_encode($femalePatients); ?>;

        // Patient Demographics Chart
        const ctx = document.getElementById('demographicsChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [malePatients, femalePatients],
                    backgroundColor: ['rgba(119,169,215)', 'rgba(234,209,220)'],
                    borderColor: ['rgba(119,169,215)', 'rgba(234,209,220)'],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Service Type Bar Chart
        var serviceTypeCounts = <?php echo json_encode($serviceTypeCounts); ?>;
        var labels = Object.keys(serviceTypeCounts);
        var data = Object.values(serviceTypeCounts);

        var ctx2 = document.getElementById('serviceTypeChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Most Availed Services',
                    data: data,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        // PHP Variables for Daily Revenue
        const dailyRevenueDates = <?php echo $dailyRevenueDatesJSON; ?>;
        const dailyRevenueAmounts = <?php echo $dailyRevenueAmountsJSON; ?>;

        // Format the daily revenue dates (e.g., "2024-11-25" to "Nov 25, 2024")
        const formattedDailyRevenueDates = dailyRevenueDates.map(date => {
            const dateObj = new Date(date);  // Create a Date object from the string
            return dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        });

        // Daily Revenue Line Chart
        const ctx3 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx3, {
            type: 'line',
            data: {
                labels: formattedDailyRevenueDates,
                datasets: [
                    {
                        label: 'Daily Revenue',
                        data: dailyRevenueAmounts,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        borderWidth: 2,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return '₱' + tooltipItem.raw.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        type: 'category',
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        ticks: {
                            callback: function (value) {
                                return '₱' + value.toFixed(2);
                            }
                        }
                    }
                },
                layout: {
                    padding: {
                        top: 20,
                        right: 20,
                        bottom: 40,
                        left: 40
                    }
                }
            }
        });


        // Ensure that the PHP variables are outputted as expected
        const appointmentMonths = <?php echo $appointmentMonthsJson; ?>;
    const appointmentsCreatedByMonth = <?php echo $appointmentsCreatedByMonthJson; ?>;


    // Format the appointment months (e.g., "2024-11" to "Nov 2024")
    const formattedAppointmentMonths = appointmentMonths.map(month => {
        const [year, monthNum] = month.split('-');
        const dateObj = new Date(year, monthNum - 1); // Month is 0-based
        return dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
    });


    const ctx7 = document.getElementById('monthlyAppointmentsChart').getContext('2d');
    const monthlyAppointmentsChart = new Chart(ctx7, {
        type: 'line',
        data: {
            labels: formattedAppointmentMonths,  // Use the formatted month array
            datasets: [{
                label: 'Monthly Appointments Created',
                data: appointmentsCreatedByMonth,  // Monthly appointments count on the y-axis
                backgroundColor: 'rgba(39, 76, 245, 0.5)',
                borderColor: 'rgb(1, 7, 88)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Month'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Appointments Created'
                    }
                }
            }
        }
    });
        // PHP Variables for Account Creation
        const creationDates = <?php echo $creationDatesJson; ?>;
        const accountsCreated = <?php echo $accountsCreatedJson; ?>;

        // Format the creation dates (e.g., "2024-11-25" to "Nov 25, 2024")
        const formattedCreationDates = creationDates.map(date => {
            const dateObj = new Date(date);  // Create a Date object from the string
            return dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        });

        // Log formatted dates to check
        console.log(formattedCreationDates);  // Check the formatted dates
        console.log(accountsCreated);  // Check the accounts creation data

        // Account Creation Line Chart
        const ctx4 = document.getElementById('accountsChart').getContext('2d');
        new Chart(ctx4, {
            type: 'line',  // Change this to 'bar' if you prefer a bar chart
            data: {
                labels: formattedCreationDates,  // Use the formatted date array
                datasets: [{
                    label: 'Accounts Created',
                    data: accountsCreated,  // Accounts created count on the y-axis
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    fill: true,
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return tooltipItem.raw + ' Accounts'; // Label text
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: function (value) {
                                return value + ' Accounts'; // Format as accounts
                            }
                        }
                    }
                }
            }
        });

    </script>

</body>

</html>