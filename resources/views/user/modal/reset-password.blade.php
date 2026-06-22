<div class="modal fade" id="modalResetPassword">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mb-0">Reset Password</h4>
            </div>
            <div class="modal-body">
                <form id="frmResetPassword">
                    <input type="hidden" name="id">
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="w-100">
                                Password <span class="text-danger">*</span>
                                <div class="input-group">
                                    <input type="password" 
                                        class="form-control"
                                        name="password"
                                        placeholder="Enter new password"
                                        required
                                    >
                                    <button type="button" class="btn btn-light btnTogglePassword"><i class="fa fa-eye-slash"></i></button>
                                </div>
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <label class="w-100">
                                Confirm Password <span class="text-danger">*</span>
                                <div class="input-group">
                                    <input type="password" 
                                        class="form-control"
                                        name="confirm_password"
                                        placeholder="Confirm new password"
                                        required
                                    >
                                    <button type="button" class="btn btn-light btnTogglePassword"><i class="fa fa-eye-slash"></i></button>
                                </div>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save me-1"></i> Reset Password</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>