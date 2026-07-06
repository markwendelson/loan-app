<div class="modal fade" id="modalVendorType">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mb-0">New Vendor Type</h4>
            </div>
            <div class="modal-body">
                <form id="frmVendorType">
                    <div class="row">
                        <div class="col-lg-7">
                            <label class="w-100">
                                Name <span class="txt-danger">*</span>
                                <input type="text"
                                    class="form-control"
                                    placeholder="Vendor Type Name"
                                >
                            </label>
                        </div>

                        <div class="col-lg-5">
                            <label class="w-100">
                                Status <span class="txt-danger">*</span>
                                <select class="form-select">
                                    <option value="" selected hidden disabled>-- Select Status --</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </label>
                        </div>

                        <div class="col-lg-12">
                            <label class="w-100">
                                Description (optional)
                                <textarea class="form-control" placeholder="Enter vendor type description"
                                    rows="3"
                                ></textarea>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save me-1"></i>Submit New Vendor Type</button>
                <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times me-1"></i>Cancel</button>
            </div>
        </div>
    </div>
</div> 