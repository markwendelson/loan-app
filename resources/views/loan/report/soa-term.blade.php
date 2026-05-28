
   @extends('layouts.app')
   
   @php 
    $mockData = [
            [
                'month' => 'January 2024',
                'insurance' => '₱5,000.00',
                'principal_payment' => '₱15,000.00',
                'principal_running_balance' => '₱611,600.00',
            ],
            [
                'month' => 'February 2024',
                'insurance' => '₱5,500.00',
                'principal_payment' => '₱20,000.00',
                'principal_running_balance' => '₱591,600.00',
            ],
            [
                'month' => 'March 2024',
                'insurance' => '₱5,200.00',
                'principal_payment' => '₱18,500.00',
                'principal_running_balance' => '₱573,100.00',
            ],
            [
                'month' => 'April 2024',
                'insurance' => '₱4,800.00',
                'principal_payment' => '₱17,000.00',
                'principal_running_balance' => '₱556,100.00',
            ],
            [
                'month' => 'May 2024',
                'insurance' => '₱5,300.00',
                'principal_payment' => '₱19,000.00',
                'principal_running_balance' => '₱537,100.00',
            ],
            [
                'month' => 'June 2024',
                'insurance' => '₱6,000.00',
                'principal_payment' => '₱21,000.00',
                'principal_running_balance' => '₱516,100.00',
            ],
            [
                'month' => 'July 2024',
                'insurance' => '₱5,500.00',
                'principal_payment' => '₱20,500.00',
                'principal_running_balance' => '₱495,600.00',
            ],
            [
                'month' => 'August 2024',
                'insurance' => '₱6,200.00',
                'principal_payment' => '₱23,000.00',
                'principal_running_balance' => '₱472,600.00',
            ],
            [
                'month' => 'September 2024',
                'insurance' => '₱5,800.00',
                'principal_payment' => '₱21,500.00',
                'principal_running_balance' => '₱451,100.00',
            ],
            [
                'month' => 'October 2024',
                'insurance' => '₱6,300.00',
                'principal_payment' => '₱24,000.00',
                'principal_running_balance' => '₱427,100.00',
            ],
            [
                'month' => 'November 2024',
                'insurance' => '₱5,700.00',
                'principal_payment' => '₱22,200.00',
                'principal_running_balance' => '₱404,900.00',
            ],
            [
                'month' => 'December 2024',
                'insurance' => '₱6,100.00',
                'principal_payment' => '₱23,500.00',
                'principal_running_balance' => '₱381,400.00',
            ],
            [
                'month' => 'January 2025',
                'insurance' => '₱5,900.00',
                'principal_payment' => '₱22,800.00',
                'principal_running_balance' => '₱358,600.00',
            ],
            [
                'month' => 'February 2025',
                'insurance' => '₱6,400.00',
                'principal_payment' => '₱24,000.00',
                'principal_running_balance' => '₱334,600.00',
            ],
            [
                'month' => 'March 2025',
                'insurance' => '₱5,800.00',
                'principal_payment' => '₱23,300.00',
                'principal_running_balance' => '₱311,300.00',
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
            [
                'type' => 'PEI',
                'days' => 40,
                'amount' => '₱2,500.00',
                'advance_interest' => '₱875.00',
                'actual_interest' => '₱166.67',
                'rebates' => '₱708.33',
                'still_due' => '₱1,792.00'
            ]
        ];
    @endphp
    
    @section('content')
    <div class="container-fluid">
        <!-- report content goes here -->
        <div class="card">
            <div class="card-body">
                <div class="row justify-content-center mt-5">
                    <div class="col-lg-4 text-center">
                        <h3 class="card-title txt-primary mb-0 text-uppercase">Statement of Account</h3>
                        <span class="text-uppercase"> as of {{ date('F d, Y') }}</span>
                    </div>
                </div>

                <!-- loan information table -->
                <div class="d-flex justify-content-between">
                    <div>
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Name</td>
                                    <td>John Doe</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold">Loan Amount</td>
                                    <td>₱626,400.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <table class="table table-sm table-bordered">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Date of Loan</td>
                                    <td>{{ date('F d, Y', strtotime('2024-01-27')) }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold">Loan Term</td>
                                    <td>1%(72 MOS)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Breakdown Table -->
                <table class="table table-sm table-bordered mt-2">
                    <thead>
                        <tr>
                            <th class="align-middle" width="50">Month</th>
                            <th></th>
                            <th></th>
                            <th class="text-center align-middle" width="180">Insurance</th>
                            <th class="text-center align-middle" width="180">Principal Payment</th>
                            <th class="text-center align-middle" width="200">Principal Running Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $insuranceTotal = 0;
                            $principalPaymentTotal = 0;
                            $principalRunningBalanceTotal = 0;
                        @endphp
                        @foreach($mockData as $key => $val)
                            @php 
                                $insuranceTotal += floatval(str_replace(['₱', ','], '', $val['insurance']));
                                $principalPaymentTotal += floatval(str_replace(['₱', ','], '', $val['principal_payment']));
                                $principalRunningBalanceTotal -= floatval(str_replace(['₱', ','], '', $val['principal_running_balance']));
                            @endphp

                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>{{ $val['month'] }}</td>
                                <td></td>
                                <td class="text-end">{{ $val['insurance'] }}</td>
                                <td class="text-end">{{ $val['principal_payment'] }}</td>
                                <td class="text-end">{{ $val['principal_running_balance'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td colspan="2"></td>
                            <td class="text-end">₱{{ number_format($insuranceTotal, 2) }}</td>
                            <td class="text-end">₱{{ number_format($principalPaymentTotal, 2) }}</td>
                            <td class="text-end"></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Other Charges Section -->
                <div class="mt-3">
                    <span>Other Charges</span>
                </div>
                <div class="row justify-content-between">
                    <div class="col-lg-8">
                        <table class="table table-sm table-bordered border-bottom">
                            <thead>
                                <tr>
                                    <th class="align-middle">Type</th>
                                    <th class="align-middle">Days</th>
                                    <th class="align-middle">Amount</th>
                                    <th class="align-middle">Advance Interest</th>
                                    <th class="align-middle">Actual Interest</th>
                                    <th class="align-middle">Rebates</th>
                                    <th class="align-middle">Still Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mockOthersData as $key => $val)
                                    <tr>
                                        <td>{{ $val['type'] }}</td>
                                        <td class="text-center">{{ $val['days'] }}</td>
                                        <td class="text-end">{{ $val['amount'] }}</td>
                                        <td class="text-end">{{ $val['advance_interest'] }}</td>
                                        <td class="text-end">{{ $val['actual_interest'] }}</td>
                                        <td class="text-end">{{ $val['rebates'] }}</td>
                                        <td class="text-end">{{ $val['still_due'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4">
                        <table class="table table-sm table-bordered border-bottom">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold">Loan Amount</td>
                                    <td class="text-end">₱458,700.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">YEB 2025</td>
                                    <td class="text-end">₱10,000.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">MYB 2026</td>
                                    <td class="text-end">₱4,947.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">CLOTH 2026</td>
                                    <td class="text-end">₱3,100.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">PEI 2025</td>
                                    <td class="text-end">₱1,792.00</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold">TOTAL DUE</td>
                                    <td class="text-end text-danger fw-semibold">₱478,539.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection