<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Services</title>
    <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/services.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header class="inventory-header">
        <div class="header-content">
            <h1>Dental Services</h1>
            <p>Manage your dental services efficiently and effectively.</p>
        </div>
        <div class="header-buttons">
            <button class="btn btn-add" onclick="openModal('add-modal')">
                <i class="fas fa-plus"></i> Add New Service
            </button>
        </div>
    </header>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Service Name</th>
                <th>Price Range</th>
                <th>Unit-Based</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="services-table-body">
            <?php
            include '../conn/db_connection.php';
            $query = "SELECT 
                        service_id, 
                        service_name, 
                        COALESCE(price_min, 0) AS price_min, 
                        COALESCE(price_max, 0) AS price_max, 
                        COALESCE(unit_based, 0) AS unit_based, 
                        description 
                      FROM services 
                      ORDER BY service_name ASC";
            $result = $conn->query($query);

            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <tr id="service-row-<?= $row['service_id'] ?>">
                    <td><?= $row['service_id'] ?></td>
                    <td><?= htmlspecialchars($row['service_name']) ?></td>
                    <td>
                        <?php
                        if ($row['price_min'] > 0 && $row['price_max'] > 0) {
                            echo '₱' . number_format($row['price_min'], 2) . ' - ₱' . number_format($row['price_max'], 2);
                        } elseif ($row['price_min'] > 0) {
                            echo '₱' . number_format($row['price_min'], 2);
                        } else {
                            echo 'Contact us for pricing';
                        }
                        ?>
                    </td>
                    <td><?= $row['unit_based'] ? 'Yes' : 'No' ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="openEditModal(<?= $row['service_id'] ?>, '<?= htmlspecialchars($row['service_name']) ?>', <?= $row['price_min'] ?>, <?= $row['price_max'] ?>, <?= $row['unit_based'] ?>, '<?= htmlspecialchars($row['description']) ?>')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-delete" onclick="deleteService(<?= $row['service_id'] ?>)">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </td>
                </tr>
            <?php
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="6">No services available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Add Modal -->
    <div id="add-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('add-modal')">&times;</span>
            <h2>Add New Service</h2>
            <form id="add-form" class="modal-form">
                <label for="ServiceName">Service Name:</label>
                <input type="text" id="ServiceName" name="ServiceName" required>

                <label for="PriceMin">Price Min:</label>
                <input type="number" step="0.01" id="PriceMin" name="PriceMin">

                <label for="PriceMax">Price Max:</label>
                <input type="number" step="0.01" id="PriceMax" name="PriceMax">

                <label for="UnitBased">Unit Based:</label>
                <select id="UnitBased" name="UnitBased">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>

                <label for="Description">Description:</label>
                <textarea id="Description" name="Description" required></textarea>

                <button type="submit" class="btn btn-submit">Add Service</button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('edit-modal')">&times;</span>
            <h2>Edit Service</h2>
            <form id="edit-form" class="modal-form">
                <input type="hidden" id="EditServiceID" name="ServiceID">

                <label for="EditServiceName">Service Name:</label>
                <input type="text" id="EditServiceName" name="ServiceName" required>

                <label for="EditPriceMin">Price Min:</label>
                <input type="number" step="0.01" id="EditPriceMin" name="PriceMin">

                <label for="EditPriceMax">Price Max:</label>
                <input type="number" step="0.01" id="EditPriceMax" name="PriceMax">

                <label for="EditUnitBased">Unit Based:</label>
                <select id="EditUnitBased" name="UnitBased">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>

                <label for="EditDescription">Description:</label>
                <textarea id="EditDescription" name="Description" required></textarea>

                <button type="submit" class="btn btn-submit">Update Service</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function openEditModal(id, name, priceMin, priceMax, unitBased, description) {
            document.getElementById('EditServiceID').value = id;
            document.getElementById('EditServiceName').value = name;
            document.getElementById('EditPriceMin').value = priceMin || '';
            document.getElementById('EditPriceMax').value = priceMax || '';
            document.getElementById('EditUnitBased').value = unitBased ? '1' : '0';
            document.getElementById('EditDescription').value = description;
            openModal('edit-modal');
        }

        function refreshTable() {
            fetch(location.href)
                .then(response => response.text())
                .then(html => {
                    const newTableBody = new DOMParser().parseFromString(html, 'text/html').querySelector('#services-table-body');
                    document.querySelector('#services-table-body').replaceWith(newTableBody);
                });
        }

        document.getElementById('add-form').addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('../conn/add_service.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Success', data.message, 'success');
                        closeModal('add-modal');
                        refreshTable();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => Swal.fire('Error', 'An unexpected error occurred.', 'error'));
        });

        document.getElementById('edit-form').addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch('../conn/edit_service.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Success', data.message, 'success');
                        closeModal('edit-modal');
                        refreshTable();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => Swal.fire('Error', 'An unexpected error occurred.', 'error'));
        });

        function deleteService(serviceId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this service!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('../conn/delete_service.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ service_id: serviceId }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire('Deleted!', data.message, 'success');
                                refreshTable();
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => Swal.fire('Error', 'An unexpected error occurred.', 'error'));
                }
            });
        }
    </script>
</body>
</html>
