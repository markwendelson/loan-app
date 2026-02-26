@if($isReadOnlyMode ?? false)
    <div class="alert alert-warning alert-dismissible fade show sticky-top" role="alert"
        style="margin-bottom: 0; border-radius: 0; z-index: 1050;">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Cloud Database Unavailable:</strong>
                Running on local backup.
                @if(isset($pendingOperations) && $pendingOperations > 0)
                    <span class="badge bg-primary ms-2">{{ $pendingOperations }} pending operation(s)</span>
                @else
                    You can view data but changes will be queued.
                @endif
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-dark" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Retry Connection
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds to check if cloud is back
        @if($isReadOnlyMode ?? false)
            setTimeout(function () {
                fetch('/api/health-check')
                    .then(response => response.json())
                    .then(data => {
                        if (data.cloud_available) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.log('Health check failed:', error));
            }, 30000);
        @endif
    </script>
@endif