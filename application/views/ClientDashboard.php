<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Client Dashboard</title>
        <!-- Add DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="assets/styles.css">
    </head>
    <body>
        <div class="dashboardContainer">
            <!---------------------------------------------------- Start of header ----------------------------------------------------->
            <div class="dashboardHeader">
                <h1>CLIENT LIST</h1>
                <div class="buttonsGroup">
                <button id="addClientBtn">Add Client</button>
                <button id="projectPageRedirectBtn" >Project List</button>
                </div>
            </div> 
            <!------------------------------------------------------ End of header ---------------------------------------------------->
            <!----------------- Listing Table Container ----------------->
            <div class="tableContainer">
                <table class="display" id="clientsTable">
                    <thead>
                        <tr>
                            <th>Sl No.</th>
                            <th>Client ID</th>
                            <th>Client Name</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <!--<th>Actions</th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $index => $client): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td> <!--Index position starts from 0; thats why doing +1-->
                            <td><?php echo $client->publicId; ?></td>
                            <td><?php echo $client->name; ?></td>
                            <td><?php echo date('d-m-Y', strtotime($client->createdAt)); ?></td>
                            <td>
                                <div class="switch-container">
                                    <label class="switch">
                                        <input type="checkbox" class="statusChangeBtn" data-public-id="<?php echo $client->publicId; ?>" <?php echo ($client->status === 'active') ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </td>
                            <!--<td>
                                <button class="editBtn" data-id="<?php echo $client->publicId; ?>">Edit</button>
                            </td>-->
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
                        $('#clientsTable').DataTable();
                    });
                </script>
            </div>
            <!----------------- End of Listing Table Container ----------------->
            <!--------- Start of add-client-modal -------------------------------------------->
            <div class="modal" id="addClientModal">
                <div class="modalContent">
                    <span class="closeBtn" id="closeAddClientModal">&times;</span>
                    <h2>Add New Client</h2>
                    <form id="addClientForm">
                        <div class="formGroup">
                            <input type="text" name="name" class="formInput" placeholder="" required>
                            <label class="formLabel">Client Name</label>
                        </div>
                    
                        <button type="submit" id="addClientSubmitBtn">Add Client</button>
                </form>
            </div> <!-- End of modal-content -->
        </div> 
        <!----------- End of add-client-modal -------------------------------------------->

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
            // Project List Button: Redirect to Project Listing Page
            document.getElementById('projectPageRedirectBtn').addEventListener('click', function() {
            window.location.href = '<?php echo base_url("Projects"); ?>';
            });
            // Open Add Client Modal
            document.getElementById('addClientBtn').addEventListener('click', function() {
            document.getElementById('addClientModal').style.display = 'flex';
            });
            // Close Add Client Modal
            document.getElementById('closeAddClientModal').addEventListener('click', function() {
            document.getElementById('addClientForm').reset();
            document.getElementById('addClientModal').style.display = 'none';
            });

             // ========== SUCCESS MODAL FUNCTIONS ==========
            function showSuccessModal(object, objectName) {
                let details = '';
                if (objectName === 'addClient'){
                details = `
                    <p><strong>Client ID:</strong> ${object.publicId}</p>
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

            document.addEventListener('submit', async function(e) {
            if (e.target && e.target.id === 'addClientForm') {
                e.preventDefault();
                const formData = new FormData(e.target);

                try {
                    const response = await fetch(`Clients/addClient`, {
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
                        document.getElementById('addClientModal').style.display = 'none';
                        
                        // Clear the form
                        e.target.reset();
                        
                        // Show success modal with details
                        showSuccessModal(result.client, 'addClient');
                    }
                } catch (error) {
                    alert('Request failed: ' + error.message);
                }
            }
        });

        async function setEntityStatus(publicId, checkbox) {
    const status = checkbox.checked ? 'active' : 'inactive';
    
    try {
        const formData = new FormData();
        formData.append('publicId', publicId);
        formData.append('status', status);
        
        const response = await fetch('Clients/toggleClientStatus', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error('Failed to update status');
        }

        $messageToSend = status === 'active' ? 'Client activated successfully.' : 'Client deactivated successfully.';
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
        </script>
        <!--</body>
</html>-->

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