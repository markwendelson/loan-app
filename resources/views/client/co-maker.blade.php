<div class="card">
    <div class="card-header">
        <h5>Co-Maker</h5>

        <div class="card-header-right">
            <button type="button" class="btn btn-primary btn-xs btnCoMakerModal"><i class="fa fa-plus text-white"></i></button>
        </div>
    </div>
    <div class="card-body">
       <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th class="text-leftr" width="150">Name</th>
                        <th class="text-leftr" width="150">Address</th>
                        <th class="text-center" width="80"><i class="fa fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
       </div>
    </div>
</div>

<!-- comaker modal -->
@include('client.modal.co-maker_modal')

@push('js')
<script>
    $("#modalCoMaker .btn-add-co-maker").click(function(){
        let params={};

        addCoMaker(params);
    })

    $(".btnCoMakerModal").click(function(){
        showCoMakerModal();
    })

    //remove comaker function
    $(".comaker_list").on("click", "tr .btnRemove", function(){
        $(this).closest("tr").fadeOut(300, function() {
            $(this).remove();
        });
    })

    function showCoMakerModal() {
        $("#modalCoMaker").modal("show");
    }

    function hideCoMakerModal() {
        clearCoMakerModal();
        $("#modalCoMaker").modal("hide");
    }

    function addCoMaker(params) {
        console.log(params)
        // let val="<tr>"+
        //     "<input type='hidden' name='client_comaker["+ comakerCount +"][fullname]' value='"+ params.comaker_name +"'>" +
        //     "<input type='hidden' name='client_comaker["+ comakerCount +"][contact_info]' value='"+ params.comaker_contact +"'>" +
        //     "<input type='hidden' name='client_comaker["+ comakerCount +"][occupation]' value='"+ params.comaker_occupation +"'>" +
        //     "<td>"+ params.comaker_name +"</td>" +
        //     "<td>"+ params.comaker_contact +"</td>" +
        //     "<td>"+ params.comaker_occupation +"</td>" +
        //     "<td class='text-center'><button type='button' class='btn btn-danger btn-xs btnRemove'><i class='fa fa-times'></i></button></td>" +
        // "</tr>";

        // comakerCount++;

        // $(".comaker_list").append(val); 

        hideCoMakerModal();
    }

    function clearCoMakerModal(){

    }
</script>
@endpush