<?php

return [
    'pusher_channel' => [
        'local' => 'guiuan-notifications-development',
        'production' => 'guiuan-notifications-production'
    ],
    'pusher_app_id' => '1913792',
    'pusher_app_key' => 'c7e327995adf1690cbcc',
    'pusher_app_secret' => '3464b3c7c83878e63dda',
    'company_name' => 'Guiuan Lending Loan System',
    'company_powered_by' => 'Copyright 2024 © Powered by A.J Solutions Inc.',
    'default_country' => 'Philippines',
    'default_country_id' => 137,
    'suffix_list' => [
        1 => 'Jr.',
        2 => 'Sr.',
        3 => 'II - The Second.',
        4 => 'III - The Third',
        5 => 'IV - The Fourth',
        6 => 'V - The Fifth'
    ],
    'civil_status' => [
        1 => 'Single',
        2 => 'Married',
        3 => 'Divorced',
        4 => 'Widowed',
        5 => 'Separated',
        6 => 'Annuled',
        7 => 'Engaged'
    ],
    'common_document_type' => [
        'jpg',
        'png',
        'xlsx',
        'pdf',
        'csv'
    ],
    'employee_relation' => [
        1 => 'Father',
        2 => 'Mother',
        3 => 'Sibling',
        4 => 'Spouse/Husband',
        5 => 'Son',
        6 => 'Daughter'
    ],

    'requirement_type' => [
        1 => 'ATM Card',
        2 => '2 Valid IDs (Xerox)',
        3 => '2 Pcs ID Picture',
        4 => 'Bank Statement',
        5 => 'Dated Payslip',
        6 => 'Cedula',
        7 => 'Birth Certificate',
        8 => 'Voucher for Pensioners',
        9 => 'SPA (Special Power of Attorney)'
    ],
    'client_relation' => [
        1 => 'Colleagues',
        2 => 'Friend',
        3 => 'Sibling',
        4 => 'Spouse/Husband'
    ],
    'genders' => [
        1 => 'Male',
        2 => 'Female'
    ],

    'status_value' => [
        0 => 'Inactive',
        1 => 'Active'
    ],
    'status_badge' => [
        0 => 'badge badge-danger text-white',
        1 => 'badge badge-success text-white'
    ],
    'nationality_value' => [
        1 => 'Filipino',
        2 => 'American',
        3 => 'German',
        4 => 'Australian'
    ],

    'session_emp_key' => 'emp_is_login',
    'session_emp_id' => 'emp_user_id',
    'session_emp_name' => 'emp_display_name',
    'session_username' => 'emp_username',
    'session_profile_picture' => 'emp_profile_picture',
    'status_types' => array(
        'enabled' => '1',
        'disabled' => '0'
    ),
    'designation' => [
        0 => 'Supervisor',
        1 => 'Staff',
        2 => 'Manager',      // Example for index 2
        3 => 'Administrator'
    ],
    'default_area_code' => '+63',
    'default_currency_code' => 'PHP',
    'default_currency_symbol' => '₱',

    'expense_categories' => [
        1 => [
            'name' => 'Rent Expense',
            'scope' => 'Pertain to office rent.'
        ],
        2 => [
            'name' => 'Notarial Fee',
            'scope' => 'Payment for Notarial services'
        ],
        3 => [
            'name' => 'Repair and Maintenance Expense',
            'scope' => 'Any items/purchases that help repair and maintain office property.'
        ],
        4 => [
            'name' => 'Office Supplies Expense',
            'scope' => 'Pertains to office supplies.'
        ],
        5 => [
            'name' => 'Other Supplies Expense',
            'scope' => 'Pertains to payment of photocopying office forms and drinking water.'
        ],
        6 => [
            'name' => 'Communication Expense',
            'scope' => 'Pertains to the payment of the Mobile services and internet services.'
        ],
        7 => [
            'name' => 'Utilities Expense',
            'scope' => 'Pertains to the payment of Electricity and Water'
        ],
        8 => [
            'name' => 'Professional Fee',
            'scope' => 'Pertains to the payment of office retainers'
        ],
        9 => [
            'name' => 'Transportation and Freight Expenses',
            'scope' => 'Pertains to the gasoline, fair and courier fee.'
        ],
        10 => [
            'name' => 'Other Expense',
            'scope' => 'Other expenses that do not belong above.'
        ],
        11 => [
            'name' => 'Computer Equipment',
            'scope' => 'Pertains to the purchase of computer equipment and accessories.'
        ],
        12 => [
            'name' => 'Furniture and Fixtures',
            'scope' => 'Pertains to the purchase of office furniture, such as tables and chairs, cabinets, etc.'
        ],
        13 => [
            'name' => 'Office Equipment',
            'scope' => 'Pertains to the purchase of office equipment such as CCTV, electric fan, aircon, etc.'
        ],
        14 => [
            'name' => 'Food and Meal Allowance',
            'scope' => 'Pertains to the allowance given to the employee and purchase for rice.'
        ]
    ],
    'payment_methods' => [
        'Cash',
        'Bank Transfer',
        'Check',
        'GCash',
        'Credit Card'
    ],
    'savings_classifications' => [
        'Salary',
        'Receivable',
        'Pension',
        'Allotment',
        'Cash'
    ],
    'vendor_types' => [
        'Raw Materials Supplier',
        'Office Supplies',
        'Equipment Supplier',
        'Service Providers',
        'Wholesaler',
        'Distributor',
        'Contractor/Subcontractor',
        'Technology/Software Provider',
        'Transport and Logistics',
        'Food and Beverage Supplier',
        'Utility Providers',
        'Maintenance and Repair',
        'Marketing and Advertising',
        'Cleaning Services',
        'Packaging Supplier',
        'Financial Service Providers',
        'Security Services',
        'Consultants',
        'Importers/Exporters',
        'Healthcare Providers'
    ],
    'loan_status' => [
        1 => 'Approved',
        2 => 'Declined',
        3 => 'Withdrawn',
        5 => 'Closed',
    ],
    'loan_status_labels' => [
        1 => '<span class="badge badge-success w-100">Approved</span>',
        2 => '<span class="badge badge-danger w-100">Declined</span>',
        3 => '<span class="badge badge-warning w-100">Withdrawn</span>',
        4 => '<span class="badge badge-primary w-100">Pending</span>',
        5 => '<span class="badge badge-dark w-100">Closed</span>',
    ]
];