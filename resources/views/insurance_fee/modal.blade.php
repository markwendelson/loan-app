
<div class = "modal fade" id = "modalInsFee">
    <div class = "modal-dialog modal-sm">
        <div class = "modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Insurance Fee Schedule Information</h5>
            </div>

            <div class="modal-body">
                <form id="frmInsFeeModal">
                    <div class = "row">
                        @csrf
                        <!-- id -->
                        <input type = "hidden" value = "" name = "id">

                        <!-- Min. Amount -->
                        <div class = "col-12">
                            <label class = "w-100">
                                Min. Amount
                                <input type = "text"
                                    class = "form-control form-control-sm"
                                    name = "min_amount"
                                    id ="minAmount"
                                    placeholder = "Min amount..."
                                    required
                                >
                            </label>
                        </div>

                        <!-- Amount -->
                        <div class = "col-12">
                            <label class = "w-100">
                                Max. Amount
                                <input type = "tex"
                                    class = "form-control form-control-sm"
                                    placeholder = "Max amount..."
                                    name = "max_amount"
                                    id ="maxAmount"
                                >
                            </label>
                        </div>

                          <!-- Monthly Amount -->
                          <div class = "col-12">
                            <label class = "w-100">
                                Monthly Amount
                                <input type = "text"
                                    class = "form-control form-control-sm"
                                    placeholder = "Monthly amount..."
                                    name = "monthly_amount"
                                    id ="monthlyAmount"
                                >
                            </label>
                        </div>

                          <!-- Annual Amount -->
                          <div class = "col-12">
                            <label class = "w-100">
                                Annual
                                <input type = "text"
                                    class = "form-control form-control-sm"
                                    placeholder = "Annual amount"
                                    name = "annual_amount"
                                    id ="annualAmount"
                                >
                            </label>
                        </div>

                        <!-- status -->
                        <div class = "col-12">
                            <label class = "w-100">
                                Status
                                <select class = "form-select form-select-sm" name = "status">
                                    <option value = "" selected hidden disabled>-- Select --</option>
                                    @foreach(config('constants.status_value') as $key => $val)
                                        <option value = "{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer gap-1">
                <button type="button" class="btn btn-primary btnSubmit w-100"><i class = "fa fa-save"></i> Submit</button>
                <button type="button" class="btn btn-ligh w-100" onclick = "hideModal()"><i class = "fa fa-close"></i> Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>

//TEmporary hide for Autonumber format -------------
    // Function to format number input
    function formatNumberInput(event) {
        let value = event.target.value;

    // Allow only numeric characters and one decimal point
    value = value.replace(/[^0-9.]/g, '');  // Allow numbers and the decimal point

    // Ensure only one decimal point
    const decimalIndex = value.indexOf('.');
    if (decimalIndex !== -1) {
        value = value.slice(0, decimalIndex + 3);  // Limit to two decimal places after the point
    }

    // Format the number with thousands separators (before the decimal point)
    let [integerPart, decimalPart] = value.split('.');

    // Add thousands separator to the integer part
    integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

    // If there's a decimal part, ensure it has two decimal places
    if (decimalPart) {
        decimalPart = decimalPart.slice(0, 2);  // Limit to 2 decimal places
        value = `${integerPart}.${decimalPart}`;
    } else {
        value = integerPart;  // No decimal part
    }

    // Update the input field with the formatted value
    setTimeout(() => {
        event.target.value = value;
    }, 0);
}

    // Apply the formatting function to both inputs
    // Apply the formatting function to all relevant inputs
    ['minAmount', 'maxAmount', 'monthlyAmount', 'annualAmount'].forEach(id => {
        document.getElementById(id).addEventListener('input', formatNumberInput);
    });

    $(document).on('click', '#minAmount', function() {
        // Only select the text if it's not already being typed (focus on click only)
             $(this).select();
    });

    $(document).on('click', '#maxAmount', function() {
       // Only select the text if it's not already being typed (focus on click only)
    //    if ($(this).val() === "") {
             $(this).select();
        //  }
    });

    $(document).on('click', '#monthlyAmount', function() {
        // Only select the text if it's not already being typed (focus on click only)
             $(this).select();
    });

    $(document).on('click', '#annualAmount', function() {
         // Only select the text if it's not already being typed (focus on click only)
             $(this).select();
    });

</script>
