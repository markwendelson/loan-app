<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Savings Receipt</title>
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
            <span>Date:</span>
            <span>{{ \Carbon\Carbon::parse($savings->transaction_date)->format('Y-m-d') }}</span>
        </div>

        @if(isset($savings->reference_no) && $savings->reference_no)
            <div class="label">
                <span>Reference No:</span>
                <span>{{ $savings->reference_no }}</span>
            </div>
        @endif

        <div class="label">
            <span>Client:</span>
            <span>{{ $savings->client->details->fullname ?? 'N/A' }}</span>
        </div>
        <br>

        <div class="label">
            <span>Transaction:</span>
            <span>{{ ucfirst($savings->transaction_type) }}</span>
        </div>

        @if($savings->mode_of_payment)
        <div class="label">
            <span>Mode of Payment:</span>
            <span>{{ config('settings.mode_of_payment.'.$savings->mode_of_payment, $savings->mode_of_payment) }}</span>
        </div>
        @endif

       

        <br>
        <div class="label">
            <span>Total:</span>
            <span class="bold">{{ number_format($savings->amount, 2) }}</span>
        </div>
        @if($savings->transaction_type == 'deposit')
        <div class="label">
            <span>Amount:</span>
            <span class="bold">{{ number_format($savings->amount, 2) }}</span>
        </div>
        @endif

        @if($savings->transaction_type == 'withdraw')
        <div class="label">
            <span>Interest:</span>
            <span class="bold">{{ number_format($savings->interest, 2) }}</span>
        </div>
        <div class="label">
            <span>Insurance:</span>
            <span class="bold">{{ number_format($savings->insurance, 2) }}</span>
        </div>
        <div class="label">
            <span>Principal:</span>
            <span class="bold">{{ number_format($savings->principal, 2) }}</span>
        </div>
        <div class="label">
            <span>Membership Fee:</span>
            <span class="bold">{{ number_format($savings->membership_fee, 2) }}</span>
        </div>
        
        <div class="label">
            <span>Due Change:</span>
            <span class="bold">{{ number_format($savings->due_change, 2) }}</span>
        </div>

            @if($savings->mode_of_payment)
            <div class="label">
                <span>Code:</span>
                <span>{{ $savings->mode_of_payment }}</span>
            </div>
            @endif
        @endif

        @if($savings->description)
        <div class="spaced">
            <span>Description:</span><br>
            <span>{{ $savings->description }}</span>
        </div>
        @endif

        <hr>
        <div class="label">
            <span>Principal Balance:</span>
            <span class="bold">{{ number_format($savings->principal_balance, 2) }}</span>
        </div>
        <br>
        <hr>
        <p class="center">Transaction Successful</p>
        <br>

        <div class="center" style="border-top: 1px solid #000; width: 80%; margin: 20px auto 0 auto; padding-top: 5px;">
            Signature over Printed Name
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>
</html>
