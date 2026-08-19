<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Walk-in Receipt</title>
        <style>
            @page {
                margin: 6px;
            }

            body {
                margin: 0;
                padding: 0;
                font-family: DejaVu Sans, sans-serif;
                font-size: 10px;
            }

            table {
                width: 100%;
                table-layout: fixed;
                border-collapse: collapse;
            }

            td {
                padding: 2px;
                vertical-align: middle;
            }

            .title {
                font-size: 14px;
                font-weight: bold;
                text-align: center;
            }

            .heading {
                font-size: 13px;
                font-weight: bold;
                text-align: center;
            }

            .bold {
                font-weight: bold;
            }

            .center {
                text-align: center;
            }

            .right {
                text-align: right;
            }

            .border-top {
                border-top: 1px dotted #000;
            }

            .border-bottom {
                border-bottom: 1px dotted #000;
            }

            .signature {
                border-top: 1px solid #000;
                text-align: center;
                font-weight: bold;
            }

            .footer {
                font-size: 9px;
                text-align: center;
                font-style: italic;
            }
        </style>

    </head>

    <body>
        <table>
            <!-- Branch -->
            <tr>
                <td colspan="3" class="title border-bottom">
                    {{strtoupper($branchTitle)}}
                </td>
            </tr>

            <tr>
                <td colspan="3" style="height:10px;"></td>
            </tr>
        </table>

        <table>
            <!-- Details -->
            <tr>
                <td class="bold">DATE</td>
                <td width="10" class="center bold">:</td>
                <td >{{ $walkin->date->format('Y-m-d') }}</td>
            </tr>

            <tr>
                <td class="bold">NAME</td>
                <td width="10" class="center bold">:</td>
                <td>{{ strtoupper($walkin->last_name) }}, {{ strtoupper($walkin->first_name) }} {{ strtoupper($walkin->middle_initial) }}</td>
            </tr>

            <tr>
                <td class="bold border-bottom">REFERENCE NO</td>
                <td width="50" class="center bold border-bottom">:</td>
                <td class="border-bottom">{{$walkin->reference_no}}</td>
            </tr>
        </table>

        <table>
            <tr>
                
                <td style="height:12px;"></td>
                <td></td>
                <td></td>
            </tr>

            <!-- Transaction -->
            <tr>
                <td colspan="3" class="heading border-top border-bottom">
                    WALK-IN TRANSACTION
                </td>
            </tr>

            <tr>
                <td colspan="3" style="height:12px;"></td>
            </tr>

            <!-- Total -->
            <tr>
                <td class="bold">TOTAL WITHDRAWN</td>
                <td class="center bold">:</td>
                <td class="right">{{ number_format($walkin->amount_withdrawn, 2) }}</td>
            </tr>

            <tr>
                <td colspan="3" style="height:8px;"></td>
            </tr>

            <!-- Less -->
            <tr>
                <td class="bold">LESS :</td>
                <td class="center">SERVICE FEE</td>
                <td class="right">{{ number_format($walkin->charge, 2) }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td colspan="3" style="height:10px;"></td>
            </tr>
        </table>

        <table>
            <!-- Net -->
            <tr>
                <td class="bold border-top" colspan="2" style="font-size:12px;">
                    NET PROCEEDS
                </td>

                <td class="right border-top">
                    {{ number_format($walkin->net_proceeds, 2) }}
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td colspan="3" style="height:10px;"></td>
            </tr>
        </table>

        <table>
            <!-- Mode -->
            <tr>
                <td class="bold">Mode of Disbursement</td>
                <td class="center bold">:</td>
                <td>CASH</td>
            </tr>
        </table>

        <table>
            <tr>
                <td colspan="3" style="height:75px;"></td>
            </tr>
        </table>

        <table>
            <!-- Signature -->
            <tr>
                <td></td>
                <td align="center" class="signature" style="width:150px !important">Signature</td>
                <td></td>
            </tr>
        </table>

        <table>
            <tr>
                <td colspan="3" style="height:15px;"></td>
            </tr>
            <tr>
                <td colspan="3" class="footer">
                    <strong>**Merchant's Copy**</strong>
                </td>
            </tr>
        </table>
    </body>
</html>