<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Receipt</title>
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
            <span>Date:</span>
            <span>{{ $loan->disbursed_date ? \Carbon\Carbon::parse($loan->disbursed_date)->format('Y-m-d') : date('Y-m-d') }}</span>
        </div>

        <div class="label">
            <span>Borrower:</span>
          <span>{{ $loan->borrower->details->fullname ?? 'N/A' }}</span>
        </div>

        <div class="label">
            <span>Loan No.:</span>
            <span>{{ $loan->loan_no }}</span>
        </div>

        <div class="label">
            <span>Principal:</span>
            <span>{{ number_format($loan->principal, 2) }}</span>
        </div>

        <!-- Less: Deductions -->
        <div class="spaced">
            <div class="bold">Less:</div>
            
            <div class="label" style="margin-left:10px;">
                <span>Adv. Interest:</span>
                <span>{{ number_format($loan->loanCharges->advance_interest ?? 0, 2) }}</span>
            </div>

            <div class="label" style="margin-left:10px;">
                <span>Service Fee:</span>
                <span>{{ number_format($loan->loanCharges->service_fee ?? 0, 2) }}</span>
            </div>

            <div class="label" style="margin-left:10px;">
                <span>Insurance Fee:</span>
                <span>{{ number_format($loan->loanCharges->insurance_fee ?? 0, 2) }}</span>
            </div>

            <div class="label" style="margin-left:10px;">
                <span>Notary Fee:</span>
                <span>{{ number_format($loan->loanCharges->notary_fee ?? 0, 2) }}</span>
            </div>

            @if(($loan->membership_fee ?? 0) > 0)
            <div class="label" style="margin-left:10px;">
                <span>Membership Fee:</span>
                <span>{{ number_format($loan->membership_fee, 2) }}</span>
            </div>
            @endif

            @if($loan->is_reloan)
                <div class="label" style="margin-left:10px;">
                    <span>Reloan Ded.:</span>
                    <span>{{ number_format($loan->loanCharges->reloan_deduction ?? 0, 2) }}</span>
                </div>
                @if(($loan->loanCharges->special_loan_balance ?? 0) > 0)
                <div class="label" style="margin-left:10px;">
                    <span>Special Bal.:</span>
                    <span>{{ number_format($loan->loanCharges->special_loan_balance, 2) }}</span>
                </div>
                @endif
            @endif

        </div>

        <!-- Net Proceeds -->
        <div class="spaced label net-proceeds">
            <span class="bold">Net Proceeds:</span>
            <span class="bold">{{ number_format($net_proceeds, 2) }}</span>
        </div>

        <hr>
        <p class="center">Received the above amount.</p>
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
