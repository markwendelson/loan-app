
@extends('layouts.pdf')

@section('title') SOA No Term @endsection

@push('css')
    <style>
        .font-12 {
            font-size: 12px !important;
        }
    </style>
@endpush

@php
    $mockData = [
        [
            "salary_date" => "", 
            "date" => "May 17, 2023", 
            "number_of_days" => "8", 
            "principal_loan" => 295000.00, 
            "interest_charge" => "", 
            "column1" => "", 
            "advance_interest" => 8850.00, 
            "deducted_to_loan_renewal" => "165,027.00 ", 
            "interest" => "", 
            "payment_principal" => "", 
            "principal" => "", 
            "ins" => 3000.00, 
            "savings" => 8850.00, 
            "principal_balance" => 295000.00, 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "May 17, 2023", 
            "date" => "", 
            "number_of_days" => "8", 
            "principal_loan" => 0, 
            "interest_charge" => "", 
            "column1" => "", 
            "advance_interest" => 8850.00, 
            "deducted_to_loan_renewal" => "", 
            "interest" => 165027.00, 
            "payment_principal" => 2000, 
            "principal" => 3000.00, 
            "ins" => 295000.00, 
            "savings" => 8850.00, 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "May 25, 2023", 
            "date" => "", 
            "number_of_days" => "21", 
            "principal_loan" => 0, 
            "interest_charge" => 0.27, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 2000.00, 
            "interest" => "",
            "payment_principal" => 0,
            "principal" => 293000.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "June 15, 2023", 
            "date" => "", 
            "number_of_days" => "12", 
            "principal_loan" => 0, 
            "interest_charge" => 0.70, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 4395.00, 
            "interest" => "",
            "payment_principal" => 0,
            "principal" => 293000.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "September 27, 2023", 
            "date" => "", 
            "number_of_days" => "17", 
            "principal_loan" => 0, 
            "interest_charge" => 0.93, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 8700.00, 
            "interest" => "",
            "payment_principal" => 0,
            "principal" => 290000.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "December 28, 2023", 
            "date" => "", 
            "number_of_days" => "13", 
            "principal_loan" => 0, 
            "interest_charge" => 0.43, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 4170.00, 
            "interest" => "",
            "payment_principal" => 0,
            "principal" => 278000.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "April 15, 2024", 
            "date" => "", 
            "number_of_days" => "19", 
            "principal_loan" => 0, 
            "interest_charge" => 0.80, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 4600.00, 
            "interest" => "",
            "payment_principal" => 0,
            "principal" => 277738.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "July 30, 2024", 
            "date" => "", 
            "number_of_days" => "15", 
            "principal_loan" => 0, 
            "interest_charge" => 0.10, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 2134.00, 
            "interest" => "",
            "payment_principal" => 0, 
            "principal" => 270558.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "August 16, 2024", 
            "date" => "", 
            "number_of_days" => "28", 
            "principal_loan" => 0, 
            "interest_charge" => 0.57, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 3350.00, 
            "interest" => 250.00, 
            "payment_principal" => 0,
            "principal" => 270558.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "November 12, 2024", 
            "date" => "", 
            "number_of_days" => "28", 
            "principal_loan" => 0, 
            "interest_charge" => 0.60, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 3548.00, 
            "interest" => 5590.00,
            "payment_principal" => 0,
            "principal" => 246000.00, 
            "ins" => 250.00, 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "November 26, 2024", 
            "date" => "", 
            "number_of_days" => "6", 
            "principal_loan" => 0, 
            "interest_charge" => 0.47, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 9600.00, 
            "interest" => "",
            "payment_principal" => 0, 
            "principal" => 246000.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "December 13, 2024", 
            "date" => "", 
            "number_of_days" => "11", 
            "principal_loan" => 0, 
            "interest_charge" => 0.57, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 1612.00, 
            "interest" => 838.00,
            "payment_principal" => 0, 
            "principal" => 245162.00, 
            "ins" => 250.00, 
            "savings" => "",
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "December 16, 2024", 
            "date" => "", 
            "number_of_days" => "10", 
            "principal_loan" => 0, 
            "interest_charge" => 0.10, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 2500.00, 
            "interest" => "",
            "payment_principal" => 0, 
            "principal" => 242662.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "December 24, 2024", 
            "date" => "", 
            "number_of_days" => "23", 
            "principal_loan" => 0, 
            "interest_charge" => 0.27, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 4000.00, 
            "interest" => "",
            "payment_principal" => 0, 
            "principal" => 238662.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "January 30, 2025", 
            "date" => "", 
            "number_of_days" => "29", 
            "principal_loan" => 0, 
            "interest_charge" => 1.23, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 5850.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 238662.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "February 18, 2025", 
            "date" => "", 
            "number_of_days" => "13", 
            "principal_loan" => 0, 
            "interest_charge" => 0.63, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 3750.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 238662.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "February 27, 2025", 
            "date" => "", 
            "number_of_days" => "19", 
            "principal_loan" => 0, 
            "interest_charge" => 0.30, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 4720.00, 
            "interest" => 2000.00,
            "payment_principal" => 0, 
            "principal" => 236662.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "March 25, 2025", 
            "date" => "", 
            "number_of_days" => "14", 
            "principal_loan" => 0, 
            "interest_charge" => 0.87, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 5950.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 236662.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "April 24, 2025", 
            "date" => "", 
            "number_of_days" => "16", 
            "principal_loan" => 0, 
            "interest_charge" => 1.00, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 8250.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 236662.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "May 6, 2025", 
            "date" => "", 
            "number_of_days" => "12", 
            "principal_loan" => 0, 
            "interest_charge" => 0.40, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 7100.00, 
            "interest" => 10662.00,
            "payment_principal" => 0, 
            "principal" => 250.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "July 15, 2025", 
            "date" => "", 
            "number_of_days" => "24", 
            "principal_loan" => 0, 
            "interest_charge" => 0.93, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 6050.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 222930.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "August 13, 2025", 
            "date" => "", 
            "number_of_days" => "24", 
            "principal_loan" => 0, 
            "interest_charge" => 0.97, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 5950.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 222930.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "August 27, 2025", 
            "date" => "", 
            "number_of_days" => "16", 
            "principal_loan" => 0, 
            "interest_charge" => 0.47, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 3200.00, 
            "interest" => "",
            "payment_principal" => 0, 
            "principal" => 222930.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "August 28, 2025", 
            "date" => "", 
            "number_of_days" => "1", 
            "principal_loan" => 0, 
            "interest_charge" => 0.03, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 584.00, 
            "interest" => 2916.00,
            "payment_principal" => 0, 
            "principal" => 220014.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "October 28, 2025", 
            "date" => "", 
            "number_of_days" => "15", 
            "principal_loan" => 0, 
            "interest_charge" => 0.90, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 5950.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 220014.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "November 12, 2025", 
            "date" => "", 
            "number_of_days" => "16", 
            "principal_loan" => 0, 
            "interest_charge" => 0.50, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 5500.00, 
            "interest" => 4650.00,
            "payment_principal" => 0, 
            "principal" => 215364.00, 
            "ins" => 250.00, 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "December 2, 2025", 
            "date" => "", 
            "number_of_days" => "20", 
            "principal_loan" => 0, 
            "interest_charge" => 0.67, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 2361.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 215364.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "December 11, 2025", 
            "date" => "", 
            "number_of_days" => "20", 
            "principal_loan" => 0, 
            "interest_charge" => 0.30, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 6461.00, 
            "interest" => 1339.00,
            "payment_principal" => 0, 
            "principal" => 214025.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "January 28, 2026", 
            "date" => "", 
            "number_of_days" => "5", 
            "principal_loan" => 0, 
            "interest_charge" => 1.20, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 6050.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 212525.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "February 13, 2026", 
            "date" => "", 
            "number_of_days" => "16", 
            "principal_loan" => 0, 
            "interest_charge" => 0.53, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 5850.00, 
            "interest" => 250.00,
            "payment_principal" => 0, 
            "principal" => 212525.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "February 24, 2026", 
            "date" => "", 
            "number_of_days" => "11", 
            "principal_loan" => 0, 
            "interest_charge" => 0.37, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => 852.00, 
            "interest" => 3248.00,
            "payment_principal" => 0, 
            "principal" => 209277.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ],
        [
            "salary_date" => "March 9, 2026", 
            "date" => "", 
            "number_of_days" => "13", 
            "principal_loan" => 0, 
            "interest_charge" => 0.43, 
            "column1" => "", 
            "advance_interest" => "", 
            "deducted_to_loan_renewal" => "", 
            "interest" => "",
            "payment_principal" => 0, 
            "principal" => 209277.00, 
            "ins" => "", 
            "savings" => "", 
            "principal_balance" => "", 
            "remarks" => "", 
            "service_fee" => ""
        ]
    ];

    $mockOthersData = [
        [
            'type' => 'MYB',
            'days' => 40,
            'amount' => '₱7,000.00',
            'advance_interest' => '₱2,520.00',
            'actual_interest' => '₱466.67',
            'rebates' => '₱2,053.33',
            'still_due' => '₱4,947.00'
        ],
        [
            'type' => 'CLOTH 2022',
            'days' => 40,
            'amount' => '₱6,000.00',
            'advance_interest' => '₱3,300.00',
            'actual_interest' => '₱400.00',
            'rebates' => '₱2,900.00',
            'still_due' => '₱3,100.00'
        ],
        // [
        //     'type' => 'PEI',
        //     'days' => 40,
        //     'amount' => '₱2,500.00',
        //     'advance_interest' => '₱875.00',
        //     'actual_interest' => '₱166.67',
        //     'rebates' => '₱708.33',
        //     'still_due' => '₱1,792.00'
        // ]
    ];

@endphp

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <!-- report header -->
            <div class="header-container mt-5">
                <div class="d-flex justify-content-center">
                    <div class="d-flex flex-column align-items-center">
                        <h4 class="car-title txt-primary text-uppercase">Statement Of Account</h4>
                        <span>Statement as of {{ date('F d, Y') }}</span>
                    </div>
                </div>

                <table width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <!-- Left Column -->
                        <td valign="top" width="15%">
                            <table class="table" width="100%" cellspacing="0" cellpadding="3">
                                <tr>
                                    <td><strong>NAME: *</strong></td>
                                    <td>{{ strtoupper($loan->borrower->details->lastname) }}, {{ strtoupper($loan->borrower->details->firstname) }} {{ strtoupper($loan->borrower->details->middlename) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>HOME ADDRESS: *</strong></td>
                                    <td>{{ $loan->borrower->details->present_full_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>WORK ADDRESS: *</strong></td>
                                    <td>{{ $loan->borrower->jobs->first()->office_name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </td>
                        {{-- <td valign="top" width="30%"></td> --}}

                        <!-- Right Column -->
                        <td valign="top" width="10%">
                            @php
                                $coMaker = $loan->borrower->coMakers()->wherePivot('loan_id', $loan->id)->first();
                            @endphp
                            <table clsas="table" width="100%" cellspacing="0" cellpadding="3">
                                <tr>
                                    <td><strong>CO MAKER: *</strong></td>
                                    <td>{{ $coMaker ? strtoupper($coMaker->details->fullname) : '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>HOME ADDRESS: *</strong></td>
                                    <td>{{ $coMaker ? $coMaker->details->present_full_address : '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>WORK ADDRESS: *</strong></td>
                                    <td>{{ $coMaker ? ($coMaker->jobs->first()->office_name ?? 'N/A') : '' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- report body -->
            <div class="table-container table-responsive mt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle font-12">DATE</th>
                            <th rowspan="2" class="align-middle font-12">SALARY DATE</th>
                            <th rowspan="2" class="align-middle font-12">NUMBER OF DAYS</th>
                            <th rowspan="2" class="align-middle font-12">PRINCIPAL LOAN</th>
                            <th rowspan="2" class="align-middle font-12">INTEREST CHARGE</th>
                            <th rowspan="2" class="align-middle font-12">ADVANCE INTEREST</th>
                            <th rowspan="2" class="align-middle font-12">DEDUCTED TO LOAN RENEWAL</th>

                            <th colspan="4" class="align-middle text-center font-12">PAYMENTS</th>
                            
                            <th rowspan="2" class="align-middle font-12">PRINCIPAL BALANCE</th>
                            <th rowspan="2" class="align-middle font-12">REMARKS</th>
                            <th rowspan="2" class="align-middle font-12">SERVICE FEE</th>
                        </tr>
                        <tr>
                            <th class="text-center align-middle font-12">INTEREST</th>
                            <th class="text-center align-middle font-12">PAYMENT PRINCIPAL</th>
                            <th class="text-center align-middle font-12">INS</th>
                            <th class="text-center align-middle font-12">SAVINGS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_principal_loan = 0;
                            $total_advance_interest = 0;
                            $total_deducted_to_loan_renewal = 0;
                            $total_interest = 0;
                            $total_payment_principal = 0;
                            $total_ins = 0;
                            $total_savings = 0;
                            $total_service_fee = 0;
                        @endphp
                        @foreach($soaRows as $row)
                            @php
                                $total_principal_loan += (float)$row['principal_loan'];
                                $total_advance_interest += (float)$row['advance_interest'];
                                $total_deducted_to_loan_renewal += (float)$row['deducted_to_loan_renewal'];
                                $total_interest += (float)$row['interest'];
                                $total_payment_principal += (float)$row['payment_principal'];
                                $total_ins += (float)$row['ins'];
                                $total_savings += (float)$row['savings'];
                                $total_service_fee += (float)$row['service_fee'];
                            @endphp
                            <tr>
                                <td class="font-12">{{ $row['date'] }}</td>
                                <td class="font-12">{{ $row['salary_date'] }}</td>
                                <td class="text-center font-12">{{ $row['number_of_days'] }}</td>
                                <td align="right" class="font-12">{{ $row['principal_loan'] > 0 ? number_format($row['principal_loan'], 2) : '-' }}</td>
                                <td align="center" class="font-12">{{ $row['interest_charge'] }}</td>
                                <td align="right" class="font-12">{{ $row['advance_interest'] > 0 ? number_format($row['advance_interest'], 2) : '-' }}</td>
                                <td align="right" class="font-12">{{ $row['deducted_to_loan_renewal'] > 0 ? number_format($row['deducted_to_loan_renewal'], 2) : '-' }}</td>
                                <td align="right" class="font-12">{{ $row['interest'] > 0 ? number_format($row['interest'], 2) : '-' }}</td>
                                <td align="right" class="font-12">{{ $row['payment_principal'] > 0 ? number_format($row['payment_principal'], 2) : '-' }}</td>
                                <td align="right" class="font-12">{{ $row['ins'] > 0 ? number_format($row['ins'], 2) : '-' }}</td>
                                <td align="right" class="font-12">{{ $row['savings'] > 0 ? number_format($row['savings'], 2) : '-' }}</td>
                                <td align="right" class="font-12">{{ $row['principal_balance'] > 0 ? number_format($row['principal_balance'], 2) : '-' }}</td>
                                <td class="font-12">{{ $row['remarks'] }}</td>
                                <td align="right" class="font-12">{{ $row['service_fee'] > 0 ? number_format($row['service_fee'], 2) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-secondary-subtle">
                        <tr>
                            <td class="text-center fw-semibold font-12">Total</td>
                            <td></td>
                            <td></td>
                            <td align="right" class="fw-semibold font-12">{{ $total_principal_loan > 0 ? number_format($total_principal_loan, 2) : '-' }}</td>
                            <td></td>
                            <td align="right" class="fw-semibold font-12">{{ $total_advance_interest > 0 ? number_format($total_advance_interest, 2) : '-' }}</td>
                            <td align="right" class="fw-semibold font-12">{{ $total_deducted_to_loan_renewal > 0 ? number_format($total_deducted_to_loan_renewal, 2) : '-' }}</td>
                            <td align="right" class="fw-semibold font-12">{{ $total_interest > 0 ? number_format($total_interest, 2) : '-' }}</td>
                            <td align="right" class="fw-semibold font-12">{{ $total_payment_principal > 0 ? number_format($total_payment_principal, 2) : '-' }}</td>
                            <td align="right" class="fw-semibold font-12">{{ $total_ins > 0 ? number_format($total_ins, 2) : '-' }}</td>
                            <td align="right" class="fw-semibold font-12">{{ $total_savings > 0 ? number_format($total_savings, 2) : '-' }}</td>
                            <td></td>
                            <td></td>
                            <td align="right" class="fw-semibold font-12">{{ $total_service_fee > 0 ? number_format($total_service_fee, 2) : '-' }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Other Charges Section -->
            <div class="mt-3">
                <span>Other Charges</span>
            </div>
            <table width="100%" cellspacing="0" cellpadding="5">
                <tr>
                    <!-- Left Column: Main Table -->
                    <td valign="top" width="60%">
                        <table class="table table-bordered" cellspacing="0" cellpadding="5" width="100%">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Days</th>
                                    <th>Amount</th>
                                    <th>Advance Interest</th>
                                    <th>Actual Interest</th>
                                    <th>Rebates</th>
                                    <th>Still Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary['special_loans'] as $special_loan)
                                    <tr>
                                        <td>{{ $special_loan['type'] }}</td>
                                        <td align="center">{{ $special_loan['days'] }}</td>
                                        <td align="right">{{ number_format($special_loan['amount'], 2) }}</td>
                                        <td align="right">-</td>
                                        <td align="right">-</td>
                                        <td align="right">-</td>
                                        <td align="right">{{ number_format($special_loan['still_due'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                    <td width="10%"></td>

                    <!-- Right Column: Summary Tables -->
                    <td valign="top" width="30%">
                        <!-- First Summary Table -->
                        <table class="table table-bordered" cellspacing="0" cellpadding="5" width="100%">
                            <tbody>
                                <tr>
                                    <td><strong>TOTAL INTEREST CHARGED</strong></td>
                                    <td align="right">{{ number_format($summary['total_interest_charged'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>LESS : ADVANCE INTEREST</strong></td>
                                    <td align="right">{{ number_format($summary['less_advance_interest'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>TOTAL INTEREST COLLECTED</strong></td>
                                    <td align="right">{{ number_format($summary['total_interest_collected'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="color:red;"><strong>REBATES</strong></td>
                                    <td align="right">{{ number_format($summary['rebates'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Spacer -->
                        <div style="height:10px;"></div>

                        <!-- Second Summary Table -->
                        <table class="table table-bordered" cellspacing="0" cellpadding="5" width="100%">
                            <tbody>
                                <tr>
                                    <td><strong>PRINCIPAL AMOUNT STILL DUE</strong></td>
                                    <td align="right">{{ number_format($summary['principal_due'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>LESS: REBATES</strong></td>
                                    <td align="right">{{ number_format($summary['rebates'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>TOTAL AMOUNT DUE</strong></td>
                                    <td align="right">{{ number_format($summary['total_amount_due'], 2) }}</td>
                                </tr>
                                @foreach($summary['special_loans'] as $special_loan)
                                    <tr>
                                        <td><strong>{{ strtoupper($special_loan['type']) }}</strong></td>
                                        <td align="right">{{ number_format($special_loan['still_due'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td><strong>TOTAL AMOUNT DUE</strong></td>
                                    <td align="right"><strong>{{ number_format($summary['final_total_due'], 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection