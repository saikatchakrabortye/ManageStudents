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
        'field' => 'city',
        'label' => 'City',
        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\-]+$/]'
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
        'field' => 'city',
        'label' => 'City',
        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\-]+$/]'
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
        'field' => 'city',
        'label' => 'City',
        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\-]+$/]'
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
        'field' => 'city',
        'label' => 'City',
        'rules' => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-Z\s\-]+$/]'
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