<!DOCTYPE html>
<html lang="en">
<head>
    <title>Projects Cost Summary</title>
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
        
        .form-control:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-reset {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-reset:hover {
            background: #545b62;
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
        
        .cost-value {
            color: #dc3545;
            font-weight: 600;
            font-size: 15px;
        }
        
        .projects-count {
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
        
        /* Error message */
        .error-message {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
            font-size: 14px;
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
        #summaryTable {
            width: 100% !important;
        }
        
        #summaryTable thead th {
            background: #2c3e50;
            color: white;
            font-weight: 600;
            padding: 12px 15px;
            font-size: 14px;
        }
        
        #summaryTable tbody td {
            padding: 12px 15px;
            font-size: 14px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        #summaryTable tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        #summaryTable tfoot td {
            background: #f8f9fa;
            font-weight: 700;
            padding: 12px 15px;
            font-size: 14px;
            border-top: 2px solid #dee2e6;
        }
        
        /* Cost column styling */
        .cost-cell {
            font-family: 'Courier New', monospace;
            font-weight: 600;
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
        
        /* Column alignments */
        #summaryTable th:first-child,
        #summaryTable td:first-child {
            text-align: left;
        }
        
        #summaryTable th:nth-child(2),
        #summaryTable td:nth-child(2),
        #summaryTable th:nth-child(3),
        #summaryTable td:nth-child(3) {
            text-align: right;
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
            
            #summaryTable thead th,
            #summaryTable tbody td,
            #summaryTable tfoot td {
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
            <h2 class="page-title">Projects Cost Summary</h2>
        </div>
        
        <!----------------- Filter Section ----------------->
        <div class="filter-section">
            <form id="summaryForm" method="GET" action="<?php echo base_url('ProjectsCostSummary'); ?>">
                <div class="filter-row">
                    
                    <!-- Project Dropdown -->
                    <div class="filter-group">
                        <label class="filter-label">Select Project (Optional)</label>
                        <div class="dropdown-container">
                            <input type="text" id="projectSearchInput" class="form-control" 
                                   placeholder="All projects..." 
                                   style="cursor: pointer;"
                                   value="<?php 
                                        if (isset($selectedProjectId) && !empty($selectedProjectId)) {
                                            foreach ($projects as $proj) {
                                                if ($proj->id == $selectedProjectId) {
                                                    echo htmlspecialchars($proj->name . ' (' . $proj->publicId . ')');
                                                    break;
                                                }
                                            }
                                        } else {
                                            echo 'All Projects';
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
                    
                    <!-- Upto Date -->
                    <div class="filter-group">
                        <label class="filter-label">Upto Date</label>
                        <input type="date" name="uptoDate" id="uptoDate" class="form-control" 
                               value="<?php echo $uptoDate ?? date('Y-m-d'); ?>">
                    </div>
                    
                    <!-- Clear Selection -->
                    <?php if (!empty($selectedProjectId)): ?>
                    <div class="filter-group" style="flex: none; min-width: 120px;">
                        <label class="filter-label" style="visibility: hidden;">Button</label>
                        <a href="<?php echo base_url('ProjectsCostSummary'); ?>" class="btn-reset">
                            Clear Filter
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Search Button -->
                    <div class="filter-group" style="flex: none; min-width: 100px;">
                        <label class="filter-label" style="visibility: hidden;">Button</label>
                        <button type="submit" class="btn-primary">
                            Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!----------------- Info Bar ----------------->
        <div class="info-bar" style="display: none;">
            <div class="info-item">
                <span class="info-label">Report Date:</span>
                <span class="info-value period-value">
                    <?php 
                    echo date('d-m-Y', strtotime($uptoDate ?? date('Y-m-d')));
                    ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Projects:</span>
                <span class="info-value projects-count">
                    <?php 
                    if (!empty($selectedProjectId)) {
                        echo '1 Selected';
                    } else {
                        echo count($summaryData) . ' Projects';
                    }
                    ?>
                </span>
            </div>
        </div>
        
        <!----------------- DataTable ----------------->
        <?php if (!empty($summaryData)): ?>
        <div class="table-container">
            <table id="summaryTable" class="display" style="width:100%;">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Total Effort (Hours:Mins)</th>
                        <th>Total Cost (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summaryData as $row): ?>
                    <tr>
                        <td>
                            <div class="project-name"><?php echo htmlspecialchars($row['projectName']); ?></div>
                            <div class="project-id">ID: <?php echo htmlspecialchars($row['projectPublicId']); ?></div>
                        </td>
                        <td data-order="<?php echo $row['totalEffortMinutes']; ?>" style="text-align: center;">
                            <?php echo $row['totalEffort']; ?>
                        </td>
                        <td data-order="<?php echo $row['totalCost']; ?>" class="cost-cell">
                            ₹<?php echo number_format($row['totalCost'], 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="text-align: right; padding-right: 20px;">
                            <strong>GRAND TOTAL</strong>
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            <?php 
                            $totalHours = floor($totalEffortMinutes / 60);
                            $totalMinutes = $totalEffortMinutes % 60;
                            echo sprintf("%d:%02d", $totalHours, $totalMinutes);
                            ?>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #dc3545;">
                            ₹<?php echo number_format($totalCost, 2); ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php elseif (empty($summaryData) && !empty($selectedProjectId)): ?>
        <div class="no-data">
            <h3>No Project Found</h3>
            <p style="font-size: 16px; color: #868e96;">
                No active project found with the selected criteria.
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
        
        // Add "All Projects" option
        allProjects.unshift({
            id: '',
            name: 'All Projects',
            publicId: ''
        });
        
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
                        selectProject(this.dataset.id, this.dataset.name, this.dataset.publicid);
                    });
                });
            } else {
                results.innerHTML = '<div class="dropdown-item" style="color: #6c757d; font-style: italic; text-align: center;">No projects found</div>';
            }
        }
        
        function selectProject(projectId, projectName, projectPublicId) {
            // Update the input field
            if (projectId === '') {
                document.getElementById('projectSearchInput').value = 'All Projects';
            } else {
                document.getElementById('projectSearchInput').value = `${projectName} (${projectPublicId})`;
            }
            document.getElementById('selectedProjectId').value = projectId;
            
            // Hide dropdown and clear search
            document.getElementById('projectDropdown').style.display = 'none';
            document.getElementById('projectSearch').value = '';
            
            // Submit form automatically
            document.getElementById('summaryForm').submit();
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            const projectDropdown = document.getElementById('projectDropdown');
            const projectInput = document.getElementById('projectSearchInput');
            
            if (!e.target.closest('#projectDropdown') && e.target !== projectInput) {
                projectDropdown.style.display = 'none';
            }
        });
        
        // ========== DATE VALIDATION ==========
        document.getElementById('summaryForm').addEventListener('submit', function(e) {
            const uptoDate = document.getElementById('uptoDate').value;
            const today = new Date().toISOString().split('T')[0];
            
            if (uptoDate) {
                if (new Date(uptoDate) > new Date(today)) {
                    e.preventDefault();
                    alert('Upto date cannot be in the future');
                    return false;
                }
            }
            return true;
        });
        
        // Set max date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('uptoDate').max = today;
        
        // ========== DATATABLE INITIALIZATION ==========
        <?php if (!empty($summaryData)): ?>
        $(document).ready(function() {
            $('#summaryTable').DataTable({
                "paging": true,
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true,
                "order": [[2, 'desc']], // Default sort by Total Cost descending
                "language": {
                    "search": "Search projects:",
                    "lengthMenu": "Show _MENU_ projects",
                    "info": "Showing _START_ to _END_ of _TOTAL_ projects",
                    "infoEmpty": "Showing 0 to 0 of 0 projects",
                    "infoFiltered": "(filtered from _MAX_ total projects)",
                    "zeroRecords": "No matching projects found",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "dom": '<"top"lf>rt<"bottom"ip><"clear">',
                "columnDefs": [
                    {
                        "targets": [0],
                        "orderable": true
                    },
                    {
                        "targets": [1, 2],
                        "orderable": true,
                        "className": "dt-right"
                    }
                ]
            });
        });
        <?php endif; ?>
        
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