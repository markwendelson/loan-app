<div>
    <ul>
        <li>Principal Loan  <span class="advance_interest">{{number_format($loan->principal,2)}}</span></li>
        <li class="mt-3 mb-3">Deductions</li>
        <ul style="margin-left: 1rem;">
            <li>Advance Interest <span class="advance_interest">{{number_format($loan->loanCharges->advance_interest,2)}}</span></li>
            <li>Service Fee <span class="service_fee">{{number_format($loan->loanCharges->service_fee,2)}}</span></li>
            <li>Insurance Fee <span class="insurance_fee">{{number_format($loan->loanCharges->insurance_fee,2)}}</span></li>
            <li>Notary Fee <span class="notary_fee">{{number_format($loan->loanCharges->notary_fee,2)}}</span></li>
            @if($loan->is_reloan)
            <li>Reloan Deduction <span class="reloan_deduction">{{$loan->loanCharges->reloan_deduction}}</span></li>
            @endif
        </ul>
        @php
        $principal = $loan->principal ?? 0;
        $advance_interest = $loan->loanCharges->advance_interest ?? 0;
        $service_fee = $loan->loanCharges->service_fee ?? 0;
        $insurance_fee = $loan->loanCharges->insurance_fee ?? 0;
        $notary_fee = $loan->loanCharges->notary_fee ?? 0;
        $reloan_deduction = $loan->loanCharges->reloan_deduction ?? 0;
        $deductions = $advance_interest + $service_fee + $insurance_fee + $notary_fee + $reloan_deduction;
        $net_proceeds = $principal - $deductions;
        @endphp
        <li class="mt-3 mb-3">Net Proceeds <span class="net_proceeds">{{number_format($net_proceeds,2)}}</span></li>
        <li class="mt-3 mb-3">Monthly Amortization <span class="monthly_amortization">{{number_format($loan->loanFees->monthly_amortization,2)}}</span></li>
    </ul>
</div>