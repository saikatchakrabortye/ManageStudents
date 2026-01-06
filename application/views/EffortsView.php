<!DOCTYPE html>
<html lang="en">
    <head>
        <title>My Efforts Listings</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
        <div class="dashboardContainer">
            <!---------------------------------------------------- Start of header ----------------------------------------------------->
            <div class="dashboardHeader">
                <h1>EFFORTS LIST</h1>
                <div class="totalHoursDisplay" style="margin-top: 10px; background: #f5f5f5; padding: 10px 20px; border-radius: 5px; display: inline-block;">
                    <strong>Total Hours Worked:</strong> <span id="totalHours"><?php echo $totalHoursWorked ?? '0:00'; ?></span>
                </div>
                <div class="buttonsGroup">
                <button id="addEffortBtn">Add Effort</button>
                </div>
                
            </div> 
            <!------------------------------------------------------ End of header ---------------------------------------------------->
            <!----------------- Start of Filter Section ----------------->
<div class="filterContainer" style="background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; border: 1px solid #ddd;">
    <h3 style="margin-top: 0; margin-bottom: 15px;">Filter Efforts</h3>
    <form id="filterForm" method="GET" action="<?php echo base_url('Efforts'); ?>" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
        
        <!-- From Date -->
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">From Date</label>
            <input type="date" name="fromDate" id="fromDate" 
                   value="<?php echo isset($fromDate) ? $fromDate : date('Y-m-d'); ?>" 
                   class="formInput" style="width: 100%; padding: 8px;">
        </div>
        
        <!-- To Date -->
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">To Date</label>
            <input type="date" name="toDate" id="toDate" 
                   value="<?php echo isset($toDate) ? $toDate : date('Y-m-d'); ?>" 
                   class="formInput" style="width: 100%; padding: 8px;">
        </div>
        
        <?php if ($designationId == 10): ?>
        <!-- Employee Filter (Admin only) -->
        <div style="flex: 1; min-width: 200px; position: relative;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Employee</label>
            <input type="text" id="employeeSelectInput" class="formInput" 
                   placeholder="Select employee..." 
                   style="width: 100%; padding: 8px; cursor: pointer;"
                   value="<?php 
                        if (isset($filterEmployeeId) && !empty($filterEmployeeId)) {
                            foreach ($employees as $emp) {
                                if ($emp->id == $filterEmployeeId) {
                                    echo htmlspecialchars($emp->name . ' (' . $emp->publicId . ')');
                                    break;
                                }
                            }
                        }
                   ?>">
            <input type="hidden" name="employeeId" id="selectedEmployeeId" 
                   value="<?php echo isset($filterEmployeeId) ? $filterEmployeeId : ''; ?>">
            <div id="employeeDropdown" class="dropdown" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 9999; background: white;">
                <input type="text" id="employeeSearch" class="dropdownSearchInput" 
                       placeholder="Search employees..." 
                       style="width: 100%; padding: 8px;">
                <div id="employeeResults" style="max-height: 200px; overflow-y: auto;"></div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Filter Buttons -->
        <div style="flex: none; display: flex; gap: 10px;">
            <button type="submit" style="padding: 8px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Apply Filters
            </button>
            <button type="button" id="clearFilters" style="padding: 8px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Clear Filters
            </button>
        </div>
    </form>
    
    <!-- Error Display -->
    <?php if (isset($error)): ?>
    <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; margin-top: 10px; border-radius: 4px;">
        <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
</div>
<!----------------- End of Filter Section ----------------->
            <!----------------- Listing Table Container ----------------->
            <div class="tableContainer">
                <table class="display" id="effortsTable">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Effort ID</th>
                            <?php if ($this->session->userdata('designationId') == 10): ?>
                            <th>Employee Name</th>
                            <?php endif; ?>
                            <th>Date</th>
                            <th>Project Name</th>
                            <th>Effort (In Hours and Mins)</th>
                            <?php if ($this->session->userdata('designationId') == 10): ?>
                            <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($efforts as $index => $effort): ?>
                    <tr id="effort-row-<?php echo $effort->publicId; ?>">
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo $effort->publicId; ?></td>
                        <?php if ($this->session->userdata('designationId') == 10): ?>
                        <td><?php echo $effort->employeeName; ?></td>
                        <?php endif; ?>
                        <td><?php echo date('d-m-Y', strtotime($effort->effortDate)); ?></td>
                        <td><?php echo $effort->projectName; ?></td>
                        <td id="duration-<?php echo $effort->publicId; ?>"><?php echo $effort->duration; ?></td>
                        <?php if ($this->session->userdata('designationId') == 10): ?>
                        <td>
                            <button class="edit-duration-btn" data-effort-id="<?php echo $effort->publicId; ?>">Edit</button>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
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
                        $('#effortsTable').DataTable();
                    });
                </script>
            </div>
            <!--------- Start of add-effort-modal -------------------------------------------->
            <div class="modal" id="addEffortModal">
                <div class="modalContent">
                    <span class="closeBtn" id="closeAddEffortModal">&times;</span>
                    <h2>Add New Effort</h2>
                    <form id="addEffortForm">
                        <div class="formGroup">
                            <input type="date" name="date" class="formInput" placeholder="" max="<?php echo date('Y-m-d'); ?>" required>
                            <label class="formLabel">Select Date</label>
                        </div>
                        <div class="formGroup">
                            <input type="time" name="effortDuration" class="formInput" placeholder="" required>
                            <label class="formLabel">Effort Duration (in Hrs & Mins)</label>
                        </div>
                        <!-- ************** For Project Selection Dropdown with Search ***************** -->
                        <div class="formGroup">
                            <input type="text" id="projectSelectInput" class="formInput" placeholder="" readonly>
                            <label class="formLabel">Select Project</label>
                            <input type="hidden" id="selectedProjectId" name="projectId" value="">
                            <div id="projectDropdown" class="dropdown" style="display: none;">
                                <input type="text" id="Search" class="dropdownSearchInput" placeholder="Search projects..." data-validation>
                                <div id="projectResults"></div>
                            </div>
                        </div>
                        <button type="submit" id="addEffortSubmitBtn">Add Effort</button>
                </form>
            </div> <!-- End of modal-content -->
        </div> 
        <!----------- End of add-effort-modal -------------------------------------------->
        <!-- Edit Duration Modal -->
        <div class="modal" id="editDurationModal">
            <div class="modalContent" style="width: 400px;">
                <span class="closeBtn" id="closeEditDurationModal">&times;</span>
                <h2>Edit Effort Duration</h2>
                <form id="editDurationForm">
                    <div class="formGroup">
                        <input type="text" id="editEffortId" class="formInput" readonly>
                        <label class="formLabel">Effort ID</label>
                    </div>
                    <div class="formGroup">
                        <input type="text" id="editEffortDate" class="formInput" readonly>
                        <label class="formLabel">Date</label>
                    </div>
                    <div class="formGroup">
                        <input type="text" id="editProjectName" class="formInput" readonly>
                        <label class="formLabel">Project</label>
                    </div>
                    <div class="formGroup">
                        <input type="time" name="effortDuration" id="editEffortDuration" class="formInput" required>
                        <label class="formLabel">New Duration (in Hrs & Mins)</label>
                    </div>
                    <input type="hidden" id="editEffortPublicId" name="effortPublicId">
                    <button type="submit" id="updateEffortBtn">Update Duration</button>
                </form>
            </div>
        </div>
        <!-- End of Edit Duration Modal -->
                <!-- Success Modal -->
        <div class="modal" id="successModal">
            <div class="modalContent" style="width: 400px;">
                <h2>✅ Effort Added Successfully</h2>
                <div id="successDetails">
                    <!-- Success details data populated by Javascript -->
                </div>
                <button onClick="closeSuccessModal()">OK</button>
            </div>
        </div>
        <!-- End of Success Modal -->
        
        <!-- Error Modal -->
        <div class="modal" id="errorModal">
            <div class="modalContent" style="width: 300px;">
                <h2>❌ Error</h2>
                <div id="errorDetails">
                    <!-- Error details data populated by Javascript -->
                </div>
                <button onClick="closeErrorModal()">OK</button>
            </div>
        </div>
        <!-- End of Error Modal -->
        </div> <!-- End of dashboard container -->
        <script>
            // Open Add Project Modal
            document.getElementById('addEffortBtn').addEventListener('click', function() {
            document.getElementById('addEffortModal').style.display = 'flex';
            });
            // Close Add Project Modal
            document.getElementById('closeAddEffortModal').addEventListener('click', function() {
            document.getElementById('addEffortForm').reset();
            document.getElementById('addEffortModal').style.display = 'none';
            });

                        // ========== SUCCESS MODAL FUNCTIONS ==========
            function showSuccessModal(effort) {
                const details = `
                    <p><strong>Effort ID:</strong> ${effort.publicId}</p>
                    <p><strong>Date:</strong> ${effort.effortDate}</p>
                    <p><strong>Duration:</strong> ${effort.duration}</p>
                    <p><strong>Project:</strong> ${effort.projectName}</p>
                    <p><strong>Created At:</strong> ${effort.createdAt}</p>
                `;
                
                document.getElementById('successDetails').innerHTML = details;
                document.getElementById('successModal').style.display = 'flex';
            }

            function closeSuccessModal() {
                document.getElementById('successModal').style.display = 'none';
                window.location.reload(); // Refresh to show new effort in table
            }
            
            // ========== ERROR MODAL FUNCTIONS ==========
            function showErrorModal(errorMessage) {
                document.getElementById('errorDetails').innerHTML = `
                    <p>${errorMessage}</p>
                `;
                document.getElementById('errorModal').style.display = 'flex';
            }

            function closeErrorModal() {
                document.getElementById('errorModal').style.display = 'none';
            }
            // ========== END ERROR MODAL FUNCTIONS ==========

                        // ========== ADD EFFORT FORM SUBMISSION ==========
            document.addEventListener('submit', async function(e) {
                if (e.target && e.target.id === 'addEffortForm') {
                    e.preventDefault();
                    
                    // Validate required fields
                    const projectId = document.getElementById('selectedProjectId').value;
                    if (!projectId) {
                        showErrorModal('Please select a project');
                        return;
                    }
                    
                    const formData = new FormData(e.target);
                    formData.append('projectId', projectId);

                    try {
                        const response = await fetch(`Efforts/addEffort`, {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();

                        if (!result.success) {
                            showErrorModal(cleanMessage);
                        } else {
                            // Success case - close modal and show success
                            document.getElementById('addEffortModal').style.display = 'none';
                            e.target.reset();
                            resetProjectForm();
                            showSuccessModal(result.effort);
                        }
                    } catch (error) {
                        showErrorModal('Request failed: ' + error.message);
                    }
                }
            });
            // ========== END ADD EFFORT FORM SUBMISSION ==========

           // ========== PROJECT DROPDOWN FUNCTIONALITY ==========
let allProjects = [];

// Load projects on focus
document.getElementById('projectSelectInput').addEventListener('click', async function() {
    if (allProjects.length === 0) {
        try {
            const response = await fetch('EmployeeProjectAssignment/getEmployeeAssignments');
            const result = await response.json();
            if (result.success) {
                allProjects = result.assignments;  // ← Changed from 'projects' to 'assignments'
            }
        } catch (error) {
            console.error('Error loading projects:', error);
            allProjects = [];
        }
    }
    document.getElementById('projectDropdown').style.display = 'block';
    filterProjects();
});

// Filter projects on typing
document.getElementById('Search').addEventListener('input', filterProjects);

function filterProjects() {
    const search = document.getElementById('Search').value.toLowerCase();
    const filtered = allProjects.filter(assignment => 
        assignment.projectName.toLowerCase().includes(search) ||  
        assignment.projectPublicId.toLowerCase().includes(search)
    );
    
    const results = document.getElementById('projectResults');
    if (filtered.length) {
        results.innerHTML = filtered.map(assignment => 
            `<div class="dropdownItem projectItem" data-id="${assignment.projectPublicId}" data-name="${assignment.projectName}">
                ${assignment.projectName} (${assignment.projectPublicId})
            </div>`
        ).join('');
        
        // Add event listeners
        results.querySelectorAll('.dropdownItem.projectItem').forEach(item => {
            item.addEventListener('click', function() {
                selectProject(this.dataset.id, this.dataset.name);
            });
        });
    } else {
        results.innerHTML = '<div style="padding: 10px; color: #666;">No projects found</div>';
    }
}

function selectProject(projectId, projectName) {
    // Update the input field
    document.getElementById('projectSelectInput').value = `${projectName} (${projectId})`;
    document.getElementById('selectedProjectId').value = projectId;
    
    // Hide dropdown and clear search
    document.getElementById('projectDropdown').style.display = 'none';
    document.getElementById('Search').value = '';
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    const projectDropdown = document.getElementById('projectDropdown');
    const projectInput = document.getElementById('projectSelectInput');
    
    if (!e.target.closest('#projectDropdown') && e.target !== projectInput) {
        projectDropdown.style.display = 'none';
    }
});

// Clear project form when modal closes
document.getElementById('closeAddEffortModal').addEventListener('click', function() {
    resetProjectForm();
});

// Function to reset the project form
function resetProjectForm() {
    document.getElementById('projectSelectInput').value = '';
    document.getElementById('selectedProjectId').value = '';
    document.getElementById('Search').value = '';
    document.getElementById('projectResults').innerHTML = '';
    document.getElementById('projectDropdown').style.display = 'none';
    
    // Reset allProjects array
    allProjects = [];
}
// ========== END PROJECT DROPDOWN FUNCTIONALITY ==========

// ========== EMPLOYEE DROPDOWN FUNCTIONALITY (Admin only) ==========
<?php if ($designationId == 10): ?>
let allEmployees = <?php echo json_encode($employees ?? []); ?>;

// Load employees on focus
document.getElementById('employeeSelectInput').addEventListener('click', function() {
    document.getElementById('employeeDropdown').style.display = 'block';
    filterEmployees();
});

// Filter employees on typing
document.getElementById('employeeSearch').addEventListener('input', filterEmployees);

function filterEmployees() {
    const search = document.getElementById('employeeSearch').value.toLowerCase();
    const filtered = allEmployees.filter(emp => 
        emp.name.toLowerCase().includes(search) ||  
        emp.publicId.toLowerCase().includes(search) ||
        (emp.designationName && emp.designationName.toLowerCase().includes(search))
    );
    
    const results = document.getElementById('employeeResults');
    if (filtered.length) {
        results.innerHTML = filtered.map(emp => 
            `<div class="dropdownItem employeeItem" data-id="${emp.id}" data-name="${emp.name}" data-publicid="${emp.publicId}">
                <strong>${emp.name}</strong> (${emp.publicId})<br>
                <small>${emp.designationName || 'No designation'}</small>
            </div>`
        ).join('');
        
        // Add event listeners
        results.querySelectorAll('.dropdownItem.employeeItem').forEach(item => {
            item.addEventListener('click', function() {
                selectEmployee(this.dataset.id, this.dataset.name, this.dataset.publicid);
            });
        });
    } else {
        results.innerHTML = '<div style="padding: 10px; color: #666;">No employees found</div>';
    }
}

function selectEmployee(employeeId, employeeName, employeePublicId) {
    // Update the input field
    document.getElementById('employeeSelectInput').value = `${employeeName} (${employeePublicId})`;
    document.getElementById('selectedEmployeeId').value = employeeId;
    
    // Hide dropdown and clear search
    document.getElementById('employeeDropdown').style.display = 'none';
    document.getElementById('employeeSearch').value = '';
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
    const employeeDropdown = document.getElementById('employeeDropdown');
    const employeeInput = document.getElementById('employeeSelectInput');
    
    if (!e.target.closest('#employeeDropdown') && e.target !== employeeInput) {
        employeeDropdown.style.display = 'none';
    }
});

// Clear employee selection
function clearEmployeeFilter() {
    document.getElementById('employeeSelectInput').value = '';
    document.getElementById('selectedEmployeeId').value = '';
    document.getElementById('employeeSearch').value = '';
    document.getElementById('employeeResults').innerHTML = '';
    document.getElementById('employeeDropdown').style.display = 'none';
}
<?php endif; ?>
// ========== END EMPLOYEE DROPDOWN FUNCTIONALITY ==========

// ========== CLEAR FILTERS FUNCTIONALITY ==========
document.getElementById('clearFilters').addEventListener('click', function() {
    // Clear date filters
    document.getElementById('fromDate').value = '';
    document.getElementById('toDate').value = '';
    
    // Clear employee filter if admin
    <?php if ($designationId == 10): ?>
    clearEmployeeFilter();
    <?php endif; ?>
    
    // Submit form to reload with default values
    document.getElementById('filterForm').submit();
});

// ========== DATE VALIDATION ==========
document.getElementById('filterForm').addEventListener('submit', function(e) {
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;
    
    if (fromDate && toDate) {
        if (new Date(fromDate) > new Date(toDate)) {
            e.preventDefault();
            alert('From date cannot be greater than To date');
            return false;
        }
    }
    return true;
});

// Set max date to today for both date inputs
const today = new Date().toISOString().split('T')[0];
document.getElementById('fromDate').max = today;
document.getElementById('toDate').max = today;

// ========== EDIT DURATION FUNCTIONALITY ==========
let currentEffortId = '';

// Use event delegation for dynamically created buttons
document.addEventListener('click', function(e) {
    // Check if the clicked element is an edit button or inside an edit button
    const editButton = e.target.closest('.edit-duration-btn');
    
    if (editButton) {
        e.preventDefault();
        console.log('Edit button clicked via delegation');
        
        const effortId = editButton.getAttribute('data-effort-id');
        currentEffortId = effortId;
        
        // Find the row data
        const row = document.getElementById(`effort-row-${effortId}`);
        if (row) {
            // Get current values from the row
            const cells = row.getElementsByTagName('td');
            
            // Calculate indices based on admin status
            const isAdmin = <?php echo ($designationId == 10) ? 'true' : 'false'; ?>;
            let effortDateIndex, projectNameIndex, currentDurationIndex;
            
            if (isAdmin) {
                effortDateIndex = 3; // Adjust based on your table structure
                projectNameIndex = 4;
                currentDurationIndex = 5;
            } else {
                effortDateIndex = 2;
                projectNameIndex = 3;
                currentDurationIndex = 4;
            }
            
            const effortIdCell = cells[1].textContent;
            const effortDateCell = cells[effortDateIndex].textContent;
            const projectNameCell = cells[projectNameIndex].textContent;
            const currentDuration = cells[currentDurationIndex].textContent;
            
            // Format date from dd-mm-yyyy to yyyy-mm-dd for display
            const dateParts = effortDateCell.split('-');
            const formattedDate = dateParts.length === 3 ? 
                `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}` : 
                effortDateCell;
            
            console.log('Row data:', {
                effortIdCell,
                effortDateCell,
                projectNameCell,
                currentDuration
            });
            
            // Populate modal fields
            document.getElementById('editEffortId').value = effortIdCell;
            document.getElementById('editEffortDate').value = formattedDate;
            document.getElementById('editProjectName').value = projectNameCell;
            document.getElementById('editEffortDuration').value = currentDuration;
            document.getElementById('editEffortPublicId').value = effortId;
            
            // Show modal
            document.getElementById('editDurationModal').style.display = 'flex';
        }
    }
});

// Close Edit Duration Modal
document.getElementById('closeEditDurationModal').addEventListener('click', function() {
    document.getElementById('editDurationModal').style.display = 'none';
    document.getElementById('editDurationForm').reset();
    currentEffortId = '';
});

// Edit Duration Form Submission
document.getElementById('editDurationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const effortPublicId = document.getElementById('editEffortPublicId').value;
    const effortDuration = document.getElementById('editEffortDuration').value;
    
    if (!effortDuration) {
        alert('Please enter a duration');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('effortPublicId', effortPublicId);
        formData.append('effortDuration', effortDuration);
        
        const response = await fetch(`Efforts/updateEffortDuration`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();
        
        if (!result.success) {
            alert(cleanMessage);
        } else {
            // Update the duration in the table
            const durationCell = document.getElementById(`duration-${effortPublicId}`);
            if (durationCell) {
                durationCell.textContent = result.effort.duration;
            }
            
            // Close modal
            document.getElementById('editDurationModal').style.display = 'none';
            document.getElementById('editDurationForm').reset();
            currentEffortId = '';
            
            alert('Duration updated successfully!');
        }
    } catch (error) {
        alert('Request failed: ' + error.message);
    }
});

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const editModal = document.getElementById('editDurationModal');
    if (e.target === editModal) {
        editModal.style.display = 'none';
        document.getElementById('editDurationForm').reset();
        currentEffortId = '';
    }
});
// ========== END EDIT DURATION FUNCTIONALITY ==========
        </script>
    </body>
</html>