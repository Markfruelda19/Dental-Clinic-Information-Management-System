<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Billing Invoice</title>
    <link rel="stylesheet" href="../css/bills.css"> <!-- CSS for Bills -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Varela+Round&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script> <!-- jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert -->
</head>

<body>
    <div class="content-wrapper">
        <div class="main-content">
            <div class="billing-header">
                <h2>Generate Billing Invoice</h2>
                <p>Follow the steps below to generate and finalize the invoice.</p>
            </div>
            <div class="billing-progress">
                <div class="progress-step" id="step-1">
                    <div class="step-number">1</div>
                    <div class="step-title">Select Patient</div>
                </div>
                <div class="progress-step" id="step-2">
                    <div class="step-number">2</div>
                    <div class="step-title">Review Treatments</div>
                </div>
                <div class="progress-step" id="step-3">
                    <div class="step-number">3</div>
                    <div class="step-title">Preview Invoice</div>
                </div>
                <div class="progress-step" id="step-4">
                    <div class="step-number">4</div>
                    <div class="step-title">Confirm Invoice</div>
                </div>
            </div>
            <!-- Billing Layout -->
            <div class="billing-layout">
                
                <!-- Left Column: Select Patient -->
                <div class="billing-section" id="select-patient-section">
                    <div class="card">
                        <h3>Select Patient</h3>
                        <label for="patient_id">Patient:</label>
                        <select id="patient_id" name="patient_id">
                            <option value="">Select a patient</option>
                            <!-- Patient options will be populated dynamically -->
                        </select>
                        <button class="btn-primary" onclick="fetchTreatments()">View Treatments</button>
                    </div>
                </div>

                <!-- Center Column: Treatments -->
                <div class="billing-section" id="treatment-section">
                    <div class="card">
                        <h3>Treatments for Patient</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Treatment</th>
                                    <th>Tooth Number</th>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="treatmentTable">
                                <!-- Dynamically populated -->
                            </tbody>
                        </table>
                        
                        <!-- Additional Dental Fees Section -->
                        <div class="additional-fees-container">
                            <label for="additional_fee_amount">Additional Fee (₱):</label>
                            <input type="number" id="additional_fee_amount" name="additional_fee_amount" placeholder="Enter fee amount">
                            <label for="additional_fee_description">Description:</label>
                            <input type="text" id="additional_fee_description" name="additional_fee_description" placeholder="Enter fee description">
                        </div>

                        <button class="btn-primary" onclick="previewInvoice()">Preview Invoice</button>
                    </div>
                </div>

                <!-- Right Column: Invoice Preview -->
                <div class="billing-section" id="invoice-preview-section">
                    <div class="card">
                        <h3>Invoice Preview</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Treatment</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="invoiceTable">
                                <!-- Dynamically populated -->
                            </tbody>
                        </table>
                        <div class="total-container">
                            Total: ₱<span id="totalAmount"></span>
                        </div>
                        <button class="btn-primary" onclick="finalizeInvoice()">Confirm Invoice</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="../script/billing.js"></script> <!-- Billing JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</body>

</html>
