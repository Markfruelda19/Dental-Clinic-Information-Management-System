function fetchPatients() {
    fetch('../conn/billcon.php?action=fetch_patients')
        .then(response => response.json())
        .then(data => {
            const patientSelect = document.getElementById('patient_id');
            patientSelect.innerHTML = '<option value="">Select a patient</option>';

            // Populate the dropdown with patients
            data.forEach(patient => {
                const option = document.createElement('option');
                option.value = patient.patient_id;
                option.textContent = patient.full_name;
                patientSelect.appendChild(option);
            });

            // Initialize Select2 on the patient dropdown for searchable functionality
            $('#patient_id').select2({
                placeholder: 'Search patients...',
                allowClear: true // Allows clearing the selection
            });
        })
        .catch(error => console.error('Error fetching patients:', error));
}

// Fetch treatments for a selected patient
function fetchTreatments() {
    const patientId = document.getElementById('patient_id').value;

    if (!patientId) {
        Swal.fire('Error', 'Please select a patient.', 'error');
        return;
    }

    // Debug log to verify patient ID
    console.log(`Fetching treatments for patient ID: ${patientId}`);

    fetch(`../conn/billcon.php?action=fetch_treatments&patient_id=${patientId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch treatments.');
            }
            return response.json();
        })
        .then(data => {
            const treatmentTable = document.getElementById('treatmentTable');
            treatmentTable.innerHTML = '';

            if (data.length === 0) {
                Swal.fire('No Treatments Found', 'The selected patient has no treatments available.', 'info');
                return;
            }

            data.forEach(treatment => {
                const price = treatment.service_price ? parseFloat(treatment.service_price) : 0;
                const row = `
                    <tr>
                        <td><input type="checkbox" value="${treatment.treatment_id}" class="treatmentCheckbox"></td>
                        <td>${treatment.treatment}</td>
                        <td>${treatment.tooth_number}</td>
                        <td>${treatment.date}</td>
                        <td>₱${price.toFixed(2)}</td>
                        <td><input type="number" min="1" value="1" class="quantityInput" data-treatment-id="${treatment.treatment_id}" /></td>
                    </tr>
                `;
                treatmentTable.insertAdjacentHTML('beforeend', row);
            });

            // Show the treatments section
            document.getElementById('treatment-section').classList.remove('hidden');
            // Step 2 completion
            setStepActive(2);
        })
        .catch(error => {
            console.error('Error fetching treatments:', error);
            Swal.fire('Error', 'An error occurred while fetching treatments. Please try again later.', 'error');
        });
}

// Set active step in the billing progress bar
function setStepActive(stepNumber) {
    // Reset all steps
    document.querySelectorAll('.progress-step').forEach(step => {
        step.classList.remove('active', 'completed');
    });

    // Set current step active
    document.getElementById(`step-${stepNumber}`).classList.add('active');
    if (stepNumber > 1) {
        document.getElementById(`step-${stepNumber}`).classList.add('completed');
    }
}

// Preview the invoice, including additional fees
function previewInvoice() {
    const selectedTreatments = Array.from(document.querySelectorAll('.treatmentCheckbox:checked'))
        .map(checkbox => checkbox.value);

    if (selectedTreatments.length === 0) {
        Swal.fire('Error', 'No treatments selected.', 'error');
        return;
    }

    const invoiceTable = document.getElementById('invoiceTable');
    invoiceTable.innerHTML = ''; // Clear any existing rows

    let totalAmount = 0;

    // Adding treatments to the invoice
    selectedTreatments.forEach(treatmentId => {
        const row = document.querySelector(`.treatmentCheckbox[value="${treatmentId}"]`).closest('tr');
        const treatmentName = row.cells[1].textContent;
        const price = parseFloat(row.cells[4].textContent.replace('₱', ''));
        const quantityInput = row.querySelector(`.quantityInput[data-treatment-id="${treatmentId}"]`);
        const quantity = parseInt(quantityInput.value, 10);

        const subtotal = price * quantity;
        totalAmount += subtotal;

        invoiceTable.insertAdjacentHTML('beforeend', `
            <tr>
                <td>${treatmentName}</td>
                <td>${quantity}</td>
                <td>₱${price.toFixed(2)}</td>
                <td>₱${subtotal.toFixed(2)}</td>
            </tr>
        `);
    });

    // Add additional fees if any
    const additionalFeeDescription = document.getElementById('additional_fee_description').value;
    const additionalFeeAmount = parseFloat(document.getElementById('additional_fee_amount').value);

    if (additionalFeeDescription && !isNaN(additionalFeeAmount)) {
        const additionalFeeSubtotal = additionalFeeAmount;
        totalAmount += additionalFeeSubtotal;

        invoiceTable.insertAdjacentHTML('beforeend', `
            <tr>
                <td>${additionalFeeDescription}</td>
                <td>1</td>
                <td>₱${additionalFeeAmount.toFixed(2)}</td>
                <td>₱${additionalFeeSubtotal.toFixed(2)}</td>
            </tr>
        `);
    }

    // Update the total amount in the invoice preview
    document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);

    // Show the invoice preview section
    document.getElementById('invoice-preview-section').classList.remove('hidden');
}

function finalizeInvoice() {
    const patientId = document.getElementById('patient_id').value;
    const selectedTreatments = Array.from(document.querySelectorAll('.treatmentCheckbox:checked')).map(checkbox => {
        const row = checkbox.closest('tr');
        const quantityInput = row.querySelector(`.quantityInput[data-treatment-id="${checkbox.value}"]`);
        return { treatment_id: checkbox.value, quantity: parseInt(quantityInput.value, 10) };
    });

    // Get additional fee details
    const additionalFeeDescription = document.getElementById('additional_fee_description').value;
    const additionalFeeAmount = parseFloat(document.getElementById('additional_fee_amount').value);

    // If additional fee is provided, include it in the request
    let additionalFee = null;
    if (additionalFeeDescription && !isNaN(additionalFeeAmount)) {
        additionalFee = {
            description: additionalFeeDescription,
            amount: additionalFeeAmount
        };
    }

    fetch('../conn/billcon.php?action=generate_invoice', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            patient_id: patientId,
            selected_treatments: selectedTreatments,
            additional_fee: additionalFee
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire('Success', `Invoice #${data.billing_id} generated successfully!`, 'success');
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => console.error('Error generating invoice:', error));
}

// Load patients when the page loads
document.addEventListener('DOMContentLoaded', fetchPatients);
