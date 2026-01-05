<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #f8f9fa;
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
    </style>
</head>
<body>
    <div class="dashboardContainer">
    <div class="modal-content">
        <h2>Change Password</h2>
        <form id="changePasswordForm">
            <div class="form-group">
                    <input type="password" name="oldPassword" id="oldPassword" class="form-input" placeholder="" required>
                    <label class="form-label">Old Password</label>
                </div>
            
            <div class="form-group">
                    <input type="password" name="newPassword" id="newPassword" class="form-input" placeholder="" data-validation>
                    <label class="form-label">Set New Password</label>
                </div>
                <div class="form-group">
                    <input type="password" name="confirmPassword" id="confirmPassword" class="form-input" placeholder="" data-validation>
                    <label class="form-label">Confirm New Password</label>
                </div>
            <button type="submit"  id="updatePasswordBtn">Update</button>
            <!-- Add this div to show error message -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger mt-3">
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
    </div>
    <script>
        document.getElementById('changePasswordForm').addEventListener('submit', async function (e){
            e.preventDefault();
            
                const oldPassword = document.getElementById('oldPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                const employeeId = <?php echo $this->session->userdata('employeeId'); ?>;

                const formData = new FormData();
                formData.append('oldPassword', oldPassword);
                formData.append('newPassword', newPassword);
                formData.append('confirmPassword', confirmPassword);
                formData.append('employeeId', employeeId);
                try{
                const url = 'ChangePassword/changePassword';
                const response = await fetch(url, {method: 'POST', body: formData} );

                const result = await response.json();
                if (result.success)
                {
                    alert(result.message);
                        window.location.href = '<?php echo base_url("Employees/logout"); ?>';
                }
                else
                {
                    alert('Error: ' + result.message);
                }
                } catch (error)
                {
                    alert('Request Failed: ' + error.message);
                }
            
        });
    </script>
</body>
</html>