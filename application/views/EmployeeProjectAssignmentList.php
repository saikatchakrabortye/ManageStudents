<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Employee Project Assignment</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
        <div class="dashboardContainer">
            <div class="dashboardHeader">
                <h1>EMPLOYEE PROJECT ASSIGNMENTS</h1>
            </div>

            <!-- Employee Selection Section - Only show for admin users -->
            <?php if($this->session->userdata('designationId') == 10): ?>
            <div class="formGroup" style="margin: 20px;">
                <input type="text" id="employeeSelectInput" class="formInput" placeholder="" readonly>
                <label class="formLabel">Select Employee</label>
                <input type="hidden" id="selectedEmployeeId" name="employeeId" value="">
                <div id="employeeDropdown" class="dropdown" style="display: none;">
                    <input type="text" id="employeeSearch" class="dropdownSearchInput" placeholder="Search employees...">
                    <div id="employeeResults"></div>
                </div>
            </div>
            <?php else: ?>
            <!-- Hidden inputs for non-admin users with session data -->
            <input type="hidden" id="selectedEmployeeId" name="employeeId" value="<?php echo $this->session->userdata('employeePublicId'); ?>">
            <input type="hidden" id="employeeName" value="<?php echo htmlspecialchars($this->session->userdata('name')); ?>">
            <input type="hidden" id="employeeDesignation" value="<?php echo htmlspecialchars($this->session->userdata('designationId')); ?>">
            <?php endif; ?>

            <div class="afterSelectionArea" style="display: none; margin: 20px;">
                <!-- Table showing current project assignments for the employee -->
                <h2>Current Project Assignments</h2>
                <table id="assignmentTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl. No.</th>
                            <th>Project ID</th>
                            <th>Project Name</th>
                            <th>Client Name</th>
                            <th>Assigned From</th>
                            <?php if($this->session->userdata('designationId') == 10): ?>
                            <th>Currently Working ?</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="assignmentTableBody">
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>
                <?php if($this->session->userdata('designationId') == 10): ?>
                <!-- Form to assign employee to a new project -->
                <h2 style="margin-top: 30px;">Assign To New Project</h2>
                <form id="addProjectForm">
                    <input type="hidden" id="currentEmployeeId" name="employeeId" value="">
                    
                    <div class="formGroup">
                        <input type="text" id="projectSelectInput" class="formInput" placeholder="" readonly>
                        <label class="formLabel">Select Project</label>
                        <input type="hidden" id="selectedProjectId" name="projectId" value="">
                        <div id="projectDropdown" class="dropdown" style="display: none;">
                            <input type="text" id="projectSearch" class="dropdownSearchInput" placeholder="Search projects...">
                            <div id="projectResults"></div>
                        </div>
                    </div>

                    <div class="formGroup">
                        <input type="date" name="assignFrom" id="assignFrom" class="formInput" placeholder="" required>
                        <label class="formLabel">Assign From</label>
                    </div>

                    <button type="submit" class="submitButton">Assign Project</button>
                </form>
                <?php endif; ?>
            </div> <!-- afterSelectionArea Ends -->

        </div> <!-- dashboardContainer Ends -->

        <!-- Success Modal -->
        <div class="modal" id="successModal">
            <div class="modalContent" style="width: 400px;">
                <h2>Success</h2>
                <div id="successDetails"></div>
                <button onClick="closeSuccessModal()">Ok</button>
            </div>
        </div>

        <!-- Error Modal -->
        <div class="modal" id="errorModal">
            <div class="modalContent" style="width: 300px;">
                <h2>Error</h2>
                <div id="errorDetails"></div>
                <button onClick="closeErrorModal()">Ok</button>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
        <script>
            // Global variables
            let allEmployees = [];
            let allProjects = [];
            let currentEmployeeAssignments = [];
            let isAdmin = <?php echo ($this->session->userdata('designationId') == 10) ? 'true' : 'false'; ?>;
            
            // Session data for non-admin users
            let sessionEmployeeId = '<?php echo $this->session->userdata("employeePublicId"); ?>';
            let sessionEmployeeName = '<?php echo htmlspecialchars($this->session->userdata("name"), ENT_QUOTES); ?>';
            let sessionEmployeeDesignation = '<?php echo $this->session->userdata("designationId"); ?>';

            // ========== INITIALIZATION ==========
            document.addEventListener('DOMContentLoaded', function() {
                if (isAdmin) {
                    // Admin: Initialize employee dropdown
                    initEmployeeDropdown();
                } else {
                    // Non-admin: Auto-select the logged-in employee
                    autoSelectEmployee();
                }
            });

            // ========== NON-ADMIN: AUTO-SELECT EMPLOYEE ==========
            async function autoSelectEmployee() {
                if (sessionEmployeeId && sessionEmployeeName) {
                    // Show the after selection area immediately for non-admin
                    document.querySelector('.afterSelectionArea').style.display = 'block';
                    
                    // Load current project assignments for this employee
                    await loadEmployeeAssignments(sessionEmployeeId);
                    
                    // Optional: Show a message indicating whose assignments are being viewed
                    const dashboardHeader = document.querySelector('.dashboardHeader h1');
                    if (dashboardHeader) {
                        dashboardHeader.textContent = 'MY PROJECT ASSIGNMENTS';
                    }
                } else {
                    showErrorModal('Unable to load your employee information. Please contact administrator.');
                }
            }

            // ========== ADMIN: EMPLOYEE DROPDOWN FUNCTIONS ==========
            function initEmployeeDropdown() {
                const employeeSelectInput = document.getElementById('employeeSelectInput');
                if (employeeSelectInput) {
                    employeeSelectInput.addEventListener('click', async function() {
                        if (allEmployees.length === 0) {
                            try {
                                const response = await fetch('EmployeeProjectAssignment/getAllEmployeesForDropdown');
                                const result = await response.json();
                                if (result.success) {
                                    allEmployees = result.employees || [];
                                } else {
                                    console.error('Failed to load employees');
                                    allEmployees = [];
                                }
                            } catch (error) {
                                console.error('Error loading employees:', error);
                                allEmployees = [];
                            }
                        }
                        document.getElementById('employeeDropdown').style.display = 'block';
                        filterEmployees();
                    });

                    document.getElementById('employeeSearch').addEventListener('input', filterEmployees);

                    // Close dropdown when clicking outside
                    document.addEventListener('click', (e) => {
                        const employeeDropdown = document.getElementById('employeeDropdown');
                        if (!e.target.closest('#employeeDropdown') && e.target !== employeeSelectInput) {
                            employeeDropdown.style.display = 'none';
                        }
                    });
                }
            }

            function filterEmployees() {
                const search = document.getElementById('employeeSearch').value.toLowerCase();
                const filtered = allEmployees.filter(employee => 
                    (employee.name && employee.name.toLowerCase().includes(search)) || 
                    (employee.publicId && employee.publicId.toLowerCase().includes(search)) ||
                    (employee.designationName && employee.designationName.toLowerCase().includes(search))
                );
                
                const results = document.getElementById('employeeResults');
                if (filtered.length) {
                    results.innerHTML = filtered.map(employee => 
                        `<div class="dropdownItem employeeItem" data-id="${employee.publicId}" 
                              data-name="${employee.name}" data-designation="${employee.designationName || ''}">
                            ${employee.name} (${employee.publicId}) - ${employee.designationName || 'No Designation'}
                        </div>`
                    ).join('');
                    
                    // Add event listeners
                    results.querySelectorAll('.dropdownItem.employeeItem').forEach(item => {
                        item.addEventListener('click', function() {
                            selectEmployee(
                                this.dataset.id,
                                this.dataset.name,
                                this.dataset.designation
                            );
                        });
                    });
                } else {
                    results.innerHTML = '<div style="padding: 10px; color: #666;">No employees found</div>';
                }
            }

            async function selectEmployee(employeePublicId, employeeName, designationName) {
                // Update the input field
                document.getElementById('employeeSelectInput').value = `${employeeName} (${employeePublicId}) - ${designationName}`;
                document.getElementById('selectedEmployeeId').value = employeePublicId;
                
                // Only set currentEmployeeId if admin element exists
                const currentEmployeeIdElement = document.getElementById('currentEmployeeId');
                if (currentEmployeeIdElement) {
                    currentEmployeeIdElement.value = employeePublicId;
                }
                
                // Hide dropdown and clear search
                document.getElementById('employeeDropdown').style.display = 'none';
                document.getElementById('employeeSearch').value = '';
                document.getElementById('employeeResults').innerHTML = '';
                
                // Show the after selection area
                document.querySelector('.afterSelectionArea').style.display = 'block';
                
                // Load current project assignments for this employee
                await loadEmployeeAssignments(employeePublicId);
            }

            // ========== PROJECT DROPDOWN FUNCTIONS ==========
            // Only initialize project dropdown if admin
            if (isAdmin) {
                document.getElementById('projectSelectInput').addEventListener('click', async function() {
                    if (allProjects.length === 0) {
                        try {
                            const response = await fetch('EmployeeProjectAssignment/getAllProjectsForDropdown');
                            const result = await response.json();
                            if (result.success) {
                                allProjects = result.projects || [];
                            } else {
                                console.error('Failed to load projects');
                                allProjects = [];
                            }
                        } catch (error) {
                            console.error('Error loading projects:', error);
                            allProjects = [];
                        }
                    }
                    document.getElementById('projectDropdown').style.display = 'block';
                    filterProjects();
                });

                document.getElementById('projectSearch').addEventListener('input', filterProjects);

                function filterProjects() {
                    const search = document.getElementById('projectSearch').value.toLowerCase();
                    const filtered = allProjects.filter(project => 
                        (project.name && project.name.toLowerCase().includes(search)) || 
                        (project.publicId && project.publicId.toLowerCase().includes(search)) ||
                        (project.clientName && project.clientName.toLowerCase().includes(search))
                    );
                    
                    const results = document.getElementById('projectResults');
                    if (filtered.length) {
                        results.innerHTML = filtered.map(project => 
                            `<div class="dropdownItem projectItem" data-id="${project.publicId}" 
                                  data-name="${project.name}" data-client="${project.clientName || ''}">
                                ${project.name} (${project.publicId}) - ${project.clientName || 'No Client'}
                            </div>`
                        ).join('');
                        
                        // Add event listeners
                        results.querySelectorAll('.dropdownItem.projectItem').forEach(item => {
                            item.addEventListener('click', function() {
                                selectProject(
                                    this.dataset.id,
                                    this.dataset.name,
                                    this.dataset.client
                                );
                            });
                        });
                    } else {
                        results.innerHTML = '<div style="padding: 10px; color: #666;">No projects found</div>';
                    }
                }

                function selectProject(projectPublicId, projectName, clientName) {
                    // Update the input field
                    document.getElementById('projectSelectInput').value = `${projectName} (${projectPublicId}) - ${clientName}`;
                    document.getElementById('selectedProjectId').value = projectPublicId;
                    
                    // Hide dropdown and clear search
                    document.getElementById('projectDropdown').style.display = 'none';
                    document.getElementById('projectSearch').value = '';
                    document.getElementById('projectResults').innerHTML = '';
                }

                // ========== ADD PROJECT FORM SUBMISSION ==========
                document.getElementById('addProjectForm').addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const employeeId = document.getElementById('selectedEmployeeId').value;
                    const projectId = document.getElementById('selectedProjectId').value;
                    
                    if (!employeeId || !projectId) {
                        showErrorModal('Please select both employee and project');
                        return;
                    }
                    
                    try {
                        const response = await fetch('EmployeeProjectAssignment/assignProjectToEmployee', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();
                        
                        if (!result.success) {
                            showErrorModal(cleanMessage);
                        } else {
                            // Clear form
                            this.reset();
                            document.getElementById('projectSelectInput').value = '';
                            document.getElementById('selectedProjectId').value = '';
                            
                            // Reload assignments
                            await loadEmployeeAssignments(employeeId);
                            
                            // Show success
                            showSuccessModal({message: 'Project assigned successfully!'}, 'addAssignment');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showErrorModal('Request failed: ' + error.message);
                    }
                });
            }

            // ========== LOAD EMPLOYEE ASSIGNMENTS ==========
            async function loadEmployeeAssignments(employeePublicId) {
                try {
                    const formData = new FormData();
                    formData.append('employeePublicId', employeePublicId);
                    
                    const response = await fetch('EmployeeProjectAssignment/getEmployeeAssignments', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        currentEmployeeAssignments = result.assignments || [];
                        populateAssignmentTable();
                    } else {
                        showErrorModal('Failed to load employee assignments: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error loading assignments:', error);
                    showErrorModal('Error loading employee assignments');
                }
            }

            function populateAssignmentTable() {
                const tableBody = document.getElementById('assignmentTableBody');
                tableBody.innerHTML = '';
                
                // DESTROY DataTable FIRST if it exists
                if ($.fn.DataTable.isDataTable('#assignmentTable')) {
                    $('#assignmentTable').DataTable().destroy();
                    $('#assignmentTable').find('tbody').empty();
                }
                
                if (currentEmployeeAssignments.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="${isAdmin ? '6' : '5'}" style="text-align: center; padding: 20px;">
                                ${isAdmin ? 'Employee is not assigned to any projects yet.' : 'You are not assigned to any projects yet.'}
                            </td>
                        </tr>
                    `;
                    // Still initialize DataTable even when empty
                    $('#assignmentTable').DataTable({
                        searching: false,
                        paging: false,
                        info: false
                    });
                    return;
                }
                
                currentEmployeeAssignments.forEach((assignment, index) => {
                    const row = document.createElement('tr');
                    if (isAdmin) {
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${assignment.projectPublicId}</td>
                            <td>${assignment.projectName}</td>
                            <td>${assignment.clientName || 'N/A'}</td>
                            <td>${assignment.assignedFrom}</td>
                            <td>
                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" class="assignmentStatusBtn" 
                                               data-assignment-id="${assignment.id}"
                                               ${assignment.status === 'active' ? 'checked' : ''}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </td>
                        `;
                    } else {
                        // Non-admin view - no toggle switch
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${assignment.projectPublicId}</td>
                            <td>${assignment.projectName}</td>
                            <td>${assignment.clientName || 'N/A'}</td>
                            <td>${assignment.assignedFrom}</td>
                        `;
                    }
                    tableBody.appendChild(row);
                });
                
                // Initialize DataTable with options
                $('#assignmentTable').DataTable({
                    searching: true,
                    paging: true,
                    ordering: true,
                    info: true,
                    columnDefs: isAdmin ? [
                        { orderable: false, targets: [5] } // Make status column non-orderable for admin
                    ] : []
                });
                
                // Add event listeners for toggle switches (admin only)
                if (isAdmin) {
                    document.querySelectorAll('.assignmentStatusBtn').forEach(button => {
                        button.addEventListener('change', handleStatusToggle);
                    });
                }
            }

            // ========== TOGGLE ASSIGNMENT STATUS ==========
            async function handleStatusToggle(e) {
                const assignmentId = e.target.getAttribute('data-assignment-id');
                const status = e.target.checked ? 'active' : 'inactive';
                const employeeId = document.getElementById('selectedEmployeeId').value;
                
                try {
                    const formData = new FormData();
                    formData.append('assignmentId', assignmentId);
                    formData.append('status', status);
                    
                    const response = await fetch('EmployeeProjectAssignment/toggleAssignmentStatus', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (!result.success) {
                        // Revert on error
                        e.target.checked = !e.target.checked;
                        showErrorModal('Failed to update status');
                    } else {
                        // Reload assignments to reflect the change
                        await loadEmployeeAssignments(employeeId);
                        
                        const message = status === 'active' 
                            ? 'Employee reactivated for this project' 
                            : 'Employee deactivated from this project';
                        showSuccessModal({message: message}, 'statusChange');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    e.target.checked = !e.target.checked;
                    showErrorModal('Error updating status');
                }
            }

            // ========== MODAL FUNCTIONS ==========
            function showSuccessModal(object, objectName) {
                let details = '';
                if (objectName === 'addAssignment' || objectName === 'statusChange') {
                    details = `<p>${object.message}</p>`;
                }
                document.getElementById('successDetails').innerHTML = details;
                document.getElementById('successModal').style.display = 'flex';
            }

            function closeSuccessModal() {
                document.getElementById('successModal').style.display = 'none';
            }

            function showErrorModal(errorMessage) {
                document.getElementById('errorDetails').innerHTML = `<p>${errorMessage}</p>`;
                document.getElementById('errorModal').style.display = 'flex';
            }

            function closeErrorModal() {
                document.getElementById('errorModal').style.display = 'none';
            }

            // ========== CLOSE DROPDOWNS WHEN CLICKING OUTSIDE ==========
            if (isAdmin) {
                document.addEventListener('click', (e) => {
                    // Project dropdown - only if admin
                    const projectDropdown = document.getElementById('projectDropdown');
                    const projectInput = document.getElementById('projectSelectInput');
                    if (!e.target.closest('#projectDropdown') && e.target !== projectInput) {
                        projectDropdown.style.display = 'none';
                    }
                });
            }
        </script>
    </body>
</html>