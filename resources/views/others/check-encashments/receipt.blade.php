<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check-Encashment Receipt</title>
    <style>

        .net-proceeds {
            background-color: #e6e6e6; /* lighter grey highlight */
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
        .receipt { padding: 5px; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .spaced { margin-top: 10px; }
        .label { display: flex; justify-content: space-between; }
        .net-proceeds { background-color: #f0f0f0; padding: 3px 5px; font-size: 14px; }
        hr { border: none; border-top: 1px dashed #000; margin: 5px 0; }

        @media print {
            body { width: 80mm; }
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
            <span>Transaction Date:</span>
            <span>{{ $check->transDate->format('Y-m-d') }}</span>
        </div>

        <div class="label">
            <span>Bank Name:</span>
            <span>{{ $check->bankName ?? 'N/A' }}</span>
        </div>

        <div class="label">
            <span>Amount Encashment:</span>
            <span>{{ number_format($check->checkAmount, 2) }}</span>
        </div>

        <!-- Less: Service Fee -->
        <div class="spaced">
            <div class="bold">Less:</div>
            <div class="label" style="margin-left:10px;">
                <span>Service Fee:</span>
                <span>{{ number_format($check->charge, 2) }}</span>
            </div>
        </div>

        <!-- Net Proceeds -->
        <div class="spaced label net-proceeds">
            <span class="bold">Net Proceeds:</span>
            <span class="bold">{{ number_format($check->net_proceeds, 2) }}</span>
        </div>

        <div class="spaced label">
            <span>Reference No.:</span>
            <span>CE-{{ str_pad($check->id, 6, '0', STR_PAD_LEFT) }}</span>
        </div>

        <hr>
        <p class="center">Thank you!</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
