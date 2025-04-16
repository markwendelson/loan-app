<div class = "modal fade" id = "modalIDs">
    <div class = "modal-dialog">
        <div class = "modal-content">
            <div class = "modal-header">
                <h5>Employee Identification</h5>
            </div>
            <div class = "modal-body">
                <div class = "row">
                    <!-- target field -->
                    <input type = "hidden" id = "IDsTarget" value = "">

                        <!-- Identification -->
                        <div class = "col-12">
                        <label class = "w-100">
                            Identication Type
                            <select class = "form-select" id = "id_type_id">
                                <option value = "" selected hidden disabled>-- Select --</option>
                                @if(isset($identification_Data) && $identification_Data)
                                    @foreach($identification_Data as $key => $val)
                                        <option value = "{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </label>
                    </div>


                    <!-- Birtdate -->
                    <div class = "col-12" id="idNoContainer">
                        <label class = "w-100">
                            ID No.
                            <input type = "text"
                                id = "id_no"
                                class = "form-control"
                                placholder = "Id No."
                            >
                        </label>
                    </div>



                </div>
            </div>

            <div class = "modal-footer">
                <button type = "button" id="addIDsInfo" class = "btn btn-primary btn-sm btnAdd"><i class = "fa fa-save"></i> Add</button>
                <button type="button" id="updateIDsInfo" class="btn btn-success" style="display:none;">Update</button>
                <button type = "button" class = "btn btn-danger btn-sm btnCancel"><i class = "fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


@push('js')
<script>
    $(document).on('click', '#id_no', function() {
        // Select the text inside the salary field when clicked
        $(this).select();
    });
</script>
@endpush