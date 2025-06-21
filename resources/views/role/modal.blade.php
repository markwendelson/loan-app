
<div class="modal fade" id="modalRole" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Role Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmRoleModal" class="row g-3 needs-validation custom-input" novalidate="">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="col-12">
                        <label class="w-100" for="name">Name</label>
                        <input type="text" class="form-control form-control-sm" name="name" placeholder="Name" required />
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5>Permissions</h5>
                        </div>
                        <div class ="card-body">
                            <div class="row">
                                @foreach (config('modules') as $moduleKey => $module)
                                    <div class="card-header"><h5>{{Str::title($moduleKey)}}</h5></div>
                                    <div class="card-body">
                                        <div class="row">
                                        @foreach ($module['permissions'] as $permission)
                                            <div class="col-3">
                                                <input type="checkbox" name="permissions[]" id="{{$permission['name']}}">
                                                <label for="{{$permission['name']}}">{{$permission['label']}}</label>
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit"><i class="fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-light" onclick="hideModal('modalRole')"><i class="fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


