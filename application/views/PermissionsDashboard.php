<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Permissions Management</title>
        <style>
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

                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                }

                .permission-item {
                    margin-bottom: 15px;
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                }

                .permission-groups {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 20px;
                    margin-top: 20px;
                }

                .permission-group {
                    flex: 1 0 calc(25% - 20px); /* 4 cards per row */
                    min-width: 200px;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 15px;
                    background: #f9f9f9;
                }

                .permission-group h3 {
                    margin-top: 0;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 10px;
                }
                .permission-item label:not(.switch) {
    flex: 1; /* Allow text to take remaining space */
    line-height: 1.4; /* Better line spacing for multi-line text */
}
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Permissions Management</h1>
            <select name="roleId" id="roleSelect" onchange="showPermissionSection()">
                <option value="">Select Role</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role->id; ?>"><?php echo $role->name; ?></option>
                <?php endforeach; ?>
            </select>
        
                <!--Display Permission Section only after Role is selected-->
        <div id="permissionsSection" style="display: none;">
            <div class="permission-groups">
            <?php foreach($groupedPermissions as $groupName => $permissions): ?>
            <div class="permission-group">
            <h3><?= $groupName ?></h3>
            
            <?php foreach($permissions as $perm): ?>
                <div class="permission-item">
                <label class="switch">
                    <!--
                    If multiple checkboxes have same name like name="permissions", then only last checked value
                    is sent. Remaining are lost. with name="permissions[]", all checked values are collected and sent as an array.
                    -->
                    <input type="checkbox" name="permissions[]" value="<?= $perm->id ?>">
                    <span class="slider round"></span>
                </label><label><?= $perm->description ?></label><br>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        </div>
        </div>
        </div>

        <script>
            /** Function that show permission section only if a role is selected. Else hides the section */
            function showPermissionSection() {
                const roleSelect = document.getElementById('roleSelect');
                const permissionsSection = document.getElementById('permissionsSection');
                
                if(roleSelect.value !== '') {
                    permissionsSection.style.display = 'block';
                    updateCheckboxes(roleSelect.value); // Add this line
                } else {
                    permissionsSection.style.display = 'none';
                }
            }

            async function updateCheckboxes(roleId) {
            try {
                const response = await fetch(`Permissions/getRolePermissionsById/${roleId}`);
                const permissions = await response.json();
                
                // Uncheck all first
                document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                    cb.checked = false;
                });
                
                // Check the permission IDs from API
                permissions.forEach(perm => {
                    const checkbox = document.querySelector(`input[name="permissions[]"][value="${perm.permissionId}"]`);
                    if(checkbox) checkbox.checked = true;
                });
                
            } catch (error) {
                console.error('Error:', error);
            }
        }

            // Add onchange event to checkboxes
            function setupCheckboxEvents() {
                document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                    checkbox.onchange = function() {
                        const roleId = document.getElementById('roleSelect').value;
                        const permissionId = this.value;
                        
                        if (this.checked) {
                            activatePermission(roleId, permissionId);
                        } else {
                            deactivatePermission(roleId, permissionId);
                        }
                    };
                });
            }

            // Add these functions
            async function activatePermission(roleId, permissionId) {
                await fetch('Permissions/activatePermission', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({roleId, permissionId})
                });
            }

            async function deactivatePermission(roleId, permissionId) {
                await fetch('Permissions/deactivatePermission', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({roleId, permissionId})
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                setupCheckboxEvents();
            });
        
</script>
    </body>
</html>