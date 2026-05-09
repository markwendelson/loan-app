<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title">Membership</h4>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tblMembership">
                <thead class="bg-default">
                    <tr>
                        <!-- <th class="text-center" width="40">Id</th> -->
                        <th class="text-center" width="80">Transaction Date</th>
                        <th class="text-center" width="150">Description</th>
                        <th class="text-center" width="80">Amount</th>
                    </tr>
                    <tbody>
                        <tr>
                            <td>{{ $ploan->loan_date }}</td>
                            <td>Membership Fee</td>
                            <td class="text-end">{{ $ploan->loanCharges->membership_fee ?? number_format(0,2) }}</td>
                        </tr>
                        {{-- membership fee payments --}}
                        @if($ploan->loanCharges->membership_fee_paid != 0)
                        <tr>
                            <td>{{ $ploan->loan_date }}</td>
                            <td>Membership Fee Paid</td>
                            <td class="text-end">({{ $ploan->loanCharges->membership_fee_paid ?? number_format(0,2) }})</td>
                        </tr>
                        @endif
                        @php
                            $payments = \App\Models\Savings::where('loan_id', $ploan->id)->where('transaction_type', 'withdraw')->where('membership_fee',0)->get();
                            $total = $payments->sum('membership_fee') + $ploan->loanCharges->membership_fee_paid;
                            $balance = $ploan->loanCharges->membership_fee - $total;
                        @endphp
                        @foreach ($payments as $p)
                            <tr>
                                <td>{{ $p->transaction_date }}</td>
                                <td>Membership Fee Paid</td>
                                <td>({{ $p->membership_fee }})</td>
                            </tr>
                        @endforeach
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Balance</th>
                                <th class="text-end">{{ number_format($balance,2) }}</th>
                            </tr>
                        </tfoot>
                    </tbody>
                </thead>
            </table>
        </div>
    </div>
</div>