<div class = "modal fade" id = "modalReferences" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class = "modal-dialog">
        <div class = "modal-content">
            <div class = "modal-header">
                <h5 class = "modal-title">Reference Information</h5>
            </div>

            <div class = "modal-body">
                <div class = "row">
                    <!-- name -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Name
                            <input type = "text"
                                class = "form-control"
                                id = "reference_name"
                            >
                        </label>
                    </div>

                    <!-- contact -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Contact
                            <input
                                type = "number"
                                class = "form-control"
                                id = "reference_contact"
                            >
                        </label>
                    </div>

                    <!-- occupation -->
                    <div class = "col-12">
                        <label class = "w-100">
                            Occupation
                            <input type = "text"
                                class = "form-control"
                                id = "reference_occupation"
                            >
                        </label>
                    </div>
                </div>
            </div>

            <div class = "modal-footer">
                <button type = "button" class = "btn btn-primary btn-sm btnAdd"><i class = "fa fa-save"></i> Add</button>
                <button type = "button" class = "btn btn-danger btn-sm btnCancel" onclick = "hideReferenceModal()"><i class = "fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>