@if(isset($notaryFeeSched) && $notaryFeeSched)
    @foreach ($notaryFeeSched as $key => $val)
        <tr class="table-row-pointer">
            <td class = "text-center">{{ $key + 1 }}</td>
            <td class = "text-right">{{ number_format($val->min_amount  ?? 0,2) }}</td>
            <td class = "text-center">to</td>
            <td class = "text-right">    {{ $val->max_amount == 0 ? 'Up' : number_format($val->max_amount, 2) }}</td>
            <td class = "text-right">{{ number_format($val->notary_fee  ?? 0,2) }}</td>
            <td class="text-center">
                <input type="checkbox" {{ $val->is_percentage == 1 ? 'checked' : '' }} disabled class="custom-checkbox">
            </td>
            <td class = "text-align">{{config('constants.status_value')[$val->status]}}</td>
            <td class = "text-center">
                <button class = "btn btn-primary btn-xs btnEdit" data-id = "{{$val->id}}"><i class = "fa fa-pencil"></i></button>
                <button class = "btn btn-danger btn-xs"><i class = "fa fa-trash"></i></button>
            </td>
        </tr>
    @endforeach
@endif
