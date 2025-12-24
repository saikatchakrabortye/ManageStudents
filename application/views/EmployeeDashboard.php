<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset=UTF-8>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Employee Dashboard</title>
        <link rel="stylesheet" type="text/css" href="assets/styles.css">
        <!-- Add DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <style>
        
        /****** Success Modal Styles****** */
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

        .no-results {
            padding: 10px;
            color: #666;
            text-align: center;
        }

        
    </style>
    </head>
    <body>
    <div class="dashboardContainer">
        <!---------------------------------------------------- Start of header ----------------------------------------------------->
        <div class="dashboardHeader">
            <h1>Employee Dashboard</h1>
            <div class="buttonsGroup">
            <button id="addEmployeeBtn">Add Employee</button>
            <button id="ctcFormBtn">CTC Form</button>
            <button id="designationMasterBtn" >Designation Master</button>
            </div>
        </div> 
        <!------------------------------------------------------ End of header ---------------------------------------------------->
        <!-------------------------------------------- Start of employee-table-container ------------------------------------------>
        <div class="tableContainer">
            <table id="employeeTable" class="display"> <!-- class="display" style is present in Datatable CSS CDN ---->
                <thead>
                    <tr>
                        <th>Sl No.</th>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Date of Birth</th>
                        <th>Designation</th>
                        <th>Date of Joining</th>
                        <th>Last CTC</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!--
                    Array association operator "=>" connects key to its value.
                    $employees = [0 => EmployeeObject1, 1 => EmployeeObject2] 
                    Now $index gets the key (0,1,...) and $employee gets the value (EmployeeObject1, EmployeeObject2,...)
                    When I write like "$employees as $index => $employee", I can access both the position and the value
                    When I write like "$employees as $employee", I can access only the value
                    -->
                    <?php foreach ($employees as $index => $employee): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td> <!--Index position starts from 0; thats why doing +1-->
                            <td><?php echo $employee->publicId; ?></td>
                            <td><?php echo $employee->name; ?></td>
                            <td><?php echo $employee->phone; ?></td>
                            <td><?php echo $employee->email; ?></td>
                            <!--below code converts database date (yyyy-mm-dd) to Indian format (dd-mm-yyyy)-->
                            <td><?php echo date('d-m-Y', strtotime($employee->dob)); ?></td>
                            <td><?php echo $employee->designationName; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($employee->joiningDate)); ?></td>
                            <td><?php echo isset($employee->yearlyCtc) ? '₹ ' . number_format($employee->yearlyCtc) : 'N/A'; ?></td>
                            <td><?php echo $employee->status; ?></td>
                            <td>
                                <button class="viewEmployeeBtn" data-id="<?php echo $employee->id; ?>">View</button>
                                <button class="ctcEmployeeBtn" data-id="<?php echo $employee->id; ?>">CTC</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table> <!-- End of employee-table -->
            <!------------------- For using JQuery Datatables for automatically pagenating & having real time search -------------->
            <!----- Step 1: Display a normal table using PHP as Above. Datatable converts above table into interactive table ----->
            <!----- Step 2: Loading JQuery Library First -------->
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <!----- Step 3: Loading Datatable Library from CDN ---->
            <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
            <!----- Step 4: Initializing the Datatable ------>
            <script>
                $(document).ready(function() {
                    // Find the table by its ID and call .DataTable()
                    $('#employeeTable').DataTable();
                });
            </script>

            <!--- Note: Apart from above 4 steps, add Datatable stylesheet in <header> section fetching stylesheet from Datatable CDN 
                <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"> 
                and set table's class="display" like above: <table id="employeeTable" class="display"> --->

            <!-- Remember that: The above code follows formar as described in JQuery official docs. This is professional way of writting code.
             Having multiple scripts; each having one specific responsibility/purpose. The datatable's code available in one readable block --->

            <!---------------------------------- JQuery Datatable Specific Functionality Code ends here ---------------------------->
        </div> 
        <!---------------------------------------------- End of employee-table-container ------------------------------------------>

        <!------------------------------------------------ Start of add-employee-modal -------------------------------------------->
        <div class="modal" id="addEmployeeModal">
            <div class="modalContent">
                <span class="closeBtn" id="closeAddEmployeeModal">&times;</span>
                <h2>Add New Employee</h2>
                <form id="addEmployeeForm">
                    <div class="formGroup">
                        <input type="text" name="name" class="formInput" placeholder="" required>
                        <label class="formLabel">Employee Name</label>
                    </div>
                    <div class="formGroup">
                        <select placeholder="" name="gender" class="formInput" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                            <option value="TRANS">Transgender</option>
                            <option value="NTD">Not To Disclose</option>
                        </select>
                        <label class="formLabel">Gender</label>
                    </div>
                    <div class="formGroup">
                        <input type="date" name="dob" class="formInput" placeholder="" required>
                        <label class="formLabel">Date of Birth</label>
                    </div>
                    <div class="formGroup">
                        <input type="tel" name="phone" class="formInput" placeholder="" required>
                        <label class="formLabel">Phone Number</label>
                    </div>
                    <div class="formGroup">
                        <input type="email" name="email" class="formInput" placeholder="" required>
                        <label class="formLabel">Email Address</label>
                    </div>
                    <div class="formGroup">
                        <input type="password" name="password" class="formInput" placeholder="" required>
                        <label class="formLabel">Password</label>
                    </div>
                    <div class="formGroup">
                        <input type="date" name="joiningDate" class="formInput" placeholder="" required>
                        <label class="formLabel">Date of Joining</label>
                    </div>
                    <div class="formGroup">
                        <select placeholder="" name="designationId" class="formInput" required>
                            <option value="" disabled selected>Select Designation</option>
                            <?php foreach ($designations as $designation): ?>
                                <option value="<?php echo $designation->id; ?>"><?php echo $designation->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label class="formLabel">Designation</label>
                    </div>
                    <button type="submit" id="addEmployeeSubmitBtn">Add Employee</button>
                    <div id="addEmployeeFormErrorContainer"></div>
                </form>
            </div> <!-- End of modal-content -->
        </div> 
        <!------------------------------------------------ End of add-employee-modal -------------------------------------------->
        <!--------------------------------------------Employee Details Modal Start ---------------------------------------------->
        <div class="modal" id="viewEmployeeModal">
            <div class="modalContent">
                <span class="closeBtn" id="closeViewEmployeeModal">&times;</span>
                <div class="formHeader">
                <h2>Employee Details</h2>
                <div class="buttonsGroup">
                    <button id="editEmployeeBtn">Edit</button>
                    <div class="switch-container">
                    <label class="switch">
                    <input type="checkbox" id="statusChangeBtn">
                    <span class="slider round"></span>
                    </label><label>Status</label><br>
                    </div>
                </div>
                </div>
                <form id="updateEmployeeForm">
                <div id="employeeDetailsContent">
                    <div class="info-card">
                        <div><strong>Employee ID:</strong> <span id="viewId">-</span></div>
                    </div>
                    
                    <div class="formGroup">
                        <input type="text" name="name" id="viewName" class="formInput" placeholder="" readonly>
                        <label class="formLabel">Employee Name</label>
                    </div>
                    <div class="formGroup">
                        <select id="viewGender" name="gender" class="formInput" placeholder="" readonly>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>   
                            <option value="TRANS">Transgender</option>
                            <option value="NTD">Not To Disclose</option>
                        </select>
                        <label class="formLabel">Gender</label>
                    </div>
                    <div class="formGroup">
                        <input type="date" name="dob" id="viewDob" class="formInput" placeholder="" readonly>
                        <label class="formLabel">Date of Birth</label>
                    </div>
                    <div class="formGroup">
                        <input type="tel" name="phone" id="viewPhone" class="formInput" placeholder="" readonly>
                        <label class="formLabel">Phone Number</label>
                    </div>
                    <div class="formGroup">
                        <input type="email" name="email" id="viewEmail" class="formInput" placeholder="" readonly>
                        <label class="formLabel">Email Address</label>    
                    </div>

                    <div class="formGroup">
                        <input type="date" name="joiningDate" id="viewJoiningDate" class="formInput" placeholder="" readonly>
                        <label class="formLabel">Date of Joining</label>
                    </div>
                    <div class="formGroup">
                        <select id="viewDesignation" name="designationId" class="formInput" placeholder="" readonly>
                            <option value="" disabled selected>Select Designation</option>
                            <?php foreach ($designations as $designation): ?>
                                <option value="<?php echo $designation->id; ?>"><?php echo $designation->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label class="formLabel">Designation</label>
                    </div>
                    <button type="submit" id="updateEmployeeBtn" hidden>Update Changes</button>
                    </form>


                    <div class="info-card">
                        <div><strong>Created At:</strong> <span id="viewCreatedAt">-</span></div>
                        <div><strong>Updated At:</strong> <span id="viewUpdatedAt">-</span></div>
                    </div>
                </div> <!-- End of employeeDetailsContent -->
            </div> <!-- End of modal-content -->
        </div> 
        <!--------------------------------------------Employee Details Modal End ------------------------------------------------>
        
        <!------------------------------------ CTC Form Modal Starts Here -------------------------------------------------->
        <div class="modal" id="ctcModal">
        <div class="modalContent">
            <span class="closeBtn" id="closeCtcModal">&times;</span>
            <h2>CTC Form</h2>
            <form id="ctcForm">
                <!-- ************** For Employee Selection Dropdown with Search ***************** -->
                <div class="formGroup">
                    <input type="text" id="employeeSelectInput" class="formInput" placeholder="" readonly>
                    <label class="formLabel">Select Employee</label>
                    <input type="hidden" id="selectedEmployeeId" name="employeeId" value="">
                    <div id="employeeDropdown" class="dropdown" style="display: none;">
                        <input type="text" id="employeeSearch" class="dropdownSearchInput" placeholder="Search employees..." data-validation>
                        <div id="employeeResults"></div>
                    </div>
                </div>
                <!-- Employee Selection dropdown with search container ends here-->
                
                <div id="ctcEmployeeDetails" style="margin-top: 10px; padding: 10px; display: none;">
                    <div class="info-card">
                        <div><strong>Employee ID:</strong> <span id="ctcEmployeeId">-</span></div>
                        <div><strong>Name:</strong> <span id="ctcEmployeeName">-</span></div>
                        <div><strong>Designation:</strong> <span id="ctcEmployeeDesignation">-</span></div>
                        <div><strong>Joining Date:</strong> <span id="ctcEmployeeJoiningDate">-</span></div>
                        <div><strong>Last Revised CTC Per Month:</strong> <span id="ctcEmployeeCtcPerMonth">-</span></div>
                    </div>
                        <div class="tableContainer">
                            <table class="display" id="employeeCtcRevisionTable">
                                <thead>
                                <tr>
                                    <th>CTC Amount (Rs)</th>
                                    <th>Effective Start Date</th>
                                    <th>Effective End Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <!-- CTC Revision records will be populated here via JavaScript -->
                                    
                                </tbody>
                            </table>
                        </div> <!-- End of tableContainer -->
                    
                    <div id="UpdateLastCtcContainer">
                        <h2>Update Last CTC</h2>
                        <div class="formGroup">
                            <input type="date" id="effectiveStartDate" name="effectiveStartDate" class="formInput" placeholder="">
                            <label class="formLabel">Effective Start Date </label>
                        </div>
                        <div class="formGroup">
                            <input type="number" id="ctcPerYear" name="ctcPerYear" class="formInput" placeholder="">
                            <label class="formLabel">CTC Per Year (₹)</label>
                        </div>
                        <button type="submit" id="updateCtcBtn">Update CTC</button>
                    </div>
                    <hr>
                    <div id="AddCtcRevisionContainer">
                        <h2>Add CTC Revision</h2>
                        <div class="formGroup">
                            <input type="date" id="effectiveStartDateAddForm" name="effectiveStartDate" class="formInput" placeholder="">
                            <label class="formLabel">Effective Start Date </label>
                        </div>
                        <div class="formGroup">
                            <input type="number" id="ctcPerYearAddForm" name="ctcPerYear" class="formInput" placeholder="">
                            <label class="formLabel">CTC Per Year (₹)</label>
                        </div>
                        <div>
                            <button type="submit" id="saveCtcBtn">Save CTC</button>
                        </div>
                    </div>
                </div>

                
            </form>
        </div> <!-- End of modal-content -->
    </div> <!-- End of ctcModal -->
        <!------------------------------------ CTC Form Modal Ends Here -------------------------------------------------->
        
        <!-- Add Success modal after all my existing modals so that it appears on top of all modals -->
        <div class="modal" id="successModal">
            <div class="modalContent" style="width: 400px;">
                <h2>✅ Employee Added Successfully</h2>
                <div id="successDetails">
                    <!-- Details will be populated by JavaScript -->
                </div>
                <button onclick="closeSuccessModal()">OK</button>
            </div>
        </div>

    </div> <!-- End of dashboard-container -->

    <!---------------------------------------Javascript Code Begins Here--------------------------------------------------------->
    <script>
        // ========== MODAL FUNCTIONALITY ==========
        // Open Add Employee Modal
        document.getElementById('addEmployeeBtn').addEventListener('click', function() {
            document.getElementById('addEmployeeModal').style.display = 'flex';
        });

        // Open CTC Form Modal
        document.getElementById('ctcFormBtn').addEventListener('click', function() {
            document.getElementById('ctcModal').style.display = 'flex';
        });

        // Close CTC Form Modal
        document.getElementById('closeCtcModal').addEventListener('click', function() {
            resetEmployeeForm(); // Call the existing reset function
            document.getElementById('ctcModal').style.display = 'none';
        });

        // Close Add Employee Modal
        document.getElementById('closeAddEmployeeModal').addEventListener('click', function() {
            document.getElementById('addEmployeeForm').reset();
            document.getElementById('addEmployeeModal').style.display = 'none';
        });

        // Close View Employee Modal
        document.getElementById('closeViewEmployeeModal').addEventListener('click', function() {
            document.getElementById('updateEmployeeForm').reset();
            document.getElementById('updateEmployeeBtn').hidden = true; // Hide update button on close
            document.getElementById('editEmployeeBtn').style.display = "inline-block"; // Show edit button on close
            document.getElementById('viewEmployeeModal').style.display = 'none';
        });
        // ========== END MODAL FUNCTIONALITY ==========

        // ========== SUCCESS MODAL FUNCTIONS ==========
        function showSuccessModal(employee) {
            const details = `
                <p><strong>Employee ID:</strong> ${employee.id}</p>
                <p><strong>Name:</strong> ${employee.name}</p>
                <p><strong>Created At:</strong> ${employee.createdAt}</p>
            `;
            
            document.getElementById('successDetails').innerHTML = details;
            document.getElementById('successModal').style.display = 'flex';
        }

        function closeSuccessModal() {
            document.getElementById('successModal').style.display = 'none';
            window.location.reload(); // Refresh to show new employee in table
        }
        // ========== END SUCCESS MODAL FUNCTIONS ==========


        // Designation Master Button (if you have functionality for it)
        document.getElementById('designationMasterBtn').addEventListener('click', function() {
            //alert('Designation Master functionality to be implemented');
            window.location.href = '<?php echo base_url("Designations"); ?>';
        });

        /***Below is called event delegation. The javascript code will not break if I remove the html element.
         * It simply checks for conditions; if the element is removed, then the condition will fail silently and the code will not break system.
         * This removes the direct dependency on below code on the existance of html element.
         */
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === "editEmployeeBtn")
            {
                // Step 1: Remove readonly from all input fields
                const inputs = document.querySelectorAll('#updateEmployeeForm input');
                inputs.forEach(input => {input.removeAttribute('readonly');});

                // Step 2: Hide Edit Button
                e.target.style.display = "none";

                // Step 3: Show The Update Changes Button
                const varUpdateBtn = document.getElementById("updateEmployeeBtn");
                if (varUpdateBtn) // This way, The code won't break even when the update button doesn't exist.
                {
                    varUpdateBtn.hidden = false;
                }
                }
        });

        // Add event listener for view buttons
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('viewEmployeeBtn')) {
                const employeeId = e.target.getAttribute('data-id');
                viewEmployeeDetails(employeeId);
            }
        });

        // Add event listener for ctc buttons for each employee row
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('ctcEmployeeBtn')) {
                const employeeId = e.target.getAttribute('data-id');
                populateSelectEmployeeCtcForm(employeeId);
            }
        });

        async function populateSelectEmployeeCtcForm(employeeId) {
        try {
            const response = await fetch(`Employees/getEmployeeById/${employeeId}`);
            const employee = await response.json();
            
            selectEmployee(employee.id, employee.name, employee.designationName, employee.joiningDate);
            
            
            // Show modal
            document.getElementById('ctcModal').style.display = 'flex';
            
        } catch (error) {
            console.error('Error fetching employee details:', error);
        }
        }



        // Add event listener for status change
        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'statusChangeBtn') {
                setEmployeeStatus(e.target);
            }
        });

        document.addEventListener('submit', async function(e) {
            if (e.target && e.target.id === 'addEmployeeForm') {
                e.preventDefault();
                const formData = new FormData(e.target);

                try {
                    const response = await fetch(`Employees/addEmployee`, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();
                    const errorContainer = document.getElementById('addEmployeeFormErrorContainer');
                    // Remove existing error
                    errorContainer.innerHTML = '';

                    if (!result.success) {
                        errorContainer.innerHTML = `<div class="error-message-onsubmit" style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">${cleanMessage}</div>`;
                    } else {
                        // Success case - close modal and reload page
                        /*const modal = document.getElementById('addEmployeeModal');
                        modal.style.display = 'none';
                        
                        // Reload the page to refresh the table
                        window.location.reload();*/

                        // Close the add employee modal
                        document.getElementById('addEmployeeModal').style.display = 'none';
                        
                        // Clear the form
                        e.target.reset();
                        
                        // Show success modal with details
                        showSuccessModal(result.employee);
                    }
                } catch (error) {
                    alert('Request failed: ' + error.message);
                }
            }
        });

        async function viewEmployeeDetails(employeeId) {
        try {
            const response = await fetch(`Employees/getEmployeeById/${employeeId}`);
            const employee = await response.json();
            
            // Populate modal fields
            document.getElementById('viewId').textContent = employee.id;
            document.getElementById('viewName').value = employee.name;
            document.getElementById('viewEmail').value = employee.email;
            document.getElementById('viewPhone').value = employee.phone;
            document.getElementById('viewDob').value = employee.dob;
            document.getElementById('viewJoiningDate').value = employee.joiningDate;
            
            // Set gender
            document.getElementById('viewGender').value = employee.gender;
            
            // Set designation
            document.getElementById('viewDesignation').value = employee.designationId;
            
            // Set status checkbox
            document.getElementById('statusChangeBtn').checked = employee.status === 'active';
            
            // Show timestamps
            document.getElementById('viewCreatedAt').textContent = employee.createdAt;
            document.getElementById('viewUpdatedAt').textContent = employee.updatedAt;
            
            // Show modal
            document.getElementById('viewEmployeeModal').style.display = 'flex';
            
        } catch (error) {
            console.error('Error fetching employee details:', error);
        }
        }

        document.addEventListener('submit', async function(e) {
            if (e.target && e.target.id === 'updateEmployeeForm') {
                e.preventDefault();
                const formData = new FormData(e.target);
                formData.append('employeeId', document.getElementById('viewId').textContent);

                try {
                    const response = await fetch('Employees/updateEmployee', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();

                    if (!result.success) {
                        alert('Update failed: ' + cleanMessage);
                    } else {
                        // Success case - close modal and reload page
                        const modal = document.getElementById('viewEmployeeModal');
                        modal.style.display = 'none';
                        
                        // Reload the page to refresh the table
                        window.location.reload();
                    }
                } catch (error) {
                    alert('Request failed: ' + error.message);
                }
            }
        });

            async function setEmployeeStatus(checkbox) {
            const employeeId = document.getElementById('viewId').textContent;
            const status = checkbox.checked ? 'active' : 'inactive';
            
            try {
                const formData = new FormData();
                formData.append('employeeId', employeeId);
                formData.append('status', status);
                
                const response = await fetch('Employees/setEmployeeStatus', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error('Failed to update status');
                }
                else{
                    window.location.reload();
                }
                
            } catch (error) {
                console.error('Error:', error);
                // Revert on error
                checkbox.checked = !checkbox.checked;
                alert('Error updating employee status');
            }
        }

        /*************************** Employee Dropdown with Search **************************** */
    // Employee dropdown functions
    let allEmployees = [];

    // Load employees on focus
    document.getElementById('employeeSelectInput').addEventListener('click', async function() {
        if (allEmployees.length === 0) {
            try {
                const response = await fetch('http://localhost/ManageStudents/Employees/getAllEmployeesForDropdown');
                allEmployees = await response.json();
            } catch (error) {
                console.error('Error loading employees:', error);
                allEmployees = [];
            }
        }
        document.getElementById('employeeDropdown').style.display = 'block';
        filterEmployees();
    });

    // Filter employees on typing
    document.getElementById('employeeSearch').addEventListener('input', filterEmployees);

    function filterEmployees() {
        const search = document.getElementById('employeeSearch').value.toLowerCase();
        const filtered = allEmployees.filter(employee => 
            employee.name.toLowerCase().includes(search) || 
            employee.id.toLowerCase().includes(search)
        );
        
        const results = document.getElementById('employeeResults');
        if (filtered.length) {
            results.innerHTML = filtered.map(employee => 
                `<div class="dropdownItem employeeItem" data-id="${employee.id}" data-name="${employee.name}" data-designation="${employee.designationName || employee.designation}" data-joiningdate="${employee.joiningDate}">
                    ${employee.name} (${employee.id})
                </div>`
            ).join('');
            
            // Add event listeners
            results.querySelectorAll('.dropdownItem.employeeItem').forEach(item => {
                item.addEventListener('click', function() {
                    selectEmployee(
                        this.dataset.id,
                        this.dataset.name,
                        this.dataset.designation,
                        this.dataset.joiningdate
                    );
                });
            });
        } else {
            results.innerHTML = '<div style="padding: 10px; color: #666;">No employees found</div>';
        }
    }

    function selectEmployee(employeeId, employeeName, designation, joiningDate) {
        // Update the input field
        document.getElementById('employeeSelectInput').value = `${employeeName} (${employeeId})`;
        document.getElementById('selectedEmployeeId').value = employeeId;
        
        // Show and update employee details
        const detailsContent = document.getElementById('ctcEmployeeDetails');
        detailsContent.style.display = 'block';
        
        // Format joining date if needed
        const formattedJoiningDate = joiningDate ? formatDate(joiningDate) : '-';
        
        // Update details
        document.getElementById('ctcEmployeeId').textContent = employeeId || '-';
        document.getElementById('ctcEmployeeName').textContent = employeeName || '-';
        document.getElementById('ctcEmployeeDesignation').textContent = designation || '-';
        document.getElementById('ctcEmployeeJoiningDate').textContent = formattedJoiningDate;
        
        // Hide dropdown and clear search
        document.getElementById('employeeDropdown').style.display = 'none';
        document.getElementById('employeeSearch').value = '';

        // Reset CTC Form fields
        document.getElementById('effectiveStartDate').value = '';
        document.getElementById('ctcPerYear').value = '';
        document.getElementById('effectiveStartDateAddForm').value = '';
        document.getElementById('ctcPerYearAddForm').value = '';
        // Check if CTC record exists for selected employee
        checkCtcRecordExists(employeeId);
        loadCtcRevisions(employeeId);
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } catch (e) {
            return dateString;
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const employeeDropdown = document.getElementById('employeeDropdown');
        const employeeInput = document.getElementById('employeeSelectInput');
        
        if (!e.target.closest('#employeeDropdown') && e.target !== employeeInput) {
            employeeDropdown.style.display = 'none';
        }
    });

    // Clear form when modal closes
    document.getElementById('closeCtcModal').addEventListener('click', function() {
        resetEmployeeForm();
    });

    // Function to reset the employee form
    function resetEmployeeForm() {
        document.getElementById('employeeSelectInput').value = '';
        document.getElementById('selectedEmployeeId').value = '';
        document.getElementById('employeeSearch').value = '';
        document.getElementById('employeeResults').innerHTML = '';
        document.getElementById('employeeDropdown').style.display = 'none';
        
        // Hide and reset employee details
        const detailsContent = document.getElementById('ctcEmployeeDetails');
        detailsContent.style.display = 'none';
        
        document.getElementById('ctcEmployeeId').textContent = '-';
        document.getElementById('ctcEmployeeName').textContent = '-';
        document.getElementById('ctcEmployeeDesignation').textContent = '-';
        document.getElementById('ctcEmployeeJoiningDate').textContent = '-';
        
        // Reset allEmployees array if needed
        allEmployees = [];
    }

    /****************************** Code ends here for employee dropdown with search ******************* */

    /****************************** Check Existing CTC and Handle Form State ******************* */
    async function checkCtcRecordExists(employeeId)
    {
        try {
            
            const response = await fetch(`Employees/checkCtcRecordExists/${employeeId}`);
            const employeeCtcDetails = await response.json();

            //const saveBtn = document.querySelector('#saveCtcBtn');
            //const updateBtn = document.querySelector('#updateCtcBtn');

            if (employeeCtcDetails.exists)
            {
                //if (saveBtn) saveBtn.hidden = employeeCtcDetails.exists; // Hide Save button if record exists + the button exists
                //if (updateBtn) updateBtn.hidden = !employeeCtcDetails.exists; // Show Update button if record exists + the button exists (done to keep code independent of button existance)

                // Populate the fields
                document.getElementById('effectiveStartDate').value = employeeCtcDetails.effectiveStartDate;
                document.getElementById('ctcPerYear').value = employeeCtcDetails.ctcPerYear;

                document.getElementById('ctcEmployeeCtcPerMonth').textContent = (employeeCtcDetails.ctcPerYear / 12).toFixed(2);
            
                // Store the CTC ID for update
                document.getElementById('selectedEmployeeId').dataset.ctcId = employeeCtcDetails.id;
                /****** dataset property allows us to set and access the value of custom data attribute (data-*) present in html elements.
                 * When I set element.dataset.ctcId = '123', it creates or updates a data-ctc-id attribute on the element (note the conversion from camelCase to kebab-case).
                 * This way, I can store additional information directly within the HTML element without needing extra hidden inputs.
                 * document.getElementById('selectedEmployeeId').dataset.ctcId = employeeCtcDetails.id; stores the CTC record's ID on the hidden input element for later retrieval during update operations.
                 * The conversion happens because HTML attributes use kebab-case (hyphens) by convention (data-ctc-id), while JavaScript uses camelCase (ctcId) for property names. The dataset property acts as a bridge between these two conventions.
                 * When I write in javascript: element.dataset.ctcId = "123";
                 * It creates in html: <input data-ctc-id="123">
                */
            }
            else
            {
                //if (saveBtn) saveBtn.hidden = employeeCtcDetails.exists; // Show Save button if record does not exist + the button exists
                //if (updateBtn) updateBtn.hidden = !employeeCtcDetails.exists; // Hide Update button if record does not exist + the button exists
                // Clear fields
                document.getElementById('effectiveStartDate').value = '';
                document.getElementById('ctcPerYear').value = '';

                document.getElementById('ctcEmployeeCtcPerMonth').textContent = '';
                // Remove stored CTC ID
                delete document.getElementById('selectedEmployeeId').dataset.ctcId;
            }
        } catch (error) {
            console.error('Error checking CTC record:', error);
        }
    }

    document.addEventListener('click', async function(e) {
    if (e.target && e.target.id=== 'saveCtcBtn') {
        e.preventDefault();
        const employeeId = document.getElementById('selectedEmployeeId')?.value;
        const effectiveStartDate = document.getElementById('effectiveStartDateAddForm')?.value;
        var ctcPerYear = document.getElementById('ctcPerYearAddForm')?.value;
        if (ctcPerYear && ctcPerYear.includes('-')) {
            alert('CTC cannot be negative');
        }
        ctcPerYear = Math.abs(parseInt(ctcPerYear || 0));
        // Prepare form data
        const formData = new FormData();
        formData.append('employeeId', employeeId);
        formData.append('effectiveStartDate', effectiveStartDate);
        formData.append('yearlyCtc', ctcPerYear);
        // Determine API endpoint based on which button was clicked
        const url = 'Employees/addCtc';

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                // Refresh CTC check after successful operation
                checkCtcRecordExists(employeeId);
                loadCtcRevisions(employeeId);
            } else {
                alert('Error: ' + result.message);
            }
            
        } catch (error) {
            alert('Request failed: ' + error.message);
        }
    }
    
    else if (e.target && e.target.id === 'updateCtcBtn') {
        e.preventDefault();
        const employeeId = document.getElementById('selectedEmployeeId')?.value;
        const effectiveStartDate = document.getElementById('effectiveStartDate')?.value;
        const ctcPerYear = document.getElementById('ctcPerYear')?.value;
        if (ctcPerYear && ctcPerYear.includes('-')) {
            alert('CTC cannot be negative');
            // Optional: clear or reset input field here
        }
        const ctc = Math.abs(parseInt(ctcPerYear || 0));
        const ctcId = document.getElementById('selectedEmployeeId')?.dataset.ctcId;

        // Prepare form data
        const formData = new FormData();
        formData.append('employeeId', employeeId);
        formData.append('effectiveStartDate', effectiveStartDate);
        formData.append('yearlyCtc', ctc);
        formData.append('ctcId', ctcId);
        

        // Determine API endpoint based on which button was clicked
        const url = 'Employees/updateCtc';

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(result.message);
                // Refresh CTC check after successful operation
                checkCtcRecordExists(employeeId);
                loadCtcRevisions(employeeId);
            } else {
                alert('Error: ' + result.message);
            }
            
        } catch (error) {
            alert('Request failed: ' + error.message);
        }
    }
    });
    /**** ******** Check Existing CTC Logic ends here ************************************** */

    /************* loading data for CTC records table for individual employees */
    async function loadCtcRevisions(employeeId) {
        try {
            const response = await fetch(`Employees/getCtcRevisionsForEmployee/${employeeId}`);
            const data = await response.json();

            const tbody = document.querySelector("#employeeCtcRevisionTable tbody");
            tbody.innerHTML = ""; // Clear existing rows

            if (data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="3">No records found</td></tr>`;
                return;
            }

            data.forEach(item => {
                const tr = document.createElement("tr");
                if (item.effectiveEndDate === null) {
                    item.effectiveEndDate = "Present";
                }
                else{
                    item.effectiveEndDate = formatDate(item.effectiveEndDate);
                }
                tr.innerHTML = `
                    <td>${item.yearlyCtc}</td>
                    <td>${formatDate(item.effectiveStartDate)}</td>
                    <td>${item.effectiveEndDate}</td>
                `;

                tbody.appendChild(tr);
            });

        } catch (error) {
            console.error("Error loading CTC revisions:", error);
        }
    }
       /*** loading data for ctc table for individual ends here ******** */ 
    </script>
    <!-------------------------------------- Javascript Code Ends Here ---------------------------------------------------------->
    </body>
</html>

<!-- designations table structure -->
<!--CREATE TABLE designations(
    id int(11) primary key AUTO_INCREMENT, 
	name varchar(50) UNIQUE NOT NULL,
	status enum('active', 'inactive') DEFAULT 'active',
	createdAt timestamp default CURRENT_TIMESTAMP,
	updatedAt timestamp ON UPDATE CURRENT_TIMESTAMP); -->

    <!-- Insert Initial Table Data -->
     <!-- INSERT INTO designations (name) VALUES ('Principal'), ('Vice Principal'), ('Class Teacher'), ('Teacher'), ('Librarian'); -->

     <!-- Employee Table Structure -->
      <!--
     CREATE TABLE employees (
    id int(11) primary key auto_increment,
    name varchar(50) not null,
    gender enum('M', 'F', 'TRANS', 'NTD') not null,
    dob date not null,
    phone int(10) unique not null,
    email varchar(50) unique not null,
    joiningDate date not null,
    designationId int(11) not null,
    status enum('active', 'inactive') DEFAULT 'active',
    createdAt timestamp default CURRENT_TIMESTAMP,
    updatedAt timestamp on update CURRENT_TIMESTAMP,
	CONSTRAINT fk_employees_designationId FOREIGN KEY (designationId) REFERENCES designations(id)); -->

    <!-- ALTER TABLE employees 
ADD COLUMN publicId VARCHAR(20) UNIQUE AFTER id; -->

<!-- CTC Table Structure -->
 <!-- CREATE TABLE employeeCtc (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    employeeId INT(11) NOT NULL,
    effectiveStartDate DATE NOT NULL,
    yearlyCtc DECIMAL(10,2) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_employeeCtc_employeeId 
        FOREIGN KEY (employeeId) 
        REFERENCES employees(id),
    -- Unique constraint to prevent duplicate effective dates for same employee
    CONSTRAINT unique_employee_effective_date 
        UNIQUE KEY (employeeId, effectiveStartDate)
);-->

