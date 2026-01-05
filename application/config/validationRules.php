<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['addUser'] = [
    [
        'field' => 'roleId',
        'label' => 'Role',
        'rules' => 'required|integer'
    ],
    [
        'field' => 'name',
        'label' => 'Full Name',
        'rules' => 'required|min_length[6]|max_length[30]|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]'
    ],
    [
        'field' => 'email',
        'label' => 'Email Address',
        'rules' => 'required|valid_email|max_length[50]'
    ],
    [
        'field' => 'phone',
        'label' => 'Phone Number',
        'rules' => 'required|regex_match[/^\+?[1-9]\d{1,14}$/]|min_length[10]|max_length[10]'
    ],
    [
        'field' => 'address',
        'label' => 'Address',
        'rules' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]'
    ],
    [
        'field' => 'cityId',
        'label' => 'City',
        'rules' => 'required'
    ],
    [
        'field' => 'dob',
        'label' => 'Date of Birth',
        'rules' => 'required'
    ],
    [
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'required|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        /**The errors array allow us to set custom validation messages */
        'errors' => [
        'regex_match' => 'Password should contain at least 1 Capital Letter, Small Letter, Number, and Special Characters like @$!%*?&'
    ]
    ]
];

$config['editUser'] = [
    [
        'field' => 'roleId',
        'label' => 'Role',
        'rules' => 'required|integer'
    ],
    [
        'field' => 'name',
        'label' => 'Full Name',
        'rules' => 'required|min_length[6]|max_length[30]|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]'
    ],
    [
        'field' => 'email',
        'label' => 'Email Address',
        'rules' => 'required|valid_email|max_length[50]'
    ],
    [
        'field' => 'phone',
        'label' => 'Phone Number',
        'rules' => 'required|regex_match[/^\+?[1-9]\d{1,14}$/]|min_length[10]|max_length[10]'
    ],
    [
        'field' => 'address',
        'label' => 'Address',
        'rules' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]'
    ],
    [
        'field' => 'cityId',
        'label' => 'City',
        'rules' => 'required'
    ],
    [
        'field' => 'dob',
        'label' => 'Date of Birth',
        'rules' => 'required'
    ],
    [
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        'errors' => [
        'regex_match' => 'Password should contain at least 1 Capital Letter, Small Letter, Number, and Special Characters like @$!%*?&'
    ]
    ],
    [
        'field' => 'status',
        'label' => 'Status',
        'rules' => 'in_list[active,inactive]'
    ]
];

$config['addStudent'] = [
    [
        'field' => 'name',
        'label' => 'Full Name',
        'rules' => 'required|min_length[6]|max_length[30]|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]'
    ],
    [
        'field' => 'email',
        'label' => 'Email Address',
        'rules' => 'required|valid_email|max_length[50]'
    ],
    [
        'field' => 'phone',
        'label' => 'Phone Number',
        'rules' => 'required|regex_match[/^\+?[1-9]\d{1,14}$/]|min_length[10]|max_length[10]'
    ],
    [
        'field' => 'address',
        'label' => 'Address',
        'rules' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]'
    ],
    [
        'field' => 'cityId',
        'label' => 'City',
        'rules' => 'required'
    ],
    [
        'field' => 'dob',
        'label' => 'Date of Birth',
        'rules' => 'required'
    ],
    [
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'required|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        'errors' => [
        'regex_match' => 'Password should contain at least 1 Capital Letter, Small Letter, Number, and Special Characters like @$!%*?&'
    ]
    ]
];

$config['editStudent'] = [
    [
        'field' => 'name',
        'label' => 'Full Name',
        'rules' => 'required|min_length[6]|max_length[30]|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]'
    ],
    [
        'field' => 'email',
        'label' => 'Email Address',
        'rules' => 'required|valid_email|max_length[50]'
    ],
    [
        'field' => 'phone',
        'label' => 'Phone Number',
        'rules' => 'required|regex_match[/^\+?[1-9]\d{1,14}$/]|min_length[10]|max_length[10]'
    ],
    [
        'field' => 'address',
        'label' => 'Address',
        'rules' => 'required|min_length[10]|max_length[255]|regex_match[/^[a-zA-Z0-9\s\-\.,#]+$/]'
    ],
    [
        'field' => 'cityId',
        'label' => 'City',
        'rules' => 'required'
    ],
    [
        'field' => 'dob',
        'label' => 'Date of Birth',
        'rules' => 'required'
    ],
    [
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'required|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        'errors' => [
        'regex_match' => 'Password should contain at least 1 Capital Letter, Small Letter, Number, and Special Characters like @$!%*?&'
    ]
    ],
    [
        'field' => 'status',
        'label' => 'Status',
        'rules' => 'in_list[active,inactive]'
    ]
];

$config['addRole'] = [
    [
        'field' => 'roleName',
        'label' => 'Role Name',
        'rules' => 'required|min_length[3]|max_length[50]|regex_match[/^[a-zA-Z\s]+$/]'
    ],
    [
        'field' => 'description',
        'label' => 'Description',
        'rules' => 'required|min_length[10]|max_length[255]'
    ]
];

$config['addEmployee'] = [
    [
        'field' => 'name',
        'label' => 'Employee Name',
        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\.]+$/]'
    ],
    [
        'field' => 'gender',
        'label' => 'Gender',
        'rules' => 'required|in_list[M,F,TRANS,NTD]'
    ],
    [
        'field' => 'dob',
        'label' => 'Date of Birth',
        'rules' => 'required'
    ],
    [
        'field' => 'phone',
        'label' => 'Phone Number',
        'rules' => 'required|numeric|exact_length[10]|is_unique[employees.phone]'
    ],
    [
        'field' => 'email',
        'label' => 'Email Address',
        'rules' => 'required|valid_email|max_length[50]|is_unique[employees.email]'
    ],
    [
        'field' => 'joiningDate',
        'label' => 'Date of Joining',
        'rules' => 'required'
    ],
    [
        'field' => 'designationId',
        'label' => 'Designation',
        'rules' => 'required|numeric'
    ],
    [
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'required|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        /**The errors array allow us to set custom validation messages */
        'errors' => [
        'regex_match' => 'Password should contain at least 1 Capital Letter, Small Letter, Number, and Special Characters like @$!%*?&'
    ]
    ]
];

$config['updateEmployee'] = [
    [
        'field' => 'name',
        'label' => 'Employee Name',
        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\.]+$/]'
    ],
    [
        'field' => 'gender',
        'label' => 'Gender',
        'rules' => 'required|in_list[M,F,TRANS,NTD]'
    ],
    [
        'field' => 'dob',
        'label' => 'Date of Birth',
        'rules' => 'required'
    ],
    [
        'field' => 'phone',
        'label' => 'Phone Number',
        'rules' => 'required|numeric|exact_length[10]' // Removed is_unique rules for phone and email - because during update, the existing record should be excluded from uniqueness checks
    ],
    [
        'field' => 'email',
        'label' => 'Email Address',
        'rules' => 'required|valid_email|max_length[50]'
    ],
    [
        'field' => 'joiningDate',
        'label' => 'Date of Joining',
        'rules' => 'required'
    ],
    [
        'field' => 'designationId',
        'label' => 'Designation',
        'rules' => 'required|numeric'
    ],
    [
        'field' => 'password',
        'label' => 'Password',
        'rules' => 'required|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        /**The errors array allow us to set custom validation messages */
        'errors' => [
        'regex_match' => 'Password should contain at least 1 Capital Letter, Small Letter, Number, and Special Characters like @$!%*?&'
    ]
    ]
];

$config['addDesignation'] = [
    [
        'field' => 'name',
        'label' => 'Designation Name',
        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\0-9\-]+$/]|is_unique[designations.name]'
    ]
    /*[
        'field' => 'status',
        'label' => 'Status',
        'rules' => 'required|in_list[active,inactive]'
    ]*/
];

$config['updateDesignation'] = [
    [
        'field' => 'name',
        'label' => 'Designation Name',
        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\0-9\-]+$/]'
    ],
    [
        'field' => 'status',
        'label' => 'Status',
        'rules' => 'required|in_list[active,inactive]'
    ]
];

$config['addClient'] = [
    [
        'field' => 'name',
        'label' => 'Client Name',
        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\.]+$/]'
    ]
];

$config['addProject'] = [
    [
        'field' => 'name',
        'label' => 'Project Name',
        'rules' => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-Z\s\.]+$/]'
    ],
    [
        'field' => 'clientId',
        'label' => 'Client ID',
        'rules' => 'required'
    ],
    [
        'field' => 'startDate',
        'label' => 'Start Date',
        'rules' => 'required'
    ]
];

$config['updateProject'] = [
    [
        'field' => 'name',
        'label' => 'Project Name',
        'rules' => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-Z\s\.]+$/]'
    ],
    [
        'field' => 'clientId',
        'label' => 'Client ID',
        'rules' => 'required'
    ],
    [
        'field' => 'startDate',
        'label' => 'Start Date',
        'rules' => 'required'
    ]
];

$config['changePassword'] = [
    [
        'field' => 'oldPassword',
        'label' => 'Old Password',
        'rules' => 'required|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        /**The errors array allow us to set custom validation messages */
        'errors' => [
        'regex_match' => 'Old Password should contain at least 1 Capital Letter, Small Letter, Number, and Special Characters like @$!%*?&'
    ]
    ],
    [
        'field' => 'newPassword',
        'label' => 'New Password',
        'rules' => 'required|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        /**The errors array allow us to set custom validation messages */
        'errors' => [
        'regex_match' => 'New Password should contain at least 1 Capital Letter, Small Letter, Number, and Special Characters like @$!%*?&'
    ]
    ],
    [
        'field' => 'confirmPassword',
        'label' => 'Confirm Password',
        'rules' => 'required|min_length[8]|max_length[255]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
        /**The errors array allow us to set custom validation messages */
        'errors' => [
        'regex_match' => 'Confirm Password should contain at least 1 Capital Letter, Small Letter, Number, and Special Characters like @$!%*?&'
    ]
    ],
    [
        'field' => 'employeeId',
        'label' => 'Employee ID',
        'rules' => 'required'
    ]
];