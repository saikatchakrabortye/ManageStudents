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
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .page-title {
            color: #333;
            margin: 0;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 180px;
        }
        
        .filter-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
            font-size: 13px;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
            background: white;
        }
        
        .form-control:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        .info-bar {
            background: #e9ecef;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: center;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            color: #212529;
        }
        
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
            background: white;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Dropdown Styles */
        .dropdown-container {
            position: relative;
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
            max-height: 250px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            margin-top: 2px;
        }
        
        .dropdown-search {
            width: 100%;
            padding: 8px 12px;
            border: none;
            border-bottom: 1px solid #dee2e6;
            font-size: 13px;
        }
        
        .dropdown-search:focus {
            outline: none;
        }
        
        .dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f5f5f5;
            font-size: 13px;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .project-name {
            font-weight: 500;
        }
        
        .project-id {
            font-size: 11px;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .info-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!----------------- Header ----------------->
        <div class="page-header">
            <h2 class="page-title">Monthly Project Cost Allocation</h2>
        </div>
        
        <!----------------- Filter Section ----------------->
        <div class="filter-section">
            <form id="allocationForm" method="GET" action="<?php echo base_url('MonthlyProjectCostAllocation'); ?>">
                <div class="filter-row">
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
                <span class="info-value"><?php echo date('F Y', strtotime("$year-$month-01")); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Project:</span>
                <span class="info-value"><?php echo htmlspecialchars($projectName ?? ''); ?></span>
            </div>
            <?php if (isset($projectPublicId)): ?>
            <div class="info-item">
                <span class="info-label">Project ID:</span>
                <span class="info-value"><?php echo htmlspecialchars($projectPublicId); ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($allocationData)): ?>
            <div class="info-item">
                <span class="info-label">Total Project Cost:</span>
                <span class="info-value" style="font-weight: 600; color: #007bff;">
                    ₹<?php echo number_format($totalProjectCost, 2); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!----------------- DataTable ----------------->
        <?php if (!empty($allocationData)): ?>
        <div style="overflow-x: auto;">
            <table id="allocationTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee</th>
                        <th>Monthly CTC (₹)</th>
                        <th>Project Effort (Hrs)</th>
                        <th>All Projects Effort (Hrs)</th>
                        <th>Cost Allocation (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalMonthlyCtc = 0;
                    $totalProjectEffort = 0;
                    $totalAllProjectsEffort = 0;
                    $totalCostAllocation = 0;
                    
                    foreach ($allocationData as $row): 
                        $totalMonthlyCtc += $row['monthlyCtc'];
                        $totalProjectEffort += $row['projectEffortHours'];
                        $totalAllProjectsEffort += $row['allProjectsEffortHours'];
                        $totalCostAllocation += $row['costAllocation'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['employeePublicId']); ?></td>
                        <td><?php echo htmlspecialchars($row['employeeName']); ?></td>
                        <td data-order="<?php echo $row['monthlyCtc']; ?>">₹<?php echo number_format($row['monthlyCtc'], 2); ?></td>
                        <td data-order="<?php echo $row['projectEffortHours']; ?>"><?php echo number_format($row['projectEffortHours'], 2); ?></td>
                        <td data-order="<?php echo $row['allProjectsEffortHours']; ?>"><?php echo number_format($row['allProjectsEffortHours'], 2); ?></td>
                        <td data-order="<?php echo $row['costAllocation']; ?>">₹<?php echo number_format($row['costAllocation'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #e7f3ff; font-weight: 600;">
                        <td colspan="2" style="text-align: right;"><strong>TOTAL</strong></td>
                        <td><strong>₹<?php echo number_format($totalMonthlyCtc, 2); ?></strong></td>
                        <td><strong><?php echo number_format($totalProjectEffort, 2); ?></strong></td>
                        <td><strong><?php echo number_format($totalAllProjectsEffort, 2); ?></strong></td>
                        <td><strong>₹<?php echo number_format($totalCostAllocation, 2); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php elseif (!empty($selectedProjectId) && empty($allocationData)): ?>
        <div class="no-data">
            <p>No effort data found for this project in <?php echo date('F Y', strtotime("$year-$month-01")); ?></p>
            <p style="font-size: 12px; color: #868e96; margin-top: 10px;">
                No employees have logged efforts for this project during the selected period.
            </p>
        </div>
        <?php endif; ?>
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
        });
        
        // Filter projects on typing
        document.getElementById('projectSearch').addEventListener('input', filterProjects);
        
        function filterProjects() {
            const search = document.getElementById('projectSearch').value.toLowerCase();
            const filtered = allProjects.filter(proj => 
                proj.name.toLowerCase().includes(search) ||  
                proj.publicId.toLowerCase().includes(search)
            );
            
            const results = document.getElementById('projectResults');
            if (filtered.length) {
                results.innerHTML = filtered.map(proj => 
                    `<div class="dropdown-item project-item" data-id="${proj.id}" data-name="${proj.name}" data-publicid="${proj.publicId}">
                        <div class="project-name">${proj.name}</div>
                        <div class="project-id">ID: ${proj.publicId}</div>
                    </div>`
                ).join('');
                
                // Add event listeners
                results.querySelectorAll('.dropdown-item.project-item').forEach(item => {
                    item.addEventListener('click', function() {
                        selectProject(this.dataset.id, this.dataset.name);
                    });
                });
            } else {
                results.innerHTML = '<div class="dropdown-item" style="color: #6c757d; font-style: italic;">No projects found</div>';
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
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "pageLength": 25,
                "order": [[5, 'desc']], // Default sort by Cost Allocation descending
                "language": {
                    "search": "Search employees:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "dom": '<"top"lf>rt<"bottom"ip><"clear">',
                "initComplete": function() {
                    // Add custom CSS to DataTables
                    $('.dataTables_length select').addClass('form-control');
                    $('.dataTables_filter input').addClass('form-control');
                }
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
    </script>
</body>
</html>