<div class="card-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0">Permissions</h5>
        </div>

        <div class="d-flex gap-2">
            <div class="form-check checkbox checkbox-primary mb-0">
                <input class="form-check-input chckAll" id="chckAll" type="checkbox">
                <label class="form-check-label mb-1" for="chckAll"><small>All</small></label>
            </div>
        </div>
    </div>
    
</div>
<div class ="card-body chck-permissions">
    <form method="post" action="{{route('user.update-branch-permission', ['user'=>$user->id,'branch' => $branch->id] )}}">
        @csrf
        
        @foreach (config('modules') as $moduleKey => $module)
            <div class="card">
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
                <div class="card-body group-permissions">
                    <div class="row">
                    @foreach ($module['permissions'] as $permission)
                        <div class="col-md-4 col-sm-2">
                            <div class="form-check checkbox checkbox-primary mb-0">
                                <input class="form-check-input subCheckBox" type="checkbox"
                                    name="permissions[]"
                                    id="{{$permission['name']}}"
                                    value="{{$permission['name']}}"
                                    @checked(in_array($permission['name'], $assignedPermissionNames))
                                >
                                <label class="form-check-label mb-1" for="{{$permission['name']}}">{{$permission['label']}}</label>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-lg-none w-100">
            <button type="submit" class="btn btn-primary mt-2 w-100"><i class="fa fa-save"></i> Update Permissions</button>
        </div>
        
        <div class="d-none d-lg-block">
            <button type="submit" class="btn btn-primary mt-2 float-end"><i class="fa fa-save"></i> Update Permissions</button>
        </div>
    </form>
</div>