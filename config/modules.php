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
            ['name' => 'approve-secondary-approval','label' => 'Approve Request Secondary Approval'],
            ['name' => 'request-loan-cancellation','label' => 'Request Loan Cancellation'],
            ['name' => 'manage-loan-cancellation','label' => 'Manage Loan Cancellation'],
        ]
    ],
    'savings' => [
        'permissions' => [
            ['name' => 'view-savings','label' => 'View Savings'],
            ['name' => 'view-transactions','label' => 'View Transactions'],
            ['name' => 'deposit-transactions','label' => 'Deposit Transactions'],
            ['name' => 'withdraw-transactions','label' => 'Withdraw Transactions'],
            // ['name' => 'cash-transactions','label' => 'Cash Transactions'],
            // ['name' => 'bank-transfer-transactions','label' => 'Bank Transfer Transactions'],
            ['name' => 'edit-savings-transaction','label' => 'Edit Savings Transaction'],
            ['name' => 'delete-savings-transaction','label' => 'Delete Savings Transaction'],
        ]
    ],
    'walk-in-transaction' => [
        'permissions' => [
            ['name' => 'view-walk-in-transaction','label' => 'View Walk-in Transaction'],
            ['name' => 'create-walk-in-transaction','label' => 'Create Walk-in Transaction'],
            ['name' => 'edit-walk-in-transaction','label' => 'Edit Walk-in Transaction'],
            ['name' => 'delete-walk-in-transaction','label' => 'Delete Walk-in Transaction'],
        ]
    ],
    'check-encashment' => [
        'permissions' => [
            ['name' => 'view-check-encashment','label' => 'View Check Encashment'],
            ['name' => 'create-check-encashment','label' => 'Create Check Encashment'],
            ['name' => 'edit-check-encashment','label' => 'Edit Check Encashment'],
            ['name' => 'delete-check-encashment','label' => 'Delete Check Encashment'],
        ]
    ],
    'cash-request' => [
        'permissions' => [
            ['name' => 'view-cash-request','label' => 'View Cash Request'],
            ['name' => 'create-cash-request','label' => 'Create Cash Request'],
            ['name' => 'edit-cash-request','label' => 'Edit Cash Request'],
            ['name' => 'delete-cash-request','label' => 'Delete Cash Request'],
            ['name' => 'approve-cash-request','label' => 'Approve Cash Request'],
        ]
    ],
    'incentives' => [
        'permissions' => [
            ['name' => 'view-incentives','label' => 'View Incentives'],
            ['name' => 'create-incentives','label' => 'Create Incentives'],
            ['name' => 'edit-incentives','label' => 'Edit Incentives'],
            ['name' => 'delete-incentives','label' => 'Delete Incentives'],
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
            ['name' => 'view-document-type','label' => 'View Document Types'],
            ['name' => 'create-document-type','label' => 'Create Document Types'],
            ['name' => 'edit-document-type','label' => 'Edit Document Types'],
            ['name' => 'view-expense-category','label' => 'View Expense Category'],
            ['name' => 'create-expense-category','label' => 'Create Expense Category'],
            ['name' => 'edit-expense-category','label' => 'Edit Expense Category'],
            ['name' => 'view-expense','label' => 'View Expense'],
            ['name' => 'create-expense','label' => 'Create Expense'],
            ['name' => 'edit-expense','label' => 'Edit Expense'],
            ['name' => 'delete-expense','label' => 'Delete Expense'],
            ['name' => 'view-vendors','label' => 'View Vendors'],
            ['name' => 'create-vendors','label' => 'Create Vendors'],
            ['name' => 'edit-vendors','label' => 'Create Vendors'],
            ['name' => 'delete-vendors','label' => 'Create Vendors'],
            ['name' => 'view-client-group-type','label' => 'View Client Group Type'],
            ['name' => 'create-client-group-type','label' => 'Create Client Group Type'],
            ['name' => 'edit-client-group-type','label' => 'Edit Client Group Type'],
            ['name' => 'delete-client-group-type','label' => 'Delete Client Group Type'],
            ['name' => 'view-client-group','label' => 'View Client Group'],
            ['name' => 'create-client-group','label' => 'Create Client Group'],
            ['name' => 'update-client-group','label' => 'Update Client Group'],
            ['name' => 'delete-client-group','label' => 'Delete Client Group'],
            ['name' => 'view-color-coding', 'label' => 'View Color Coding'],
            ['name' => 'create-color-coding', 'label' => 'Create Color Coding'],
            ['name' => 'edit-color-coding', 'label' => 'Edit Color Coding'],
            ['name' => 'delete-color-coding', 'label' => 'Delete Color Coding'],
            ['name' => 'view-database-backups', 'label' => 'View Database Backups'],
            ['name' => 'create-database-backup', 'label' => 'Create Database Backup'],
            ['name' => 'download-database-backup', 'label' => 'Download Database Backup'],
            ['name' => 'restore-database-backup', 'label' => 'Restore Database Backup'],
            // ['name' => 'delete-database-backup', 'label' => 'Delete Database Backup'],
        ]
    ],
    'Reports' => [
        'permissions' => [
            ['name' => 'view-expense-report','label' => 'View Expense Report'],
            ['name' => 'create-expense-report','label' => 'Create Expense Report'],
            ['name' => 'view-reports', 'label' => 'View General Reports'],
            ['name' => 'view-loan-report', 'label' => 'View Loan Report'],
            ['name' => 'view-petty-cash-report', 'label' => 'View Petty Cash Report'],
            ['name' => 'view-payment-report', 'label' => 'View Payment Report'],
            ['name' => 'view-cash-request-report', 'label' => 'View Cash Request Report'],
        ]
    ],
];