<div>
    @if(!empty($colorCodes))
        <div class="d-flex flex-column gap-2">
            @foreach($colorCodes as $key => $val)
                <div class="border border-1 p-2 rounded w-100 d-flex justify-content-between align-items-center">
                    <div class="d-flex justify-content-start align-items-center">
                        <div class="mx-2 rounded-circle" style="background-color: {{$val->color_code}}; height:20px;width:20px;"></div>
                        <div class="d-flex flex-column">
                            <span>{{$val->color_name ?? ''}}</span>
                            <span clsas="fw-light">{{$val->color_description ?? ''}}</span>
                        </div>

                    </div>
                    <div class="d-flex justify-content-start align-items-center gap-3 pr-2">
                        <a href="#" class="txt-primary fs-6 mx-2 btnEdit" data-id="{{$val->id}}"><i class="fa fa-pencil"></i></a>
                        <a href="#" class="txt-danger fs-6 mx-2" wire:click="removeColorCode({{$val->id}})"><i class="fa fa-trash"></i></a>
                    </div>
                </div> 
            @endforeach
        </div>
    @endif
</div>      