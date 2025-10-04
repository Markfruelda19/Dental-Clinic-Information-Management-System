// dashboard.js

window.onload = function() {
    // Check if serviceData is passed correctly
    if (typeof serviceData === 'undefined' || typeof billingTrends === 'undefined') {
        console.error('Data not passed correctly from PHP');
        return;
    }

    // Revenue by Service - Bar Chart
    var ctx1 = document.getElementById('serviceRevenueChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: serviceData.map(function(item) { return item.service_name; }),
            datasets: [{
                label: 'Revenue by Service',
                data: serviceData.map(function(item) { return item.revenue; }),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Billing Trends Over Time - Line Chart
    var ctx2 = document.getElementById('billingTrendsChart').getContext('2d');
    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: billingTrends.map(function(item) { return item.month; }),
            datasets: [{
                label: 'Monthly Revenue',
                data: billingTrends.map(function(item) { return item.total_revenue; }),
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
};
