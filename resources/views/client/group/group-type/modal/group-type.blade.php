<div class="modal fade" id= "modalGroupTypes" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Group Types</h4>
            </div>
            <div class="modal-body">
                <form id="frmGroupTypes">
                    <!-- groupt type id -->
                    
                    <input type="hidden"
                        name="id"
                        value=""
                    >

                    <div class="row">

                        <!-- name -->
                        <div class="col-lg-7">
                            <label class="w-100">
                                Group Type Name <span class="text-danger">*</span>
                                <input type="text"
                                    name="name"
                                    class="form-control"
                                    placeholder="Group Type Name"
                                    required
                                >
                            </label>
                        </div>

                        <!-- status -->
                        <div class="col-lg-5">
                            <label class="w-100">
                                Status <span class="text-danger">*</span>
                                <select class="form-select" name="status" required>
                                    <option value="" selected hidden disabled>-- Status --</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </label>
                        </div>

                        <!-- description -->
                        <div class="col-lg-12">
                            <label class="w-100">
                                Description (optional)
                                <textarea class="form-control"
                                    rows="3"
                                    name="description"
                                    placeholder="Enter a description"
                                ></textarea>
                            </label>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit Group Type</button>
                <button type="button" class="btn btn-danger btnCancel"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>