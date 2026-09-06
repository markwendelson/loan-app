<div class="modal fade" id="existingClientModal" tabindex="-1" aria-labelledby="existingClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="existingClientModalLabel">Client Already Exists</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ $errors->first() }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ session('existing_client_id') ? route('client.show', session('existing_client_id')) : '#' }}" class="btn btn-primary">Load Existing Profile</a>
            </div>
        </div>
    </div>
</div>
