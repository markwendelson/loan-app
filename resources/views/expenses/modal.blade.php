<!-- Modal -->
<div class="modal fade" id="expenseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 90% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">Add Expense</h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    <input type="hidden" name="id" id="expenseId">

                    <!-- amount -->
                    <input type="hidden" name="total_amount">

                    <!-- tax -->

                    <div class="row">
                        <!-- date -->
                        <div class="col-lg-2">
                            <label class="w-100">
                                Date <span class="text-danger">*</span>
                                <input type="date" 
                                    class="form-control" 
                                    id="expense_date" 
                                    name="expense_date" 
                                    value="{{ date('Y-m-d') }}"
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
                                            <option value="{{ $val->id }}">{{ $val->name }}</option>
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
                                    {{ isset($user->branch_id) && $user->branch_id ? 'disabled' : ''; }}
                                >
                                    <option value="" selected hidden disabled>-- Branch --</option>
                                    @if(isset($branches) && $branches)
                                        @foreach($branches as $key => $val)
                                            <option value="{{ $val->id }}" {{ isset($user->branch_id) && $user->branch_id == $val->id ? 'selected' : '';  }}>{{ $val->name }}</option>
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
                                        <option value="{{ $key }}">{{ $val }}</option>
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
                            ></textarea>
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
                                    data-addline-target-table="#expenseModal #expenseItems tbody"
                                    data-target-table="#expenseModal #expenseItems tbody tr"
                                    data-parent-modal="#expenseModal"
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
                                    <tr>
                                        <td>
                                            <select class="form-select selectVendor" name="vendor_id[]">
                                                <option value="" selected hidden disabled>-- Select --</option>
                                                @if(isset($vendorsData) && !empty($vendorsData))
                                                    @foreach($vendorsData as $key => $val)
                                                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="description[]"
                                                class="form-control"
                                            >
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control"
                                                name="ref_no[]"
                                                placeholder="E.g. Invoice #, Receipt #, etc."
                                            >
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control text-end"
                                                name="sub_amount[]"
                                                data-target-table="#expenseModal #expenseItems tbody"
                                                placeholder="0.00"
                                            >
                                        </td>
                                        <td class="text-center">
                                            <a href="#" class="text-danger btnRemove"><i class="fa fa-trash  fs-5"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- total -->
                    <div class="row justify-content-end">
                        <div class="col-lg-3">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Total</span>
                                <span class="fw-semibold total-amount">0.00</span>
                            </div>
                        </div>
                    </div>

                    <span class="fw-semibold">Image Attachments</span>
                    <!-- dropzone -->
                    <div class = "dropzone myDropzone mt-1" id = "myDropzone" style = "border: dashed !important;">
                        <div class="d-flex flex-column align-items-center">
                            <h4 class="txt-primary">Drop your image(s) here</h4>
                            <span>JPEG, PNG</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSubmit" data-url="{{ route('expenses.store') }}"><i class="fa fa-save"></i> Save Expense</button>
                <button type="button" class="btn btn-secondary btnCancel"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>