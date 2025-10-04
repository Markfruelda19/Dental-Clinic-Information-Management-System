document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var modal = document.getElementById("appointmentModal");
    var closeBtn = document.getElementsByClassName("close")[0];
    var confirmBtn = document.querySelector(".confirm-btn");
    var declineBtn = document.querySelector(".decline-btn");
    var finishBtn = document.querySelector(".btn-finish");

    let currentAppointmentId = null;
    let isModalOpen = false; // Track modal state

    // Open Modal
    function openModal() {
        if (!isModalOpen) {
            modal.style.display = "flex";
            isModalOpen = true;
        }
    }

    // Close Modal
    function closeModal() {
        if (isModalOpen) {
            modal.style.display = "none";
            isModalOpen = false;
        }
    }

    modal.style.display = "none"; // Ensure modal is hidden initially

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function (fetchInfo, successCallback, failureCallback) {
            fetch('../conn/manageapp.php')
                .then(response => response.json())
                .then(data => {
                    data.forEach(event => {
                        // Add className based on status
                        if (event.extendedProps.status === 'Confirmed') {
                            event.className = 'fc-event-confirmed';
                        } else if (event.extendedProps.status === 'Cancelled') {
                            event.className = 'fc-event-cancelled';
                        } else if (event.extendedProps.status === 'Pending') {
                            event.className = 'fc-event-pending';
                        }
                    });
                    successCallback(data);
                })
                .catch(error => {
                    showCustomAlert("Error fetching events.", "error");
                    failureCallback(error);
                });
        },
        eventClick: function (info) {
            if (!info.event) {
                return;
            }
        
            currentAppointmentId = info.event.id;
        
            // Populate modal fields
            document.getElementById('appointmentID').innerText = info.event.id || "N/A";
            document.getElementById('serviceType').innerText = info.event.title || "N/A";
        
            const details = info.event.extendedProps || {};
            document.getElementById('otherDetails').innerText = details.other_details || "N/A";
            document.getElementById('medicalHistory').innerText = details.medical_history || "N/A";
            document.getElementById('allergies').innerText = details.allergies || "N/A";
        
            const statusBadge = document.getElementById('statusBadge');
            statusBadge.innerText = details.status || "N/A";
            statusBadge.className = ''; // Remove previous classes
            
            // Apply the correct class based on status
            if (details.status === 'Confirmed') {
                statusBadge.classList.add('confirmed');
            } else if (details.status === 'Pending') {
                statusBadge.classList.add('pending');
            } else if (details.status === 'Cancelled') {
                statusBadge.classList.add('cancelled');
            } else if (details.status === 'Completed') {
                statusBadge.classList.add('completed');
            }            
        
            // Populate additional patient info
            document.getElementById('fullName').innerText = `${details.first_name} ${details.middle_initial}. ${details.last_name}`;
            document.getElementById('occupation').innerText = details.occupation || "N/A";
            document.getElementById('phoneNumber').innerText = details.phone_number || "N/A";
            document.getElementById('patientEmail').innerText = details.email || "N/A";
            document.getElementById('age').innerText = details.age || "N/A";
            document.getElementById('gender').innerText = details.gender || "N/A";
            document.getElementById('address').innerText = details.address || "N/A";
        
            openModal();
        }
        
    });
    calendar.render();

    // Close modal when clicking the close button
    closeBtn.onclick = closeModal;

    // Close modal when clicking outside of it
    window.onclick = function (event) {
        if (event.target === modal) {
            closeModal();
        }
    };

    // Confirm appointment function
// Confirm appointment function
confirmBtn.onclick = function () {
    if (currentAppointmentId) {
        updateAppointmentStatus(currentAppointmentId, "Confirmed", "Appointment confirmed successfully!");
    }
};

// Decline appointment function
declineBtn.onclick = function () {
    if (currentAppointmentId) {
        updateAppointmentStatus(currentAppointmentId, "Cancelled", "Appointment canceled successfully!");
    }
};
// Finish Appointment
// Finish Appointment
finishBtn.addEventListener("click", function() {
    if (currentAppointmentId) {
        // Update appointment status to 'Completed'
        updateAppointmentStatus(currentAppointmentId, "Completed", "Appointment marked as Completed.");
    }
});

// Function to update the appointment status
function updateAppointmentStatus(appointmentId, status, message) {
    fetch('../conn/updatestatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: appointmentId, status: status }) // Send 'id' and 'status'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCustomAlert("Appointment status updated to " + status, "success");
            closeModal();
            // Optionally, refresh the calendar to reflect the status change
            calendar.refetchEvents();
        } else {
            showCustomAlert("Failed to update status.", "error");
        }
    })
    .catch(error => {
        showCustomAlert("Error updating status.", "error");
    });
}


// Show Custom Alert
function showCustomAlert(message, type) {
    Swal.fire({
        title: type === 'success' ? 'Success!' : 'Error!',
        text: message,
        icon: type,
        confirmButtonText: 'OK'
    });
}

// Function to update appointment status in the database
function updateAppointmentStatus(appointmentId, status, message) {
    fetch('../conn/updatestatus.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: appointmentId, status: status })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCustomAlert(message, "success"); // Show success alert
                closeModal(); // Close the modal
                calendar.refetchEvents(); // Refresh the calendar
            } else {
                showCustomAlert("Failed to update appointment.", "error"); // Show error alert
            }
        })
        .catch(error => {
            showCustomAlert("An error occurred. Please try again.", "error"); // Handle fetch errors
        });
}

function showCustomAlert(message, icon) {
    Swal.fire({
        position: "top-end", // Align it at the top-right corner
        icon: icon, // 'success', 'error', etc.
        title: message,
        toast: true, // Makes it look like a toast notification
        showConfirmButton: false, // Removes the "OK" button
        timer: 1500, // Automatically disappears after 1.5 seconds
        customClass: {
            popup: 'custom-swal-popup', // Optional custom styling
            title: 'custom-swal-title'  // Custom styling for title
        },
        background: '#f9f9f9', // Optional, lightens the background
    });
}


});