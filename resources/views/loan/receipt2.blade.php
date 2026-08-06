<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">

        <style>
            @page {
                margin: 5px;
            }

            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 11px;
                color: #000;
                margin: 0;
                padding: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td {
                padding: 2px 0;
                vertical-align: top;
            }

            .title {
                text-align: center;
                font-weight: bold;
                font-size: 15px;
                border-bottom: 1px dotted #000;
                padding-bottom: 5px;
            }

            .section {
                margin-top: 8px;
                margin-bottom: 5px;
            }

            .section-title {
                text-align: center;
                font-weight: bold;
                border-top: 1px dotted #000;
                border-bottom: 1px dotted #000;
                padding: 4px;
            }

            .label {
                width: 42%;
                font-weight: bold;
            }

            .colon {
                width: 5%;
                text-align: center;
            }

            .value {
                width: 53%;
            }

            .amount {
                width: 30%;
                text-align: right;
            }

            .bold {
                font-weight: bold;
            }

            .big {
                font-size: 13px;
                font-weight: bold;
            }

            .line td {
                border-top: 1px dotted #000;
                height: 5px;
            }

            .indent {
                padding-left: 10px;
            }
        </style>
    </head>

    <body>
        <table>
            <tr>
                <td colspan="4" class="title">
                    {{strtoupper($branchTitle)}}
                </td>
            </tr>

            <tr><td colspan="4" style="height:8px;"></td></tr>

            <tr>
                <td class="label">DATE</td>
                <td class="colon">:</td>
                <td colspan="2">{{ $loan->disbursed_date ? \Carbon\Carbon::parse($loan->disbursed_date)->format('Y-m-d') : date('Y-m-d') }}</td>
            </tr>

            <tr>
                <td class="label">NAME</td>
                <td class="colon">:</td>
                <td colspan="2">{{ strtoupper($loan->borrower->details->fullname) ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td class="label">CLIENT NO</td>
                <td class="colon">:</td>
                <td colspan="2">{{$loan->borrower->client_code ?? ''}}</td>
            </tr>

            <tr>
                <td class="label">LOAN PRODUCT</td>
                <td class="colon">:</td>
                <td colspan="2">SALARY LOAN</td>
            </tr>

            <tr>
                <td class="label">LOAN NO.</td>
                <td class="colon">:</td>
                <td colspan="2">{{$loan->loan_no}}</td>
            </tr>

            <tr>
                <td class="label">REFERENCE NO.</td>
                <td class="colon">:</td>
                <td colspan="2">{{$loan->reference_no ?? ''}}</td>
            </tr>

        </table>

        <table class="section">

            <tr>
                <td colspan="4" class="section-title">
                    LOAN TRANSACTION
                </td>
            </tr>

            <tr><td colspan="4" style="height:5px;"></td></tr>

            <tr>
                <td class="label bold">PRINCIPAL LOAN</td>
                <td class="colon">:</td>
                <td></td>
                <td class="amount">{{ number_format($loan->principal, 2) }}</td>
            </tr>
        </table>

        <table class="section">
            <tr>
                <td class="label bold">LESS :</td>
            </tr>

            <tr>
                <td style="width:40px !important;"></td>
                <td colspan="2" class="indent">Advance Interest</td>
                <td class="amount">{{ number_format($loan->loanCharges->advance_interest ?? 0, 2) }}</td>
            </tr>

            <tr>
                <td></td>
                <td colspan="2" class="indent">Service Fee</td>
                <td class="amount">{{ number_format($loan->loanCharges->service_fee ?? 0, 2) }}</td>
            </tr>

            <tr>
                <td></td>
                <td colspan="2" class="indent">Insurance</td>
                <td class="amount">{{ number_format($loan->loanCharges->insurance_fee ?? 0, 2) }}</td>
            </tr>

            <tr>
                <td></td>
                <td colspan="2" class="indent">Notary</td>
                <td class="amount">{{ number_format($loan->loanCharges->notary_fee ?? 0, 2) }}</td>
            </tr>

            @if(($loan->loanCharges->membership_fee ?? 0) > 0)
            <tr>
                <td></td>
                <td colspan="2" class="indent">Membership Fee</td>
                <td class="amount">{{ number_format($loan->loanCharges->membership_fee_paid, 2) }}</td>
            </tr>
            @endif

            <tr>
                <td></td>
                <td colspan="2" class="indent">Principal Advance</td>
                <td class="amount">{{ number_format($loan->loanCharges->principal_advance, 2) }}</td>
            </tr>

            <tr>
                <td></td>
                <td colspan="2" class="indent">Redeem</td>
                <td class="amount">{{ number_format($loan->loanCharges->redeem_charges, 2) }}</td>
            </tr>

            @if($loan->is_reloan)
            <tr>
                <td></td>
                <td colspan="2" class="indent">Old Loan</td>
                <td>{{ number_format($loan->loanCharges->reloan_deduction ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2" class="indent">Special Balance</td>
                <td class="amount">{{ number_format($loan->loanCharges->special_loan_balance, 2) }}</td>
            </tr>
            @endif

            <tr class="line">
                <td colspan="4"></td>
            </tr>

            <tr>
                <td class="label" style="width: 100px !important">NET PROCEEDS</td>
                <td>:</td>
                <td></td>
                <td class="amount">{{ number_format($net_proceeds, 2) }}</td>
            </tr>
        </table>

        <table class="section">

            <tr>
                <td class="label" style="width: 170px !important;height:70px !important">MODE OF DISBURSEMENT</td>
                <td class="colon">:</td>
                <td align="right">{{strtoupper($loan->mode_of_payment ?? '')}}</td>
            </tr>

        </table>

        {{-- <table class="section">
            <tr class="line">
                <td class="label" style="width:130px !important;" >DESCRIPTION</td>
                <td class="colon">:</td>
                <td align="right">Sample Description</td>
            </tr>
            <tr>
                <td class="label">PRINCIPAL BALANCE</td>
                <td class="colon">:</td>
                <td align="right">39,0000.00</td>
            </tr>
            <tr>
                <td style="height:30px !important"></td>
                <td></td>
                <td></td>
            </tr>
        </table> --}}

        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="25%"></td>
                <td width="50%" align="center" style="border-top:1px solid #000; padding-top:3px;">
                    Signature
                </td>
                <td width="25%"></td>
            </tr>
        </table>

    </body>

</html> 