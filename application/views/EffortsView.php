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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($efforts as $index => $effort): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td> <!--Index position starts from 0; thats why doing +1-->
                            <td><?php echo $effort->publicId; ?></td>
                            <?php if ($this->session->userdata('designationId') == 10): ?>
                            <td><?php echo $effort->employeeName; ?></td>
                            <?php endif; ?>
                            <td><?php echo date('d-m-Y', strtotime($effort->effortDate)); ?></td>
                            <td><?php echo $effort->projectName; ?></td>
                            <td><?php echo $effort->duration; ?></td>
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
        </script>
    </body>
</html>