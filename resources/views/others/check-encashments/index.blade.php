@extends('layouts.app')

@section('title') Check Encashments @endsection

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Check Encashments</h4>
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
                        <li class="breadcrumb-item active">Check Encashments</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Header + Add button card -->
    <div class="card mb-3">
        <div class="card-body">
            <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#checkModal" onclick="openCreateModal()">
                <i class="fa fa-plus me-1"></i>Add Check
            </button>
        </div>
    </div>

    <!-- Table card -->
    <div class="card">
        <div class="card-header">
            <livewire:CheckEncashment.FilterData />
        </div>
        <div class="card-body p-0">
            <livewire:CheckEnCashment.EncashmentData  defer />
        </div>
    </div>
{{-- Include modal --}}
@include('others.check-encashments.modal')
@endsection

@push('js')
    <script>
        function openCreateModal() {

            $('#checkForm')[0].reset();
            $('#checkId').val('');
            // Clear name fields
            $('#lastName').val('');
            $('#firstName').val('');
            $('#middleName').val('');

            $('#idPicturePreview').attr('src', ''); // clear preview
            $('#checkModalLabel').text('Add Check');

            // Set default date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById("transDate").value = today;

            // Reset image preview
            const preview = document.getElementById("idPicturePreview");
            preview.src = '';
            preview.style.display = "none";
        }



        // Format a number with commas
        function formatNumberWithCommas(num) {
            if (!num) return '';
            let parts = num.toString().split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.join('.');
        }

        // Remove commas before saving
        function unformatNumber(num) {
            return num ? num.replace(/,/g, '') : 0;
        }

        const checkAmountInput = document.getElementById('checkAmount');

        // Select all on focus
        checkAmountInput.addEventListener('focus', function() {
            this.select();
        });

        // Prevent mouse click from deselecting
        checkAmountInput.addEventListener('mouseup', function(e){
            e.preventDefault();
        });

        // Live formatting on key input
        ['checkAmount', 'charge', 'netProceeds'].forEach(id => {
            const input = document.getElementById(id);
            input.addEventListener('input', function() {
                // Remove any non-digit and non-dot characters
                let clean = this.value.replace(/[^0-9.]/g, '');
                // Format with commas
                this.value = formatNumberWithCommas(clean);
            });
        });

        window.serviceCharges = @json($serviceCharges);

        /* ===========================
        LOAD CHECK INTO EDIT MODAL
        =========================== */
        function editCheck(id) {
            if (!id) return;

            $.get('{{ url("check-encashments") }}/' + id, function(data){
            console.log("FULL DATA:", data);
            console.log("checkAmount:", data.checkAmount);
            console.log("charge:", data.charge);
            console.log("net_proceeds:", data.net_proceeds);

                $('#checkId').val(data.id);
                $('#transDate').val(data.transDate);
                $('#checkNo').val(data.checkNo);
                $('#bankName').val(data.bankName);
                $('#checkAmount').val(formatNumberWithCommas(data.checkAmount));
                $('#charge').val(formatNumberWithCommas(data.charge));
                $('#netProceeds').val(formatNumberWithCommas(data.net_proceeds));

                $('#idType').val(data.id_type);
                $('#idNumber').val(data.id_number);

                // ✅ APPLY no_service_charge
                const noService = data.no_service_charge == 1 || data.no_service_charge === true;
                $('#noServiceCharge').prop('checked', noService);
                $('#charge').prop('readonly', noService);

                // 🔁 Recalculate to ensure consistency
                calculateCharges();

                // Populate Last Name, First Name, Middle Initial
                $('#lastName').val(data.last_name ? data.last_name : '');
                $('#firstName').val(data.first_name ? data.first_name : '');
                $('#middleName').val(data.middle_name ? data.middle_name.toUpperCase() : '');

                const preview = $('#idPicturePreview');
                if (data.id_picture_path) {
                    preview.attr('src', '/storage/' + data.id_picture_path);
                    preview.show();
                } else {
                    preview.attr('src', '');
                    preview.hide();
                }

                $('#checkModalLabel').text('Edit Check');
                $('#checkModal').modal('show');
            });
        }

        /* ===========================
        SAVE CHECK (CREATE/UPDATE)
        =========================== */
        document.getElementById('checkForm').addEventListener('submit', async e => {
            e.preventDefault();

            // Normalize numeric inputs BEFORE FormData
            const checkAmountEl = document.getElementById('checkAmount');
            const chargeEl = document.getElementById('charge');
            const netProceedsEl = document.getElementById('netProceeds');

            checkAmountEl.value = unformatNumber(checkAmountEl.value);
            chargeEl.value = unformatNumber(chargeEl.value);
            netProceedsEl.value = unformatNumber(netProceedsEl.value);


            const noServiceCheckbox = document.getElementById('noServiceCharge');

        // Force boolean-compatible values
        if (noServiceCheckbox.checked) {
            noServiceCheckbox.value = 1;
        } else {
            // Ensure it's still submitted
            noServiceCheckbox.checked = true;
            noServiceCheckbox.value = 0;
        }

            const form = e.target;
            const formData = new FormData(form);

            const id = document.getElementById('checkId').value;
            const url = id ? `/check-encashments/${id}` : '/check-encashments';

            if (id) {
                formData.append('_method', 'PUT');
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    },
                    body: formData
                });

                const contentType = response.headers.get('content-type');

                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    alert('Server error occurred');
                    return;
                }

                const result = await response.json();

                if (!response.ok) {
                    alert(result.message || 'Failed to save check');
                    console.error(result);
                    return;
                }

                $('#checkModal').modal('hide');

                if (result.print_url) {
                    window.open(result.print_url, '_blank');
                }

                location.reload();

            } catch (err) {
                console.error(err);
                alert('Unexpected error while saving check');
            }
        });

        /* ===========================
        DELETE CHECK
        =========================== */
        function deleteCheck(id) {
            if(confirm('Are you sure you want to delete this check?')) {
                $.ajax({
                    url: '/check-encashments/' + id,
                    method: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        $('#checkRow' + id).remove();
                    },
                    error: function(err) {
                        alert('Error deleting check');
                    }
                });
            }
        }


        function getTierPercentage(amount) {
            if (amount <= 3000) return 3.5;
            if (amount <= 5000) return 3.0;
            return 2.0;
        }

        function getServiceChargePercentage(amount) {

            // controller already filtered is_active + fee_type
            // Default if DB is missing something
            const DEFAULT_RATE = 3;

            if (!Array.isArray(window.serviceCharges) || window.serviceCharges.length === 0) {
                return DEFAULT_RATE;
            }

            // Normalize DB values
            const rates = window.serviceCharges.map(p => parseFloat(p.percentage));

            if (amount > 5000) {
                return rates.includes(2) ? 2 : DEFAULT_RATE;
            }

            if (amount >= 3001 && amount <= 5000) {
                return rates.includes(3) ? 3 : DEFAULT_RATE;
            }

            if (amount >= 1 && amount <= 3000) {
                return rates.includes(3.5) ? 3.5 : DEFAULT_RATE;
            }

            return DEFAULT_RATE;
        }

        function calculateCharges() {
            const amount = parseFloat(unformatNumber($('#checkAmount').val())) || 0;
            const noCharge = $('#noServiceCharge').is(':checked');

            if (amount <= 0) {
                $('#charge').val('');
                $('#netProceeds').val('');
                return;
            }

            // override
            if (noCharge) {
                $('#charge').val(formatNumberWithCommas(0));
                $('#netProceeds').val(formatNumberWithCommas(amount));
                return;
            }

            const percentage = getServiceChargePercentage(amount);

            let charge = amount * (percentage / 100);
            charge = Math.round(charge * 100) / 100;

            const net = Math.round((amount - charge) * 100) / 100;

            $('#charge').val(formatNumberWithCommas(charge));
            $('#netProceeds').val(formatNumberWithCommas(net));
        }


        $(document).ready(function () {

            $('#noServiceCharge').on('change', function () {
            $('#charge').prop('readonly', this.checked);
            });

            $('#checkAmount').on('input', calculateCharges);
            $('#noServiceCharge').on('change', calculateCharges);

            // recalc when modal opens (important for Edit)
            $('#checkModal').on('shown.bs.modal', function () {
                calculateCharges();
            });

        });
    </script>
@endpush
