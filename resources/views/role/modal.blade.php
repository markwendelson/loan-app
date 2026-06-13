
<div class="modal fade" id="modalRole" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Role Information</h5>
            </div>

            <div class="modal-body chck-permissions">
                <form id="frmRoleModal" class="row g-3 needs-validation custom-input" novalidate="">
                    @csrf
                    <input type="hidden" value="" name="id" id="id">
                    <div class="col-12">
                        <label class="w-100" for="name">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Name" required />
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="modal-title">Permissions</h5>
                                </div>

                                <div class="d-flex gap-2">
                                    <div class="form-check checkbox checkbox-primary mb-0">
                                        <input class="form-check-input chckAll" id="chckAll" type="checkbox">
                                        <label class="form-check-label mb-1" for="chckAll"><small>All</small></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class ="card-body">
                            @foreach (config('modules') as $moduleKey => $module)
                                <div class="card group-permissions">
                                    <div class="card-header">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="modal-title mb-0">{{Str::title($moduleKey)}}</h5> 
                                            </div>
                                            <div>
                                                <div class="form-check checkbox checkbox-primary mb-0">
                                                    <input class="form-check-input chckAllGroup" id="{{$moduleKey}}"  type="checkbox">
                                                    <label class="form-check-label mb-1" for="{{$moduleKey}}"><small>All</small></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                        @foreach ($module['permissions'] as $permission)
                                            <div class="col-3">
                                                <div class="form-check checkbox checkbox-primary mb-0">
                                                    <input class="form-check-input subCheckBox" type="checkbox"
                                                        name="permissions[]"
                                                        id="{{$permission['name']}}"
                                                        value="{{$permission['name']}}"
                                                    >
                                                    <label class="form-check-label mb-1" for="{{$permission['name']}}">{{$permission['label']}}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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


