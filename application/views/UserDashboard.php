<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Dashboard</title>
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
        /** *************** Add user Modal Style Ends Here ********** */

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
    </style>
</head>
<body>
    <div style="padding: 2rem; text-align: center;">
        <h1>Users Dashboard</h1>
        
        <!--To show add form modal-->
        <button id="addUserModalBtn" class="submit-btn">Add User</button> 
    </div>

    <!--**********Show Users Table using CI3 Listing Style using foreach loop ***************************** -->
    <div class="table-container">    
    <table class="records-table" id="usersTable">
            <thead>
                <tr>
                    <th>Sl. No.</th>
                    <th>Profile</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>City</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $slCount=1; 
                foreach($users as $user): ?>
                    <tr>
                        <td><?php echo $slCount; ?></td> <!-- getting data from role object; roles is collection of objects -->
                        <td>
                            <?php if($user->profilePic): ?>
                                <img src="http://localhost/ManageStudents/uploads/profile_pics/users/<?php echo $user->profilePic ?>" alt="Profile" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                                <div style="width:40px;height:40px;border-radius:50%;background:#0056b3; color: white; display:flex;align-items:center;justify-content:center;">
                                    <?php echo getInitials($user->name); ?> <!--First Created Helper, Then loaded into Controller, Then used it here-->
                                </div>
                            <?php endif; ?>
                        </td>
                        <td onClick="viewUserDetails(<?php echo htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8'); ?>)"><b><?php echo $user->name; ?></b></td>
                        <td><?php echo $user->email; ?></td>
                        <td><?php echo $user->phone; ?></td>
                        <td><?php echo $user->roleName; ?></td>
                        <td><?php echo $user->cityName; ?></td>
                        <td><?php echo $user->status; ?></td>
                    </tr>
                <?php $slCount++; endforeach;?>
                <?php if(empty($users)): ?>
                    <tr>
                        <td colspan="7">No Users found.</td>
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
            $('#usersTable').DataTable();
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

    <!-- ************************* Show users Table ********************************************************* -->
    <!-- Table Container --
    <div class="table-container">
        <table id="usersTable" class="records-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="recordsBody"></tbody>
        </table>
    </div>
    <!-- Table Container Ends Here --
    <!-- ************************ Pagination Container **************** -
        <div class="pagination">
            <button id="prevBtn" class="page-btn">Previous</button>
            <span id="pageInfo" class="page-info">Page 1 of 1</span>
            <button id="nextBtn" class="page-btn">Next</button>
        </div>
    <!-- Pagination Container Ends Here -->

    <!-- ************************* Show users Table Ends Here ************************************************ -->

    <!-- ************************************  Add user Modal Structure ******************************* -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModalBtn">&times;</span>
            <h2>Add New user</h2>
            
            <form id="userForm" method="post" enctype="multipart/form-data">
                <div class="form-group">
                <select id="roleSelect" name="roleId" class="form-input" data-validation required>
                    <option value="null">Select Role</option>
                </select>
                <label class="form-label">Role</label>
                </div>    
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
                    <input type="date" name="dob" class="form-input" placeholder="" data-validation>
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
                
                <button type="submit" class="submit-btn">Enroll User</button>
                <div id="form-error-container"></div>
            </form>
        </div>
    </div>
    <!-- ********************************** Add user Modal Ends Here ************************************************ -->

    <!-- *********************************  View user Modal **************************************************** -->

<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeViewModalBtn">&times;</span>
        <h2>User Details</h2>
        <button type="button" id="editBtn" class="submit-btn" style="position: absolute; right: 60px; top: 20px;">Edit</button>
        <form id="viewUserForm" method="post" enctype="multipart/form-data" onsubmit="event.preventDefault(); updateUserDetails(this);">
            <img id="viewProfilePic" style="width:100px;height:100px;border-radius:50%; margin:20px;">
            <div id="viewProfileFallback" 
                style="width:100px;height:100px;border-radius:50%;background:#0056b3; color: white; display:none;align-items:center;justify-content:center; margin:20px; font-size:36px; font-weight:bold;">
            </div>
            
            <div class="form-group">
                <input type="text" id="viewName" name="name" class="form-input" placeholder="" data-validation readonly>
                <label class="form-label">Full Name</label>
            </div>
            
            <div class="form-group">
                <select id="viewRoleSelect" name="roleId" class="form-input" readonly disabled>
                    <option value="">Select Role</option>
                </select>
                <label class="form-label">Role</label>
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
                <input type="text" id="viewCityInput" placeholder="" class="form-input" readonly>
                <label class="form-label">City</label>
                <input type="hidden" id="viewSelectedCityId" name="cityId" value="">
                <div id="viewCityDropdown" class="dropdown">
                    <input type="text" id="viewCitySearch" placeholder="Search cities..." class="form-input" data-validation>
                    <div id="viewCityResults"></div>
                </div>
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
                <span><b>User ID:</b></span> <span id="viewId"></span>
                <span><b>Status: </b></span><span id="viewStatus"></span><br>
                <span><b>Created At:</b></span> <span id="viewCreatedAt"></span>
                <span><b>Updated At:</b></span> <span id="viewUpdatedAt"></span>
            </div>    
        </form>
        
        <label class="switch">
            <input type="checkbox" id="deactivate-cb" name="deactivate" onchange="activateDeactivateUser(this)">
            <span class="slider round"></span>
        </label>
        <label>Deactivate User</label><br>
    </div>
</div>
<!-- View user Modal Ends Here -->


    <script>
        // Get elements
        const userModal = document.getElementById('userModal');
        const adduserModalBtn = document.getElementById('adduserModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const userForm = document.getElementById('userForm');
        
        // Open modal
        addUserModalBtn.addEventListener('click', function() {
            userModal.style.display = 'flex';
        });
        
        // Close modal
        closeModalBtn.addEventListener('click', function() {
            userModal.style.display = 'none';
        });
        
        let roleMap = {};

        /** Code for loading Role ID and Role Name Mapping: will be used in displaying role name in users table  */
        // Fetch roles and create mapping
        async function loadRoleMap() {
            try {
                const response = await fetch('http://localhost/ManageStudents/Users/getRoles');
                const roles = await response.json();
                roles.forEach(role => {
                    roleMap[role.id] = role.name;
                });
            } catch (error) {
                console.error('Error loading roles:', error);
            }
        }
        /** Code for loading Role ID , Role Name Mapping ends here *************************** */

        /************************************Functions for View Details, Deactivate User, Update User ************ */
        // View Modal Elements
            const viewModal = document.getElementById('viewModal');
            const closeViewModalBtn = document.getElementById('closeViewModalBtn');

            // Close view modal
            closeViewModalBtn.addEventListener('click', function() {
                viewModal.style.display = 'none';
                document.getElementById('viewUserForm').reset();
            });

            function getInitials(name) {
                return name
                    .split(" ")
                    .map(n => n[0])
                    .join("")
                    .toUpperCase();
            }

            // Edit button functionality
            document.getElementById('editBtn').addEventListener('click', function() {
                // Remove readonly from all input fields
                const inputs = document.querySelectorAll('#viewUserForm input, #viewUserForm textarea, #viewUserForm select');
                inputs.forEach(input => {
                    input.removeAttribute('readonly');
                    input.disabled = false;
                });
                
                // Hide edit button, show update section
                this.style.display = 'none';
                document.getElementById('updateSection').style.display = 'block';
            });

            // View User Details
            async function viewUserDetails(user) {

                await loadRoles();
                // Populate modal fields
                if (user.profilePic) {
                    document.getElementById("viewProfilePic").src ="http://localhost/ManageStudents/uploads/profile_pics/users/" + user.profilePic;
                    document.getElementById("viewProfileFallback").style.display = "none";
                    document.getElementById("viewProfilePic").style.display = "block";
                } else {
                    document.getElementById("viewProfilePic").style.display = "none";
                    const fallback = document.getElementById("viewProfileFallback");
                    fallback.style.display = "flex";
                    fallback.innerText = getInitials(user.name);
                }
                
                document.getElementById('viewName').value = user.name;
                document.getElementById('viewEmail').value = user.email;
                document.getElementById('viewPhone').value = user.phone;
                document.getElementById('viewAddress').value = user.address;
                document.getElementById('viewCityInput').value = user.cityName || 'N/A';
                document.getElementById('viewSelectedCityId').value = user.cityId || '';
                document.getElementById('viewDob').value = user.dob;
                document.getElementById('viewPassword').value = user.password;
                document.getElementById('viewRoleSelect').value = user.roleId || '';

                

                // Set deactivate checkbox
                document.getElementById('deactivate-cb').value = user.id;

                if (user.status === 'inactive') {
                document.getElementById('deactivate-cb').checked = true;
            } else {
                document.getElementById('deactivate-cb').checked = false;
            }

                // Set info fields
                document.getElementById('viewId').textContent = user.id;
                document.getElementById('viewStatus').textContent = user.status;
                document.getElementById('viewCreatedAt').textContent = user.createdAt || 'N/A';
                document.getElementById('viewUpdatedAt').textContent = user.updatedAt || 'Not Updated Yet';
                
                viewModal.style.display = 'flex';
            }

            // Activate/Deactivate User
            async function activateDeactivateUser(checkbox) {
                const userId = checkbox.value;
                const status = checkbox.checked ? 'inactive' : 'active';
                
                try {
                    const formData = new FormData();
                    formData.append('userId', userId);
                    formData.append('status', status);
                    
                    const response = await fetch('Users/deactivateUser', {
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
                    checkbox.checked = !checkbox.checked;
                    alert('Error updating status');
                }
            }

            // Update User Details
            async function updateUserDetails(form) {
                const formData = new FormData(form);
                formData.append('userId', document.getElementById('viewId').textContent);

                try {
                    const response = await fetch('Users/updateUser', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    const cleanMessage = result.message.replace(/<p>|<\/p>/g, '').trim();

                    if (!result.success) {
                        alert('Update failed: ' + cleanMessage);
                    } else {
                        const modal = document.getElementById('viewModal');
                        modal.style.display = 'none';
                        window.location.reload();
                    }
                } catch (error) {
                    alert('Request failed: ' + error.message);
                }
            }

            // City Dropdown for View Modal
            let allCitiesView = [];

            document.getElementById('viewCityInput').addEventListener('focus', async function() {
                if (allCitiesView.length === 0) {
                    const response = await fetch('http://localhost/ManageStudents/Users/getCities');
                    allCitiesView = await response.json();
                }
                document.getElementById('viewCityDropdown').style.display = 'block';
                filterCitiesView();
            });

            document.getElementById('viewCitySearch').addEventListener('input', filterCitiesView);

            function filterCitiesView() {
                const search = document.getElementById('viewCitySearch').value.toLowerCase();
                const filtered = allCitiesView.filter(city => city.name.toLowerCase().includes(search));
                
                const results = document.getElementById('viewCityResults');
                results.innerHTML = filtered.length 
                    ? filtered.map(city => `<div class="city-item" data-id="${city.id}" data-name="${city.name}">${city.name}</div>`).join('')
                    : '<div style="padding: 10px; color: #666;">No cities found</div>';
                    
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


        /*****************************************Code ends here for View Details Modal and others **************** */

        

        /* *******************Javascript Code to Fetch users Data and Display that in Table***************** */
        
        /******* Below Code is without applying pagenation */
        /*async function loadusers() {
            const response = await fetch('http://localhost/ManageStudents/Users/getUsers');
            const users = await response.json(); // Converts JSON string to JS objects */
        /*******Only the above code changes to below block******** */

        /******* Below Code is with applying pagenation Pagenation Step (1/3)******** 
        let currentPage = 1;
        const itemsPerPage = 5; // Show 5 users per page
        async function loadUsers(page = 1) {
            const searchTerm = document.getElementById('searchInput').value; // Search Specific: Get search value
            const response = await fetch(`http://localhost/ManageStudents/Users/getUsers?page=${page}&limit=${itemsPerPage}&search=${encodeURIComponent(searchTerm)}`); // Modified url by add &search=${encodeURIComponent(searchTerm)}` for search functionality
            const data = await response.json(); // Expect {users: [], total: 100}
        /**Pagenation specific code ends here. Only after rendering users, we call updatePagenation() function as below 

            
            /* Display Profile Pic or Fallback to display Full Name Initials 
            document.querySelector('#usersTable tbody').innerHTML = 
            //users.map(s => `<tr><td>${s.id}</td> //when not using pagenation
            data.users.map(u => `<tr><td>${u.id}</td>
            <td>
                ${u.profilePic ? 
                `<img src="http://localhost/ManageStudents/uploads/profile_pics/users/${u.profilePic}" alt="Profile" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">` : 
                `<div style="width:40px;height:40px;border-radius:50%;background:#0056b3; color: white; display:flex;align-items:center;justify-content:center;">
                ${getInitials(u.name)}
                </div>`
                }
            </td>
            <td>${u.name}</td><td>${u.email}</td><td>${u.phone}</td><td>${roleMap[u.roleId] || 'N/A'}</td><td>${u.status}</td></tr>`).join('');

            updatePagination(data.total, page); // Update pagination controls; Pagenation Step (2/3)
        }
        /* ************************ Helper function to get initials from full name ******************** 
        function getInitials(fullName) {
            return fullName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        }
        /* ************************* End of Helper function ***************************** 

        /**********************  Helper Function needed for Pagenation ************************** 
        /**Front End of Pagenation only tracks current page and calculates offset 
        function updatePagination(totalUsers, currentPage) {
            const totalPages = Math.ceil(totalUsers / itemsPerPage);
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const pageInfo = document.getElementById('pageInfo');
            
            pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
            prevBtn.disabled = currentPage === 1; // Disable if on first page
            nextBtn.disabled = currentPage === totalPages; // Disable if on last page
            
            // Store current page for event handlers
            prevBtn.onclick = () => currentPage > 1 && loadUsers(currentPage - 1);
            nextBtn.onclick = () => currentPage < totalPages && loadUsers(currentPage + 1);
        }
        /********************** End of Helper Function needed for Pagenation ************************** 


        /* Search handler helper function
        function handleSearch() {
            currentPage = 1; // Reset to first page when searching
            loadUsers(1);
        }
        /**Search Handler Helper Funtion ends here 

        // Call on page load, if not using pagenation
        /*document.addEventListener('DOMContentLoaded', loadusers);

        // Initialize when page loads; use it when using pagenation, so start from page 1; Pagenation Step (3/3)
        document.addEventListener('DOMContentLoaded', async () => {
        await loadRoleMap(); // Load roles first 
        await loadRoles(); // Load roles for dropdown       
        loadUsers(1)}); // Then load users

        // NEW: Add event listener for search input
        document.getElementById('searchInput').addEventListener('input', handleSearch);

        /************** Javascript code for Fetching users data and loading in table ends here *********** */

        /*************************** City Dropdown with Search **************************** */
        // City dropdown functions
        let allCities = [];

        // Load cities on focus
        document.getElementById('cityInput').addEventListener('focus', async function() {
            if (allCities.length === 0) {
                const response = await fetch('http://localhost/ManageStudents/Users/getCities');
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

        /** ********************* Helper Function To load roles in Select Dropdown *************** */
        // Fetch roles from API and populate dropdown
        async function loadRoles() {
            try {
                const response = await fetch('http://localhost/ManageStudents/Users/getRoles');
                const roles = await response.json();
                
                const roleSelect = document.getElementById('roleSelect');
                
                // Clear existing options except the first one
                roleSelect.innerHTML = '<option value="">Select Role</option>';
                
                // Populate dropdown with roles and exclude admin having role_id=1
                roles
                .filter(role => role.id != 1)
                .forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id;
                    option.textContent = role.name;
                    roleSelect.appendChild(option);
                });

                // Populate VIEW form role dropdown (new functionality)
                    const viewRoleSelect = document.getElementById('viewRoleSelect');
                    viewRoleSelect.innerHTML = '<option value="">Select Role</option>';
                    roles.forEach(role => {
                        const option = document.createElement('option');
                        option.value = role.id;
                        option.textContent = role.name;
                        viewRoleSelect.appendChild(option);
                    });
                
            } catch (error) {
                console.error('Error loading roles:', error);
            }
        }

        
        // If using in modal, also call when modal opens:
        addUserModalBtn.addEventListener('click', loadRoles);

        
        /********************* End of code for laoding Roles in Dropdown **************************** */

        /***************** Code To Execute When Submit Button in Add user Form is clicked ************ */
        document.getElementById('userForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
        const response = await fetch(`http://localhost/ManageStudents/Users/addUser`, {
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
            const modal = document.getElementById('userModal');
            modal.style.display = 'none';
            
            // Reload the page to refresh the table
            window.location.reload();
            }
        } catch (error) {
            alert('Request failed: ' + error.message);
        }

        });
        /***************** Code ends here To Execute When Submit Button in Add user Form is clicked ************ */
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

        const response = await fetch(`http://localhost/ManageStudents/Users/validateField`, {
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