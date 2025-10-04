<?php
include '../conn/db_connection.php';

// Fetch tools from the database
$query = "SELECT ToolID, ToolName, Category, Quantity, CostPrice, PurchaseDate, Supplier,
          IFNULL(AverageDailyUsage, 0) AS AverageDailyUsage,
          IFNULL(LeadTime, 0) AS LeadTime,
          IFNULL(SafetyStock, 0) AS SafetyStock
          FROM tools";
$result = $conn->query($query);

// Debugging fetched data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        error_log("Fetched ToolID {$row['ToolID']} with Supplier: {$row['Supplier']}");
    }
} else {
    error_log("No rows found for tools.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Tools Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Varela+Round&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/inventory.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Header Section -->
    <header class="inventory-header">
        <div class="header-content">
            <h1>Dental Tools Inventory</h1>
            <p>Efficiently manage your dental tools and equipment inventory.</p>
        </div>
        <div class="header-buttons">
            <button class="btn btn-add" onclick="openModal('add-modal')"><i class="fas fa-plus"></i> Add New Tool</button>
        </div>
    </header>

    <!-- Inventory Table -->
    <table>
    <thead>
    <tr>
        <th>#</th>
        <th>Tool Name</th>
        <th>Category</th>
        <th>Quantity</th>
        <th>Cost Price</th>
        <th>Purchase Date</th>
        <th>Supplier</th>
        <th>Reorder Point</th>
        <th>Actions</th>
    </tr>
</thead>
<tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php $result->data_seek(0); while ($row = $result->fetch_assoc()): 
            $reorder_point = ($row['AverageDailyUsage'] * $row['LeadTime']) + $row['SafetyStock'];
        ?>
            <tr id="tool-row-<?= $row['ToolID'] ?>">
                <td><?= $row['ToolID'] ?></td>
                <td><?= htmlspecialchars($row['ToolName']) ?></td>
                <td><?= htmlspecialchars($row['Category']) ?></td>
                <td><?= $row['Quantity'] ?></td>
                <td><?= number_format($row['CostPrice'], 2) ?></td>
                <td><?= $row['PurchaseDate'] ?></td>
                <td><?= htmlspecialchars($row['Supplier']) ?></td>
                <td><?= number_format($reorder_point, 2) ?></td>
                <td class="action-btn">
                    <button class="btn btn-edit" onclick="openEditModal(<?= $row['ToolID'] ?>, '<?= htmlspecialchars($row['ToolName']) ?>', '<?= htmlspecialchars($row['Category']) ?>', <?= $row['Quantity'] ?>, <?= $row['CostPrice'] ?>, '<?= $row['PurchaseDate'] ?>', '<?= htmlspecialchars($row['Supplier']) ?>')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="9">No tools available in the inventory.</td>
        </tr>
    <?php endif; ?>
</tbody>

    </table>

    <!-- Add Tool Modal -->
    <div id="add-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('add-modal')">&times;</span>
            <h2>Add New Tool</h2>
            <form id="add-form" class="modal-form">
                <label for="ToolName">Tool Name:</label>
                <input type="text" id="ToolName" name="ToolName" required>

                <label for="Category">Category:</label>
                <input type="text" id="Category" name="Category" required>

                <label for="Quantity">Quantity:</label>
                <input type="number" id="Quantity" name="Quantity" required>

                <label for="CostPrice">Cost Price:</label>
                <input type="number" step="0.01" id="CostPrice" name="CostPrice" required>

                <label for="PurchaseDate">Purchase Date:</label>
                <input type="date" id="PurchaseDate" name="PurchaseDate" required>

                <label for="Supplier">Supplier:</label>
                <input type="text" id="Supplier" name="Supplier" required>

                <button type="submit" class="btn btn-submit">Add Tool</button>
            </form>
        </div>
    </div>

<!-- Notification Icon -->
<div id="notification-icon" onclick="toggleNotificationPopup()">
    <i class="fa fa-bell"></i> <!-- Notification Bell Icon -->
</div>

<!-- Popup for Low-Quantity Tools -->
<div id="notification-popup">
    <div class="popup-header">Low Stock Alerts</div>
    <ul>
        <!-- Populated dynamically by JavaScript -->
    </ul>
    <span class="close-btn" onclick="closeNotificationPopup()">&times;</span> <!-- Close Button -->
</div>

    <!-- Edit Tool Modal -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('edit-modal')">&times;</span>
            <h2>Edit Tool</h2>
            <form id="edit-form" class="modal-form">
                <input type="hidden" id="EditToolID" name="ToolID">

                <label for="EditToolName">Tool Name:</label>
                <input type="text" id="EditToolName" name="ToolName" required>

                <label for="EditCategory">Category:</label>
                <input type="text" id="EditCategory" name="Category" required>

                <label for="EditQuantity">Quantity:</label>
                <input type="number" id="EditQuantity" name="Quantity" required>

                <label for="EditCostPrice">Cost Price:</label>
                <input type="number" step="0.01" id="EditCostPrice" name="CostPrice" required>

                <label for="EditPurchaseDate">Purchase Date:</label>
                <input type="date" id="EditPurchaseDate" name="PurchaseDate" required>

                <label for="EditSupplier">Supplier:</label>
                <input type="text" id="EditSupplier" name="Supplier" required>

                <button type="submit" class="btn btn-submit">Update Tool</button>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function openEditModal(id, name, category, quantity, costPrice, purchaseDate, supplier) {
            console.log("Opening Edit Modal - Supplier:", supplier);

            document.getElementById('EditToolID').value = id;
            document.getElementById('EditToolName').value = name;
            document.getElementById('EditCategory').value = category;
            document.getElementById('EditQuantity').value = quantity;
            document.getElementById('EditCostPrice').value = costPrice;
            document.getElementById('EditPurchaseDate').value = purchaseDate;
            document.getElementById('EditSupplier').value = supplier;
            openModal('edit-modal');
        }
        // Add Tool
        document.getElementById('add-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            console.log("Adding Tool with Supplier:", formData.get("Supplier"));

            fetch('../conn/add_tool.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('add-modal');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Tool added successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => location.reload(), 1600);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.error });
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to add tool: ' + error });
            });
        });

        // Edit Tool
        document.getElementById('edit-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            console.log("Editing Tool with Supplier:", formData.get("Supplier"));

            fetch('../conn/edit_tool.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal('edit-modal');
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Tool updated successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    const row = document.getElementById('tool-row-' + formData.get('ToolID'));
                    row.cells[1].textContent = formData.get('ToolName');
                    row.cells[2].textContent = formData.get('Category');
                    row.cells[3].textContent = formData.get('Quantity');
                    row.cells[4].textContent = formData.get('CostPrice');
                    row.cells[5].textContent = formData.get('PurchaseDate');
                    row.cells[6].textContent = formData.get('Supplier');
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.error });
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update tool: ' + error });
            });
        });
/// Function to fetch low quantity tools from PHP
var lowQuantityTools = <?php
    $low_quantity_tools = [];
    $query = "SELECT ToolID, ToolName, Category, Quantity, CostPrice, PurchaseDate, Supplier FROM tools WHERE Quantity < 5 ORDER BY ToolName ASC";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $low_quantity_tools[] = $row;
        }
    }
    echo json_encode($low_quantity_tools);
?>;

// Function to toggle the notification popup
function toggleNotificationPopup() {
    var popup = document.getElementById('notification-popup');

    // Check if the popup is already visible
    if (popup.style.display === "block") {
        popup.style.display = "none"; // Hide the popup
    } else {
        popup.style.display = "block"; // Show the popup

        // Clear existing content in the popup
        var list = popup.querySelector('ul');
        list.innerHTML = ""; // Clear previous items

        // Check if there are any low quantity tools
        if (lowQuantityTools.length > 0) {
            lowQuantityTools.forEach(function(tool) {
                var li = document.createElement('li');
                li.innerHTML = `<span class="tool-name">${tool.ToolName}</span> (${tool.Quantity} left)`;
                list.appendChild(li);
            });
        } else {
            var li = document.createElement('li');
            li.classList.add('no-tools');
            li.textContent = "No low-stock tools.";
            list.appendChild(li);
        }
    }
}

// Close the notification popup
function closeNotificationPopup() {
    var popup = document.getElementById('notification-popup');
    popup.style.display = "none";
}

// Fetch reorder recommendations
function fetchRecommendations() {
    fetch('../conn/recommendations.php')
        .then(response => response.json())
        .then(data => {
            const notificationList = document.querySelector('#notification-popup ul');
            notificationList.innerHTML = ''; // Clear existing items

            if (data.length > 0) {
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.innerHTML = `<span class="tool-name">${item.ToolName}</span> 
                                    (Current: ${item.Quantity}, Reorder Point: ${item.ReorderPoint})`;
                    notificationList.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = "All tools are sufficiently stocked.";
                notificationList.appendChild(li);
            }
        })
        .catch(error => console.error('Error fetching recommendations:', error));
}
fetchRecommendations();

    </script>
    </body>
</body>
</html>
