<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles Dashboard</title>
    <!-- STEP 1: Include REQUIRED CSS from DataTables CDN -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <style>
        /*Below style centers the table div and all others */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;}
        
        /**Add Form Modal Style starts */
        /* Modal base styles */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        /* Modal content box - FIXED PADDING */
        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            box-sizing: border-box; /* This fixes boundary issues */
            max-height: 600px;      /* Sets maximum height */
            overflow-y: auto; /*Adds vertical scrollbar when needed*/
            position: relative;
        }
        /* Form group with floating labels */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        /* Input fields - FIXED WIDTH */
        .form-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            background: transparent;
            box-sizing: border-box; /* This prevents overflow */
        }
        
        /* Floating labels */
        .form-label {
            position: absolute;
            left: 1rem;
            top: 1rem;
            color: #6c757d;
            transition: all 0.3s;
            pointer-events: none;
            background: white;
            padding: 0 0.25rem;
        }
        
        /* When input is focused or has value */
        .form-input:focus,
        .form-input:not(:placeholder-shown) {
            border-color: #007bff;
            outline: none;
        }
        
        /* Move label up when focused or has value */
        .form-input:focus + .form-label,
        .form-input:not(:placeholder-shown) + .form-label {
            top: -0.5rem;
            font-size: 0.8rem;
            color: #007bff;
        }
        
        /* Submit button */
        .submit-btn {
            /*width: 100%;*/
            padding: 1rem;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            box-sizing: border-box; /* Consistent sizing */
        }
        
        .submit-btn:hover {
            background: #0056b3;
        }
        /* Close button */
        .close {
            position: absolute;
            top: 1rem;
            right: 1.5rem;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
            transition: color 0.3s;
            z-index: 10;
        }
        
        .close:hover {
            color: #000;
        }
        
        h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
        }
        /** *************** Add Student Modal Style Ends Here ********** */

        /* Table styles */
        /* Table */
        .table-container {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            /*box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);*/
            overflow: hidden;
            margin-bottom: 24px;
            width: 90%;
            max-width: 90%;
        }
        
        .records-table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .records-table th {
            background: #0056b3;
            color: white;
            padding: 18px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
        }
        
        .records-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
        }
        .records-table tr:last-child td {
            border-bottom: none;
        }
        
        .records-table tr:hover {
            background-color: rgba(58, 134, 255, 0.03);
        }
        
        .record-title {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .record-title:hover {
            color: var(--primary-dark);
        }

        /* Table styles Ends Here */

        /* ******* Pagination styles ******** */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            margin-top: 20px;
        }

        .page-btn {
            padding: 12px 24px;
            border: none;
            background: #0056b3;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            min-width: 100px; /* Makes entire button clickable */
        }

        .page-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .page-info {
            font-weight: bold;
        }
        /* Pagination styles Ends Here */

        /********** Style for Dropdown with Search *************************** */
        /*Style for City Dropdown with search */
        .dropdown {
            display: none;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
        }
        #cityResults div {
            padding: 8px 12px;
            cursor: pointer;
        }
        #cityResults div:hover {
            background: #f0f0f0;
        }
        /************ Style Code ends here for dropdown with search */
    </style>
</head>
<body>
    <div class="dashboardContainer">
        <!---------------------------------------------------- Start of header ----------------------------------------------------->
            <div class="dashboardHeader">
                <h1>ROLES LIST</h1>
                <div class="buttonsGroup">
                <button id="addRoleModalBtn">Add Role</button>
                </div>
            </div> 
            <!------------------------------------------------------ End of header ---------------------------------------------------->
    

    <!--**********Show Roles Table using CI3 Listing Style using foreach loop ***************************** -->
    <div class="tableContainer">    
    <!--<table class="records-table">-->
        <table id="studentsTable" class="display">
            <thead>
                <tr>
                    <th>Sl. No.</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $recordsCounter=1; 
                foreach($roles as $role): ?>
                    <tr>
                        <td><?php echo $recordsCounter; ?></td> <!-- getting data from role object; roles is collection of objects -->
                        <td><?php echo $role->name; ?></td>
                        <td><?php echo $role->description; ?></td>
                        <td><?php echo $role->status; ?></td>
                    </tr>
                <?php $recordsCounter++; endforeach; ?>
                <?php if(empty($roles)): ?>
                    <tr>
                        <td colspan="4">No roles found.</td>
                    </tr>
                <?php endif;?>
            </tbody>

        </table>
         <!-- STEP 4: Include REQUIRED JavaScript Libraries -->
    <!-- jQuery must be included FIRST -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Then the DataTables script -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- STEP 5: The Magic - Initialize DataTable -->
    <script>
        $(document).ready(function() {
            // Find the table by its ID and call .DataTable()
            $('#studentsTable').DataTable();
        });
    </script>
    </div>
    <!--My Code for Listing using CI3 ends here-->

    <!-- ************************* Show Roles Table ********************************************************* -->
    <!-- Table Container -->
    <!--<div class="table-container">
        <table id="studentsTable" class="records-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="recordsBody"></tbody>
        </table>
    </div>-->
    <!-- Table Container Ends Here -->
    <!-- ************************ Pagination Container **************** -->
        <!--<div class="pagination">
            <button id="prevBtn" class="page-btn">Previous</button>
            <span id="pageInfo" class="page-info">Page 1 of 1</span>
            <button id="nextBtn" class="page-btn">Next</button>
        </div>-->
    <!-- Pagination Container Ends Here -->

    <!-- ************************* Show Students Table Ends Here ************************************************ -->

    <!-- ************************************  Add Student Modal Structure ******************************* -->
    <div id="roleModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModalBtn">&times;</span>
            <h2>Add New Role</h2>
            
            <form id="roleForm" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="roleName" class="form-input" placeholder="" data-validation>
                    <label class="form-label">Role Name</label>
                </div>
                
            
                <div class="form-group">
                    <textarea id="address" name="description" class="form-input" placeholder="" rows="4" data-validation></textarea>
                    <label class="form-label">Description</label>
                </div>
                
                
                <button type="submit" class="submit-btn">Add Role</button>
                <div id="form-error-container"></div>
            </form>
        </div>
    </div>
    </div> <!-- Dashboard Container Ends Here -->


    <script>
        // Get elements
        const roleModal = document.getElementById('roleModal');
        const addRoleModalBtn = document.getElementById('addRoleModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const roleForm = document.getElementById('roleForm');
        
        // Open modal
        addRoleModalBtn.addEventListener('click', function() {
            roleModal.style.display = 'flex';
        });
        
        // Close modal
        closeModalBtn.addEventListener('click', function() {
            roleModal.style.display = 'none';
            document.getElementById('roleForm').reset();
        });
        
        

        /* *******************Javascript Code to Fetch Students Data and Display that in Table***************** */
        
        /******* Below Code is without applying pagenation */
        /*async function loadStudents() {
            const response = await fetch('http://localhost/ManageStudents/Students/getStudents');
            const students = await response.json(); // Converts JSON string to JS objects */
        /*******Only the above code changes to below block******** */

        /******* Below Code is with applying pagenation Pagenation Step (1/3)******** */
       /* let currentPage = 1;
        const itemsPerPage = 5; // Show 5 students per page
        async function loadStudents(page = 1) {
            const searchTerm = document.getElementById('searchInput').value; // Search Specific: Get search value
            const response = await fetch(`http://localhost/ManageStudents/Roles/getRoles?page=${page}&limit=${itemsPerPage}&search=${encodeURIComponent(searchTerm)}`); // Modified url by add &search=${encodeURIComponent(searchTerm)}` for search functionality
            const data = await response.json(); // Expect {students: [], total: 100}
        //Pagenation specific code ends here. Only after rendering students, we call updatePagenation() function as below

            
            
            document.querySelector('#studentsTable tbody').innerHTML = 
            //students.map(s => `<tr><td>${s.id}</td> //when not using pagenation
            data.roles.map(r => `<tr><td>${r.id}</td><td>${r.name}</td><td>${r.description}</td><td>${r.status}</td></tr>`).join('');

            updatePagination(data.total, page); // Update pagination controls; Pagenation Step (2/3)
        }
        /* ************************ Helper function to get initials from full name ******************** 
        function getInitials(fullName) {
            return fullName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        }
        /* ************************* End of Helper function ***************************** */

        /**********************  Helper Function needed for Pagenation **************************
        /**Front End of Pagenation only tracks current page and calculates offset 
        function updatePagination(totalStudents, currentPage) {
            const totalPages = Math.ceil(totalStudents / itemsPerPage);
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const pageInfo = document.getElementById('pageInfo');
            
            pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
            prevBtn.disabled = currentPage === 1; // Disable if on first page
            nextBtn.disabled = currentPage === totalPages; // Disable if on last page
            
            // Store current page for event handlers
            prevBtn.onclick = () => currentPage > 1 && loadStudents(currentPage - 1);
            nextBtn.onclick = () => currentPage < totalPages && loadStudents(currentPage + 1);
        }
        /********************** End of Helper Function needed for Pagenation ************************** 


        /* Search handler helper function
        function handleSearch() {
            currentPage = 1; // Reset to first page when searching
            loadStudents(1);
        }
        /**Search Handler Helper Funtion ends here

        // Call on page load, if not using pagenation
        /*document.addEventListener('DOMContentLoaded', loadStudents);

        // Initialize when page loads; use it when using pagenation, so start from page 1; Pagenation Step (3/3)
        document.addEventListener('DOMContentLoaded', () => loadStudents(1));
        // NEW: Add event listener for search input
        document.getElementById('searchInput').addEventListener('input', handleSearch);*/

        /************** Javascript code for Fetching Students data and loading in table ends here *********** */

        
        /***************** Code To Execute When Submit Button in Add Student Form is clicked ************ */
        document.getElementById('roleForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
        const response = await fetch(`http://localhost/ManageStudents/Roles/addRole`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();
        const errorContainer = document.getElementById('form-error-container');
        // Remove existing error
        errorContainer.innerHTML = '';

        if (!result.success) {
            errorContainer.innerHTML = `<div class="error-message-onsubmit" style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">${cleanMessage}</div>`;
        } else {
            // Success case - close modal and reload page
            const modal = document.getElementById('roleModal');
            modal.style.display = 'none';
            
            // Reload the page to refresh the table
            window.location.reload();
            }
        } catch (error) {
            alert('Request failed: ' + error.message);
        }

        });
        /***************** Code ends here To Execute When Submit Button in Add Student Form is clicked ************ */

        document.querySelectorAll('[data-validation]').forEach(input => {
    input.addEventListener('blur', async function() {
        const formData = new FormData();
        const fieldName = this.name;
        formData.append('field', fieldName);
        
        if (this.type === 'file') {
            formData.append('value', this.files[0]);
        } else {
            formData.append('value', this.value);
        }

        const response = await fetch(`http://localhost/ManageStudents/Roles/validateField`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
                
        
        
        
        const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();
        
        // Remove existing error message if validation passed
        const existingError = this.parentNode.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        if (!result.success) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.style.color = 'red';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.style.marginTop = '0.25rem';
            errorDiv.textContent = cleanMessage;
            this.parentNode.appendChild(errorDiv);
        }
        
        
    });
});



    </script>
</body>
</html>