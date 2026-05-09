<!-- Modal -->
<div class="modal fade" id="expenseModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">Add Expense</h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    <input type="hidden" name="id" id="expenseId">

                    <!-- amount -->
                    <input type="hidden" name="amount">

                    <!-- sub total -->
                    <input type="hidden" name="sub_amount">

                    <!-- tax -->
                    <input type="hidden" name="tax">

                    <div class="row">
                        <!-- date -->
                        <div class="col-lg-3">
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

                        <!-- vendor / payee -->
                        <div class="col-lg-6">
                            <label class="w-100">
                                Vendor/ Payee <span class="text-danger">*</span>
                                <input type="text"
                                    class="form-control"
                                    name="vendor_name"
                                    required
                                >
                            </label>
                        </div>

                        <!-- reference # -->
                        <div class="col-lg-3">
                            <label class="w-100">
                                Reference #
                                <input type="text"
                                    name="reference_no"
                                    class="form-control"
                                >
                            </label>
                        </div>

                        <!-- branch -->
                        <div class="col-lg-3">
                            <label class="w-100">
                                Branch <span class="text-danger">*</span>
                                <select class="form-select"
                                    name="branch_id"
                                    required
                                >
                                    <option value="" selected hidden disabled>-- Branch --</option>
                                    @if(isset($branches) && $branches)
                                        @foreach($branches as $key => $val)
                                            <option value="{{ $val->id }}">{{ $val->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </label>
                        </div>

                        <!-- payment method -->
                        <div class="col-lg-4">
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
                        
                        <!-- tax rate -->
                        <div class="col-lg-3">
                            <label class="w-100">
                                Tax Rate (%) <span class="text-danger">*</span>
                                <input type="number"
                                    name="tax_rate"
                                    class="form-control"
                                    value="12"
                                    required
                                >
                            </label>
                        </div>
                    </div>

                    <!-- line items -->
                    <div class="card mt-2">
                        <div class="card-header d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btnAddline"><i class="fa fa-plus"></i> Add Line</button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered table-hover" id="expenseItems">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th class="text-center" width="100">Qty</th>
                                        <th class="text-center">Unit Price</th>
                                        <th class="text-center">Subtotal</th>
                                        <th class="text-center" width="50"><i class="fa fa-cog"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-select" name="category_id[]">
                                                <option value="" selected hidden disabled>-- Category --</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"
                                                name="description[]"
                                                class="form-control"
                                            >
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control text-center"
                                                name="qty[]"
                                                value="1"
                                            >
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control text-end"
                                                name="unit_price[]"
                                                placeholder="0.00"
                                            >
                                            <input type="hidden"
                                                name="sub_total[]"
                                            >
                                        </td>
                                        <td class="tdSubTotal text-end">
                                            0.00
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
                                <span>Subtotal</span>
                                <span class="overallSubTotal">0.00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Tax</span>
                                <span class="total-tax-amount">0.00</span>
                            </div>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Total</span>
                                <span class="fw-semibold total-amount">0.00</span>
                            </div>
                        </div>
                    </div>

                    <span class="fw-semibold">Image Attachments</span>
                    <!-- dropzone -->
                    <div class = "dropzone mt-1" id = "myDropzone" style = "border: dashed !important;">
                        <div class="d-flex flex-column align-items-center">
                            <h4 class="txt-primary">Drop your image(s) here</h4>
                            <span>JPEG, PNG</span>
                        </div>
                    </div>
                    


                    {{-- <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div> --}}

                    <div class="mt-2">
                        <label for="description" class="form-label">Notes</label>
                        <textarea class="form-control" id="description" name="notes" rows="3"></textarea>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Save Expense</button>
                        <button type="button" class="btn btn-secondary btnCancel"><i class="fa fa-times"></i> Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>