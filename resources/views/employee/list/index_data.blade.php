@if($allEmployees->isNotEmpty())
    @foreach ($allEmployees as $key => $val)
        <tr class="table-row-pointer">
            <td class="text-center">{{ $key + 1 }}</td>
            <td class="text-center">{{ $val->emp_code }}</td>
            <td>{{ $val->display_name }}</td>
            <td class="text-center">{{ config('constants.status_value')[$val->status] }}</td>
            <td class="text-center">
                <button class="btn btn-primary btn-xs btnEdit" data-id="{{ $val->id }}"><i class="fa fa-pencil"></i></button>
                <!-- Add a delete button with form or a modal -->
                <button class="btn btn-danger btn-xs btnDelete" data-id="{{ $val->id }}"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="5" class="text-center">No employees found.</td>
    </tr>
@endif
