<div class="card">
    <div class="card-header">
        <h5>Permissions</h5>
    </div>
    <div class ="card-body">
        <form method="post" action="{{route('user.update-branch-permission', ['user'=>$user->id,'branch' => $branch->id] )}}">
            @csrf
            <div class="row">
                @foreach (config('modules') as $moduleKey => $module)
                    <div class="card-header"><h5>{{Str::title($moduleKey)}}</h5></div>
                    <div class="card-body">
                        <div class="row">
                        @foreach ($module['permissions'] as $permission)
                            <div class="col-md-4 col-sm-2">
                                <input type="checkbox"
                                        name="permissions[]"
                                        id="{{$permission['name']}}"
                                        value="{{$permission['name']}}"
                                        @checked(in_array($permission['name'], $assignedPermissionNames))>
                                <label for="{{$permission['name']}}">{{$permission['label']}}</label>
                            </div>
                        @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary mt-2 float-end"><i class="fa fa-save"></i> Update Permissions</button>
            </div>
        </form>
    </div>
</div>