<div class="card">
    <div class="card-header">
        <h5>References</h5>

        <div class="card-header-right">
            <button type="button" class="btn btn-primary btn-xs btnReferencesModal" ><i class="fa fa-plus text-white"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
       <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact No.</th>
                        <th>Occupation</th>
                        <th class="text-center" width="80"><i class="fa fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody class="references_list">

                </tbody>
            </table>
       </div>
    </div>
</div>

<!-- references modal -->
@include('client.modal.references_modal')

@push('js')
<script>
    $("#modalReferences .btnAdd").click(function(){
        let params={
            reference_name : $("#modalReferences #reference_name").val(),
            reference_contact : $("#modalReferences #reference_contact").val(),
            reference_occupation : $("#modalReferences #reference_occupation").val()
        };

        addReferences(params);
    })

    $(".btnReferencesModal").click(function(){
        showReferenceModal();
    })

    //remove reference function
    $(".references_list").on("click", "tr .btnRemove", function(){
        $(this).closest("tr").fadeOut(300, function() {
            $(this).remove();
        });
    })

    function showReferenceModal() {
        $("#modalReferences").modal("show");
    }

    function hideReferenceModal() {
        clearReferencesModal();
        $("#modalReferences").modal("hide");
    }

    function addReferences(params) {
        let val="<tr>"+
            "<input type='hidden' name='client_references["+ referenceCount +"][fullname]' value='"+ params.reference_name +"'>" +
            "<input type='hidden' name='client_references["+ referenceCount +"][contact_info]' value='"+ params.reference_contact +"'>" +
            "<input type='hidden' name='client_references["+ referenceCount +"][occupation]' value='"+ params.reference_occupation +"'>" +
            "<td>"+ params.reference_name +"</td>" +
            "<td>"+ params.reference_contact +"</td>" +
            "<td>"+ params.reference_occupation +"</td>" +
            "<td class='text-center'><button type='button' class='btn btn-danger btn-xs btnRemove'><i class='fa fa-times'></i></button></td>" +
        "</tr>";

        referenceCount++;

        $(".references_list").append(val);

        hideReferenceModal();
    }

    function clearReferencesModal(){
        $("#modalReferences #reference_name").val("");
        $("#modalReferences #reference_contact").val("");
        $("#modalReferences #reference_occupation").val(""  );
    }
</script>
@endpush