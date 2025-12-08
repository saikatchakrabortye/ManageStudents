<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designation Master</title>
    <!-- Add DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        /***** Styles for Modal Display *****/
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modalContent {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            width: 50%;
            border-radius: 5px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .closeBtn {
            float: right;
            cursor: pointer;
            font-size: 20px;
        }
        
        /***** Header Styles *****/
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        /***** Table Styles *****/
        .designationTableContainer {
            padding: 20px;
        }
        
        /***** Success Modal Styles *****/
        #successModal .modalContent {
            text-align: center;
            padding: 30px;
        }

        #successModal h2 {
            color: green;
            margin-bottom: 20px;
        }

        #successModal button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 20px;
        }
        
        /***** Form Styles *****/
        form div {
            margin-bottom: 15px;
            position: relative;
        }
        
        form input, form select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        form label {
            position: absolute;
            left: 10px;
            top: -10px;
            background: white;
            padding: 0 5px;
            font-size: 14px;
            color: #666;
        }
        
        button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        button[type="submit"]:hover {
            background-color: #0056b3;
        }
        
        .error-message-onsubmit {
            color: red;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
        
        /***** Action Buttons *****/
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .viewDesignationBtn, .editDesignationBtn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .viewDesignationBtn {
            background-color: #28a745;
            color: white;
        }
        
        .viewDesignationBtn:hover {
            background-color: #218838;
        }
        
        .editDesignationBtn {
            background-color: #ffc107;
            color: black;
        }
        
        .editDesignationBtn:hover {
            background-color: #e0a800;
        }
        
        #addDesignationBtn, #employeeDashboardBtn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-left: 10px;
        }
        
        #addDesignationBtn:hover, #employeeDashboardBtn:hover {
            background-color: #0056b3;
        }
        
        /***** Status Styles *****/
        .status-active {
            color: green;
            font-weight: bold;
        }
        
        .status-inactive {
            color: red;
            font-weight: bold;
        }
        
        /***** Info Container *****/
        .infoDisplayContainer {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        
        .viewOnlyInfoContainer {
            display: block;
            margin-bottom: 10px;
        }
        
        /***** Update Button Styles *****/
        #updateDesignationBtn {
            margin-top: 20px;
            background-color: #28a745;
        }
        
        #updateDesignationBtn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="dashboardContainer">
        <!---------------------------------------------------- Start of header ----------------------------------------------------->
        <div class="header">
            <h1>Designation Master</h1>
            <div>
                <button id="addDesignationBtn">Add Designation</button>
                <button id="employeeDashboardBtn">Employee Dashboard</button>
            </div>
        </div> 
        <!------------------------------------------------------ End of header ---------------------------------------------------->
        
        <!-------------------------------------------- Start of designation-table-container ------------------------------------------>
        <div class="designationTableContainer">
            <table id="designationTable" class="display">
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Designation Name</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($designations as $index => $designation): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($designation->name); ?></td>
                            <td>
                                <span class="status-<?php echo $designation->status; ?>">
                                    <?php echo ucfirst($designation->status); ?>
                                </span>
                            </td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($designation->createdAt)); ?></td>
                            <td><?php echo date('d-m-Y H:i:s', strtotime($designation->updatedAt)); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="viewDesignationBtn" data-id="<?php echo $designation->id; ?>">View</button>
                                    <button class="editDesignationBtn" data-id="<?php echo $designation->id; ?>">Edit</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!---------------------------------------------- End of designation-table-container ------------------------------------------>
        
        <!------------------------------------------------ Start of add-designation-modal -------------------------------------------->
        <div class="modal" id="addDesignationModal">
            <div class="modalContent">
                <span class="closeBtn" id="closeAddDesignationModal">&times;</span>
                <h2>Add New Designation</h2>
                <form id="addDesignationForm">
                    <div>
                        <input type="text" name="name" placeholder="" required>
                        <label>Designation Name</label>
                    </div>
                    <!--<div>
                        <select name="status" required>
                            <option value="" disabled selected>Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <label>Status</label>
                    </div>-->
                    <button type="submit" id="addDesignationSubmitBtn">Add Designation</button>
                    <div id="addDesignationFormErrorContainer"></div>
                </form>
            </div>
        </div>
        <!------------------------------------------------ End of add-designation-modal -------------------------------------------->
        
        <!-------------------------------------------- Designation Details Modal Start ---------------------------------------------->
        <div class="modal" id="viewDesignationModal">
            <div class="modalContent">
                <span class="closeBtn" id="closeViewDesignationModal">&times;</span>
                <h2>Designation Details</h2>
                <form id="updateDesignationForm">
                    <div id="designationDetailsContent">
                        <span class="viewOnlyInfoContainer"><b>Designation ID:</b> <span id="viewId"></span></span>
                        
                        <div>
                            <input type="text" name="name" id="viewName" placeholder="" readonly>
                            <label>Designation Name</label>
                        </div>
                        
                        <div>
                            <select name="status" id="viewStatus" readonly>
                                <option value="" disabled selected>Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <label>Status</label>
                        </div>
                        
                        <div class="infoDisplayContainer">
                            <span class="viewOnlyInfoContainer"><b>Created At:</b> <span id="viewCreatedAt"></span></span><br>
                            <span class="viewOnlyInfoContainer"><b>Updated At:</b> <span id="viewUpdatedAt"></span></span><br>
                        </div>
                        
                        <button type="submit" id="updateDesignationBtn">Update Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <!-------------------------------------------- Designation Details Modal End ------------------------------------------------>
        
        <!-- Success Modal -->
        <div class="modal" id="successModal">
            <div class="modalContent" style="width: 400px;">
                <h2>✅ Designation Added Successfully</h2>
                <div id="successDetails"></div>
                <button onclick="closeSuccessModal()">OK</button>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <script>
        // ========== DATATABLE INITIALIZATION ==========
        $(document).ready(function() {
            $('#designationTable').DataTable({
                "order": [[0, "asc"]],
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100]
            });
        });

        // ========== MODAL FUNCTIONALITY ==========
        // Open Add Designation Modal
        document.getElementById('addDesignationBtn').addEventListener('click', function() {
            document.getElementById('addDesignationModal').style.display = 'block';
        });

        // Close Add Designation Modal
        document.getElementById('closeAddDesignationModal').addEventListener('click', function() {
            document.getElementById('addDesignationModal').style.display = 'none';
            document.getElementById('addDesignationForm').reset();
            document.getElementById('addDesignationFormErrorContainer').innerHTML = '';
        });

        // Close View Designation Modal
        document.getElementById('closeViewDesignationModal').addEventListener('click', function() {
            document.getElementById('viewDesignationModal').style.display = 'none';
        });

        // Employee Dashboard Button
        document.getElementById('employeeDashboardBtn').addEventListener('click', function() {
            window.location.href = '<?php echo base_url("Employees"); ?>';
        });

        // ========== SUCCESS MODAL FUNCTIONS ==========
        function showSuccessModal(designation) {
            const details = `
                <p><strong>Designation ID:</strong> ${designation.id}</p>
                <p><strong>Name:</strong> ${designation.name}</p>
                <p><strong>Status:</strong> ${designation.status}</p>
                <p><strong>Created At:</strong> ${designation.createdAt}</p>
            `;
            
            document.getElementById('successDetails').innerHTML = details;
            document.getElementById('successModal').style.display = 'block';
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
            window.location.reload();
        }

        // ========== EVENT DELEGATION ==========
        document.addEventListener('click', function(e) {
            // View Designation Button
            if (e.target && e.target.classList.contains('viewDesignationBtn')) {
                const designationId = e.target.getAttribute('data-id');
                viewDesignationDetails(designationId);
                // Set to view mode
                setViewMode(true);
            }
            
            // Edit Designation Button
            if (e.target && e.target.classList.contains('editDesignationBtn')) {
                const designationId = e.target.getAttribute('data-id');
                viewDesignationDetails(designationId);
                // Set to edit mode
                setViewMode(false);
            }
        });

        // Function to set view/edit mode
        function setViewMode(isViewMode) {
            const nameField = document.getElementById('viewName');
            const statusField = document.getElementById('viewStatus');
            const updateBtn = document.getElementById('updateDesignationBtn');
            
            if (isViewMode) {
                nameField.setAttribute('readonly', true);
                statusField.setAttribute('readonly', true);
                updateBtn.style.display = 'none';
            } else {
                nameField.removeAttribute('readonly');
                statusField.removeAttribute('readonly');
                updateBtn.style.display = 'block';
            }
        }

        // ========== FORM SUBMISSIONS ==========
        // Add Designation Form Submission
        document.getElementById('addDesignationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            try {
                const response = await fetch('<?php echo base_url("Designations/addDesignation"); ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                const errorContainer = document.getElementById('addDesignationFormErrorContainer');
                
                errorContainer.innerHTML = '';

                if (!result.success) {
                    const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();
                    errorContainer.innerHTML = `<div class="error-message-onsubmit">${cleanMessage}</div>`;
                } else {
                    // Close modal, clear form, show success
                    document.getElementById('addDesignationModal').style.display = 'none';
                    this.reset();
                    showSuccessModal(result.designation);
                }
            } catch (error) {
                alert('Request failed: ' + error.message);
            }
        });

        // Update Designation Form Submission
        document.getElementById('updateDesignationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const designationId = document.getElementById('viewId').textContent;
            formData.append('designationId', designationId);

            try {
                const response = await fetch('<?php echo base_url("Designations/updateDesignation"); ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();

                if (!result.success) {
                    const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();
                    alert('Update failed: ' + cleanMessage);
                } else {
                    // Close modal and reload
                    document.getElementById('viewDesignationModal').style.display = 'none';
                    window.location.reload();
                }
            } catch (error) {
                alert('Request failed: ' + error.message);
            }
        });

        // ========== AJAX FUNCTIONS ==========
        async function viewDesignationDetails(designationId) {
            try {
                const response = await fetch('<?php echo base_url("Designations/getDesignationById/"); ?>' + designationId);
                const designation = await response.json();
                
                if (!designation.success) {
                    alert(designation.message);
                    return;
                }
                
                // Populate modal fields
                document.getElementById('viewId').textContent = designation.id;
                document.getElementById('viewName').value = designation.name;
                document.getElementById('viewStatus').value = designation.status;
                document.getElementById('viewCreatedAt').textContent = designation.createdAt;
                document.getElementById('viewUpdatedAt').textContent = designation.updatedAt;
                                
                
                // Show modal
                document.getElementById('viewDesignationModal').style.display = 'block';
                
            } catch (error) {
                console.error('Error fetching designation details:', error);
                alert('Error loading designation details');
            }
        }

        // ========== STATUS CHANGE FUNCTION ==========
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'viewStatus') {
                const nameField = document.getElementById('viewName');
                if (nameField.hasAttribute('readonly')) {
                    // In view mode, update status immediately
                    const designationId = document.getElementById('viewId').textContent;
                    const status = e.target.value;
                    updateDesignationStatus(designationId, status);
                }
            }
        });

        async function updateDesignationStatus(designationId, status) {
            try {
                const formData = new FormData();
                formData.append('designationId', designationId);
                formData.append('status', status);
                
                const response = await fetch('<?php echo base_url("Designations/setDesignationStatus"); ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error('Failed to update status');
                } else {
                    window.location.reload();
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('Error updating designation status');
            }
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(e) {
            const addModal = document.getElementById('addDesignationModal');
            const viewModal = document.getElementById('viewDesignationModal');
            const successModal = document.getElementById('successModal');
            
            if (e.target === addModal) {
                addModal.style.display = 'none';
                document.getElementById('addDesignationForm').reset();
                document.getElementById('addDesignationFormErrorContainer').innerHTML = '';
            }
            if (e.target === viewModal) {
                viewModal.style.display = 'none';
            }
            if (e.target === successModal) {
                closeSuccessModal();
            }
        });

        // Press ESC to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('addDesignationModal').style.display = 'none';
                document.getElementById('viewDesignationModal').style.display = 'none';
                document.getElementById('successModal').style.display = 'none';
                document.getElementById('addDesignationForm').reset();
                document.getElementById('addDesignationFormErrorContainer').innerHTML = '';
            }
        });
    </script>
</body>
</html>