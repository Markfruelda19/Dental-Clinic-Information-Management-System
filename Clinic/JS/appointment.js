document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    let selectedTimeSlot = null;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        selectable: true,
        editable: true,
        events: '../connections/getevents.php',
        dateClick: function (info) {
            const selectedDate = info.dateStr;
            document.getElementById('appointment-date').value = selectedDate;
            highlightSelectedDate(info.dayEl);
            populateTimeSlots(selectedDate);
        },
    });

    calendar.render();

    function highlightSelectedDate(dayElement) {
        document.querySelectorAll('.fc-day-selected').forEach((el) => el.classList.remove('fc-day-selected'));
        dayElement.classList.add('fc-day-selected');
    }

    async function populateTimeSlots(date) {
        const startHour = 9;
        const endHour = 16;
        const maxSlots = 2;
        const timeSlotsList = document.getElementById('timeSlotsList');
        timeSlotsList.innerHTML = '';

        try {
            const response = await fetch(`../connections/getslotavailability.php?date=${date}`);
            const data = await response.json();

            if (!data.success) {
                console.error('Error from server:', data.message);
                return;
            }

            const slotAvailability = data.slots || {};

            for (let hour = startHour; hour < endHour; hour++) {
                const startTime = formatTime12(hour);
                const endTime = formatTime12(hour + 1);
                const timeRangeDisplay = `${startTime} - ${endTime}`;

                const slotDiv = document.createElement('div');
                slotDiv.className = 'time-slot';
                slotDiv.innerText = timeRangeDisplay;

                const availabilityCount = slotAvailability[startTime] || 0;
                const remainingSlots = maxSlots - availabilityCount;
                const availabilityText = remainingSlots > 0 ? `${remainingSlots} slot(s) available` : 'Full';

                const availabilityDiv = document.createElement('div');
                availabilityDiv.className = 'availability';
                availabilityDiv.innerText = availabilityText;

                if (remainingSlots > 0) {
                    slotDiv.addEventListener('click', function () {
                        selectedTimeSlot = startTime;
                        document.getElementById('appointment-time').value = selectedTimeSlot;
                        highlightSelectedTimeSlot(slotDiv);
                    });
                } else {
                    slotDiv.classList.add('full-slot');
                }

                slotDiv.appendChild(availabilityDiv);
                timeSlotsList.appendChild(slotDiv);
            }
        } catch (error) {
            console.error('Error fetching or parsing slot availability:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while loading time slots. Please try again later.',
                showConfirmButton: true,
            });
        }
    }

    function highlightSelectedTimeSlot(slotDiv) {
        document.querySelectorAll('.time-slot').forEach((el) => el.classList.remove('selected-time'));
        slotDiv.classList.add('selected-time');
    }

    function formatTime12(hour) {
        const period = hour >= 12 ? 'PM' : 'AM';
        const adjustedHour = hour % 12 || 12;
        return `${adjustedHour}:00 ${period}`;
    }

    // Initialize Select2 for the services dropdown
    $(document).ready(function () {
        $('#services').select2({
            placeholder: 'Select one or more services',
            allowClear: true,
        });
    });
});

function submitAppointment() {
    const selectedServices = $('#services').val();

    const appointmentData = {
        services: selectedServices,
        complaint: document.getElementById('complaint').value,
        other_details: document.getElementById('other-details').value,
        medical_history: document.getElementById('medical-history').value,
        allergies: document.getElementById('allergies').value,
        expected_date: document.getElementById('appointment-date').value,
        expected_time: document.getElementById('appointment-time').value,
        status: 'Scheduled',
    };

    fetch('../connections/addappointment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(appointmentData),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                resetAppointmentForm();
                Swal.fire({
                    icon: 'success',
                    title: 'Appointment successfully booked!',
                    text: 'Your appointment has been scheduled successfully.',
                    showConfirmButton: true,
                    confirmButtonColor: '#28a745',
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error Booking Appointment',
                    text: data.message,
                    showConfirmButton: true,
                    confirmButtonColor: '#dc3545',
                });
            }
        })
        .catch((error) => {
            console.error('Error adding appointment:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while adding the appointment. Please try again later.',
                showConfirmButton: true,
                confirmButtonColor: '#dc3545',
            });
        });
}

// Function to reset form inputs
function resetAppointmentForm() {
    $('#services').val(null).trigger('change');
    document.getElementById('complaint').value = '';
    document.getElementById('medical-history').value = '';
    document.getElementById('allergies').value = '';
    document.getElementById('other-details').value = '';
    document.getElementById('appointment-date').value = '';
    document.getElementById('appointment-time').value = '';
}
