
<!-- Family information -->
<div class = "row">
    <div class = "col-sm-12 col-md-7 col-lg-9">
        <div class = "card">
            <div class = "card-header">
                <h5>Family Information</h5>
                <div class = "card-header-right">
                    <button type = "button"
                        class = "btn btn-primary btn-xs btnFamInformation"
                        data-target = ".employee-fam-informations"
                    >
                        <i class = "fa fa-plus text-white"></i>
                    </button>
                </div>
            </div>
            <!-- <input type = "hidden" name = "emp_fam[deleted_list]" value = ""> -->
            <div class = "card-body">
                <div class = "row employee-fam-informations">
                @if(isset($family_data) && is_array($family_data))
                    @foreach($family_data as $key => $val)
                        <div id="{{$val['id'] ?? '' }}" class="col-sm-12 col-md-4 col-lg-3 employee-fam-information-items">
                            <div class="card"
                                data-id = "{{ $val['id'] ?? '' }}"
                                data-relation="{{ $val['relation'] ?? '' }}"
                                data-fname="{{ $val['fname'] ?? '' }}"
                                data-lname="{{ $val['lname'] ?? '' }}"
                                data-mname="{{ $val['mname'] ?? '' }}"
                                data-birthdate="{{ $val['birthdate'] ?? '' }}"
                            >
                                <input type="hidden" name="emp_fam[{{$val['id'] ?? '' }}][is_old]" value="1" >
                                <input type="hidden" name="emp_fam[{{$val['id'] ?? '' }}][id]" value="{{$val['id']}}">
                                <input type="hidden" name="emp_fam[{{$val['id'] ?? '' }}][relation]" value="{{$val['relation']}}">
                                <input type="hidden" name="emp_fam[{{$val['id'] ?? '' }}][fname]" value="{{$val['fname']}}">
                                <input type="hidden" name="emp_fam[{{$val['id'] ?? '' }}][lname]" value="{{$val['lname']}}">
                                <input type="hidden" name="emp_fam[{{$val['id'] ?? '' }}][mname]" value="{{$val['mname']}}">
                                <input type="hidden" name="emp_fam[{{$val['id'] ?? '' }}][birthdate]" value="{{$val['birthdate']}}">

                                <div class="card-body">
                                    <button type="button" class="btn btn-danger btn-xs float-end btnRemove" data-id="{{$val['id'] ?? '' }}">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <h5>{{config('constants.employee_relation')[$val['relation']]}}</h5>
                                    <p class="mb-0">Last Name: {{isset($val['lname']) ? $val['lname'] : ''}}</p>
                                    <p class="mb-0">First Name: {{isset($val['fname']) ? $val['fname'] : ''}}</p>
                                    <p class="mb-0">Middle Name: {{isset($val['mname']) ? $val['mname'] : ''}}</p>
                                    <p class="mb-0">Birth Date: {{isset($val['birthdate']) ? $val['birthdate'] : 'N/A'}}</p>
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
<div class = "fam-information-modal-container">
    @include('employee.register.modal.family_information_modal')
</div>

@push('js')
<script>



//Family Retrieval Info. -----------
$(document).ready(function () {
    // Use event delegation to attach click event to dynamically generated cards
     $(document).on('click', '.employee-fam-informations .card', function (event) {
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
         if (cardElement.parent().hasClass('new-fam-card')) {
             // Extract data from the dynamically added card
             let params = {
                 cardId: cardElement.parent().attr('id'),
                 relation: cardElement.find('input[name*="[relation]"]').val(),
                 fname: cardElement.find('input[name*="[fname]"]').val(),
                 lname: cardElement.find('input[name*="[lname]"]').val(),
                 mname: cardElement.find('input[name*="[mname]"]').val(),
                 birthDate: cardElement.find('input[name*="[birthdate]"]').val(),

             };

             console.log("Dynamically Added Job Information:", params);

            //  alert(params.birthDate);
            //  alert(params.cardId);
             // Populate modal fields with data
             $('#relation').val(params.relation);
             $('#fname').val(params.fname);
             $('#lname').val(params.lname);
             $('#mname').val(params.mname);
             $('#birthdate').val(params.birthDate);

             // Store the card ID in the hidden field
             $('#FamInformationTarget').val(params.cardId);

            // Show the "Add" button and hide the "Update" button
            $('#addFamInfo').hide();
             $('#updateFamInfo').show();

             // Set add mode flag (optional, if needed)
             $('#modalFamInfo').data('isEditMode', true);

             // Show the modal
             $('#modalFamInfo').modal('show');

         } else {

            // Handle database-generated card logic here
            let params = {
                cardId: $(this).data('id'),
                relation: $(this).data('relation'),
                fname: $(this).data('fname'),
                lname: $(this).data('lname'),
                mname: $(this).data('mname'),
                birthDate: $(this).data('birthdate'),
            };



            // alert(params);
            console.log("Database Job Information:", params);

            // alert(params.relation);

            // Populate modal fields with data
            $('#relation').val(params.relation);
            $('#fname').val(params.fname);
            $('#lname').val(params.lname);
            $('#mname').val(params.mname);
            $('#birthdate').val(params.birthDate);

            // Store the card ID in the hidden field
            $('#FamInformationTarget').val(params.cardId);

             // Show the "Add" button and hide the "Update" button
            $('#addFamInfo').hide();
            $('#updateFamInfo').show();

             // Set add mode flag (optional, if needed)
             $('#modalFamInfo').data('isEditMode', true);

            // Show the modal
            $('#modalFamInfo').modal('show');
         }
      }
    });

     // Remove Job Information
      $(document).on("click", ".btnRemove", function(event) {
        event.stopPropagation();

        let cardId = $(this).data('id');
            $(`#${cardId}`).fadeOut(300, function () {
                $(this).remove();
            });

     });

});


// On "Update" button click, update the card with the new values
$(document).on('click', '#updateFamInfo', function () {
    // Get the updated values from the modal

    let updatedFname = $('#fname').val();
    let updatedLname = $('#lname').val();
    let updatedMname = $('#mname').val();
    let updatedBirthDate = $('#birthdate').val();
    let updatedRelationID = $('#relation').val();
    let updatedRelation = $('#relation option:selected').text(); // Gets the text of the selected option

    // Get the card ID stored in the hidden field
    let cardId = $('#FamInformationTarget').val();

    let card;

    if (cardId.includes('fam-card')) {
        card = $('#' + cardId); // Find card using the card's ID directly
    } else {
        card = $('.card[data-id="' + cardId + '"]'); // Find the card by its data-id attribute
    }
    // alert(cardId);

    console.log("Card ID to update:", cardId);

    if (!card || card.length === 0) {
        // Log error if the card still cannot be found
        alert('Error: Could not find the card to update. Please try again.');
        console.error('Card not found with ID: ' + cardId);
        return;
    }

    // alert(updatedLname);

    // Update the card content
    card.find('.card-body h5').text(updatedRelation);
    card.find('.card-body .mb-0').eq(0).text('Last Name: ' + updatedLname);
    card.find('.card-body .mb-0').eq(1).text('First Name: ' + updatedFname);
    card.find('.card-body .mb-0').eq(2).text('Middle Name: ' + updatedMname);
    card.find('.card-body .mb-0').eq(3).text('Birth Date: ' + updatedBirthDate);


    // Update the card's data attributes in the DOM
    card.attr('data-relation', updatedRelationID);
    card.attr('data-lname', updatedLname);
    card.attr('data-fname', updatedFname);
    card.attr('data-mname', updatedMname);
    card.attr('data-birthdate', updatedBirthDate);


    // Update the hidden input fields inside the card (for form submission)
    card.find('input[name$="[relation]"]').val(updatedRelationID);
    card.find('input[name$="[lname]"]').val(updatedLname);
    card.find('input[name$="[fname]"]').val(updatedFname);
    card.find('input[name$="[mname]"]').val(updatedMname);
    card.find('input[name$="[birthdate]"]').val(updatedBirthDate);


    // Refresh jQuery's internal cache
    card.data('relation', updatedRelationID);
    card.data('lname', updatedLname);
    card.data('fname', updatedFname);
    card.data('mname', updatedMname);
    card.data('birthdate', updatedBirthDate);


    // Clear modal fields
    clearFamInformationModal();

    // Close the modal
    $('#modalFamInfo').modal('hide');

});




//family information functions --------------------------------------------------------------------------------------------------------------------------------------
    $("#tab-family-information .btnFamInformation").on("click", function(){
        let targetField = $(this).attr("data-target");

        $("#modalFamInfo #FamInformationTarget").val(targetField);

        clearFamInformationModal();

        // Set add mode flag
        $('#modalJobInformation').data('isEditMode', false);

        showFamInformationModal();

    })

    $("#modalFamInfo .btnAdd").on("click", function(){
        // Constructing parameters for adding family information
         let params = {
             targetField: $("#modalFamInfo #FamInformationTarget").val(), //'.employee-fam-informations', // Directly specify the container class
             fname: $("#modalFamInfo #fname").val(),
             lname: $("#modalFamInfo #lname").val(),
             mname: $("#modalFamInfo #mname").val(),
             birthdate: $("#modalFamInfo #birthdate").val(),
             relation: $("#modalFamInfo #relation").val(),
             employee_relation: $("#modalFamInfo #relation option:selected").text(),
             isEditMode: $("#modalJobInformation").data('isEditMode') // Check mod
         };

         if (params.isEditMode) {
             // Handle update logic here
             console.log("Updating existing fam info...");
             updateFamInformation(params); // Create this function if needed
         } else {
             // Handle add logic here
             console.log("Adding new fam info...");
             addFamInformation(params); // Existing function
         }
        //  console.log(params); // Check params in the console
        //  // Call the function to add the family information card
        //  addFamInformation(params);

    })

    $("#modalFamInfo .btnCancel").on("click", function(){
        hideFamInformationModal();
    })

    function showFamInformationModal(){
        $("#modalFamInfo").modal("show");
    }

    function hideFamInformationModal() {
        clearFamInformationModal();
        $("#modalFamInfo").modal("hide");

    }

    function clearFamInformationModal() {
        $("#modalFamInfo #FamInformationTarget").val("");
        $("#modalFamInfo #fname").val("");
        $("#modalFamInfo #lname").val("");
        $("#modalFamInfo #mname").val("");
        $("#modalFamInfo #relation").val("");
        $("#modalFamInfo #birthdate").val("");
    }

    function addFamInformation(params){
        // Ensure famInformationCount is initialized globally
        if (typeof famInformationCount === 'undefined') {
            famInformationCount = 0;
        }
          // Debug: Log the params object to check the data being passed
         console.log('Adding family member information:', params);

        // Creating a unique ID for the job information card
        let uniqueId = `fam-card-${Date.now()}`;

        // HTML structure for the job information card
        let card = `
            <div id="${uniqueId}" class="col-sm-12 col-md-4 col-lg-3 employee-fam-information-items new-fam-card">
                <div class="card">
                    <input type="hidden" name="emp_fam[[${famInformationCount}][relation]" value="${params.relation}">
                    <input type="hidden" name="emp_fam[[${famInformationCount}][fname]" value="${params.fname}">
                    <input type="hidden" name="emp_fam[[${famInformationCount}][lname]" value="${params.lname}">
                    <input type="hidden" name="emp_fam[[${famInformationCount}][mname]" value="${params.mname}">
                    <input type="hidden" name="emp_fam[[${famInformationCount}][birthdate]" value="${params.birthdate}">

                    <div class="card-body">
                        <button type="button" class="btn btn-danger btn-xs float-end btnRemove" data-id="${uniqueId}">
                            <i class="fa fa-times"></i>
                        </button>
                        <h5>${params.employee_relation}</h5>
                        <p class="mb-0">Last Name: ${params.lname}</p>
                        <p class="mb-0">First Name: ${params.fname}</p>
                        <p class="mb-0">Middle Name: ${params.mname}</p>
                        <p class="mb-0">Birth Date: ${params.birthdate}</p>
                    </div>
                    <div class="card-footer">
                        <p>Active</p>
                    </div>
                </div>
            </div>`;

        // Append the new card to the target container with animation
        // $(card).hide().appendTo(params.targetField).fadeIn(300);

        // Append the new card to the target container
        $(".employee-fam-informations").append(card);

        // Increment the job information counter
        famInformationCount++;

        // Optionally, trigger a reflow to ensure layout updates properly
        setTimeout(function () {
            targetField[0].offsetHeight; // Trigger reflow
        }, 300);

        // Hide the modal after adding the job information
        hideFamInformationModal();
    }

</script>

<!-- <script>
    var family_data = @json($family_data ?? []);
    console.log("Family Data Loaded:", family_data);
</script> -->
@endpush