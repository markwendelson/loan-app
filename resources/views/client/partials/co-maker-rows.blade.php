@foreach($loanCoMakers ?? [] as $comaker)
    <tr data-id="{{'tmp_'.$comaker->id}}" data-client-id="{{$comaker->pivot->client_id ?? $comaker->id}}" data-loan-id="">
        <td>
            <input type="hidden" name="client_comaker[]" value="{{ $comaker->id }}">
            {{$comaker->details->fullname}}
        </td>
        <td>{{$comaker->details->present_full_address}}</td>
        <td class="text-center co-maker-action-btn-col">
            <button type="button" class="btn btn-sm btn-danger btn-remove" data-id="{{'tmp_'.$comaker->id}}" data-client-id="{{ $comaker->pivot->client_id ?? $comaker->id }}" data-loan-id="">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>
@endforeach
