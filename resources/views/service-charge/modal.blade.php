{{-- Service Fee Modal --}}
<div class="modal fade" id="serviceFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="serviceFeeForm">

                <input type="hidden" id="fee_id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title" id="serviceFeeModalLabel">
                        Add Service Fee
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- Fee Type --}}
                    <div class="mb-3">
                        <label class="form-label">Fee Type</label>
                        <select class="form-select" name="fee_type" id="fee_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="walkin">Walk-in</option>
                            <option value="check">Check</option>
                        </select>
                    </div>

                    {{-- Percentage --}}
                    <div class="mb-3">
                        <label class="form-label">Percentage (%)</label>
                        <input type="number"
                               class="form-control"
                               id="percentage"
                               name="percentage"
                               step="0.01"
                               min="0"
                               required>
                    </div>

                    {{-- Status --}}
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="is_active"
                               name="is_active"
                               checked>
                        <label class="form-check-label">
                            Active
                        </label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
