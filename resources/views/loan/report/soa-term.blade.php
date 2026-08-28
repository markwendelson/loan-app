
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
                            <h3 style="margin: 0; text-transform: uppercase; font-size: 16px !important;">
                                Statement of Account
                            </h3>

                            <span style="text-transform: uppercase; font-size: 14px;">
                                As of
                                {{ isset($transactionDate) && $transactionDate
                                    ? date('F d, Y', strtotime($transactionDate))
                                    : date('F d, Y') }}
                            </span>
                        </td>
                    </tr>
                </table>

                <!-- loan information table -->
                <div class="d-flex justify-content-between mt-4">
                    <div>
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Name</td>
                                    <td>{{ strtoupper($loan->borrower->details->lastname) }}, {{ strtoupper($loan->borrower->details->firstname) }} {{ strtoupper($loan->borrower->details->middlename) }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold">Loan Amount</td>
                                    <td>₱{{ number_format($loan->principal, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <table class="table table-sm table-bordered">
                            <tbody>
                                <tr>
                                    <td class="fw-bold">Date of Loan</td>
                                    <td>{{ date('F d, Y', strtotime($loan->disbursed_date ?? $loan->loan_date)) }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="fw-bold">Loan Term</td>
                                    <td>{{ $loan->interest_rate }}% ({{ $loan->loan_duration }} MOS)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Breakdown Table -->
                <table class="table table-sm table-bordered mt-2">
                    <thead>
                        <tr>
                            <th class="align-middle text-center" width="50">Month</th>
                            <th>Transaction Date</th>
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
                                <td>{{ $row['month'] }}</td>
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
                <div class="row justify-content-between">
                    <div class="col-lg-8">
                        <table class="table table-sm table-bordered border-bottom">
                            <thead>
                                <tr>
                                    <th class="align-middle">Type</th>
                                    <th class="align-middle">Days</th>
                                    <th class="align-middle">Amount</th>
                                    <th class="align-middle">Add : Actual Interest</th>
                                    <th class="align-middle">Less : Advance Interest</th>
                                    {{-- <th class="align-middle">Rebates</th> --}}
                                    <th class="align-middle">Still Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary['special_loans'] as $special_loan)
                                    <tr>
                                        <td>{{ $special_loan['type'] }}</td>
                                        <td class="text-center">{{ $special_loan['days'] }}</td>
                                        <td class="text-end">₱{{ number_format($special_loan['amount'], 2) }}</td>
                                        <td class="text-end">₱{{ number_format($special_loan['actual_interest'], 2) }}</td>
                                        <td class="text-end">₱{{ number_format($special_loan['advance_interest'], 2) }}</td>
                                        {{-- <td class="text-end">-</td> --}}
                                        <td class="text-end">₱{{ number_format($special_loan['still_due'], 2) }}</td>
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
                                    <td class="text-end">₱{{ number_format($summary['principal_due'], 2) }}</td>
                                </tr>
                                @foreach($summary['special_loans'] as $special_loan)
                                    <tr>
                                        <td class="fw-semibold">{{ strtoupper($special_loan['type']) }}</td>
                                        <td class="text-end">₱{{ number_format($special_loan['still_due'], 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="fw-semibold text-danger">TOTAL DUE</td>
                                    <td class="text-end text-danger fw-semibold">₱{{ number_format($summary['total_due'], 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection