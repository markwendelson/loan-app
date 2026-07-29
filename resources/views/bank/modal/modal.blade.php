<div class="modal fade" id="bankModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Bank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frmBanks">
                    <!-- id -->
                    <input type="hidden" name="id">

                    <div class="row">
                        <!-- code -->
                        <div class="col-lg-12">
                            <label class="w-100">
                                Code <span class="txt-danger">*</span>
                                <input type="text"
                                    class="form-control"
                                    name="bank_code"
                                    placeholder="Enter bank code"
                                    required
                                >
                            </label>
                        </div>

                        <!-- name -->
                        <div class="col-lg-12">
                            <label class="w-100">
                                Name <span class="txt-danger">*</span>
                                <input type="text"
                                    class="form-control"
                                    name="name"
                                    placeholder="Enter bank name"
                                    required
                                >
                            </label>
                        </div>

                        <!-- status -->
                        <div class="col-lg-12">
                            <label class="w-100">
                                Status <span class="txt-danger">*</span>
                                <select class="form-select" name="status" required>
                                    <option>-- Status --</option>
                                    @foreach(config('constants.status_value') as $key => $val)
                                        <option value="{{ $key }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save me-1"></i>Save changes</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa fa-times me-1"></i>Cancel</button>
            </div>
        </div>
    </div>
</div>