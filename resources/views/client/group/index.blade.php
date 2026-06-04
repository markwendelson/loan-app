@extends('layouts.app')

@section('title') Client Groups @endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-selection__arrow {
            display: none !important; /* Hide the dropdown arrow */
        }
        
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            line-height: 36px !important;  /* Slightly smaller line-height to compensate for borders */
            padding-top: 5px !important;
            border-color: #dee2e6 !important;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Client Grouping</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Transaction</li>
                        <li class="breadcrumb-item">Client</li>
                        <li class="breadcrumb-item active">Client Grouping</li>
                    </ol>
                </div>
            </div>
        </div>

        @if ($errors->has('error'))
            <div class="alert alert-danger rounded">
                {{ $errors->first('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success rounded">
                {{ session('success') }}
            </div>
        @endif

        <form id="frmClientGrouping" method="post" action="{{ url()->current() }}">
            @csrf
            <div class="card">
                <div class="card-header d-flex justify-content-end">
                    <button type="button" class="btn btn-primary btnNewClient"><i class="fa fa-plus"></i> New Client</button>
                </div>
                <div class="card-body p-0">
                    
                        <table class="table table-bordered" id="tblClientGroupings">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Type</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        
                    
                </div>
            </div>

            <!-- button container -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit Client Grouping</button>
            </div>
        </form>

    </div>

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        var clients = @json($clients);
        var groupTypes = @json($clientGroupType);

        $(document).ready(function() {
            //handle new client click function
            $(".btnNewClient").on("click", function(e) {
                e.preventDefault();

                let clientOptions = `<option value="" selected hidden disabled>-- Client --</option>`;
                if(clients.length > 0) {
                    clients.forEach(val => {
                        clientOptions += `<option value="${val.id}">${val.firstname}</option>`;
                    })
                }

                let typesOption = `<option value="" selected hidden disabled>-- Type --</option>`;
                groupTypes.forEach(val => {
                    typesOption += `<option value="${val.id}">${val.name}</option>`;
                });

                let tr = `
                    <tr>
                        <td>
                            <select class="form-select clientOption">
                                ${clientOptions}
                            </select>
                        </td>
                        <td>
                            <select class="form-select clientTypeOption">
                            ${typesOption}
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btnRemove btn-sm"><i class="fa fa-trash text-light"></i></button>
                        </td>
                    </tr>
                `;

                $("#tblClientGroupings tbody").append(tr);

                initializeSelect2();

            });

            //handle remove button click function
            $("#tblClientGroupings").on("click", "tbody tr .btnRemove", function(e) {
                e.preventDefault();

                $(this).closest("tr").remove();
            });
            
            //handle client select change function
            $("#tblClientGroupings").on("change", "tbody .clientOption", function(e) {
                e.preventDefault();

                let clientId = $(this).val();
                let rowId = $(this).closest("tr").index();

                // Update naming for select options
                $(this).attr('name', `client_group_tagging[${rowId}][client_id]`);
                $(this).closest("tr").find(".clientTypeOption").attr('name', `client_group_tagging[${rowId}][type]`);
            });
        });

        function initializeSelect2() {
            $(".clientOption").select2({
                placeholder : '-- Client --',
                // dropdownParent: '#modalMultipleEntries'
            });
        }
    </script>
@endpush