<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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

        /****Style for Viewing Details */
        .info {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 6px;
            width: fit-content;
            background: #fafafa;
        }
        .info div {
            margin-bottom: 6px;
        }
        /**Style for viewing details ends here */
        
        /**Style for toggle button */
        /* The switch - the box around the slider */
            .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
            flex-shrink: 0; /* Prevent switch from shrinking */
            }

            /* Hide default HTML checkbox */
            .switch input { 
            opacity: 0;
            width: 0;
            height: 0;
            }

            /* The slider */
            .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
            }

            .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            }

            input:checked + .slider {
                background-color: #2196F3;
                }

                input:focus + .slider {
                box-shadow: 0 0 1px #2196F3;
                }

                input:checked + .slider:before {
                -webkit-transform: translateX(26px);
                -ms-transform: translateX(26px);
                transform: translateX(26px);
                }

                /* Rounded sliders */
                .slider.round {
                border-radius: 34px;
                }

                .slider.round:before {
                border-radius: 50%;
                }
        /**Style for toggle button ends here */
    </style>
</head>
<body>
    <div style="padding: 2rem; text-align: center;">
        <h1>Student Dashboard</h1>
        
        
        <!--To show add form modal-->
        <?php if(checkPermission('student.create')): ?>
        <button id="addStudentModalBtn" class="submit-btn">Add Student</button> 
        <?php endif; ?>
        
    </div>
    
    <!--**********Show Students Table using CI3 Listing Style using foreach loop ***************************** -->
    <div class="table-container">    
    <table class="records-table" id="studentsTable">
            <thead>
                <tr>
                    <th>Sl. No.</th>
                    <th>Profile</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Created By</th>
                </tr>
            </thead>
            <tbody>
                <?php $slCount=1; foreach($students as $student): ?>
                    <tr>
                        <td><?php echo $slCount; ?></td> <!-- getting data from role object; roles is collection of objects -->
                        <td>
                            <?php if($student->profilePic): ?>
                                <img src="http://localhost/ManageStudents/uploads/profile_pics/students/<?php echo $student->profilePic ?>" alt="Profile" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                                <div style="width:40px;height:40px;border-radius:50%;background:#0056b3; color: white; display:flex;align-items:center;justify-content:center;">
                                    <?php echo getInitials($student->name); ?> <!--First Created Helper, Then loaded into Controller, Then used it here-->
                                </div>
                            <?php endif; ?>
                        </td>
                        <td 
                        <?php if(checkPermission('student.view')): ?>
                        onClick="viewStudentDetails(<?php echo htmlspecialchars(json_encode($student), ENT_QUOTES, 'UTF-8'); ?>)"
                        <?php endif; ?>
                        >
                        <b><?php echo $student->name; ?></b></td>

                        <td><?php echo $student->email; ?></td>
                        <td><?php echo $student->phone; ?></td>
                        <td><?php echo $student->cityName; ?></td>
                        <td><?php echo $student->status; ?></td>
                        <td><?php echo $student->createdByEmail; ?></td>
                    </tr>
                <?php $slCount++; endforeach;?>
                <?php if(empty($students)): ?>
                    <tr>
                        <td colspan="7">No Students found.</td>
                    </tr>
                <?php endif;?>
            </tbody>

        </table>
    </div>
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
    <div class="pagination">
            <!-- Previous Button -->
            <?php if($currentPage > 1): ?>
                <a href="?page=<?= $currentPage - 1 ?>" class="page-btn">Previous</a>
            <?php endif; ?>
    
            <span class="page-info">Page <?= $currentPage ?> of <?= $totalPages ?></span>
            
            <!-- Next Button -->
            <?php if($currentPage < $totalPages): ?>
                <a href="?page=<?= $currentPage + 1 ?>" class="page-btn">Next</a>
            <?php endif; ?>
    </div>
    <!--My Code for Listing using CI3 ends here-->

    <!-- ************************* Show Students Table ********************************************************* -->
    <!-- Table Container --
    <div class="table-container">
        <table id="studentsTable" class="records-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="recordsBody"></tbody>
        </table>
    </div>
    <!-- Table Container Ends Here --
    <!-- ************************ Pagination Container **************** --
        <div class="pagination">
            <button id="prevBtn" class="page-btn">Previous</button>
            <span id="pageInfo" class="page-info">Page 1 of 1</span>
            <button id="nextBtn" class="page-btn">Next</button>
        </div>
    <!-- Pagination Container Ends Here -->

    <!-- ************************* Show Students Table Ends Here ************************************************ -->

    <!-- ************************************  Add Student Modal Structure ******************************* -->
    <div id="studentModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModalBtn">&times;</span>
            <h2>Add New Student</h2>
            
            <form id="studentForm" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="name" class="form-input" placeholder="" data-validation>
                    <label class="form-label">Full Name</label>
                </div>
                
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="" data-validation>
                    <label class="form-label">Email Address</label>
                </div>
                
                <div class="form-group">
                    <input type="tel" name="phone" class="form-input" placeholder="" data-validation>
                    <label class="form-label">Phone</label>
                </div>
                <div class="form-group">
                    <textarea id="address" name="address" class="form-input" placeholder="" rows="4" data-validation></textarea>
                    <label class="form-label">Address</label>
                </div>
                <div class="form-group">
                    <!--<input type="text" name="city" class="form-input" placeholder="">-->
                    <!-- ************** For City Dropdown with Search ***************** *-->
                    <input type="text" id="cityInput" placeholder="" class="form-input" readonly>
                    <label class="form-label">City</label>
                    <input type="hidden" id="selectedCityId" name="cityId" value="">
                    <div id="cityDropdown" class="dropdown">
                        <input type="text" id="citySearch" placeholder="Search cities..." class="form-input" data-validation>
                        <div id="cityResults"></div>
                    </div>
                    <!--City dropdown with search container ends here-->
                    
                </div>
                <div class="form-group">
                    <input type="date" name="dob" class="form-input" placeholder="">
                    <label class="form-label">Birth Date</label>
                </div>
                <div class="form-group">
                    <input type="file" id="profile_pic" name="profile_pic" class="form-input" accept="image/*" placeholder="">
                    <label class="form-label">Upload Profile Picture</label>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-input" placeholder="" data-validation>
                    <label class="form-label">Set Password</label>
                </div>
                
                <button type="submit" class="submit-btn">Enroll Student</button>
                <div id="form-error-container"></div>
            </form>
        </div>
    </div>
    <!-- ********************************** Add Student Modal Ends Here ************************************************ -->



    
        <!--<input type="text" id="viewName" readonly>-->
    <!-- *********************************  View Student Modal **************************************************** -->
        <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeViewModalBtn">&times;</span>
            <h2>Student Details</h2>
            <?php if(checkPermission('student.update')): ?>
            <button type="button" id="editBtn" class="submit-btn" style="position: absolute; right: 60px; top: 20px;">Edit</button>
            <?php endif; ?>
            <form id="viewStudentForm" method="post" enctype="multipart/form-data" onsubmit="event.preventDefault(); updateStudentDetails(this);">
                <img id="viewProfilePic" style="width:100px;height:100px;border-radius:50%; margin:20px;">
                <div id="viewProfileFallback" 
                    style="width:100px;height:100px;border-radius:50%;background:#0056b3; color: white; display:none;align-items:center;justify-content:center; margin:20px; font-size:36px; font-weight:bold;">
                </div>
                <div class="form-group">
                    <input type="text" id="viewName" name="name" class="form-input" placeholder="" data-validation readonly>
                    <label class="form-label">Full Name</label>
                </div>
                <div class="form-group">
                    <input type="email" id="viewEmail" name="email" class="form-input" placeholder="" data-validation readonly>
                    <label class="form-label">Email Address</label>
                </div>
                
                <div class="form-group">
                    <input type="tel" id="viewPhone" name="phone" class="form-input" placeholder="" data-validation readonly>
                    <label class="form-label">Phone</label>
                </div>
                <div class="form-group">
                    <textarea id="viewAddress" name="address" class="form-input" placeholder="" rows="4" data-validation readonly></textarea>
                    <label class="form-label">Address</label>
                </div>
                <div class="form-group">
                    <!--<input type="text" name="city" class="form-input" placeholder="">-->
                    <!-- ************** For City Dropdown with Search ***************** *-->
                    <input type="text" id="viewCityInput" placeholder="" class="form-input" readonly>
                    <label class="form-label">City</label>
                    <input type="hidden" id="viewSelectedCityId" name="cityId" value="">
                    <div id="viewCityDropdown" class="dropdown">
                        <input type="text" id="viewCitySearch" placeholder="Search cities..." class="form-input" data-validation>
                        <div id="viewCityResults"></div>
                    </div>
                    <!--City dropdown with search container ends here-->
                    
                </div>
                <div class="form-group">
                    <input type="date" id="viewDob" name="dob" class="form-input" placeholder="" data-validation readonly>
                    <label class="form-label">Birth Date</label>
                </div>
                
                <div class="form-group">
                    <input type="password" id="viewPassword" name="password" class="form-input" placeholder="" data-validation readonly>
                    <label class="form-label">Password</label>
                </div>
                <div class="form-actions" style="display: none; margin-top: 20px;" id="updateSection">
                    <button type="submit" class="submit-btn">Update Changes</button>
                </div>
                <div class="info">
                    <span><b>Student ID:</b></span> <span id="viewId"></span>
                    <span><b>Status: </b></span><span id="viewStatus"></span><br>
                    <span><b>Created By:</b></span> <span id="viewCreatedBy"></span>
                    <span><b>Created At:</b></span> <span id="viewCreatedAt"></span><br>
                    <span><b>Updated By:</b></span> <span id="viewUpdatedBy"></span>
                    <span><b>Updated At:</b></span> <span id="viewUpdatedAt"></span>
                </div>    
            </form>
            <?php if(checkPermission('student.delete')): ?>
            <label class="switch">
                    <input type="checkbox" id="deactivate-cb" name="deactivate" onchange="activateDeactivateStudent(this)">
                    <span class="slider round"></span>
                </label><label>Deactivate Student</label><br>
            <?php endif; ?>
        </div>
    </div>
<!-- View Student Modal Ends Here -->


    <script>
        // Get elements
        const studentModal = document.getElementById('studentModal');
        const studentForm = document.getElementById('studentForm');

        const viewModal = document.getElementById('viewModal');
        
        // Use event delegation for the add student button
        document.addEventListener('click', function(e) {
            // If the clicked element is the add student button
            if (e.target && e.target.id === 'addStudentModalBtn') {
                document.getElementById('studentModal').style.display = 'flex';
            }
        });

        // Close modal buttons (these always exist in the DOM)
        document.getElementById('closeModalBtn').addEventListener('click', function() {
            document.getElementById('studentModal').style.display = 'none';
            document.getElementById('studentForm').reset();
        });

        document.getElementById('closeViewModalBtn').addEventListener('click', function() {
            document.getElementById('viewModal').style.display = 'none';
            document.getElementById('viewStudentForm').reset();
        });

        function getInitials(name) {
            return name
                .split(" ")
                .map(n => n[0])
                .join("")
                .toUpperCase();
        }

        document.getElementById('editBtn').addEventListener('click', function() {
            // Remove readonly from all input fields
            const inputs = document.querySelectorAll('#viewStudentForm input, #viewStudentForm textarea');
            inputs.forEach(input => {
                input.removeAttribute('readonly');
            });
            
            // Hide edit button, show update section
            this.style.display = 'none';
            document.getElementById('updateSection').style.display = 'block';
        });

        function viewStudentDetails(student) {
            // Populate modal fields

            const img = document.getElementById('viewProfilePic');

            if (student.profilePic) {
                document.getElementById("viewProfilePic").src =
                    BASE_URL + "uploads/profile_pics/students/" + student.profilePic;

                document.getElementById("viewProfileFallback").style.display = "none";
                document.getElementById("viewProfilePic").style.display = "block";
            } else {
                document.getElementById("viewProfilePic").style.display = "none";
                const fallback = document.getElementById("viewProfileFallback");
                fallback.style.display = "flex";
                fallback.innerText = getInitials(student.name);
            }
            document.getElementById('viewName').value = student.name;
            document.getElementById('viewEmail').value = student.email;
            document.getElementById('viewPhone').value = student.phone;
            document.getElementById('viewAddress').value = student.address;
            document.getElementById('viewCityInput').value = student.cityName || 'N/A';
            document.getElementById('viewSelectedCityId').value = student.cityId || '';
            document.getElementById('viewDob').value = student.dob;
            
            document.getElementById('viewPassword').value = student.password;

            
            
            // FIX: Check if deactivate checkbox exists before using it
            const deactivateCheckbox = document.getElementById('deactivate-cb'); // Set checkbox value to student ID for using to deactivate student
            if (deactivateCheckbox) {
                deactivateCheckbox.value = student.id;

                if (student.status === 'inactive') {
                    deactivateCheckbox.checked = true;
                } else {
                    deactivateCheckbox.checked = false;
                }
            }

            document.getElementById('viewId').textContent = student.id;
            document.getElementById('viewStatus').textContent = student.status;
            document.getElementById('viewCreatedBy').textContent = student.createdByUserId || 'N/A';
            document.getElementById('viewCreatedAt').textContent = student.createdAt || 'N/A';
            document.getElementById('viewUpdatedBy').textContent = student.updatedByUserId || 'Not Upated Yet';
            document.getElementById('viewUpdatedAt').textContent = student.updatedAt || 'Not Updated Yet';
            viewModal.style.display = 'flex';
        }
        
        async function activateDeactivateStudent(checkbox) {
            const studentId = checkbox.value;
            const status = checkbox.checked ? 'inactive' : 'active';
            
            try {
                const formData = new FormData();
                formData.append('studentId', studentId);
                formData.append('status', status);
                
                const response = await fetch('Students/deactivateStudent', {
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
                checkbox.parentNode.querySelector('.toggle-status').textContent = 
                    checkbox.checked ? 'Inactive' : 'Active';
                alert('Error updating status');
            }
        }

        async function updateStudentDetails(form) {
            const formData = new FormData(form);
            formData.append('studentId', document.getElementById('viewId').textContent);

            try {
                const response = await fetch('Students/updateStudent', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();

                if (!result.success) {
                    alert('Update failed: ' + cleanMessage);
                } else {
                    // Success case - close modal and reload page
                    const modal = document.getElementById('viewModal');
                    modal.style.display = 'none';
                    
                    // Reload the page to refresh the table
                    window.location.reload();
                }
            } catch (error) {
                alert('Request failed: ' + error.message);
            }
        }
        

        /* *******************Javascript Code to Fetch Students Data and Display that in Table***************** */
        
        /******* Below Code is without applying pagenation */
        /*async function loadStudents() {
            const response = await fetch('http://localhost/ManageStudents/Students/getStudents');
            const students = await response.json(); // Converts JSON string to JS objects */
        /*******Only the above code changes to below block******** */

        /******* Below Code is with applying pagenation Pagenation Step (1/3)******** 
        let currentPage = 1;
        const itemsPerPage = 5; // Show 5 students per page
        async function loadStudents(page = 1) {
            const searchTerm = document.getElementById('searchInput').value; // Search Specific: Get search value
            const response = await fetch(`http://localhost/ManageStudents/Students/getStudents?page=${page}&limit=${itemsPerPage}&search=${encodeURIComponent(searchTerm)}`); // Modified url by add &search=${encodeURIComponent(searchTerm)}` for search functionality
            const data = await response.json(); // Expect {students: [], total: 100}
        /**Pagenation specific code ends here. Only after rendering students, we call updatePagenation() function as below 

            
            /* Display Profile Pic or Fallback to display Full Name Initials 
            document.querySelector('#studentsTable tbody').innerHTML = 
            //students.map(s => `<tr><td>${s.id}</td> //when not using pagenation
            data.students.map(s => `<tr><td>${s.id}</td>
            <td>
                ${s.profilePic ? 
                `<img src="http://localhost/ManageStudents/uploads/profile_pics/students/${s.profilePic}" alt="Profile" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">` : 
                `<div style="width:40px;height:40px;border-radius:50%;background:#0056b3; color: white; display:flex;align-items:center;justify-content:center;">
                ${getInitials(s.name)}
                </div>`
                }
            </td>
            <td>${s.name}</td><td>${s.email}</td><td>${s.phone}</td><td>${s.status}</td></tr>`).join('');

            updatePagination(data.total, page); // Update pagination controls; Pagenation Step (2/3)
        }
        /* ************************ Helper function to get initials from full name ******************** 
        function getInitials(fullName) {
            return fullName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        }
        /* ************************* End of Helper function ***************************** 

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
        document.getElementById('searchInput').addEventListener('input', handleSearch);

        /************** Javascript code for Fetching Students data and loading in table ends here *********** */

        /*************************** City Dropdown with Search **************************** */
        // City dropdown functions
        let allCities = [];

        // Load cities on focus
        document.getElementById('cityInput').addEventListener('focus', async function() {
            if (allCities.length === 0) {
                const response = await fetch('http://localhost/ManageStudents/Students/getCities');
                allCities = await response.json();
            }
            document.getElementById('cityDropdown').style.display = 'block';
            filterCities();
        });

        // Filter cities on typing
        document.getElementById('citySearch').addEventListener('input', filterCities);

        function filterCities() {
            const search = document.getElementById('citySearch').value.toLowerCase();
            const filtered = allCities.filter(city => city.name.toLowerCase().includes(search));
            
            const results = document.getElementById('cityResults');
            results.innerHTML = filtered.length 
                ? filtered.map(city => `<div class="city-item" data-id="${city.id}" data-name="${city.name}">${city.name}</div>`).join('')
                : '<div style="padding: 10px; color: #666;">No cities found</div>';
                // Add event listeners
    results.querySelectorAll('.city-item').forEach(item => {
        item.addEventListener('click', function() {
            selectCity(this.dataset.id, this.dataset.name);
        });
    });
        }

        function selectCity(cityId, cityName) {
        
            document.getElementById('cityInput').value = cityName;
            document.getElementById('selectedCityId').value = cityId;
            document.getElementById('cityDropdown').style.display = 'none';
            document.getElementById('citySearch').value = '';
        }


        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#cityDropdown') && e.target.id !== 'cityInput') {
                document.getElementById('cityDropdown').style.display = 'none';
            }
        });
        /****************************** Code ends here for dropdown with search ******************* */

        /*************************** City Dropdown with Search for VIEW MODAL **************************** */
            // City dropdown functions for view modal
            let allCitiesView = [];

            // Load cities on focus for view modal
            document.getElementById('viewCityInput').addEventListener('focus', async function() {
                if (allCitiesView.length === 0) {
                    const response = await fetch('http://localhost/ManageStudents/Students/getCities');
                    allCitiesView = await response.json();
                }
                document.getElementById('viewCityDropdown').style.display = 'block';
                filterCitiesView();
            });

            // Filter cities on typing for view modal
            document.getElementById('viewCitySearch').addEventListener('input', filterCitiesView);

            function filterCitiesView() {
                const search = document.getElementById('viewCitySearch').value.toLowerCase();
                const filtered = allCitiesView.filter(city => city.name.toLowerCase().includes(search));
                
                const results = document.getElementById('viewCityResults');
                results.innerHTML = filtered.length 
                    ? filtered.map(city => `<div class="city-item" data-id="${city.id}" data-name="${city.name}">${city.name}</div>`).join('')
                    : '<div style="padding: 10px; color: #666;">No cities found</div>';
                    
                // Add event listeners
                results.querySelectorAll('.city-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectCityView(this.dataset.id, this.dataset.name);
                    });
                });
            }

            function selectCityView(cityId, cityName) {
                document.getElementById('viewCityInput').value = cityName;
                document.getElementById('viewSelectedCityId').value = cityId;
                document.getElementById('viewCityDropdown').style.display = 'none';
                document.getElementById('viewCitySearch').value = '';
            }

            // Close dropdown when clicking outside for view modal
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#viewCityDropdown') && e.target.id !== 'viewCityInput') {
                    document.getElementById('viewCityDropdown').style.display = 'none';
                }
            });
    /****************************** Code ends here for dropdown with search in view modal ******************* */

        /***************** Code To Execute When Submit Button in Add Student Form is clicked ************ */
        document.getElementById('studentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
        const response = await fetch(`http://localhost/ManageStudents/Students/addStudent`, {
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
            const modal = document.getElementById('studentModal');
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

        const response = await fetch(`http://localhost/ManageStudents/Students/validateField`, {
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