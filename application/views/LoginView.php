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
    <div class="modal-content">
        <h2>Login</h2>
        <form method="post" action="<?php echo base_url('Login/authenticate'); ?>">
            <div class="form-group">
                    <input type="email" name="email" class="form-input" placeholder="" required>
                    <label class="form-label">Email Address</label>
                </div>
            
            <div class="form-group">
                    <input type="password" name="password" id="password" class="form-input" placeholder="" data-validation>
                    <label class="form-label">Enter Password</label>
                </div>
            <button type="submit" class="submit-btn">Login</button>
            <!-- Add this div to show error message -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger mt-3">
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <script>
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

        const response = await fetch(`http://localhost/ManageStudents/Login/validateField`, {
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