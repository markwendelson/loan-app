<div>
    <div class="position-relative">
        <table id="tblRole" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-center"  width="50"><i class="fa fa-cog"></i></th>
                </tr>
            </thead>
            <tbody>
                @if(isset($roles) && $roles)
                    @foreach($roles as $key => $val)
                        <tr data-id="{{ $val->id }}">
                            <td>{{ $val->name }}</td>
                            <td class="d-flex justify-content-start gap-1">
                                @can('edit-role')
                                <button type="button" class="btn btn-primary btn-sm btnEdit" data-id="{{ $val->id }}"><i class="fa fa-pencil"></i></button>
                                @endcan
                                @can('delete-role')
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteData({{ $val->id }})"><i class="fa fa-trash"></i></button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
            @if($roles->hasPages())
            <tfoot>
                <tr>
                    <td colspan="5">
                        <div class="d-flex justify-content-end">
                            {{ $roles->links('partials.pagination.default-livewire') }}
                        </div>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>

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
