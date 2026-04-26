
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


<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <!-- report header -->
            <div class="header-container">
                <div class="d-flex justify-content-center">
                    <div class="d-flex flex-column align-items-center">
                        <h4 class="car-title txt-primary text-uppercase">Statement Of Account</h4>
                        <span>Statement as of {{ date('F d, Y') }}</span>
                    </div>
                </div>

                <div class="row justify-content-start gap-5 mt-4">
                    <div class="col-lg-4">
                        <div class="d-flex justify-content-between flex-wrap">
                            <span class="fw-semibold font-12">NAME: * </span>
                            <span class="fw-semibold font-12">DELLEVA, MELDRID</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold font-12">HOME ADDRESS: * </span>
                            <span class="fw-semibold font-12">BRGY. 09, GUIUAN EASTERN SAMAR</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold font-12">WORK ADDRESS: * </span>
                            <span class="fw-semibold font-12">MERCEDES NATIONAL HIGH SCHOOL</span>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="d-flex justify-content-between flex-wrap">
                            <span class="fw-semibold font-12">CO MAKER: * </span>
                            <span class="fw-semibold font-12"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold font-12">HOME ADDRESS: * </span>
                            <span class="fw-semibold font-12"></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold font-12">WORK ADDRESS: * </span>
                            <span class="fw-semibold font-12"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- report body -->
            <div class="table-container table-responsive mt-3">
                <table class="table table-sm table-bordered border-bottom">
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
                            <th class="align-middle font-12">INTEREST</th>
                            <th class="align-middle font-12">PAYMENT PRINCIPAL</th>
                            <th class="align-middle font-12">INS</th>
                            <th class="align-middle font-12">SAVINGS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mockData as $key => $val)
                            <tr>
                                <td class="font-12">{{ $val['date'] ?? ''; }}</td>
                                <td class="font-12">{{ $val['salary_date'] ?? ''; }}</td>
                                <td class="text-center font-12">{{ $val['number_of_days'] }}</td>
                                <td class="{{ isset($val['principal_loan']) && $val['principal_loan'] !=0 ? 'text-end' : 'text-center' }}">{{ $val['principal_loan'] != 0 ? number_format($val['principal_loan'], 2) : '-' }}</td>
                                <td class="font-12">{{ $val['interest_charge'] }}</td>
                                <td class="font-12">{{ $val['advance_interest'] }}</td>
                                <td class="font-12">{{ $val['deducted_to_loan_renewal'] ?? '-' }}</td>
                                <td class="font-12">{{ $val['interest'] }}</td>
                                <td class="font-12">{{ $val['payment_principal'] }}</td>
                                <td class="font-12">{{ $val['ins'] ?? ''; }}</td>
                                <td class="font-12">{{ $val['savings'] }}</td>
                                <td class="font-12">{{ $val['principal_balance'] }}</td>
                                <td class="font-12">{{ $val['remarks'] }}</td>
                                <td class="font-12">{{ $val['service_fee'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-secondary-subtle">

                        <tr>
                            <td class="text-center fw-semibold">Total</td>
                            <td colspan="13"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Other Charges Section -->
            <div class="mt-3">
                <span>Other Charges</span>
            </div>
            <div class="row justify-content-between">
                <div class="col-lg-8">
                    <table class="table table-sm table-bordered border-bottom">
                        <thead>
                            <tr>
                                <th class="align-middle font-12">Type</th>
                                <th class="align-middle font-12">Days</th>
                                <th class="align-middle font-12">Amount</th>
                                <th class="align-middle font-12">Advance Interest</th>
                                <th class="align-middle font-12">Actual Interest</th>
                                <th class="align-middle font-12">Rebates</th>
                                <th class="align-middle font-12">Still Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mockOthersData as $key => $val)
                                <tr>
                                    <td class="font-12">{{ $val['type'] }}</td>
                                    <td class="text-center font-12">{{ $val['days'] }}</td>
                                    <td class="text-end font-12">{{ $val['amount'] }}</td>
                                    <td class="text-end font-12">{{ $val['advance_interest'] }}</td>
                                    <td class="text-end font-12">{{ $val['actual_interest'] }}</td>
                                    <td class="text-end font-12">{{ $val['rebates'] }}</td>
                                    <td class="text-end font-12">{{ $val['still_due'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-4">
                    <table class="table table-sm table-bordered border-bottom">
                        <tbody>
                            <tr>
                                <td class="fw-semibold font-12">TOTAL INTEREST CHARGED</td>
                                <td class="text-end font-12">260,586.00</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold font-12">LESS : ADVANCE INTEREST </td>
                                <td class="text-end font-12">8,850.00 </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold font-12">TOTAL INTEREST COLLECTED</td>
                                <td class="text-end font-12"> 251,808.00</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-danger font-12">REBATES</td>
                                <td class="text-end font-12">(72.00)</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-sm table-bordered border-bottom mt-3">
                        <tbody>
                            <tr>
                                <td class="fw-semibold font-12">PRINCIPAL AMOUNT STILL DUE</td>
                                <td class="text-end font-12">209,277.00</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold font-12">LESS: REBATES</td>
                                <td class="text-end font-12">(72.00)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold font-12">TOTAL AMOUNT DUE</td>
                                <td class="text-end font-12">209,205.00</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold font-12">CLOTH 2026</td>
                                <td class="text-end font-12">4,550.00</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold font-12">MYB 2026</td>
                                <td class="text-end font-12">12,975.00</td>
                            </tr>
                            <tr>
                                <td class="fw-bold font-12">TOTAL AMOUNT DUE</td>
                                <td class="text-end font-12">226,730.00 </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>