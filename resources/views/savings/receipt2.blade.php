<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Savings Receipt</title>

        <link rel="icon" href="{{url('assets/images/favicon.png')}}" type="image/x-icon">
        <link rel="shortcut icon" href="{{url('assets/images/favicon.png')}}" type="image/x-icon">
        <style>
            @page {
                margin: 5px;
            }

            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 10px;
                margin: 0;
                padding: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            td {
                padding: 2px;
                vertical-align: top;
            }

            .border {
                border: 1px solid #000;
            }

            .border-top {
                border-top: 1px dotted #000;
            }

            .border-bottom {
                border-bottom: 1px dotted #000;
            }

            .center {
                text-align: center;
            }

            .right {
                text-align: right;
            }

            .bold {
                font-weight: bold;
            }

            .title {
                font-size: 12px;
                font-weight: bold;
            }

            .space {
                height: 15px;
            }

            .signature {
                border-top: 1px solid #000;
                text-align: center;
                padding-top: 3px;
            }

            .small {
                font-size: 9px;
            }
        </style>
    </head>
    <body>
        <table>
            <!-- Branch -->
            <tr>
                <td class="center title border-bottom" colspan="2">
                    {{$branchTitle}}
                </td>
            </tr>

            <!-- Client Information -->
            <tr>
                <td width="40%" class="bold">DATE</td>
                <td>: {{ \Carbon\Carbon::parse($savings->transaction_date)->format('Y-m-d') }}  </td>
            </tr>

            <tr>
                <td class="bold">NAME</td>
                <td>: {{ strtoupper($savings->client->details->fullname) ?? 'N/A' }}</td>
            </tr>

            <tr>
                <td class="bold">CLIENT NO</td>
                <td>: {{ $savings->client->client_code ?? '' }}</td>
            </tr>

            <tr>
                <td class="bold border-bottom">REFERENCE NO</td>
                <td class="border-bottom">: {{ $savings->reference_no ?? '' }}</td>
            </tr>

            <!-- Payment Transaction -->
            <tr>
                <td colspan="2" class="center bold border-bottom border-top">
                    PAYMENT TRANSACTION
                </td>
            </tr>

            <tr>
                {{-- <td class="bold">TOTAL WITHDRAWN</td> --}}
                <td class="bold">TOTAL {{ strtoupper($savings->transaction_type) }}</td>
                <td class="right">{{ number_format($savings->amount, 2) }}</td>
            </tr>

            <!-- Less -->
            @if($savings->transaction_type == 'withdraw')
                <tr>
                    <td class="bold">LESS :</td>
                    <td></td>
                </tr>   

                <tr>
                    <td>&nbsp;&nbsp;&nbsp;MEMBERSHIP FEE</td>
                    <td class="right">{{ number_format($savings->membership_fee, 2) }}</td>
                </tr>

                <tr>
                    <td>&nbsp;&nbsp;&nbsp;INSURANCE</td>
                    <td class="right">{{ number_format($savings->insurance, 2) }}</td>
                </tr>

                <tr>
                    <td>&nbsp;&nbsp;&nbsp;INTEREST</td>
                    <td class="right">{{ number_format($savings->interest, 2) }}</td>
                </tr>

                <tr>
                    <td>&nbsp;&nbsp;&nbsp;PRINCIPAL</td>
                    <td class="right">{{ number_format($savings->principal, 2) }}</td>
                </tr>

                {{-- <tr>
                    <td>&nbsp;&nbsp;&nbsp;OTHER LOAN</td>
                    <td class="right">₱ 1,000.00</td>
                </tr> --}}

                <tr>
                    <td class="bold border-top">CHANGE :</td>
                    <td class="right bold border-top">{{ number_format($savings->due_change, 2) }}</td>
                </tr>


                <!-- Mode -->
                @if(isset($savings->mode_of_payment) && $savings->mode_of_payment)
                <tr>
                    <td class="bold">Mode of Disbursement</td>
                    <td>: {{ strtoupper(config('settings.mode_of_payment.'.$savings->mode_of_payment, $savings->mode_of_payment)) }}</td>
                </tr>
                @endif
            @endif

            <tr class="space">
                <td colspan="2">&nbsp;</td>
            </tr>

            <!-- Description -->
            @if(isset($savings->description) && $savings->description)
            <tr>
                <td class="bold">Description</td>
                <td>: {{ $savings->description }}</td>
            </tr>
            @endif

            <tr>
                <td class="bold">Principal Balance</td>
                <td>: {{ number_format($savings->principal_balance, 2) }}</td>
            </tr>

            <tr>
                <td colspan="2" style="height:45px;"></td>
            </tr>

            <!-- Signature -->
            <tr>
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td width="25%"></td>

                            <td width="50%" class="signature">
                                Signature
                            </td>

                            <td width="25%"></td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="center small">
                    **Thank You**
                </td>
            </tr>

        </table>

    </body>
</html>