@if(isset($insFeeSched) && $insFeeSched)
    @foreach ($insFeeSched as $key => $val)
        <tr class="table-row-pointer">
            <td class = "text-center">{{ $key + 1 }}</td>
            <td class = "text-right">{{ number_format($val->min_amount  ?? 0,2) }}</td>
            <td class = "text-center">to</td>
            <td class = "text-right">{{ number_format($val->max_amount  ?? 0,2) }}</td>
            <td class = "text-right">{{ number_format($val->monthly_amount  ?? 0,2) }}</td>
            <td class = "text-right">{{ number_format($val->annual_amount  ?? 0,2) }}</td>
            <td class = "text-align">{{config('constants.status_value')[$val->status]}}</td>
            <td class = "text-center">
                <button class = "btn btn-primary btn-xs btnEdit" data-id = "{{$val->id}}"><i class = "fa fa-pencil"></i></button>
                <button class = "btn btn-danger btn-xs"><i class = "fa fa-trash"></i></button>
            </td>
        </tr>
    @endforeach
@endif
