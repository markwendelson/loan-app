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
            0=>'Inactive',
            1=>'Active'
        ],

        'nationality_value' => [
            1=>'Filipino',
            2=>'American',
            3=>'German',
            4=>'Australian'
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
        'designation'=>[
            0 => 'Supervisor',
            1 => 'Staff',
            2 => 'Manager',      // Example for index 2
            3 => 'Administrator'
        ],
        'default_area_code' => '+63',
        'default_currency_code' => 'PHP',
        'default_currency_symbol' => '₱'
    ];