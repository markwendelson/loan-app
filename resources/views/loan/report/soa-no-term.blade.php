
@extends('layouts.pdf')

@section('title') SOA No Term @endsection

@push('css')
    <style>
        .font-12 {
            font-size: 12px !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <!-- report header -->
            <div class="header-container mt-5">
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

                <div class="row justify-content-start gap-5 mt-4">
                    <div class="col-lg-4">
                        <div class="d-flex justify-content-between flex-wrap">
                            <span class="fw-semibold font-12">NAME: * </span>
                            <span class="fw-semibold font-12">{{ strtoupper($loan->borrower->details->lastname) }}, {{ strtoupper($loan->borrower->details->firstname) }} {{ strtoupper($loan->borrower->details->middlename) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold font-12">HOME ADDRESS: * </span>
                            <span class="fw-semibold font-12">{{ $loan->borrower->details->present_full_address ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold font-12">WORK ADDRESS: * </span>
                            <span class="fw-semibold font-12">{{ $loan->borrower->jobs->first()->office_name ?? 'N/A' }}</span>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        @php
                            $coMaker = $loan->borrower->coMakers()->wherePivot('loan_id', $loan->id)->first();
                        @endphp
                        <div class="d-flex justify-content-between flex-wrap">
                            <span class="fw-semibold font-12">CO MAKER: * </span>
                            <span class="fw-semibold font-12">{{ $coMaker ? strtoupper($coMaker->details->fullname) : '' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold font-12">HOME ADDRESS: * </span>
                            <span class="fw-semibold font-12">{{ $coMaker ? $coMaker->details->present_full_address : '' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold font-12">WORK ADDRESS: * </span>
                            <span class="fw-semibold font-12">{{ $coMaker ? ($coMaker->jobs->first()->office_name ?? 'N/A') : '' }}</span>
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
                            <th rowspan="2" class="align-middle font-12">TRANSACTION DATE</th>
                            <th rowspan="2" class="align-middle font-12">NUMBER OF DAYS</th>
                            <th rowspan="2" class="align-middle font-12">PRINCIPAL LOAN</th>
                            <th rowspan="2" class="align-middle font-12">INTEREST CHARGE</th>
                            <th rowspan="2" class="align-middle font-12">ADVANCE INTEREST</th>
                            <th rowspan="2" class="align-middle font-12">DEDUCTED TO LOAN RENEWAL</th>

                            <th colspan="4" class="align-middle text-center font-12">PAYMENTS</th>

                            <th rowspan="2" class="align-middle font-12">PRINCIPAL BALANCE</th>
                            {{-- <th rowspan="2" class="align-middle font-12">REMARKS</th>
                            <th rowspan="2" class="align-middle font-12">SERVICE FEE</th> --}}
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
                                <td class="text-end font-12">{{ $row['principal_loan'] > 0 ? number_format($row['principal_loan'], 2) : '-' }}</td>
                                <td class="text-center font-12">{{ $row['interest_charge'] }}</td>
                                <td class="text-end font-12">{{ $row['advance_interest'] > 0 ? number_format($row['advance_interest'], 2) : '-' }}</td>
                                <td class="text-end font-12">{{ $row['deducted_to_loan_renewal'] > 0 ? number_format($row['deducted_to_loan_renewal'], 2) : '-' }}</td>
                                <td class="text-end font-12">{{ $row['interest'] > 0 ? number_format($row['interest'], 2) : '-' }}</td>
                                <td class="text-end font-12">{{ $row['payment_principal'] > 0 ? number_format($row['payment_principal'], 2) : '-' }}</td>
                                <td class="text-end font-12">{{ $row['ins'] > 0 ? number_format($row['ins'], 2) : '-' }}</td>
                                <td class="text-end font-12">{{ $row['savings'] > 0 ? number_format($row['savings'], 2) : '-' }}</td>
                                <td class="text-end font-12">{{ $row['principal_balance'] > 0 ? number_format($row['principal_balance'], 2) : '-' }}</td>
                                {{-- <td class="font-12">{{ $row['remarks'] }}</td>
                                <td class="text-end font-12">{{ $row['service_fee'] > 0 ? number_format($row['service_fee'], 2) : '-' }}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-secondary-subtle">
                        <tr>
                            <td class="text-center fw-semibold font-12">Total</td>
                            <td></td>
                            <td></td>
                            <td class="text-end fw-semibold font-12">{{ $total_principal_loan > 0 ? number_format($total_principal_loan, 2) : '-' }}</td>
                            <td></td>
                            <td class="text-end fw-semibold font-12">{{ $total_advance_interest > 0 ? number_format($total_advance_interest, 2) : '-' }}</td>
                            <td class="text-end fw-semibold font-12">{{ $total_deducted_to_loan_renewal > 0 ? number_format($total_deducted_to_loan_renewal, 2) : '-' }}</td>
                            <td class="text-end fw-semibold font-12">{{ $total_interest > 0 ? number_format($total_interest, 2) : '-' }}</td>
                            <td class="text-end fw-semibold font-12">{{ $total_payment_principal > 0 ? number_format($total_payment_principal, 2) : '-' }}</td>
                            <td class="text-end fw-semibold font-12">{{ $total_ins > 0 ? number_format($total_ins, 2) : '-' }}</td>
                            <td class="text-end fw-semibold font-12">{{ $total_savings > 0 ? number_format($total_savings, 2) : '-' }}</td>
                            <td></td>
                            {{-- <td></td> --}}
                            {{-- <td class="text-end fw-semibold font-12">{{ $total_service_fee > 0 ? number_format($total_service_fee, 2) : '-' }}</td> --}}
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Other Charges Section -->
            <div class="mt-3">
                <span>Special Loan</span>
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
                            @foreach($summary['special_loans'] as $special_loan)
                                <tr>
                                    <td class="font-12">{{ $special_loan['type'] }}</td>
                                    <td class="text-center font-12">{{ $special_loan['days'] }}</td>
                                    <td class="text-end font-12">{{ number_format($special_loan['amount'], 2) }}</td>
                                    <td class="text-end font-12">-</td>
                                    <td class="text-end font-12">-</td>
                                    <td class="text-end font-12">-</td>
                                    <td class="text-end font-12">{{ number_format($special_loan['still_due'], 2) }}</td>
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
                                <td class="text-end font-12">{{ number_format($summary['total_interest_charged'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold font-12">LESS : ADVANCE INTEREST </td>
                                <td class="text-end font-12">{{ number_format($summary['less_advance_interest'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold font-12">TOTAL INTEREST COLLECTED</td>
                                <td class="text-end font-12">{{ number_format($summary['total_interest_collected'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-danger font-12">REBATES</td>
                                <td class="text-end font-12">{{ number_format($summary['rebates'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-sm table-bordered border-bottom mt-3">
                        <tbody>
                            <tr>
                                <td class="fw-semibold font-12">PRINCIPAL AMOUNT STILL DUE</td>
                                <td class="text-end font-12">{{ number_format($summary['principal_due'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold font-12">LESS: REBATES</td>
                                <td class="text-end font-12">{{ number_format($summary['rebates'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold font-12">TOTAL AMOUNT DUE</td>
                                <td class="text-end font-12">{{ number_format($summary['total_amount_due'], 2) }}</td>
                            </tr>
                            @foreach($summary['special_loans'] as $special_loan)
                                <tr>
                                    <td class="fw-semibold font-12">{{ strtoupper($special_loan['type']) }}</td>
                                    <td class="text-end font-12">{{ number_format($special_loan['still_due'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="fw-bold font-12">TOTAL AMOUNT DUE</td>
                                <td class="text-end font-12">{{ number_format($summary['final_total_due'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection