<!DOCTYPE html>
<html>
    <head>
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
                    BORONGAN BRANCH
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
                <td class="right">₱ 25,000.00</td>
            </tr>

            <!-- Less -->
            <tr>
                <td class="bold">LESS :</td>
                <td></td>
            </tr>

            <tr>
                <td>&nbsp;&nbsp;&nbsp;MEMBERSHIP FEE</td>
                <td class="right">₱ 500.00</td>
            </tr>

            <tr>
                <td>&nbsp;&nbsp;&nbsp;INSURANCE</td>
                <td class="right">₱ 300.00</td>
            </tr>

            <tr>
                <td>&nbsp;&nbsp;&nbsp;INTEREST</td>
                <td class="right">₱ 1,500.00</td>
            </tr>

            <tr>
                <td>&nbsp;&nbsp;&nbsp;PRINCIPAL</td>
                <td class="right">₱ 5,000.00</td>
            </tr>

            <tr>
                <td>&nbsp;&nbsp;&nbsp;OTHER LOAN</td>
                <td class="right">₱ 1,000.00</td>
            </tr>

            <tr>
                <td class="bold border-top">CHANGE :</td>
                <td class="right bold border-top">₱ 16,700.00</td>
            </tr>

            <!-- Mode -->
            <tr>
                <td class="bold">Mode of Disbursement</td>
                <td>: CASH</td>
            </tr>

            <tr class="space">
                <td colspan="2">&nbsp;</td>
            </tr>

            <!-- Description -->
            <tr>
                <td class="bold">Description</td>
                <td>: Loan Release</td>
            </tr>

            <tr>
                <td class="bold">Principal Balance</td>
                <td>: ₱ 45,000.00</td>
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
                    **Thanks You**
                </td>
            </tr>

        </table>

    </body>
</html>