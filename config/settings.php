<?php

    return [
        'mode_of_payment' => [
            'cash' => 'Cash',
            'check' => 'Check',
            'bank_transfer' => 'Bank Transfer',
            'gcash' => 'GCash',
            'paymaya' => 'PayMaya'
        ],
        'membership_fee' => env('MEMBERSHIP_FEE', 1000)
    ];