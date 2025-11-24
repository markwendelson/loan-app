<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Walk-in Receipt</title>
    <style>
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
        <!-- <div class="center spaced">Guiuan Lending - Corporation</div> -->
        <hr>

        <!-- Transaction details -->
        <div class="label">
            <span>Transaction Date:</span>
            <span>{{ $walkin->date->format('Y-m-d') }}</span>
        </div>

        <div class="label">
            <span>Client Name:</span>
            <span>{{ $walkin->last_name }}, {{ $walkin->first_name }} {{ $walkin->middle_initial }}</span>
        </div>

        <div class="label">
            <span>Amount Withdrawn:</span>
            <span>{{ number_format($walkin->amount_withdrawn, 2) }}</span>
        </div>

        <!-- Less: Service Fee -->
        <div class="spaced">
            <div class="bold">Less:</div>
            <div class="label" style="margin-left:10px;">
                <span>Service Fee:</span>
                <span>{{ number_format($walkin->charge, 2) }}</span>
            </div>
        </div>

        <!-- Net Proceeds -->
        <div class="spaced label net-proceeds">
            <span class="bold">Net Proceeds:</span>
            <span class="bold">{{ number_format($walkin->net_proceeds, 2) }}</span>
        </div>

        <div class="spaced label">
            <span>Reference No.:</span>
            <span>WC-{{ str_pad($walkin->id, 6, '0', STR_PAD_LEFT) }}</span>
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
