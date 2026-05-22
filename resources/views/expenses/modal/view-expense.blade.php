@php
    $items = $expenseData['expense_items'] ?? [];
    $attachments = $expenseData['expense_attachments'] ?? [];
@endphp

<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">View Expense Details</h4>
    </div>
    <div class="modal-body">
        <!-- main info contianer -->
        <div class="main-info-container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <table class="border-0">
                        <tbody>
                            <tr>
                                <td width="120" class="bg-light-subtle text-dark fw-semibold">Date</td>
                                <td>{{ date('M d, Y', strtotime($expenseData['expense_date'])) }}</td>
                            </tr>
                            <tr>
                                <td width="120" class="bg-light-subtle text-dark fw-semibold">Branch</td>
                                <td>{{ $expenseData['branch_name'] }}</td>
                            </tr>
                            <tr>
                                <td width="120" class="bg-light-subtle text-dark fw-semibold">Type</td>
                                <td>{{ $expenseData['category_name'] }}</td>
                            </tr>
                            <tr>
                                <td width="120" class="bg-light-subtle text-dark fw-semibold">Total Expense</td>
                                <td>{{ number_format($expenseData['amount'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-3">
                    <table>
                        <tbody>
                            <tr>
                                <td width="150" class="bg-light-subtle text-dark fw-semibold">Date Processed</td>
                                <td>{{ date('M-d-Y', strtotime($expenseData['created_at'])) }}</td>
                            </tr>
                            <tr>
                                <td width="150" class="bg-light-subtle text-dark fw-semibold">Processed By</td>
                                <td>{{ $expenseData['user_name'] ?? ''; }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- table container -->
        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm border-bottom">
                <thead>
                    <tr>
                        <th width="300">Vendor / Payee</th>
                        <th>Description</th>
                        <th width="300">Reference No.</th>
                        <th class="text-center" width="150">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($items) && $items)
                        @foreach($items as $key => $val)
                            <tr>
                                <td>{{ $val['vendor_name'] }}</td>
                                <td>{{ $val['description'] }}</td>
                                <td>{{ $val['ref_no'] }}</td>
                                <td class="text-end">{{ number_format($val['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-light-subtle">
                        <td colspan="2"></td>
                        <td colspan="2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Total:</span>
                                <span class="fw-semibold">{{ number_format($expenseData['amount'], 2) }}</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- attachments container -->
        <div class="attachments-container mt-3">
            <span class="fw-semibold">Attachments:</span>
            <div class="d-flex justify-content-start flex-wrap gap-2">
                @if(isset($attachments) && $attachments)
                    @foreach($attachments as $key => $val)
                        <a href="#" class="position-relative">
                            <div class="attachment-image border rounded">
                                <img class="rounded " 
                                    height="100" 
                                    width="100" 
                                    src="{{ Storage::url('expenses/attachments/' . $expenseData['slug'] . '/' . $val['name']) }}"
                                    loading="lazy"
                                >
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
    </div>
</div>