<!-- Employee Identification -->
<div class = "row">
    <div class = "col-sm-12 col-md-7 col-lg-9">
        <div class = "card">
            <div class = "card-header">
                <h5>Employee Identification</h5>
                <div class = "card-header-right">
                    <button type = "button"
                        class = "btn btn-primary btn-xs btnIDsInformation"
                        data-target = ".employee-IDs-informations"
                    >
                        <i class = "fa fa-plus text-white"></i>
                    </button>
                </div>
            </div>
            <div class = "card-body">
                <div class = "row employee-IDs-informations">
                    @if(isset($empIDs) && is_array($empIDs))
                    @foreach($empIDs as $key => $val)
                        <div id="{{$val['id'] ?? '' }}" class="col-sm-12 col-md-4 col-lg-3 employee-IDs-information-items">
                            <div class="card"
                                data-id="{{ $val['id'] ?? '' }}"
                                data-idtype-id="{{ $val['id_type_id'] ?? '' }}"
                                data-id-no="{{ $val['id_no'] ?? 'N/A' }}"
                            >
                                <input type="hidden" name="emp_ids[{{$val['id'] ?? '' }}][is_old]" value="1" >
                                <input type="hidden" name="emp_ids[{{$val['id'] ?? '' }}][id]" value="{{$val['id']}}">
                                <input type="hidden" name="emp_ids[{{$val['id'] ?? '' }}][id_type_id]" value="{{$val['id_type_id']}}">
                                <input type="hidden" name="emp_ids[{{$val['id'] ?? '' }}][id_no]" value="{{$val['id_no']}}">

                                <div class="card-body">
                                    <button type="button" class="btn btn-danger btn-xs float-end btnRemove" data-id="{{$val['id'] ?? '' }}">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <h5>{{$empIDs_data[$val['id_type_id']]}}</h5>
                                    <p class="mb-0">ID No.: {{$val['id_no']}}</p>
                                </div>
                                <div class="card-footer">
                                    <p>Active</p>
                                </div>
                            </div>
                         </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<!-- job information modal -->
<div class = "ids-information-modal-container">
    @include('employee.register.modal.identification_modal')
</div>

@push('js')
<script>

$(document).ready(function () {
    // Use event delegation to attach click event to dynamically generated cards
     $(document).on('click', '.employee-IDs-informations .card', function (event) {
        let cardElement = $(this).closest('.card');

        // Check if the clicked element is a "Remove" button or the card itself
        if ($(event.target).hasClass('btnRemove')) {

            event.stopPropagation();
            // Trigger the remove logic here
            let cardId = $(this).data('id');
            $(`#${cardId}`).fadeOut(300, function () {
                $(this).remove();
            });
        } else {

         if (cardElement.parent().hasClass('new-ids-card')) {
             // Extract data from the dynamically added card
             let params = {
                 cardId: cardElement.parent().attr('id'),
                 idType_id: cardElement.find('input[name*="[id_type_id]"]').val(),
                 idNo: cardElement.find('input[name*="[id_no]"]').val(),
             };

             console.log("Dynamically Added Job Information:", params);
            //  alert(params.cardId);
             // Populate modal fields with data
             $('#id_type_id').val(params.idType_id);
             $('#id_no').val(params.idNo);

             // Store the card ID in the hidden field
             $('#IDsTarget').val(params.cardId);

             // Show the "Add" button and hide the "Update" button
             $('#addIDsInfo').hide();
             $('#updateIDsInfo').show();

             // Set add mode flag (optional, if needed)
             $('#modalIDs').data('isEditMode', true);

             // Show the modal
             $('#modalIDs').modal('show');
         } else {

             // Handle database-generated card logic here
             let params = {
                 cardId: $(this).data('id'),
                 idType_id: $(this).data('idtype-id'),
                 idNo: $(this).data('id-no'),
             };

             console.log("Database Job Information:", params);

             alert(params.idNo);
            //  alert(params.idNo);
             // Populate modal fields with data
             $('#id_type_id').val(params.idType_id);
             $('#id_no').val(params.idNo);

             // Store the card ID in the hidden field
             $('#IDsTarget').val(params.cardId);

              // Show the "Add" button and hide the "Update" button
             $('#addIDsInfo').hide();
             $('#updateIDsInfo').show();

             // Show the modal
             $('#modalIDs').modal('show');
         }
      }
    });

     // Remove Job Information
     $(document).on("click", ".btnRemove", function(event) {
        event.stopPropagation();
        // Get the unique card ID from the button's data attribute
        let cardId = $(this).data("id");

        // Remove the card with the specified ID
        $(`#${cardId}`).fadeOut(300, function() {
            $(this).remove();
        });
     });
});


$(document).on('click', '#updateIDsInfo', function () {
    // Get the updated values from the modal

    let updatedID_type = $('#id_type_id').val();
    let updatedID_no = $('#id_no').val();
    let updatedID_name = $('#id_type_id option:selected').text(); // Gets the text of the selected option


    // alert(updatedID_name);

    // Get the card ID stored in the hidden field
    let cardId = $('#IDsTarget').val();

    let card;

    if (cardId.includes('id-card')) {
        card = $('#' + cardId); // Find card using the card's ID directly
    } else {
        card = $('.card[data-id="' + cardId + '"]'); // Find the card by its data-id attribute
    }


    console.log("Card ID to update:", cardId);

    if (!card || card.length === 0) {
        // Log error if the card still cannot be found
        alert('Error: Could not find the card to update. Please try again.');
        console.error('Card not found with ID: ' + cardId);
        return;
    }

    // Update the card content
    card.find('.card-body h5').text(updatedID_name);
    card.find('.card-body .mb-0').eq(0).text('ID No.: ' + updatedID_no);

    // Update the card's data attributes in the DOM
    card.attr('data-idtype-id', updatedID_type);
    card.attr('data-id-no', updatedID_no);

    // Update the hidden input fields inside the card (for form submission)
    card.find('input[name$="[id_type_id]"]').val(updatedID_type);
    card.find('input[name$="[id_no]"]').val(updatedID_no);

    // Refresh jQuery's internal cache
    card.data('idtype-id', updatedID_type);
    card.data('id-no', updatedID_no);

    // Clear modal fields
    clearEmpIDsModal();

    // Close the modal
    $('#modalIDs').modal('hide');

});

    //family information functions --------------------------------------------------------------------------------------------------------------------------------------
    $("#tab-Emp-Identification .btnIDsInformation").on("click", function(){
        let targetField = $(this).attr("data-target");

        $("#modalIDs #IDsTarget").val(targetField);

        clearEmpIDsModal();

         // Set add mode flag
         $('#modalJobInformation').data('isEditMode', false);

        showIDsInformationModal();
    })

    $("#modalIDs .btnAdd").on("click", function(){
        // Constructing parameters for adding IDS information
         let params = {
             targetField: $("#modalIDs #IDsTarget").val(), //'.employee-fam-informations', // Directly specify the container class
             id_type_id: $("#modalIDs #id_type_id").val(),
             id_no: $("#modalIDs #id_no").val(),
             name: $("#modalIDs #id_type_id option:selected").text(),
             isEditMode: $("#modalJobInformation").data('isEditMode') // Check mode
         };

         console.log(params); // Check params in the console

         // Call the function to add the IDS information card
         addEmpIDs(params);

    })

    $("#modalIDs .btnCancel").on("click", function(){
        hideIDsInfoModal();
    })

    function showIDsInformationModal(){

        $("#modalIDs").modal("show");
    }

    function hideIDsInfoModal() {
        clearEmpIDsModal();
        $("#modalIDs").modal("hide");
    }

    function clearEmpIDsModal() {
        $("#modalIDs #IDsTarget").val("");
        $("#modalIDs #id_type_id").val("");
        $("#modalIDs #id_no").val("");
    }

    function addEmpIDs(params){
        // Ensure is initialized globally
        if (typeof EmpInfoIDsCount === 'undefined') {
            EmpInfoIDsCount = 0;
        }

        // Creating a unique ID for the job information card
        let uniqueId = `id-card-${Date.now()}`;

        // HTML structure for the job information card
        let card = `
            <div id="${uniqueId}" class="col-sm-12 col-md-4 col-lg-3 employee-IDs-information-items new-ids-card">
                <div class="card">
                    <input type="hidden" name="emp_ids[${EmpInfoIDsCount}][id_type_id]" value="${params.id_type_id}">
                    <input type="hidden" name="emp_ids[${EmpInfoIDsCount}][id_no]" value="${params.id_no}">

                    <div class="card-body">
                        <button type="button" class="btn btn-danger btn-xs float-end btnRemove" data-id="${uniqueId}">
                            <i class="fa fa-times"></i>
                        </button>
                        <h5>${params.name}</h5>
                        <p class="mb-0">ID No: ${params.id_no}</p>
                    </div>
                    <div class="card-footer">
                        <p>Active</p>
                    </div>
                </div>
            </div>`;

          // Append the new card to the target container
          $(".employee-IDs-informations").append(card);
        // $(card).hide().appendTo(params.targetField).fadeIn(300);

        // Increment the job information counter
        EmpInfoIDsCount++;

        // Hide the modal after adding the job information
        hideIDsInfoModal();
    }

    // Remove Fam Information
    // $(document).on("click", ".btnRemove", function() {
    // // Get the unique card ID from the button's data attribute
    //     let cardId = $(this).data("id");

    // // Remove the card with the specified ID
    // $(`#${cardId}`).fadeOut(300, function() {
    //     $(this).remove();
    //     });
    // });
</script>
@endpush