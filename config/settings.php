<?php

return [
    'mode_of_payment' => [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'palawan' => 'Palawan',
        'atm_withdrawal' => 'ATM Withdrawal',
    ],
    'membership_fee' => env('MEMBERSHIP_FEE', 1000)
];