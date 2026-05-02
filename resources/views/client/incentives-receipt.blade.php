<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Incentives Receipt</title>
    <style>
        .net-proceeds {
            background-color: #e6e6e6;
            /* lighter grey highlight */
            padding: 6px 8px;
            font-size: 14px;
            border-radius: 4px;
        }

        body {
            width: 80mm;
            font-family: monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .receipt {
            padding: 5px;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .spaced {
            margin-top: 10px;
        }

        .label {
            display: flex;
            justify-content: space-between;
        }

        .net-proceeds {
            background-color: #f0f0f0;
            padding: 3px 5px;
            font-size: 14px;
        }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        @media print {
            body {
                width: 80mm;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">

        <!-- DYNAMIC HEADER -->
        <div class="center bold">{{ $branchTitle }}</div>
        <hr>

        <!-- Transaction details -->
        <div class="label">
            <span>Date:</span>
            <span>{{ $incentives->transaction_date ? \Carbon\Carbon::parse($incentives->transaction_date)->format('Y-m-d') : date('Y-m-d') }}</span>
        </div>

        <div class="label">
            <span>Borrower:</span>
            <span>{{ $incentives->borrower->details->fullname ?? 'N/A' }}</span>
        </div>

        <div class="label">
            <span>Incentive No.:</span>
            <span>{{ $incentives->incentive_no }}</span>
        </div>

        <div class="label">
            <span>Description:</span>
            <span>{{ $incentives->description }}</span>
        </div>

        <div class="spaced label net-proceeds">
            <span class="bold">Amount:</span>
            <span class="bold">{{ number_format($incentives->amount, 2) }}</span>
        </div>

        <hr>
        <p class="center">Received the above amount.</p>
        <br>

        <div class="center" style="border-top: 1px solid #000; width: 80%; margin: 20px auto 0 auto; padding-top: 5px;">
            Signature over Printed Name
        </div>
    </div>

    <script>
        window.onload = function () {
            window.print();
        }
        window.onafterprint = function () {
            window.close();
        }
    </script>
</body>

</html>