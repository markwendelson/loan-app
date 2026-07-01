@extends('layouts.app')

@section('title') Walk-in Clients @endsection

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Walk-in Clients</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Transaction</li>
                        <li class="breadcrumb-item">Others</li>
                        <li class="breadcrumb-item active">Walk-In Transactions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Search + Buttons Card -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-end align-items-center flex-wrap gap-2">

                <!-- Right side: Buttons grouped -->
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" id="btnAddWalkin"><i class="fa fa-plus me-1"></i>Add Walk-in</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card d-none d-md-block">
        <div class="card-header">
            @livewire('Walkin.FilterData')
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @livewire('Walkin.WalkinData' ,[
                    'defer' => true
                ])
            </div>
        </div>
    </div>

    {{-- Include Walk-in Modal --}}
    @include('others.walkin.modal')
@endsection

@push('js')
<script>

    $(document).ready(function(){

        //handles new walkin button click function
        $("#btnAddWalkin").on("click", function(e) {
            $("#walkinModal").modal("show");
        })

        $(document).on("click", "#walkinModal .btnSubmit", function(e) {
            e.preventDefault();
            let submitUrl;
            let method;

            let modal = $(this).closest(".modal-content");

            let formElement =  $(modal).find("form")[0];
            let formData = new FormData(formElement);

            let walkinId = $("#walkinModal input[name='id']").val();
            
            if(walkinId){
                method = "PUT";
                submitUrl = '{{ route('walkin.update') }}';
            }else{
                method = "POST";
                submitUrl = '{{ route('walkin.store') }}';
            }

            if (!$(modal).find("#walkinForm")[0].checkValidity()) {
                swal({
                    title: 'Error',
                    text: 'Please fill all required fields',
                    icon: 'warning',
                    buttons: {
                        confirm: {
                            text: "OK",
                            value: true,
                            visible: true,
                            className: "btn btn-primary",
                            closeModal: true
                        }
                    },
                    dangerMode: true,
                });
            }else{
                //show loading form
                $(".loader-wrapper").fadeIn("slow");

                $.ajax({
                    type: method,
                    url: submitUrl,
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        //hide loading form
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });

                        if(response.status !== 'undifined' && response.status == 'success') {
                            swal({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                buttons: {
                                    confirm: {
                                        text: "OK",
                                        value: true,
                                        visible: true,
                                        className: "btn btn-primary",
                                        closeModal: true
                                    }
                                },
                            }).then((willClose) => {
                                if (willClose) {
                                    Livewire.dispatch('refresh-walkin-data');

                                    $("#walkinModal").modal("hide");
                                    
                                    if(!walkinId) {
                                        window.open(`walkin/${response.data.id}/receipt`, '_blank');
                                    }
                                }
                            });
                        }else{
                            swal({
                                title: 'Error',
                                text: response.message,
                                icon: 'warning',
                                buttons: {
                                    confirm: {
                                        text: "OK",
                                        value: true,
                                        visible: true,
                                        className: "btn btn-primary",
                                        closeModal: true
                                    }
                                },
                                dangerMode: true,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        swal({
                            title: 'Error',
                            text: error,
                            icon: 'warning',
                            buttons: {
                                confirm: {
                                    text: "OK",
                                    value: true,
                                    visible: true,
                                    className: "btn btn-primary",
                                    closeModal: true
                                }
                            },
                            dangerMode: true,
                        });
                    }
                });
            }

        })
    });
    // const modal = new bootstrap.Modal(document.getElementById('walkinModal'));

    // Select/Deselect all checkboxes
    // document.getElementById('selectAll').addEventListener('change', function() {
    //     const checked = this.checked;
    //     document.querySelectorAll('.selectItem').forEach(cb => cb.checked = checked);
    // });

    // document.addEventListener('click', e => {
    //     const printBtn = e.target.closest('.printBtn');
    //     if (!printBtn) return;

    //     const id = printBtn.dataset.id;
    //     if (!id) return;

    //     window.open(`/walkin/${id}/receipt`, '_blank');
    //     // if (e.target.classList.contains('printBtn')) {
    //     //     const id = e.target.closest('tr').dataset.id;
    //     //     window.open(`/walkin/${id}/receipt`, '_blank');
    //     // }
    // });

    // document.getElementById('btnPrint').addEventListener('click', () => {
    //     const selectedIds = Array.from(document.querySelectorAll('.selectItem:checked'))
    //         .map(cb => cb.value);

    //     if(selectedIds.length === 0) {
    //         alert('Please select at least one record to print.');
    //         return;
    //     }

    //     // Open new window for each selected record
    //     selectedIds.forEach(id => {
    //         window.open(`/walkin/${id}/receipt`, '_blank');
    //     });
    // });

    // Open modal for Add
    // document.getElementById('btnAddWalkin').addEventListener('click', () => {
    //     resetForm();
    //     document.getElementById('walkinModalLabel').textContent = 'Add Walk-in';
    //     modal.show();
    // });

;

    // Reset form & default date
    function resetForm() {
        $("#walkinModal form")[0].reset();
    }

    // Auto-calculate charge & net proceeds
    // document.getElementById('amount_withdrawn').addEventListener('input', function() {
    //     const amount = parseFloat(this.value) || 0;
    //     const noChargeCheckbox = document.getElementById('no_service_charge');
    //     const chargeField = document.getElementById('charge');
    //     const netField = document.getElementById('net_proceeds');

    //     if (noChargeCheckbox.checked) {
    //         chargeField.value = '0.00';
    //         netField.value = amount.toFixed(2);
    //     } else {
    //         // Use your existing logic or DB percentage
    //         let charge = amount <= 3000 ? amount*0.035 : amount <= 5000 ? amount*0.03 : amount*0.02;
    //         chargeField.value = charge.toFixed(2);
    //         netField.value = (amount - charge).toFixed(2);
    //     }
    //     // const amount = parseFloat(this.value) || 0;
    //     //  const noChargeCheckbox = document.getElementById('no_service_charge');
    //     // let charge = amount <= 3000 ? amount*0.035 : amount <= 5000 ? amount*0.03 : amount*0.02;
    //     // document.getElementById('charge').value = charge.toFixed(2);
    //     // document.getElementById('net_proceeds').value = (amount - charge).toFixed(2);
    // });

    // Edit
    document.addEventListener('click', async e => {
        const editBtn = e.target.closest('.editBtn');
        if (!editBtn) return;

        const id = editBtn.dataset.id;
        if (!id) return;

        const res = await fetch(`/walkin/${id}`);
        const data = await res.json();

        document.getElementById('walkinId').value = data.id;
        document.getElementById('date').value = data.date;
        document.getElementById('last_name').value = data.last_name;
        document.getElementById('first_name').value = data.first_name;
        document.getElementById('middle_initial').value = data.middle_initial;
        document.getElementById('amount_withdrawn').value = data.amount_withdrawn;
        document.getElementById('charge').value = data.charge;
        document.getElementById('net_proceeds').value = data.net_proceeds;

        // Checkbox logic
        const noChargeCheckbox = document.getElementById('no_service_charge');
        const noChargeValue = document.getElementById('no_service_charge_value');

        noChargeCheckbox.checked = data.no_service_charge == 1;
        noChargeValue.value = data.no_service_charge == 1 ? 1 : 0;

        document.getElementById('charge').disabled = noChargeCheckbox.checked;

        document.getElementById('walkinModalLabel').textContent = 'Edit Walk-in';
        modal.show();
        // if (e.target.classList.contains('editBtn')) {
        //     const id = e.target.closest('tr').dataset.id;
        //     const res = await fetch(`/walkin/${id}`);
        //     const data = await res.json();

        //     document.getElementById('walkinId').value = data.id;
        //     document.getElementById('date').value = data.date;
        //     document.getElementById('last_name').value = data.last_name;
        //     document.getElementById('first_name').value = data.first_name;
        //     document.getElementById('middle_initial').value = data.middle_initial;
        //     document.getElementById('amount_withdrawn').value = data.amount_withdrawn;
        //     document.getElementById('charge').value = data.charge;
        //     document.getElementById('net_proceeds').value = data.net_proceeds;

        //   // Set checkbox & hidden field
        //     const noChargeCheckbox = document.getElementById('no_service_charge');
        //     const noChargeValue = document.getElementById('no_service_charge_value');
        //     noChargeCheckbox.checked = data.no_service_charge == 1;
        //     noChargeValue.value = data.no_service_charge == 1 ? 1 : 0;

        //     // Disable charge if no service charge
        //     document.getElementById('charge').disabled = noChargeCheckbox.checked ? true : false;

        //     document.getElementById('walkinModalLabel').textContent = 'Edit Walk-in';
        //     modal.show();
        // }
    });

    // Save (Create/Update)
    // $("walkinForm .btnSubmit").on("click", function(e) {
    // // document.getElementById('walkinForm').addEventListener('submit', async e => {
    //     e.preventDefault();

    //     const form = e.target;
    //     const data = Object.fromEntries(new FormData(form));
    //     const id = data.id;

    //     const url = id ? `/walkin/${id}` : '/walkin';
    //     const method = id ? 'PUT' : 'POST';

    //     const csrfToken =
    //         document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    //     const response = await fetch(url, {
    //         method,
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'Accept': 'application/json', // ← REQUIRED
    //             'X-CSRF-TOKEN': csrfToken
    //         },
    //         body: JSON.stringify(data)
    //     });

    //     // First, parse the JSON
    //     const result = await response.json();

    //     // Then, if successful, do modal hide, open receipt, reload table
    //     if(response.ok){
    //         modal.hide();
    //         resetForm();
    //             window.open(result.print_url, '_blank'); //  open in new tab
    //         location.reload();


    //     } else {
    //         alert(result.message || "Failed to save");
    //         console.error(result);
    //     }

    // });

    //modal submit button click function
    

    // Delete
    document.addEventListener('click', async e => {
        const deleteBtn = e.target.closest('.deleteBtn');
        if (!deleteBtn) return;

        if (!confirm('Are you sure?')) return;

        const id = deleteBtn.dataset.id;
        if (!id) return;

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        await fetch(`/walkin/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        location.reload();
        // if (e.target.classList.contains('deleteBtn')) {
        //     if (!confirm('Are you sure?')) return;
        //     const id = e.target.closest('tr').dataset.id;
        //     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        //     await fetch(`/walkin/${id}`, { method:'DELETE', headers:{ 'X-CSRF-TOKEN': csrfToken } });
        //     location.reload();
        // }
    });
</script>
@endpush
