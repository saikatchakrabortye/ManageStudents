<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
    <div style="padding: 2rem; text-align: center;">
        <h1>Student Dashboard</h1>
        
        <!-- NEW: Search input -->
        <input type="text" id="searchInput" placeholder="Search by name, email or phone..." 
        style="margin-bottom: 20px; padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 4px;">
        <!--To show add form modal-->
        <button id="addStudentModalBtn" class="submit-btn">Add Student</button> 
    </div>

    <!-- ************************* Show Students Table ********************************************************* -->
    <!-- Table Container -->
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
    <!-- Table Container Ends Here -->
    <!-- ************************ Pagination Container **************** -->
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
                    <input type="text" name="name" class="form-input" placeholder="">
                    <label class="form-label">Full Name</label>
                </div>
                
                <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="">
                    <label class="form-label">Email Address</label>
                </div>
                
                <div class="form-group">
                    <input type="tel" name="phone" class="form-input" placeholder="">
                    <label class="form-label">Phone</label>
                </div>
                <div class="form-group">
                    <textarea id="address" name="address" class="form-input" placeholder="" rows="4"></textarea>
                    <label class="form-label">Address</label>
                </div>
                <div class="form-group">
                    <!--<input type="text" name="city" class="form-input" placeholder="">-->
                    <!-- ************** For City Dropdown with Search ***************** *-->
                    <input type="text" id="cityInput" name="city" placeholder="" class="form-input" readonly>
                    <label class="form-label">City</label>
                    <div id="cityDropdown" class="dropdown">
                        <input type="text" id="citySearch" placeholder="Search cities..." class="form-input">
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
                    <input type="password" name="password" id="password" class="form-input" placeholder="">
                    <label class="form-label">Set Password</label>
                </div>
                
                <button type="submit" class="submit-btn">Enroll Student</button>
            </form>
        </div>
    </div>
    <!-- ********************************** Add Student Modal Ends Here ************************************************ -->

    <!-- *********************************  View Student Modal **************************************************** -->
<div class="modal" id="viewModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-eye"></i> VIEW STUDENT</h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="modal-body view-mode">
            <div style="text-align: center; margin-bottom: 20px;">
                <img id="viewProfilePic" src="" alt="Profile" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary);">
            </div>
            <div class="form-group">
                <label for="viewName">Name</label>
                <input type="text" id="viewName" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="viewEmail">Email</label>
                <input type="text" id="viewEmail" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="viewPhone">Phone</label>
                <input type="text" id="viewPhone" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="viewAddress">Address</label>
                <textarea id="viewAddress" class="form-control" rows="3" readonly></textarea>
            </div>
            <div class="form-group">
                <label for="viewCity">City</label>
                <input type="text" id="viewCity" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="viewDob">Date of Birth</label>
                <input type="text" id="viewDob" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="viewStatus">Status</label>
                <input type="text" id="viewStatus" class="form-control" readonly>
            </div>
        </div>
        <div class="modal-footer">
            <div class="action-buttons">
                <button class="btn btn-danger" id="deleteStudentBtn">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <a href="#" class="btn btn-primary" id="editStudentBtn">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
<!-- View Student Modal Ends Here -->


    <script>
        // Get elements
        const studentModal = document.getElementById('studentModal');
        const addStudentModalBtn = document.getElementById('addStudentModalBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const studentForm = document.getElementById('studentForm');
        
        // Open modal
        addStudentModalBtn.addEventListener('click', function() {
            studentModal.style.display = 'flex';
        });
        
        // Close modal
        closeModalBtn.addEventListener('click', function() {
            studentModal.style.display = 'none';
        });
        
        

        /* *******************Javascript Code to Fetch Students Data and Display that in Table***************** */
        
        /******* Below Code is without applying pagenation */
        /*async function loadStudents() {
            const response = await fetch('http://localhost/ManageStudents/Students/getStudents');
            const students = await response.json(); // Converts JSON string to JS objects */
        /*******Only the above code changes to below block******** */

        /******* Below Code is with applying pagenation Pagenation Step (1/3)******** */
        let currentPage = 1;
        const itemsPerPage = 5; // Show 5 students per page
        async function loadStudents(page = 1) {
            const searchTerm = document.getElementById('searchInput').value; // Search Specific: Get search value
            const response = await fetch(`http://localhost/ManageStudents/Students/getStudents?page=${page}&limit=${itemsPerPage}&search=${encodeURIComponent(searchTerm)}`); // Modified url by add &search=${encodeURIComponent(searchTerm)}` for search functionality
            const data = await response.json(); // Expect {students: [], total: 100}
        /**Pagenation specific code ends here. Only after rendering students, we call updatePagenation() function as below */

            
            /* Display Profile Pic or Fallback to display Full Name Initials */
            document.querySelector('#studentsTable tbody').innerHTML = 
            //students.map(s => `<tr><td>${s.id}</td> //when not using pagenation
            data.students.map(s => `<tr><td>${s.id}</td>
            <td>
                ${s.profile_pic_id ? 
                `<img src="http://localhost/ManageStudents/uploads/profile_pics/students/${s.profile_pic_id}" alt="Profile" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">` : 
                `<div style="width:40px;height:40px;border-radius:50%;background:#0056b3; color: white; display:flex;align-items:center;justify-content:center;">
                ${getInitials(s.name)}
                </div>`
                }
            </td>
            <td>${s.name}</td><td>${s.email}</td><td>${s.phone}</td><td>${s.status}</td></tr>`).join('');

            updatePagination(data.total, page); // Update pagination controls; Pagenation Step (2/3)
        }
        /* ************************ Helper function to get initials from full name ******************** */
        function getInitials(fullName) {
            return fullName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        }
        /* ************************* End of Helper function ***************************** */

        /**********************  Helper Function needed for Pagenation ************************** */
        /**Front End of Pagenation only tracks current page and calculates offset */
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
        /********************** End of Helper Function needed for Pagenation ************************** */


        /* Search handler helper function*/
        function handleSearch() {
            currentPage = 1; // Reset to first page when searching
            loadStudents(1);
        }
        /**Search Handler Helper Funtion ends here */

        // Call on page load, if not using pagenation
        /*document.addEventListener('DOMContentLoaded', loadStudents);*/

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
                allCities = (await response.json()).map(c => c.name);
            }
            document.getElementById('cityDropdown').style.display = 'block';
            filterCities();
        });

        // Filter cities on typing
        document.getElementById('citySearch').addEventListener('input', filterCities);

        function filterCities() {
            const search = document.getElementById('citySearch').value.toLowerCase();
            const filtered = allCities.filter(city => city.toLowerCase().includes(search));
            
            const results = document.getElementById('cityResults');
            results.innerHTML = filtered.length 
                ? filtered.map(city => `<div onclick="selectCity('${city}')">${city}</div>`).join('')
                : '<div style="padding: 10px; color: #666;">No cities found</div>';
        }

        function selectCity(city) {
            document.getElementById('cityInput').value = city;
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
        alert(result.message);
        
        if (result.success) {
            // Redirect to student list after 1 second
            setTimeout(() => {
                window.location.href = `http://localhost/ManageStudents/Students`;
            }, 1000);
        }
        } catch (error) {
            alert('Request failed: ' + error.message);
        }

        });
        /***************** Code ends here To Execute When Submit Button in Add Student Form is clicked ************ */

    </script>
</body>
</html>