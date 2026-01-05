<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Project Assignment List</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
        <div class="dashboardContainer">
            <div class="dashboardHeader">
                <h1>PROJECT ASSIGNMENT</h1>
            </div>

            <!-- Project Selection Section -->
            <div class="formGroup" style="margin: 20px;">
                <input type="text" id="projectSelectInput" class="formInput" placeholder="" readonly>
                <label class="formLabel">Select Project</label>
                <input type="hidden" id="selectedProjectId" name="projectId" value="">
                <div id="projectDropdown" class="dropdown" style="display: none;">
                    <input type="text" id="projectSearch" class="dropdownSearchInput" placeholder="Search projects...">
                    <div id="projectResults"></div>
                </div>
            </div>

            <div class="afterSelectionArea" style="display: none; margin: 20px;">
                <!-- Table showing current assignments -->
                <h2>Current Team Members</h2>
                <table id="assignmentTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Sl. No.</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Designation</th>
                            <th>Assigned From</th>
                            <th>Currently Working ?</th>
                            <!--<th>Actions</th>-->
                        </tr>
                    </thead>
                    <tbody id="assignmentTableBody">
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>

                <!-- Form to add new member -->
                <h2 style="margin-top: 30px;">Assign New Member To This Project</h2>
                <form id="addMemberForm">
                    <input type="hidden" id="currentProjectId" name="projectId" value="">
                    
                    <div class="formGroup">
                        <input type="text" id="employeeSelectInput" class="formInput" placeholder="" readonly>
                        <label class="formLabel">Select Employee</label>
                        <input type="hidden" id="selectedEmployeeId" name="employeeId" value="">
                        <div id="employeeDropdown" class="dropdown" style="display: none;">
                            <input type="text" id="employeeSearch" class="dropdownSearchInput" placeholder="Search employees...">
                            <div id="employeeResults"></div>
                        </div>
                    </div>

                    <div class="formGroup">
                        <input type="date" name="assignFrom" id="assignFrom" class="formInput" placeholder="" required>
                        <label class="formLabel">Assign From</label>
                    </div>

                    <button type="submit" class="submitButton">Add Member</button>
                </form>
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
            let allProjects = [];
            let allEmployees = [];
            let currentProjectAssignments = [];

            // ========== PROJECT DROPDOWN FUNCTIONS ==========
            document.getElementById('projectSelectInput').addEventListener('click', async function() {
                if (allProjects.length === 0) {
                    try {
                        const response = await fetch('ProjectAssignment/getAllProjectsForDropdown');
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

            async function selectProject(projectPublicId, projectName, clientName) {
                // Update the input field
                document.getElementById('projectSelectInput').value = `${projectName} (${projectPublicId}) - ${clientName}`;
                document.getElementById('selectedProjectId').value = projectPublicId;
                document.getElementById('currentProjectId').value = projectPublicId;
                
                // Hide dropdown and clear search
                document.getElementById('projectDropdown').style.display = 'none';
                document.getElementById('projectSearch').value = '';
                document.getElementById('projectResults').innerHTML = '';
                
                // Show the after selection area
                document.querySelector('.afterSelectionArea').style.display = 'block';
                
                // Load current assignments for this project
                await loadProjectAssignments(projectPublicId);
            }

            // ========== EMPLOYEE DROPDOWN FUNCTIONS ==========
            document.getElementById('employeeSelectInput').addEventListener('click', async function() {
                if (allEmployees.length === 0) {
                    try {
                        const response = await fetch('ProjectAssignment/getAllEmployeesForDropdown');
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

            function selectEmployee(employeePublicId, employeeName, designationName) {
                // Update the input field
                document.getElementById('employeeSelectInput').value = `${employeeName} (${employeePublicId}) - ${designationName}`;
                document.getElementById('selectedEmployeeId').value = employeePublicId;
                
                // Hide dropdown and clear search
                document.getElementById('employeeDropdown').style.display = 'none';
                document.getElementById('employeeSearch').value = '';
                document.getElementById('employeeResults').innerHTML = '';
            }

            // ========== LOAD PROJECT ASSIGNMENTS ==========
            async function loadProjectAssignments(projectPublicId) {
    try {
        // Use FormData (works better with CodeIgniter's CSRF protection)
        const formData = new FormData();
        formData.append('projectPublicId', projectPublicId);
        
        
        
        const response = await fetch('ProjectAssignment/getProjectAssignments', {
            method: 'POST',
            body: formData  // Use FormData, not JSON
            // Don't set Content-Type header - FormData sets it automatically
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentProjectAssignments = result.assignments || [];
            populateAssignmentTable();
        } else {
            showErrorModal('Failed to load project assignments: ' + result.message);
        }
    } catch (error) {
        console.error('Error loading assignments:', error);
        showErrorModal('Error loading project assignments');
    }
}

            function populateAssignmentTable() {
                const tableBody = document.getElementById('assignmentTableBody');
                tableBody.innerHTML = '';
                // DESTROY DataTable FIRST if it exists
                if ($.fn.DataTable.isDataTable('#assignmentTable')) {
                    $('#assignmentTable').DataTable().destroy();
                    $('#assignmentTable').find('tbody').empty(); // Clear tbody
                }
                if (currentProjectAssignments.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px;">
                                No employees assigned to this project yet.
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                currentProjectAssignments.forEach((assignment, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${assignment.employeePublicId}</td>
                        <td>${assignment.employeeName}</td>
                        <td>${assignment.designationName || 'N/A'}</td>
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
                        <!--<td>
                            <button class="removeBtn" data-assignment-id="${assignment.id}">Remove</button>
                        </td>-->
                    `;
                    tableBody.appendChild(row);
                });
                
                // Initialize DataTable
                if ($.fn.DataTable.isDataTable('#assignmentTable')) {
                    $('#assignmentTable').DataTable().destroy();
                }
                $('#assignmentTable').DataTable();
            }

            // ========== ADD MEMBER FORM SUBMISSION ==========
            document.getElementById('addMemberForm').addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const projectId = document.getElementById('selectedProjectId').value;
                const employeeId = document.getElementById('selectedEmployeeId').value;
                
                if (!projectId || !employeeId) {
                    showErrorModal('Please select both project and employee');
                    return;
                }
                
                try {
                    const response = await fetch('ProjectAssignment/assignEmployeeToProject', {
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
                        document.getElementById('employeeSelectInput').value = '';
                        document.getElementById('selectedEmployeeId').value = '';
                        
                        // Reload assignments
                        await loadProjectAssignments(projectId);
                        
                        // Show success
                        showSuccessModal({message: 'Employee assigned successfully!'}, 'addAssignment');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showErrorModal('Request failed: ' + error.message);
                }
            });

            // ========== TOGGLE ASSIGNMENT STATUS ==========
            document.addEventListener('change', async function(e) {
                if (e.target && e.target.classList.contains('assignmentStatusBtn')) {
                    const assignmentId = e.target.getAttribute('data-assignment-id');
                    const status = e.target.checked ? 'active' : 'inactive';
                    
                    try {
                        const formData = new FormData();
                        formData.append('assignmentId', assignmentId);
                        formData.append('status', status);
                        
                        const response = await fetch('ProjectAssignment/toggleAssignmentStatus', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (!result.success) {
                            // Revert on error
                            e.target.checked = !e.target.checked;
                            showErrorModal('Failed to update status');
                        } else {
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
            });

            // ========== REMOVE ASSIGNMENT ==========
            document.addEventListener('click', async function(e) {
                if (e.target && e.target.classList.contains('removeBtn')) {
                    if (!confirm('Are you sure you want to remove this employee from the project?')) {
                        return;
                    }
                    
                    const assignmentId = e.target.getAttribute('data-assignment-id');
                    const projectId = document.getElementById('selectedProjectId').value;
                    
                    try {
                        const formData = new FormData();
                        formData.append('assignmentId', assignmentId);
                        
                        const response = await fetch('ProjectAssignment/removeAssignment', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (!result.success) {
                            showErrorModal('Failed to remove assignment');
                        } else {
                            // Reload assignments
                            await loadProjectAssignments(projectId);
                            showSuccessModal({message: 'Employee removed from project'}, 'removeAssignment');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showErrorModal('Error removing assignment');
                    }
                }
            });

            // ========== MODAL FUNCTIONS ==========
            function showSuccessModal(object, objectName) {
                let details = '';
                if (objectName === 'addAssignment' || objectName === 'removeAssignment' || objectName === 'statusChange') {
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
            document.addEventListener('click', (e) => {
                // Project dropdown
                const projectDropdown = document.getElementById('projectDropdown');
                const projectInput = document.getElementById('projectSelectInput');
                if (!e.target.closest('#projectDropdown') && e.target !== projectInput) {
                    projectDropdown.style.display = 'none';
                }
                
                // Employee dropdown
                const employeeDropdown = document.getElementById('employeeDropdown');
                const employeeInput = document.getElementById('employeeSelectInput');
                if (!e.target.closest('#employeeDropdown') && e.target !== employeeInput) {
                    employeeDropdown.style.display = 'none';
                }
            });
        </script>
    </body>
</html>


<!--
create table projectAssignedTo (
    id bigint primary key auto_increment,
    projectId bigint not null,
    employeeId bigint not null,
    assignedFrom date not null,
    status enum('active', 'inactive') default 'active',
    createdAt timestamp default current_timestamp,
    updatedAt timestamp default current_timestamp on update current_timestamp,
    constraint fk_projectId_projects_id foreign key (projectId) references projects(id),
    constraint fk_employeeId_employees_id foreign key (employeeId) references employees(id));
-->