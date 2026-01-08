<!DOCTYPE html>
<html lang="en">
<head>
    <title>Monthly Project Cost Allocation</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/styles.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
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
        
        /* Info Bar */
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
        
        .period-value {
            color: #007bff;
            font-weight: 600;
            font-size: 15px;
        }
        
        .project-value {
            color: #28a745;
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
        
        .project-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .project-id {
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
            white-space: nowrap;
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
        
        /* Column specific alignments */
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
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
            
            #allocationTable {
                font-size: 13px;
            }
            
            #allocationTable thead th,
            #allocationTable tbody td {
                padding: 8px 10px;
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
        }

        /* Force footer alignment */
#allocationTable tfoot td.text-right {
    text-align: right !important;
    padding-right: 20px !important;
}
    </style>
</head>
<body>
    <div class="dashboardContainer">
    <div class="main-container">
        <!----------------- Header ----------------->
        <div class="page-header">
            <h2 class="page-title">Monthly Project Cost Allocation</h2>
        </div>
        
        <!----------------- Filter Section ----------------->
        <div class="filter-section">
            <form id="allocationForm" method="GET" action="<?php echo base_url('MonthlyProjectCostAllocation'); ?>">
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
                    
                    <!-- Project Dropdown -->
                    <div class="filter-group">
                        <label class="filter-label">Project</label>
                        <div class="dropdown-container">
                            <input type="text" id="projectSearchInput" class="form-control" 
                                   placeholder="Select project..." 
                                   style="cursor: pointer;"
                                   value="<?php 
                                        if (isset($selectedProjectId) && !empty($selectedProjectId)) {
                                            foreach ($projects as $proj) {
                                                if ($proj->id == $selectedProjectId) {
                                                    echo htmlspecialchars($proj->name);
                                                    break;
                                                }
                                            }
                                        }
                                   ?>" readonly>
                            <input type="hidden" name="projectId" id="selectedProjectId" 
                                   value="<?php echo $selectedProjectId ?? ''; ?>">
                            <div id="projectDropdown" class="dropdown-content">
                                <input type="text" id="projectSearch" class="dropdown-search" placeholder="Search projects...">
                                <div id="projectResults"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!----------------- Info Bar ----------------->
        <?php if (!empty($selectedProjectId)): ?>
        <div class="info-bar">
            <div class="info-item">
                <span class="info-label">Period:</span>
                <span class="info-value period-value"><?php echo date('F Y', strtotime("$year-$month-01")); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Project:</span>
                <span class="info-value project-value"><?php echo htmlspecialchars($projectName ?? ''); ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <!----------------- DataTable ----------------->
<?php if (!empty($allocationData)): ?>
<div class="table-container">
    <table id="allocationTable" class="display" style="width:100%;">
        <thead>
            <tr>
                <th class="text-left">Employee</th>
                <th class="text-right">CTC (Monthly)</th>
                <th class="text-center">Effort (Hours/Mins)</th>
                <th class="text-center">Effort %</th>
                <th class="text-right">Cost Allocation (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalMonthlyCtc = 0;
            $totalEffortMinutes = 0;
            $totalCostAllocation = 0;
            
            foreach ($allocationData as $row): 
                $totalMonthlyCtc += $row['monthlyCtc'];
                $totalEffortMinutes += $row['projectEffortMinutes'];
                $totalCostAllocation += $row['costAllocation'];
            ?>
            <tr>
                <td class="text-left"><?php echo htmlspecialchars($row['employeeName']); ?></td>
                <td class="text-right" data-order="<?php echo $row['monthlyCtc']; ?>">
                    ₹<?php echo number_format($row['monthlyCtc'], 2); ?>
                </td>
                <td class="text-center" data-order="<?php echo $row['projectEffortMinutes']; ?>">
                    <?php echo $row['projectEffortDisplay']; ?>
                </td>
                <td class="text-center" data-order="<?php echo $row['effortPercentage']; ?>">
                    <?php echo number_format($row['effortPercentage'], 2); ?>%
                </td>
                <td class="text-right" data-order="<?php echo $row['costAllocation']; ?>">
                    ₹<?php echo number_format($row['costAllocation'], 2); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="text-right" style="padding-right: 20px;"><strong>TOTAL</strong></td>
                <td class="text-right" style="font-weight: 700;">₹<?php echo number_format($totalMonthlyCtc, 2); ?></td>
                <td class="text-center" style="font-weight: 700;">
                    <?php 
                    $totalHours = floor($totalEffortMinutes / 60);
                    $totalMinutes = $totalEffortMinutes % 60;
                    echo sprintf("%dh / %02dm", $totalHours, $totalMinutes);
                    ?>
                </td>
                <td class="text-center" style="font-weight: 700;">-</td>
                <td class="text-right" style="font-weight: 700; color: #28a745;">
                    ₹<?php echo number_format($totalCostAllocation, 2); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
        
        <?php elseif (!empty($selectedProjectId) && empty($allocationData)): ?>
        <div class="no-data">
            <h3>No Effort Data Found</h3>
            <p style="font-size: 16px; color: #868e96;">
                No employees have logged efforts for <?php echo htmlspecialchars($projectName); ?> 
                in <?php echo date('F Y', strtotime("$year-$month-01")); ?>
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
        // ========== PROJECT DROPDOWN FUNCTIONALITY ==========
        let allProjects = <?php echo json_encode($projects ?? []); ?>;
        
        // Load projects on focus
        document.getElementById('projectSearchInput').addEventListener('click', function() {
            document.getElementById('projectDropdown').style.display = 'block';
            filterProjects();
            setTimeout(() => {
                document.getElementById('projectSearch').focus();
            }, 10);
        });
        
        // Filter projects on typing
        document.getElementById('projectSearch').addEventListener('input', filterProjects);
        
        function filterProjects() {
            const search = document.getElementById('projectSearch').value.toLowerCase();
            const filtered = allProjects.filter(proj => 
                proj.name.toLowerCase().includes(search) ||  
                (proj.publicId && proj.publicId.toLowerCase().includes(search))
            );
            
            const results = document.getElementById('projectResults');
            if (filtered.length) {
                results.innerHTML = filtered.map(proj => 
                    `<div class="dropdown-item project-item" data-id="${proj.id}" data-name="${proj.name}" data-publicid="${proj.publicId || ''}">
                        <div class="project-name">${proj.name}</div>
                        ${proj.publicId ? `<div class="project-id">ID: ${proj.publicId}</div>` : ''}
                    </div>`
                ).join('');
                
                // Add event listeners
                results.querySelectorAll('.dropdown-item.project-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectProject(this.dataset.id, this.dataset.name);
                    });
                });
            } else {
                results.innerHTML = '<div class="dropdown-item" style="color: #6c757d; font-style: italic; text-align: center;">No projects found</div>';
            }
        }
        
        function selectProject(projectId, projectName) {
            // Update the input field
            document.getElementById('projectSearchInput').value = projectName;
            document.getElementById('selectedProjectId').value = projectId;
            
            // Hide dropdown and clear search
            document.getElementById('projectDropdown').style.display = 'none';
            document.getElementById('projectSearch').value = '';
            
            // Submit form automatically
            document.getElementById('allocationForm').submit();
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const projectDropdown = document.getElementById('projectDropdown');
            const projectInput = document.getElementById('projectSearchInput');
            
            if (!e.target.closest('#projectDropdown') && e.target !== projectInput) {
                projectDropdown.style.display = 'none';
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
                "order": [[4, 'desc']], // Default sort by Cost Allocation descending
                "language": {
                    "search": "Search employees:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "zeroRecords": "No matching employees found",
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
            if (document.getElementById('selectedProjectId').value) {
                document.getElementById('allocationForm').submit();
            }
        });
        
        document.getElementById('year').addEventListener('change', function() {
            if (document.getElementById('selectedProjectId').value) {
                document.getElementById('allocationForm').submit();
            }
        });
        
        // ========== ENTER KEY IN PROJECT SEARCH ==========
        document.getElementById('projectSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstItem = document.querySelector('.project-item');
                if (firstItem) {
                    firstItem.click();
                }
            }
        });
    </script>
</body>
</html>