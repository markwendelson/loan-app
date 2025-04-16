
<div class="modal fade" id="modalLoanType" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Loan Type Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmLoanTypeModal" class="row g-3 needs-validation custom-input" novalidate="">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="col-12">
                        <label class="w-100" for="name">Name</label>
                        <input type="text" class="form-control form-control-sm" name="name" placeholder="Name" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="description">Description</label>
                        <input type="text" class="form-control form-control-sm" name="description" placeholder="Description" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100">Status</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="" disabled>-- Select --</option>
                            @foreach(config('constants.status_value') as $key => $val)
                                <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>

                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit w-100"><i class="fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-ligh w-100" onclick="hideModal('modalLoanType')"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


