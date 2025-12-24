<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project Dashboard</title>
        <!-- Add DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
        <div class="dashboardContainer">
            <!---------------------------------------------------- Start of header ----------------------------------------------------->
            <div class="dashboardHeader">
                <h1>PROJECT LIST</h1>
                <div class="buttonsGroup">
                <button id="addProjectBtn">Add Project</button>
                <button id="clientMasterBtn" >Client Master</button>
                </div>
            </div> 
            <!------------------------------------------------------ End of header ---------------------------------------------------->
            <!----------------- Listing Table Container ----------------->
            <div class="tableContainer">
                <table class="display" id="projectsTable">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Project ID</th>
                            <th>Client Name</th>
                            <th>Project Name</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $index => $project): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td> <!--Index position starts from 0; thats why doing +1-->
                            <td><?php echo $project->publicId; ?></td>
                            <td><?php echo $project->clientName; ?></td>
                            <td><?php echo $project->name; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($project->startDate)); ?></td>
                            <!--<td><?php // echo $project->status; ?></td>-->
                            <td>
                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" class="statusChangeBtn" data-public-id="<?php echo $project->publicId; ?>" <?php echo ($project->status === 'active') ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </td>
                            
                            <td>
                                <button class="editBtn" data-public-id="<?php echo $project->publicId; ?>">Edit</button>
                            </td>
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
                        $('#projectsTable').DataTable();
                    });
                </script>
            </div>
            <!--------- Start of add-project-modal -------------------------------------------->
            <div class="modal" id="addProjectModal">
                <div class="modalContent">
                    <span class="closeBtn" id="closeAddProjectModal">&times;</span>
                    <h2>Add New Project</h2>
                    <form id="addProjectForm">
                        <!-- ************** For Client Selection Dropdown with Search ***************** -->
                        <div class="formGroup">
                            <input type="text" id="clientSelectInput" class="formInput" placeholder="" readonly>
                            <label class="formLabel">Select Client</label>
                            <input type="hidden" id="selectedClientId" name="clientId" value="">
                            <div id="clientDropdown" class="dropdown" style="display: none;">
                                <input type="text" id="clientSearch" class="dropdownSearchInput" placeholder="Search clients..." data-validation>
                                <div id="clientResults"></div>
                            </div>
                        </div>
                        <!-- Client Selection dropdown with search container ends here-->
                        <div class="formGroup">
                            <input type="text" name="name" class="formInput" placeholder="" required>
                            <label class="formLabel">Project Name</label>
                        </div>
                        <div class="formGroup">
                        <input type="date" name="startDate" class="formInput" placeholder="">
                        <label class="formLabel">Start Date</label>
                    </div>
                        <button type="submit" id="addProjectSubmitBtn">Add Project</button>
                </form>
            </div> <!-- End of modal-content -->
        </div> 
        <!----------- End of add-project-modal -------------------------------------------->
        <!-------------- Update Project Modal ---------------------------------->
<div class="modal" id="updateProjectModal">
    <div class="modalContent">
        <span class="closeBtn" id="closeUpdateProjectModal">&times;</span>
        <h2>Update Project</h2>
        <form id="updateProjectForm">
            <input type="hidden" name="publicId" id="updateProjectId" value="">
            <!-- ************** For Client Selection Dropdown with Search ***************** -->
            <div class="formGroup">
                <!-- CHANGE: Add "Update" prefix to IDs -->
                <input type="text" id="updateClientSelectInput" class="formInput" placeholder="" readonly>
                <label class="formLabel">Select Client</label>
                <input type="hidden" id="updateSelectedClientId" name="clientId" value="">
                <div id="updateClientDropdown" class="dropdown" style="display: none;">
                    <input type="text" id="updateClientSearch" class="dropdownSearchInput" placeholder="Search clients..." data-validation>
                    <div id="updateClientResults"></div>
                </div>
            </div>
            <!-- Client Selection dropdown with search container ends here-->
            <div class="formGroup">
                <input type="text" id="updateViewName" name="name" class="formInput" placeholder="" required>
                <label class="formLabel">Project Name</label>
            </div>
            <div class="formGroup">
                <input type="date" id="updateViewStartDate" name="startDate" class="formInput" placeholder="">
                <label class="formLabel">Start Date</label>
            </div>
            <button type="submit" id="updateProjectSubmitBtn">Update</button>
        </form>
    </div>
</div>
<!-------------- End of Update Project Modal ---------------------------------->
            <!-- Success Modal at end of body: so that it appears on top of all other elements -->
            <div class="modal" id="successModal">
                <div class="modalContent" style="width: 400px;">
                    <h2>Success</h2>
                    <div id="successDetails">
                        <!-- Success details data populated by Javascript -->
                    </div>
                    <button onClick="closeSuccessModal()">Ok</button>
                </div>
            </div>
            <!-- End of Success Modal -->
            <!-- Error Modal Starts -->
            <div class="modal" id="errorModal">
                <div class="modalContent" style="width: 300px;">
                    <h2>Error</h2>
                    <div id="errorDetails">
                        <!-- Error details data populated by Javascript -->
                    </div>
                    <button onClick="closeErrorModal()">Ok</button>
                </div>
            </div>
            <!-- Error Modal Ends -->
        </div> <!-- dashboardContainer Ends -->
        <script>
            // Clients Master Button: Redirect to Clients Listing Page
            document.getElementById('clientMasterBtn').addEventListener('click', function() {
            window.location.href = '<?php echo base_url("Clients"); ?>';
            });

            // Open Add Project Modal
            document.getElementById('addProjectBtn').addEventListener('click', function() {
            document.getElementById('addProjectModal').style.display = 'flex';
            });
            // Close Add Project Modal
            document.getElementById('closeAddProjectModal').addEventListener('click', function() {
            document.getElementById('addProjectForm').reset();
            document.getElementById('addProjectModal').style.display = 'none';
            });

            // Close Update Project Modal
            document.getElementById('closeUpdateProjectModal').addEventListener('click', function() {
            document.getElementById('updateProjectForm').reset();
            document.getElementById('updateProjectModal').style.display = 'none';
            });

            // ========== SUCCESS MODAL FUNCTIONS ==========
            function showSuccessModal(object, objectName) {
                let details = '';
                if (objectName === 'addProject'){
                details = `
                    <p><strong>Project ID:</strong> ${object.publicId}</p>
                    <p><strong>Name:</strong> ${object.name}</p>
                    <p><strong>Created At:</strong> ${object.createdAt}</p>
                `;
                }
                else if (objectName === 'statusChange') {
                    details = `<p>${object.message}</p>`;
                }
                document.getElementById('successDetails').innerHTML = details;
                document.getElementById('successModal').style.display = 'flex';
            }

            function closeSuccessModal() {
                document.getElementById('successModal').style.display = 'none';
                window.location.reload(); // Refresh to show new employee in table
            }
            // ========== END SUCCESS MODAL FUNCTIONS ==========
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
            // Handle Add Project Form Submission
            document.addEventListener('submit', async function(e) {
            if (e.target && e.target.id === 'addProjectForm') {
                e.preventDefault();
                const formData = new FormData(e.target);

                try {
                    const response = await fetch(`Projects/addProject`, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();
                    const errorContainer = document.getElementById('errorDetails');
                    // Remove existing error
                    errorContainer.innerHTML = '';

                    if (!result.success) {
                        //errorContainer.innerHTML = `<div class="error-message-onsubmit" style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">${cleanMessage}</div>`;
                        showErrorModal(cleanMessage);
                    } else {
                        // Success case 

                        // Close the add employee modal
                        document.getElementById('addProjectModal').style.display = 'none';
                        
                        // Clear the form
                        e.target.reset();
                        
                        // Show success modal with details
                        showSuccessModal(result.project, 'addProject');
                    }
                } catch (error) {
                    alert('Request failed: ' + error.message);
                }
            }
        });
            /*************************** Client Dropdown with Search **************************** */
    // Client dropdown functions
    let allClients = [];

    // Load clients on focus
    document.getElementById('clientSelectInput').addEventListener('click', async function() {
        if (allClients.length === 0) {
            try {
                const response = await fetch('Projects/getAllClientsForDropdown');
                const result = await response.json();
                if (result.success) {
                allClients = result.clients || [];
            } else {
                console.error('Failed to load clients');
                allClients = [];
            }
            } catch (error) {
                console.error('Error loading cliens:', error);
                allClients = [];
            }
        }
        document.getElementById('clientDropdown').style.display = 'block';
        filterClients();
    });

    // Filter clients on typing
    document.getElementById('clientSearch').addEventListener('input', filterClients);

    function filterClients() {
        const search = document.getElementById('clientSearch').value.toLowerCase();
        const filtered = allClients.filter(client => 
            (client.name && client.name.toLowerCase().includes(search)) || 
            (client.publicId && client.publicId.toLowerCase().includes(search))
        );
        
        const results = document.getElementById('clientResults');
        if (filtered.length) {
            results.innerHTML = filtered.map(client => 
                `<div class="dropdownItem clientItem" data-id="${client.publicId}" data-name="${client.name}">
                    ${client.name} (${client.publicId})
                </div>`
            ).join('');
            
            // Add event listeners
            results.querySelectorAll('.dropdownItem.clientItem').forEach(item => {
                item.addEventListener('click', function() {
                    selectClient(
                        this.dataset.id,
                        this.dataset.name
                    );
                });
            });
        } else {
            results.innerHTML = '<div style="padding: 10px; color: #666;">No clients found</div>';
        }
    }

    function selectClient(clientId, clientName) {
        // Update the input field
        document.getElementById('clientSelectInput').value = `${clientName} (${clientId})`;
        document.getElementById('selectedClientId').value = clientId;
        
        
        // Hide dropdown and clear search
        document.getElementById('clientDropdown').style.display = 'none';
        document.getElementById('clientSearch').value = '';
        document.getElementById('clientResults').innerHTML = '';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const clientDropdown = document.getElementById('clientDropdown');
        const clientInput = document.getElementById('clientSelectInput');
        
        if (!e.target.closest('#clientDropdown') && e.target !== clientInput) {
            clientDropdown.style.display = 'none';
        }
    });

    /****************************** Code ends here for employee dropdown with search ******************* */

    async function setEntityStatus(publicId, checkbox) {
    const status = checkbox.checked ? 'active' : 'inactive';
    
    try {
        const formData = new FormData();
        formData.append('publicId', publicId);
        formData.append('status', status);
        
        const response = await fetch('Projects/toggleProjectStatus', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error('Failed to update status');
        }

        $messageToSend = status === 'active' ? 'Project activated successfully.' : 'Project deactivated successfully.';
        // Success - no need to reload entire page
        showSuccessModal({message: $messageToSend}, 'statusChange');
        
    } catch (error) {
        console.error('Error:', error);
        // Revert on error
        checkbox.checked = !checkbox.checked;
        alert('Error updating status');
    }
}

// Event listener for status change
document.addEventListener('change', function(e) {
    if (e.target && e.target.classList.contains('statusChangeBtn')) {
        const publicId = e.target.getAttribute('data-public-id');
        setEntityStatus(publicId, e.target);
    }
});

/** When the user clicks edit button for a particular row, data loaded into update form modal and modal displayed */
document.addEventListener('click', async function(e)
{ 
    if (e.target && e.target.classList.contains('editBtn')) {
        const publicId = e.target.getAttribute('data-public-id');

        try {
            const response = await fetch('Projects/getProjectByPublicId', {
                method: 'POST',
                body: JSON.stringify({ publicId: publicId}),
                headers: {
                    'Content-Type': 'application/json'
                }
            })

            const data = await response.json();
            if (data.success) {
                const project = data.project;
                // Populate the update form fields
                document.getElementById('updateProjectId').value = project.publicId;
                document.getElementById('updateViewName').value = project.name;
                document.getElementById('updateViewStartDate').value = project.startDate;

                
                document.getElementById('updateClientSelectInput').value = project.clientName;
                document.getElementById('updateSelectedClientId').value = project.clientId;

                // Show the update modal
                document.getElementById('updateProjectModal').style.display = 'flex';
            } else {
                alert('Failed to load project data');
            }
        } catch (error) {
        console.error('Error:', error);
        alert('Error loading project data');
        }
        
    } 
});

document.addEventListener('submit', async function(e) {
            if (e.target && e.target.id === 'updateProjectForm') {
                e.preventDefault();
                const formData = new FormData(e.target);

                try {
                    const response = await fetch(`Projects/updateProject`, {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();
                    const errorContainer = document.getElementById('errorDetails');
                    // Remove existing error
                    errorContainer.innerHTML = '';

                    if (!result.success) {
                        //errorContainer.innerHTML = `<div class="error-message-onsubmit" style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">${cleanMessage}</div>`;
                        showErrorModal(cleanMessage);
                    } else {
                        // Success case 

                        // Close the add employee modal
                        document.getElementById('updateProjectModal').style.display = 'none';
                        
                        // Clear the form
                        e.target.reset();
                        
                        // Show success modal with details
                        showSuccessModal({message: 'Project updated successfully'}, 'statusChange');
                    }
                } catch (error) {
                    alert('Request failed: ' + error.message);
                }
            }
        });
        </script>
    </body>
</html>

<!-- CLIENTS TABLE STRUCTURE
create table clients (id bigint primary key auto_increment, 
publicId varchar(30) not null, name varchar(150) not null, 
createdAt timestamp default current_timestamp, 
updatedAt timestamp default current_timestamp on UPDATE current_timestamp); 

alter table clients add column status enum('active', 'inactive') not null default 'active';
-->
<!--
create table projects(id bigint primary key auto_increment,
publicId varchar(30) not null,
clientId bigint not null,
name varchar(150) not null,
startDate date not null,
createdAt timestamp default current_timestamp,
updatedAt timestamp default current_timestamp on update current_timestamp);

alter table projects add column status enum('active', 'inactive') default 'active';
alter table projects modify status enum('active', 'inactive') default 'active' not null;
alter table projects add constraint fk_clientId_id_clients foreign key(clientId) references clients(id); 
-->