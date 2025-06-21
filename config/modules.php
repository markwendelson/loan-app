<?php

// ['name' => '','label' => ''],

return [
    'dashboard' => [
        'permissions' => [
            ['name' => 'view-dashboard','label' => 'View Dashboard']
        ],
    ],
    'clients' => [
        'permissions' => [
            ['name' => 'view-client','label' => 'View Client'],
            ['name' => 'view-ledger','label' => 'View Ledger'],
            ['name' => 'edit-client','label' => 'Edit Client'],
            ['name' => 'delete-client','label' => 'Delete Client'],
            ['name' => 'create-client','label' => 'Create Client']
        ],
    ],
    'loans' => [
        'permissions' => [
            ['name' => 'view-all-loans','label' => 'View All Loans'],
            ['name' => 'view-pending-approval','label' => 'View Pending Approval'],
            ['name' => 'view-awaiting-disbursement','label' => 'View Awaiting Disbursement'],
            ['name' => 'view-loans-declined','label' => 'View Loans Declined'],
            ['name' => 'view-loans-withdrawn','label' => 'View Loans Withdrawn'],
            ['name' => 'create-loan','label' => 'Create Loan'],
            ['name' => 'view-loan','label' => 'View Loan'],
            ['name' => 'edit-loan','label' => 'Edit Loan'],
            ['name' => 'delete-loan','label' => 'Delete Loan'],
            ['name' => 'approve-loan','label' => 'Approve Loan'],
            ['name' => 'decline-loan','label' => 'Decline Loan'],
            ['name' => 'withdraw-loan','label' => 'Withdraw Loan'],
            ['name' => 'request-secondary-approval','label' => 'Request Secondary Approval'],
        ]
    ],
    'savings' => [
        'permissions' => [
            ['name' => 'view-savings','label' => 'View Savings'],
            ['name' => 'view-transactions','label' => 'View Transactions'],
            ['name' => 'deposit-transactions','label' => 'Deposit Transactions'],
            ['name' => 'withdraw-transactions','label' => 'Withdraw Transactions'],
            ['name' => 'edit-savings-transaction','label' => 'Edit Savings Transaction'],
            ['name' => 'delete-savings-transaction','label' => 'Delete Savings Transaction'],
        ]
    ],
    'libraries' => [
        'permissions' => [
            ['name' => 'view-branch','label' => 'View Branch'],
            ['name' => 'edit-branch','label' => 'Edit Branch'],
            ['name' => 'delete-branch','label' => 'Delete Branch'],
            ['name' => 'create-branch','label' => 'Create Branch'],
            ['name' => 'view-job-title','label' => 'View Job Title'],
            ['name' => 'edit-job-title','label' => 'Edit Job Title'],
            ['name' => 'delete-job-title','label' => 'Delete Job Title'],
            ['name' => 'create-job-title','label' => 'Create Job Title'],
            ['name' => 'view-user','label' => 'View User'],
            ['name' => 'edit-user','label' => 'Edit User'],
            ['name' => 'delete-user','label' => 'Delete User'],
            ['name' => 'create-user','label' => 'Create User'],
            ['name' => 'view-notary-fee','label' => 'View Notary Fee'],
            ['name' => 'edit-notary-fee','label' => 'Edit Notary Fee'],
            ['name' => 'delete-notary-fee','label' => 'Delete Notary Fee'],
            ['name' => 'create-notary-fee','label' => 'Create Notary Fee'],
            ['name' => 'view-insurance-fee','label' => 'View Insurance Fee'],
            ['name' => 'edit-insurance-fee','label' => 'Edit Insurance Fee'],
            ['name' => 'delete-insurance-fee','label' => 'Delete Insurance Fee'],
            ['name' => 'create-insurance-fee','label' => 'Create Insurance Fee'],
            ['name' => 'view-loan-type','label' => 'View Loan Type'],
            ['name' => 'edit-loan-type','label' => 'Edit Loan Type'],
            ['name' => 'delete-loan-type','label' => 'Delete Loan Type'],
            ['name' => 'create-loan-type','label' => 'Create Loan Type'],
            ['name' => 'view-loan-product','label' => 'View Loan Product'],
            ['name' => 'edit-loan-product','label' => 'Edit Loan Product'],
            ['name' => 'delete-loan-product','label' => 'Delete Loan Product'],
            ['name' => 'create-loan-product','label' => 'Create Loan Product'],
            ['name' => 'view-role','label' => 'View Role'],
            ['name' => 'edit-role','label' => 'Edit Role'],
            ['name' => 'delete-role','label' => 'Delete Role'],
            ['name' => 'create-role','label' => 'Create Role'],
            ['name' => 'assign-role','label' => 'Assign Role'],
            ['name' => 'view-permission','label' => 'View Permission'],
            ['name' => 'edit-permission','label' => 'Edit Permission'],
            ['name' => 'delete-permission','label' => 'Delete Permission'],
            ['name' => 'create-permission','label' => 'Create Permission'],
            ['name' => 'assign-permission','label' => 'Assign Permission'],
        ]
    ],
];