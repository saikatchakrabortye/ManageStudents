<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial,sans-serif;}
        .dashboard-container{display:flex;height:100vh;}
        .sidebar{width:250px;background:#0056b3;color:white;padding:20px;display:flex;flex-direction:column;}
        .profile-section{text-align:center;padding-bottom:20px;border-bottom:1px solid rgba(255,255,255,0.2);margin-bottom:20px;}
        .profile-pic{width:60px;height:60px;border-radius:50%;object-fit:cover;border:3px solid white;margin-bottom:10px;}
        .profile-pic-fallback{width:60px;height:60px;border-radius:50%;background:#07396bff;color:white;display:flex;align-items:center;justify-content:center;border:3px solid white;margin:0 auto 10px;font-weight:bold;font-size:18px;}
        .profile-name{font-weight:bold;margin-bottom:5px;}
        .profile-email{font-size:0.9em;opacity:0.8;margin-bottom:5px;}
        .profile-role{font-size:1rem;background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:12px;display:inline-block;}
        .sidebar a{display:block;color:white;padding:12px;text-decoration:none;margin-bottom:8px;border-radius:4px;transition:background 0.3s;}
        .sidebar a:hover,.sidebar a.active{background:#07396bff;}
        .logout-btn{background:red;margin-top:auto;max-width: 100px;}
        .logout-btn:hover{background:#c82333;}
        .main-content{flex:1;background:#f8f9fa;}
        iframe{width:100%;height:100%;border:none;}
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile-section" id="profileSection"></div>
            
            <a href="http://localhost/ManageStudents/Students" target="contentFrame" class="active">Students</a>
            <a href="http://localhost/ManageStudents/Users" target="contentFrame">Users</a>
            <a href="http://localhost/ManageStudents/Roles" target="contentFrame">Roles</a>
            <a href="http://localhost/ManageStudents/Permissions" target="contentFrame">Permissions</a>
            <a href="#" class="logout-btn" id="logoutBtn">Logout</a>
        </div>
        <div class="main-content">
            <iframe name="contentFrame" src="http://localhost/ManageStudents/Students"></iframe>
        </div>
    </div>

    <script>
        function getInitials(fullName) {
            return fullName ? fullName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : '';
        }

        async function loadProfileData() {
            try {
                const response = await fetch('http://localhost/ManageStudents/Users/getProfileData');
                const user = await response.json();
                
                const profilePic = user.profilePic ? 
                    `<img src="http://localhost/ManageStudents/uploads/profile_pics/users/${user.profilePic}" class="profile-pic">` :
                    `<div class="profile-pic-fallback">${getInitials(user.name)}</div>`;
                
                document.getElementById('profileSection').innerHTML = `
                    ${profilePic}
                    <div class="profile-name">${user.name || ''}</div>
                    <div class="profile-email">${user.email || ''}</div>
                    <div class="profile-role">${user.role || ''}</div>
                `;
            } catch (error) {
                document.getElementById('profileSection').innerHTML = `
                    <div class="profile-pic-fallback">?</div>
                    <div class="profile-name">User</div>
                    <div class="profile-email">Loading...</div>
                    <div class="profile-role">User</div>
                `;
            }
        }

        // Logout functionality
        document.getElementById('logoutBtn').addEventListener('click', async function(e) {
            e.preventDefault();
            try {
                const response = await fetch('http://localhost/ManageStudents/Users/logout');
                if (response.ok) {
                    window.location.href = 'http://localhost/ManageStudents/Login';
                }
            } catch (error) {
                window.location.href = 'http://localhost/ManageStudents/Login';
            }
        });

        document.addEventListener('DOMContentLoaded', loadProfileData);
    </script>
</body>
</html>