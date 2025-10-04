    <?php
    include '../conn/db_connection.php';

    // Determine the action from the request
    $action = $_GET['action'] ?? null;

    try {
        switch ($action) {
            case 'fetch_patients':
                fetchPatients($conn);
                break;

            case 'fetch_treatments':
                fetchTreatments($conn);
                break;

            case 'generate_invoice':
                generateInvoice($conn);
                break;

            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    // Fetch patients
    function fetchPatients($conn) {
        $query = "SELECT patient_id, full_name FROM patients";
        $result = $conn->query($query);

        if (!$result) {
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
            return;
        }

        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }

        echo json_encode($patients);
    }

    // Fetch treatments for a patient
    function fetchTreatments($conn) {
        $patient_id = $_GET['patient_id'];

        // In fetchTreatments(), include `unit_based` column:
    $query = "
        SELECT 
            t.treatment_id,
            t.tooth_number,
            t.treatment,
            t.date,
            s.service_id,
            s.service_name,
            COALESCE(s.price_min, 0) AS service_price,
            s.unit_based
        FROM treatments t
        LEFT JOIN services s
            ON t.treatment = s.service_name
        WHERE t.patient_id = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $treatments = [];
        while ($row = $result->fetch_assoc()) {
            $treatments[] = $row;
        }

        echo json_encode($treatments);
    }

    // Generate an invoice
    function generateInvoice($conn) {
        $data = json_decode(file_get_contents("php://input"), true);
        $patient_id = $data['patient_id'];
        $selected_treatments = $data['selected_treatments'];
        $additional_fee = $data['additional_fee'] ?? null; // Additional fee data

        // Validate input
        if (empty($selected_treatments) || !is_array($selected_treatments)) {
            echo json_encode(['status' => 'error', 'message' => 'No treatments selected or invalid format.']);
            exit;
        }

        // Insert a new billing record
        $query = "INSERT INTO billing (patient_id, total_amount, created_at) VALUES (?, 0, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $billing_id = $stmt->insert_id;

        $total_amount = 0;

        foreach ($selected_treatments as $treatment) {
            $treatment_id = $treatment['treatment_id'];
            $quantity = $treatment['quantity'];

            // Fetch service_id and price for the treatment
            $query = "
                SELECT s.service_id, s.price_min AS service_price
                FROM treatments t
                LEFT JOIN services s ON t.treatment = s.service_name
                WHERE t.treatment_id = ?
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $treatment_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result || !isset($result['service_id'])) {
                // Log the missing service_id and treatment_id
                error_log("No matching service found for treatment ID: $treatment_id. Skipping this treatment.");
                continue; // Skip this treatment if service_id is missing
            }

            $service_id = $result['service_id'];
            $price = $result['service_price'];
            $subtotal = $price * $quantity;  // Calculate subtotal based on quantity
            $total_amount += $subtotal;

            // Insert the billing item record
            $query = "INSERT INTO billing_items (billing_id, service_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiddd", $billing_id, $service_id, $quantity, $price, $subtotal);
            
            if (!$stmt->execute()) {
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
                exit;
            }
        }

        // Add the additional fee if present
        if ($additional_fee) {
            $additional_fee_description = $additional_fee['description'];
            $additional_fee_amount = $additional_fee['amount'];

            // Insert the additional fee as a billing item (using NULL for service_id as it's a custom fee)
            $query = "INSERT INTO billing_items (billing_id, service_id, quantity, price, subtotal) VALUES (?, NULL, 1, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("idd", $billing_id, $additional_fee_amount, $additional_fee_amount);
            
            if (!$stmt->execute()) {
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
                exit;
            }

            // Add the additional fee to the total amount
            $total_amount += $additional_fee_amount;
        }

        // Update the total amount in the billing table
        $query = "UPDATE billing SET total_amount = ? WHERE billing_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("di", $total_amount, $billing_id);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'billing_id' => $billing_id]);
    }

