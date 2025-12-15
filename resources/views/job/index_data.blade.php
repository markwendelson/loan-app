@if(isset($jobs) && $jobs)
    @foreach ($jobs as $key => $val)
        <tr class="table-row-pointer">
            <td class = "text-center">{{ $key + 1 }}</td>
            <td class = "text-left">{{ $val->title }}</td>
            <td>{{ $val->description }}</td>
            <td class = "text-center">{{config('constants.status_value')[$val->status]}}</td>
            <td class = "text-center">
                @can('edit-job-title')
                <button class = "btn btn-primary btn-xs btnEdit" data-id = "{{$val->id}}"><i class = "fa fa-pencil"></i></button>
                @endcan
                @can('delete-job-title')
                <button class = "btn btn-danger btn-xs"><i class = "fa fa-trash"></i></button>
                @endcan
            </td>
        </tr>
    @endforeach
@endif
