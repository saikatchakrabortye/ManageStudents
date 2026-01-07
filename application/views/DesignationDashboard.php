<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designation Master</title>
    <!-- Add DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    
    <div class="dashboardContainer">
        <!---------------------------------------------------- Start of header ----------------------------------------------------->
        <div class="dashboardHeader">
            <h1>Designation Master</h1>
            <div class="buttonsGroup">
                <button id="addDesignationBtn">Add Designation</button>
                <button id="employeeDashboardBtn">Employee Dashboard</button>
            </div>
        </div> 
        <!------------------------------------------------------ End of header ---------------------------------------------------->
        
        <!-------------------------------------------- Start of designation-table-container ------------------------------------------>
        <div class="tableContainer">
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
                <div class="formHeader">
                    <h2>Add New Designation</h2>
                    <span class="closeBtn" id="closeAddDesignationModal">&times;</span>
                </div>
                <form id="addDesignationForm">
                    <div class="formGroup">
                        <input type="text" name="name" class="formInput" placeholder="" required>
                        <label class="formLabel">Designation Name</label>
                    </div>
                    
                    <button type="submit" id="addDesignationSubmitBtn">Add Designation</button>
                    <div id="addDesignationFormErrorContainer"></div>
                </form>
            </div>
        </div>
        <!------------------------------------------------ End of add-designation-modal -------------------------------------------->
        
        <!-------------------------------------------- Designation Details Modal Start ---------------------------------------------->
        <div class="modal" id="viewDesignationModal">
            <div class="modalContent">
                <div class="formHeader">
                    <h2>Designation Details</h2>
                    <span class="closeBtn" id="closeViewDesignationModal">&times;</span>
                </div>
                <form id="updateDesignationForm">
                    <div id="designationDetailsContent">
                        <div class="info-card">
                            <div><b>Designation ID:</b> <span id="viewId"></span></div>
                        </div>
                        
                        <div class="formGroup">
                            <input type="text" name="name" id="viewName" class="formInput" placeholder="" readonly>
                            <label class="formLabel">Designation Name</label>
                        </div>
                        
                        <div class="formGroup">
                            <select name="status" id="viewStatus" class="formInput" disabled>
                                <option value="" disabled selected>Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <label class="formLabel">Status</label>
                        </div>
                        
                        <div class="info-card">
                            <div><b>Created At:</b> <span id="viewCreatedAt"></span></div>
                            <div><b>Updated At:</b> <span id="viewUpdatedAt"></span></div>
                        </div>
                        
                        <button type="submit" id="updateDesignationBtn">Update Changes</button>
                    </div>
                </form>
            </div>
        </div>
        <!-------------------------------------------- Designation Details Modal End ------------------------------------------------>
        
        <!-- Success Modal -->
        <div class="modal" id="successModal">
            <div class="modalContent">
                <div class="formHeader">
                    <h2>✅ Designation Added Successfully</h2>
                    <span class="closeBtn" onclick="closeSuccessModal()">&times;</span>
                </div>
                <div id="successDetails"></div>
                <button onclick="closeSuccessModal()" style="margin-top: 20px;">OK</button>
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
                "lengthMenu": [10, 25, 50, 100],
                "responsive": true,
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        });

        // ========== MODAL FUNCTIONALITY ==========
        // Open Add Designation Modal
        document.getElementById('addDesignationBtn').addEventListener('click', function() {
            document.getElementById('addDesignationModal').style.display = 'flex';
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
                <div class="info-card">
                    <div><b>Designation ID:</b> ${designation.id}</div>
                    <div><b>Name:</b> ${designation.name}</div>
                    <div><b>Status:</b> ${designation.status}</div>
                    <div><b>Created At:</b> ${designation.createdAt}</div>
                </div>
            `;
            
            document.getElementById('successDetails').innerHTML = details;
            document.getElementById('successModal').style.display = 'flex';
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
                nameField.classList.add('readonly');
                statusField.setAttribute('disabled', true);
                updateBtn.style.display = 'none';
            } else {
                nameField.removeAttribute('readonly');
                nameField.classList.remove('readonly');
                statusField.removeAttribute('disabled');
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
                document.getElementById('viewDesignationModal').style.display = 'flex';
                
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

        // Add to your existing CSS or in a style tag
        const style = document.createElement('style');
        style.textContent = `
            /* Additional styles for designation page */
            .status-active {
                color: #28a745;
                font-weight: 600;
                padding: 4px 8px;
                background: #d4edda;
                border-radius: 12px;
                display: inline-block;
            }
            
            .status-inactive {
                color: #dc3545;
                font-weight: 600;
                padding: 4px 8px;
                background: #f8d7da;
                border-radius: 12px;
                display: inline-block;
            }
            
            .action-buttons {
                display: flex;
                gap: 8px;
            }
            
            .viewDesignationBtn, .editDesignationBtn {
                padding: 6px 12px;
                font-size: 14px;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.3s;
            }
            
            .viewDesignationBtn {
                background: #28a745;
                color: white;
                border: none;
            }
            
            .viewDesignationBtn:hover {
                background: #218838;
            }
            
            .editDesignationBtn {
                background: #ffc107;
                color: #212529;
                border: none;
            }
            
            .editDesignationBtn:hover {
                background: #e0a800;
            }
            
            .error-message-onsubmit {
                color: #721c24;
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                border-radius: 4px;
                padding: 12px;
                margin-top: 15px;
                font-size: 14px;
            }
            
            .formInput[readonly] {
                background-color: #f8f9fa;
                cursor: not-allowed;
            }
            
            select.formInput[disabled] {
                background-color: #f8f9fa;
                cursor: not-allowed;
                opacity: 0.7;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>