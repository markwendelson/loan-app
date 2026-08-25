<div>
    <div class="position-relative">
        @if($databaseBackupData->total() > 1)
        <div class="table-responsive">
        @else
        <div>
        @endif
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Created By</th>
                        <th>Date Created</th>
                        <th width="50"></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($databaseBackupData) && $databaseBackupData)
                        @foreach($databaseBackupData as $key => $val)
                            <tr>
                                <td>{{$val->file_name ?? '';}}</td>
                                <td>{{$val->user_name ?? '';}}</td>
                                <td>{{$val->created_at}}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @can('download-database-backup')
                                            <li><a class="dropdown-item" href="{{url('database/download/' . $val->file_name)}}" target="_blank"><i class="fa fa-download me-1"></i>Download</a></li>
                                            @endcan
                                            {{-- <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item txt-danger" href="#"><i class="fa fa-trash me-1"></i>Delete</a></li> --}}
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                @if($databaseBackupData->hasPages())
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <div class="d-flex justify-content-end">
                                {{ $databaseBackupData->links('partials.pagination.default-livewire') }}
                            </div>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        @if($databaseBackupData->total() > 1)
        </div>
        @else
        </div>
        @endif

        <div
            wire:loading.delay
            wire:loading.class.remove="d-none"
            class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-50 d-none"
            style="z-index: 10;"
        >
        
            <div class="card shadow-sm border-0">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="custom-loader" role="status"></div>
                        <span>Please wait...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
