<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <div>Loan ID: {{$loan['loan_no']}}</div>
                <div>Loan Product: {{$loan->loanProduct->name}}</div>
                <div>Date Loan Granted: {{$loan['approved_date'] ?? date('l, F j, Y, g:i A',strtotime($loan['approved_date']))}}</div>
                <div>Remarks: <p></p></div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tblLedger" style="font-size: smaller;">
                <thead class="bg-default">
                    <tr>
                        <th class="text-center" width="80" colspan="3" rowspan="2"></th>
                        <th class="text-center" width="150" colspan="6">LOAN</th>
                        <th class="text-center" width="80"></th>
                        <th class="text-center" width="150" colspan="7">PAYMENT</th>
                        <th class="text-center" width="80" colspan="3" rowspan="2"></th>
                    </tr>
                    <tr>
                        <th rowspan="3">Principal</th>
                        <th colspan="4">Deduction</th>
                        <th rowspan="3">Net Proceed</th>
                        <th></th>
                        <th colspan="7" style="border-right-width:1px;">Applied to</th>
                    </tr>
                    <tr>
                        <th rowspan="2">Transaction Date</th>
                        <th rowspan="2">Withdraw Date</th>
                        <th rowspan="2">Salary Month</th>
                        <th rowspan="2">Interest</th>
                        <th rowspan="2">Service Fee</th>
                        <th rowspan="2">Insurance Fee</th>
                        <th rowspan="2">Net Proceed</th>
                        <th rowspan="2"></th>
                        <th rowspan="2">Transaction Date</th>
                        <th colspan="2">Interest</th>
                        <th colspan="2">Insurance</th>
                        <th rowspan="2">Principal</th>
                        <th rowspan="2">Change</th>
                        <th rowspan="2">Balance</th>
                        <th rowspan="2">Remarks</th>
                        <th rowspan="2">Deficit</th>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <th>Month</th>
                        <th>Amount</th>
                        <th>Month</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>