@php
    // dump($expenseData);
    $items = $expenseData['expense_items'] ?? '';
@endphp

<div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title">Edit Expense Data</h4>
    </div>
    <div class="modal-body">
        <form id="expenseForm">
            <input type="hidden" name="expense_id" id="expenseId" value="{{ $expenseData['id'] ?? ''; }}">
            <!-- slug -->
            <input type="hidden" name="slug" value="{{ $expenseData['slug'] ?? ''; }}">

            <!-- amount -->
            <input type="hidden" name="total_amount" value="{{ $expenseData['amount']  ?? 0;}}">

            <!-- deleted attachments -->
            <input type="hidden"
                name="deleted_attachments"
                id="deletedAttachments"
            >

            <!-- deleted items -->
            <input type="hidden"
                name="deleted_items"
                id="deletedItems"
            >

            <div class="row">
                <!-- date -->
                <div class="col-lg-2">
                    <label class="w-100">
                        Date <span class="text-danger">*</span>
                        <input type="date" 
                            class="form-control" 
                            id="expense_date" 
                            name="expense_date" 
                            value="{{ date('Y-m-d', strtotime($expenseData['expense_date'])) }}"
                            required
                        >
                    </label>
                </div>

                <!-- expense type -->
                <div class="col-lg-3">
                    <label class="w-100">
                        Expense Type <span class="text-danger">*</span>
                        <select class="form-select" name="category_id" required>
                            <option value="" selected hidden disabled>-- Expense Type --</option>
                            @if(isset($categories) && !empty($categories))
                                @foreach($categories as $key => $val)
                                    <option value="{{ $val->id }}" {{ isset($expenseData['category_id']) && $expenseData['category_id'] == $val->id ? 'selected' : ''; }}>{{ $val->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </label>
                </div>

                <!-- branch -->
                <div class="col-lg-3">
                    <label class="w-100">
                        Branch <span class="text-danger">*</span>
                        <select class="form-select"
                            name="branch_id"
                            required
                            {{ isset($user->current_branch_id) && $user->current_branch_id ? 'disabled' : ''; }}
                        >
                            <option value="" selected hidden disabled>-- Branch --</option>
                            @if(isset($branches) && $branches)
                                @foreach($branches as $key => $val)
                                    <option value="{{ $val->id }}" {{ isset($user->current_branch_id) && $user->current_branch_id == $val->id ? 'selected' : '';  }}>{{ $val->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </label>
                </div>

                <!-- payment method -->
                <div class="col-lg-3">
                    <label class="w-100">
                        Payment Method <span class="text-danger">*</span>
                        <select name="payment_method"
                            class="form-select"
                            required
                        >
                            <option value="" selected hidden disabled>-- Payment Method --</option>
                            @foreach(config('constants.payment_methods') as $key => $val)
                                <option value="{{ $key }}" {{ isset($expenseData['payment_method']) && $expenseData['payment_method'] == $key ? 'selected' : ''; }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="mt-2">
                    <label for="description" class="form-label">Notes</label>
                    <textarea class="form-control" 
                        id="description" 
                        name="notes"
                        placeholder="Additional details about the expense, if needed." 
                        rows="3"
                    >{{ $expenseData['description'] ?? ''; }}</textarea>
                </div>
            </div>
            <hr>
            <!-- line items -->
            <div class="card mt-2">
                <div class="card-header d-flex justify-content-end">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btnRefreshVendor"><i class="fa fa-refresh"></i> Refresh Vendor</button>
                        <button type="button" 
                            class="btn btn-primary btnAddline"
                            data-addline-target-table="#expenseEditModal #expenseItems tbody"
                            data-target-table="#expenseEditModal #expenseItems tbody"
                            data-parent-modal="#expenseEditModal"
                        ><i class="fa fa-plus"></i> Add Line</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-hover" id="expenseItems">
                        <thead>
                            <tr>
                                <th width="300">Vendor / Payee</th>
                                <th>Description</th>
                                <th width="300">Reference No.</th>
                                <th class="text-center" width="150">Amount</th>
                                <th class="text-center" width="50"><i class="fa fa-cog"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($items) && $items)
                                @foreach($items as $key => $val)
                                    <tr data-id="{{ $val['id'] }}">
                                        <td>
                                            <input type="hidden" name="old_expense_item_id[]" value="{{ $val['id'] }}">
                                            <select class="form-select selectVendor" name="old_vendor_id[]">
                                                <option value="" selected hidden disabled>-- Select --</option>
                                                @if(isset($vendorsData) && !empty($vendorsData))
                                                    @foreach($vendorsData as $key2 => $val2)
                                                        <option value="{{ $val2->id }}" {{ isset($val['id']) && $val['id'] == $val2->id ? 'selected' : ''; }}>{{ $val2->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="old_description[]"
                                                class="form-control"
                                                value="{{ $val['description'] ?? ''; }}"
                                            >
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control"
                                                name="old_ref_no[]"
                                                placeholder="E.g. Invoice #, Receipt #, etc."
                                                value="{{ $val['ref_no'] ?? ''; }}"
                                            >
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control text-end"
                                                name="old_sub_amount[]"
                                                placeholder="0.00"
                                                value="{{ $val['amount'] }}"
                                                data-target-table="#expenseEditModal #expenseItems tbody"
                                            >
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="text-danger btnRemove"><i class="fa fa-trash  fs-5"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- total -->
            <div class="row justify-content-end">
                <div class="col-lg-9 d-flex justify-content-start flex-wrap gap-2 image-container">
                    @if(isset($expenseAttachments) && $expenseAttachments)
                        @foreach($expenseAttachments as $key => $val)
                            <a href="#" class="position-relative">
                                <div class="attachment-image border">
                                    <img class="rounded " 
                                        height="100" 
                                        width="100" 
                                        src="{{ Storage::disk('public')->exists($val['file_path']) ?  Storage::url($val['file_path']) : url('assets/images/avtar/3.jpg') }}"
                                        loading="lazy"
                                    >
                                    <button type="button" class="btn btn-danger btn-xs position-absolute btnRemoveAttachments" data-id="{{ $val['id'] }}" style="top: -5px; right: -5px;"><i class="fa fa-times"></i></button>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
                <div class="col-lg-3">
                    <div class="d-flex justify-content-between">    
                        <span class="fw-semibold">Total</span>
                        <span class="fw-semibold total-amount">{{ number_format($expenseData['amount'] ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>

            <span class="fw-semibold">Image Attachments</span>
            <!-- dropzone -->
            <div class = "dropzone myDropzone mt-2" id = "myDropzone" style = "border: dashed !important;">
                <div class="d-flex flex-column align-items-center">
                    <h4 class="txt-primary">Drop your image(s) here</h4>
                    <span>JPEG, PNG</span>
                </div>
            </div>

        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary btnSubmit" data-url="{{ route('expense.update') }}"><i class="fa fa-save"></i> Submit Expense Changes</button>
        <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
    </div>
</div> 