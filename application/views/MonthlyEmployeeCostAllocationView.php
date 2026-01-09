<!DOCTYPE html>
<html lang="en">
<head>
    <title>Monthly Employee Cost Allocation</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
    <style>
        /* Full width container */
        .main-container {
            width: 100%;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        
        /* Header styling */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
            width: 100%;
        }
        
        .page-title {
            color: #333;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        /* Filter section */
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            width: 100%;
            box-sizing: border-box;
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: flex-end;
            width: 100%;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            background: white;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        /* Info Bar - Larger text */
        .info-bar {
            background: #e9ecef;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #dee2e6;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 15px;
        }
        
        .info-value {
            color: #212529;
            font-size: 15px;
            font-weight: 500;
        }
        
        .ctc-value {
            color: #28a745;
            font-weight: 600;
            font-size: 15px;
        }
        
        .salary-value {
            color: #dc3545;
            font-weight: 600;
            font-size: 15px;
        }
        
        .period-value {
            color: #007bff;
            font-weight: 600;
            font-size: 15px;
        }
        
        /* No data message */
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            width: 100%;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }
        
        .no-data h3 {
            color: #6c757d;
            margin-bottom: 10px;
            font-size: 18px;
        }
        
        /* Dropdown Styles */
        .dropdown-container {
            position: relative;
            width: 100%;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ced4da;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-top: 2px;
        }
        
        .dropdown-search {
            width: 100%;
            padding: 10px 12px;
            border: none;
            border-bottom: 1px solid #dee2e6;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .dropdown-search:focus {
            outline: none;
            border-bottom-color: #80bdff;
        }
        
        .dropdown-item {
            padding: 10px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f5f5f5;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .employee-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .employee-details {
            font-size: 13px;
            color: #6c757d;
            margin-top: 2px;
        }
        
        /* DataTable Container */
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-top: 20px;
            border-radius: 8px;
            background: white;
            border: 1px solid #dee2e6;
        }
        
        /* Custom DataTable Styling */
        #allocationTable {
            width: 100% !important;
        }
        
        #allocationTable thead th {
            background: #2c3e50;
            color: white;
            font-weight: 600;
            padding: 12px 15px;
            font-size: 14px;
        }
        
        #allocationTable tbody td {
            padding: 12px 15px;
            font-size: 14px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        #allocationTable tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        #allocationTable tfoot td {
            background: #f8f9fa;
            font-weight: 700;
            padding: 12px 15px;
            font-size: 14px;
            border-top: 2px solid #dee2e6;
        }
        
        /* DataTable controls */
        .dataTables_wrapper {
            padding: 15px;
        }
        
        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 15px;
        }
        
        .dataTables_length select,
        .dataTables_filter input {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .dataTables_info {
            padding: 10px 0;
            font-size: 14px;
        }
        
        .dataTables_paginate {
            padding: 10px 0;
        }
        
        .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            color: #007bff;
        }
        
        .dataTables_paginate .paginate_button.current {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .filter-row {
                gap: 15px;
            }
            
            .filter-group {
                min-width: 180px;
            }
            
            .info-bar {
                gap: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .main-container {
                padding: 15px;
            }
            
            .page-title {
                font-size: 24px;
            }
            
            .filter-row {
                flex-direction: column;
                gap: 12px;
            }
            
            .filter-group {
                width: 100%;
                min-width: unset;
            }
            
            .info-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 15px;
            }
            
            .info-label,
            .info-value {
                font-size: 14px;
            }
        }
        
        @media (max-width: 480px) {
            .main-container {
                padding: 10px;
            }
            
            .page-title {
                font-size: 20px;
            }
            
            .filter-section {
                padding: 15px;
            }
            
            .form-control {
                padding: 8px 10px;
            }
            
            #allocationTable thead th,
            #allocationTable tbody td,
            #allocationTable tfoot td {
                padding: 8px 10px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboardContainer">
        <div class="main-container">
            <!----------------- Header ----------------->
            <div class="page-header">
                <h2 class="page-title">Monthly Employee Cost Allocation</h2>
            </div>
            
            <!----------------- Filter Section ----------------->
            <div class="filter-section">
                <form id="allocationForm" method="GET" action="<?php echo base_url('MonthlyEmployeeCostAllocation'); ?>">
                    <div class="filter-row">
                        
                        <!-- Year Select -->
                        <div class="filter-group">
                            <label class="filter-label">Year</label>
                            <select name="year" id="year" class="form-control">
                                <?php 
                                $currentYear = date('Y');
                                for($i = $currentYear - 5; $i <= $currentYear + 5; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $year == $i ? 'selected' : ''; ?>>
                                        <?php echo $i; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- Month Select -->
                        <div class="filter-group">
                            <label class="filter-label">Month</label>
                            <select name="month" id="month" class="form-control">
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo sprintf('%02d', $i); ?>" <?php echo $month == sprintf('%02d', $i) ? 'selected' : ''; ?>>
                                        <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <!-- Employee Dropdown -->
                        <div class="filter-group">
                            <label class="filter-label">Employee</label>
                            <div class="dropdown-container">
                                <input type="text" id="employeeSearchInput" class="form-control" 
                                       placeholder="Select employee..." 
                                       style="cursor: pointer;"
                                       value="<?php 
                                            if (isset($selectedEmployeeId) && !empty($selectedEmployeeId)) {
                                                foreach ($employees as $emp) {
                                                    if ($emp->id == $selectedEmployeeId) {
                                                        echo htmlspecialchars($emp->name);
                                                        break;
                                                    }
                                                }
                                            }
                                       ?>" readonly>
                                <input type="hidden" name="employeeId" id="selectedEmployeeId" 
                                       value="<?php echo $selectedEmployeeId ?? ''; ?>">
                                <div id="employeeDropdown" class="dropdown-content">
                                    <input type="text" id="employeeSearch" class="dropdown-search" placeholder="Search employees...">
                                    <div id="employeeResults"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!----------------- Info Bar ----------------->
            <?php if (!empty($selectedEmployeeId)): ?>
            <div class="info-bar">
                <div class="info-item">
                    <span class="info-label">Period:</span>
                    <span class="info-value period-value"><?php echo date('F Y', strtotime("$year-$month-01")); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Employee:</span>
                    <span class="info-value"><?php echo htmlspecialchars($employeeName ?? ''); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Designation:</span>
                    <span class="info-value"><?php echo htmlspecialchars($designationName ?? 'Not specified'); ?></span>
                </div>
                <?php if (!empty($employeeCtc)): ?>
                <div class="info-item">
                    <span class="info-label">CTC:</span>
                    <span class="info-value ctc-value">₹<?php echo number_format($employeeCtc, 2); ?></span>
                </div>
                <?php endif; ?>
                <?php // if (isset($monthlySalary) && $monthlySalary > 0): ?>
                <!--<div class="info-item">
                    <span class="info-label">Monthly Salary:</span>
                    <span class="info-value salary-value">₹<?php // echo number_format($monthlySalary, 2); ?></span>
                </div>-->
                <?php // endif; ?>
            </div>
            <?php endif; ?>
            
            <!----------------- DataTable ----------------->
            <?php if (!empty($allocationData)): ?>
            <div class="table-container">
                <table id="allocationTable" class="display" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="text-align: left;">Project</th>
                            <th style="text-align: center;">Effort (Hours:Mins)</th>
                            <th style="text-align: center;">Effort %</th>
                            <th style="text-align: right;">Cost Allocation (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalEffortMinutes = 0;
                        $totalEffortPercentageTable = 0;
                        $totalCostTable = 0;
                        
                        foreach ($allocationData as $row): 
                            $totalEffortMinutes += $row['effortMinutes'];
                            $totalEffortPercentageTable += $row['effortPercentage'];
                            $totalCostTable += $row['costAllocation'];
                        ?>
                        <tr>
                            <td style="text-align: left;"><?php echo htmlspecialchars($row['projectName']); ?></td>
                            <td style="text-align: center;" data-order="<?php echo $row['effortMinutes']; ?>">
                                <?php echo $row['effortHours']; ?>
                            </td>
                            <td style="text-align: center;" data-order="<?php echo $row['effortPercentage']; ?>">
                                <?php echo number_format($row['effortPercentage'], 2); ?>%
                            </td>
                            <td style="text-align: right;" data-order="<?php echo $row['costAllocation']; ?>">
                                ₹<?php echo number_format($row['costAllocation'], 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td style="text-align: right; padding-right: 20px;"><strong>TOTAL</strong></td>
                            <td style="text-align: center; font-weight: 700;">
                                <?php 
                                $totalHours = floor($totalEffortMinutes / 60);
                                $totalMinutes = $totalEffortMinutes % 60;
                                echo sprintf("%d:%02d", $totalHours, $totalMinutes);
                                ?>
                            </td>
                            <td style="text-align: center; font-weight: 700; color: #007bff;">
                                <?php echo number_format($totalEffortPercentageTable, 2); ?>%
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #28a745;">
                                ₹<?php echo number_format($totalCostTable, 2); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <?php elseif (!empty($selectedEmployeeId) && empty($allocationData)): ?>
            <div class="no-data">
                <h3>No Effort Data Found</h3>
                <p style="font-size: 16px; color: #868e96;">
                    <?php echo htmlspecialchars($employeeName); ?> has no logged efforts for <?php echo date('F Y', strtotime("$year-$month-01")); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- DataTables -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <script>
        // ========== EMPLOYEE DROPDOWN FUNCTIONALITY ==========
        let allEmployees = <?php echo json_encode($employees ?? []); ?>;
        
        // Load employees on focus
        document.getElementById('employeeSearchInput').addEventListener('click', function() {
            document.getElementById('employeeDropdown').style.display = 'block';
            filterEmployees();
            setTimeout(() => {
                document.getElementById('employeeSearch').focus();
            }, 10);
        });
        
        // Filter employees on typing
        document.getElementById('employeeSearch').addEventListener('input', filterEmployees);
        
        function filterEmployees() {
            const search = document.getElementById('employeeSearch').value.toLowerCase();
            const filtered = allEmployees.filter(emp => 
                emp.name.toLowerCase().includes(search) ||  
                emp.publicId.toLowerCase().includes(search) ||
                (emp.designationName && emp.designationName.toLowerCase().includes(search))
            );
            
            const results = document.getElementById('employeeResults');
            if (filtered.length) {
                results.innerHTML = filtered.map(emp => 
                    `<div class="dropdown-item employee-item" data-id="${emp.id}" data-name="${emp.name}" data-publicid="${emp.publicId}">
                        <div class="employee-name">${emp.name}</div>
                        <div class="employee-details">ID: ${emp.publicId} | ${emp.designationName || 'No designation'}</div>
                    </div>`
                ).join('');
                
                // Add event listeners
                results.querySelectorAll('.dropdown-item.employee-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectEmployee(this.dataset.id, this.dataset.name);
                    });
                });
            } else {
                results.innerHTML = '<div class="dropdown-item" style="color: #6c757d; font-style: italic; text-align: center;">No employees found</div>';
            }
        }
        
        function selectEmployee(employeeId, employeeName) {
            // Update the input field
            document.getElementById('employeeSearchInput').value = employeeName;
            document.getElementById('selectedEmployeeId').value = employeeId;
            
            // Hide dropdown and clear search
            document.getElementById('employeeDropdown').style.display = 'none';
            document.getElementById('employeeSearch').value = '';
            
            // Submit form automatically
            document.getElementById('allocationForm').submit();
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const employeeDropdown = document.getElementById('employeeDropdown');
            const employeeInput = document.getElementById('employeeSearchInput');
            
            if (!e.target.closest('#employeeDropdown') && e.target !== employeeInput) {
                employeeDropdown.style.display = 'none';
            }
        });
        
        // ========== DATATABLE INITIALIZATION ==========
        <?php if (!empty($allocationData)): ?>
        $(document).ready(function() {
            $('#allocationTable').DataTable({
                "paging": true,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true,
                "order": [[3, 'desc']], // Default sort by Cost Allocation descending
                "language": {
                    "search": "Search projects:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "zeroRecords": "No matching projects found",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "dom": '<"top"lf>rt<"bottom"ip><"clear">'
            });
        });
        <?php endif; ?>
        
        // ========== AUTO-SUBMIT ON MONTH/YEAR CHANGE ==========
        document.getElementById('month').addEventListener('change', function() {
            if (document.getElementById('selectedEmployeeId').value) {
                document.getElementById('allocationForm').submit();
            }
        });
        
        document.getElementById('year').addEventListener('change', function() {
            if (document.getElementById('selectedEmployeeId').value) {
                document.getElementById('allocationForm').submit();
            }
        });
        
        // ========== ENTER KEY IN EMPLOYEE SEARCH ==========
        document.getElementById('employeeSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstItem = document.querySelector('.employee-item');
                if (firstItem) {
                    firstItem.click();
                }
            }
        });
    </script>
</body>
</html>