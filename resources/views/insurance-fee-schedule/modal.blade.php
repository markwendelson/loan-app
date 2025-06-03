
<div class="modal fade" id="modalInsuranceFeeSchedule" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Insurance Fee Schedule Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmInsuranceFeeScheduleModal" class="row g-3 needs-validation custom-input" novalidate="">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="col-12">
                        <label class="w-100" for="min_amount">Loan Min. Amount</label>
                        <input type="number" class="form-control form-control-sm" name="min_amount" placeholder="Loan Min. Amount" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="max_amount">Loan Max. Amount</label>
                        <input type="number" class="form-control form-control-sm" name="max_amount" placeholder="Loan Max. Amount" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="monthly_fee">Monthly Fee</label>
                        <input type="number" class="form-control form-control-sm" name="monthly_fee" placeholder="Monthly Fee" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="annual_fee">Annual Fee</label>
                        <input type="number" class="form-control form-control-sm" name="annual_fee" placeholder="Annual Fee" required />
                    </div>

                    <div class="col-12">
                        <label class="w-100" for="status">Status</label>
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
                <button type="button" class="btn btn-light w-100" onclick="hideModal('modalInsuranceFeeSchedule')"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


