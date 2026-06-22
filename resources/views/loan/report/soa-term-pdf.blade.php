
@extends('layouts.pdf')

@section('title') SOA With Term @endsection

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
                <table class="" width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <!-- Left Column -->
                        <td valign="top" width="30%">
                            <table class="table table-bordered" cellspacing="0" cellpadding="5" width="100%">
                                <tbody>
                                    <tr>
                                        <td class="text-start"><strong>Name</strong></td>
                                        <td class="text-start">John Doe</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start"><strong>Loan Amount</strong></td>
                                        <td class="text-start">₱626,400.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>

                        <td valign="top" width="30%"></td>

                        <!-- Right Column -->
                        <td valign="top" width="20%">
                            <table class="table table-bordered" cellspacing="0" cellpadding="5" width="100%">
                                <tbody>
                                    <tr>
                                        <td class="text-start"><strong>Date of Loan</strong></td>
                                        <td class="text-start">January 27, 2024</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start"><strong>Loan Term</strong></td>
                                        <td class="text-start">1% (72 MOS)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

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
                                <td class="text-start">{{ $val['month'] }}</td>
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
                <table width="100%" cellpadding="5" cellspacing="0" border="0">
                    <tr valign="top">
                        <!-- Left Table -->
                        <td width="65%">
                            <table class="table table-bordered" width="100%" border="1" cellpadding="4" cellspacing="0">
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
                                    @foreach($mockOthersData as $key => $val)
                                    <tr>
                                        <td>{{ $val['type'] }}</td>
                                        <td align="center">{{ $val['days'] }}</td>
                                        <td align="right">{{ $val['amount'] }}</td>
                                        <td align="right">{{ $val['advance_interest'] }}</td>
                                        <td align="right">{{ $val['actual_interest'] }}</td>
                                        <td align="right">{{ $val['rebates'] }}</td>
                                        <td align="right">{{ $val['still_due'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>

                        <!-- Right Table -->
                        <td width="35%">
                            <table class="table table-bordered" width="100%" border="1" cellpadding="4" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td><strong>Loan Amount</strong></td>
                                        <td align="right">₱458,700.00</td>
                                    </tr>
                                    <tr>
                                        <td><strong>YEB 2025</strong></td>
                                        <td align="right">₱10,000.00</td>
                                    </tr>
                                    <tr>
                                        <td><strong>MYB 2026</strong></td>
                                        <td align="right">₱4,947.00</td>
                                    </tr>
                                    <tr>
                                        <td><strong>CLOTH 2026</strong></td>
                                        <td align="right">₱3,100.00</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PEI 2025</strong></td>
                                        <td align="right">₱1,792.00</td>
                                    </tr>
                                    <tr>
                                        <td><strong>TOTAL DUE</strong></td>
                                        <td align="right" style="color:red;"><strong>₱478,539.00</strong></td>
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