
@extends('layouts.pdf')

@section('title') SOA With Term @endsection

    @section('content')
    <div class="container-fluid">
        <!-- report content goes here -->
        <div class="card">
            <div class="card-body">
                <table style="width: 100%; margin-top: 40px;">
                    <tr>
                        <td style="text-align: center;">
                            <h3 class="soa-title">
                                Statement of Account
                            </h3>

                            <span style="text-transform: uppercase;">
                                As of
                                {{ isset($transactionDate) && $transactionDate
                                    ? date('F d, Y', strtotime($transactionDate))
                                    : date('F d, Y') }}
                            </span>
                        </td>
                    </tr>
                </table>

                <!-- loan information table -->
                <table class="" width="100%" cellspacing="0" cellpadding="5">
                    <tr>
                        <!-- Left Column -->
                        <td valign="top" width="30%">
                            <table class="table table-bordered" cellspacing="0" cellpadding="5" width="100%">
                                <tbody>
                                    <tr>
                                        <td class="text-start"><strong>Name</strong></td>
                                        <td class="text-start">{{ strtoupper($loan->borrower->details->lastname) }}, {{ strtoupper($loan->borrower->details->firstname) }} {{ strtoupper($loan->borrower->details->middlename) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start"><strong>Loan Amount</strong></td>
                                        <td class="text-end">₱{{ number_format($loan->principal, 2) }}</td>
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
                                        <td class="text-start">{{ date('F d, Y', strtotime($loan->disbursed_date ?? $loan->loan_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start"><strong>Loan Term</strong></td>
                                        <td class="text-start">{{ $loan->interest_rate }}% ({{ $loan->loan_duration }} MOS)</td>
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
                            <th class="align-middle text-center" width="50">Month</th>
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
                        @endphp
                        @foreach($soaRows as $key => $row)
                            @php 
                                $insuranceTotal += (float)$row['insurance'];
                                $principalPaymentTotal += (float)$row['principal_payment'];
                            @endphp

                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-start">{{ $row['month'] }}</td>
                                <td></td>
                                <td class="text-end">₱{{ number_format($row['insurance'], 2) }}</td>
                                <td class="text-end">₱{{ number_format($row['principal_payment'], 2) }}</td>
                                <td class="text-end">₱{{ number_format($row['principal_running_balance'], 2) }}</td>
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
                    <span>Special Loans</span>
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
                                        <th>Add : Actual Interest</th>
                                        <th>Less : Advance Interest</th>
                                        {{-- <th>Rebates</th> --}}
                                        <th>Still Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($summary['special_loans'] as $special_loan)
                                    <tr>
                                        <td>{{ $special_loan['type'] }}</td>
                                        <td align="center">{{ $special_loan['days'] }}</td>
                                        <td align="right">₱{{ number_format($special_loan['amount'], 2) }}</td>
                                        <td align="right">-</td>
                                        <td align="right">-</td>
                                        {{-- <td align="right">-</td> --}}
                                        <td align="right">₱{{ number_format($special_loan['still_due'], 2) }}</td>
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
                                        <td align="right">₱{{ number_format($summary['principal_due'], 2) }}</td>
                                    </tr>
                                    @foreach($summary['special_loans'] as $special_loan)
                                        <tr>
                                            <td><strong>{{ strtoupper($special_loan['type']) }}</strong></td>
                                            <td align="right">₱{{ number_format($special_loan['still_due'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td><strong>TOTAL DUE</strong></td>
                                        <td align="right" style="color:red;"><strong>₱{{ number_format($summary['total_due'], 2) }}</strong></td>
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