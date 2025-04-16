@if(isset($departments) && $departments)
    @foreach ($departments as $key => $val)
        <tr class="table-row-pointer">
            <td class = "text-center">{{ $key + 1 }}</td>
            <td class = "text-center">{{ $val->code }}</td>
            <td>{{ $val->name }}</td>
            <td class = "text-center">{{config('constants.status_value')[$val->status]}}</td>
            <td class = "text-center">
                <button class = "btn btn-primary btn-xs btnEdit" data-id = "{{$val->id}}"><i class = "fa fa-pencil"></i></button>
                <button class = "btn btn-danger btn-xs"><i class = "fa fa-trash"></i></button>
            </td>
        </tr>
    @endforeach
@endif
