@extends('layouts.app')

@push('css')
<style>
    .bg-default {
         background-color: #1f807f;
     }
     .bg-default th {
         color: white; /* Set white text for <th> elements */
         border: 1px solid white;
     }

    ul#repaymentList {
      list-style: none;
      padding: 0;
    }

    #repaymentList > li {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 5px 0;
      border: 1px solid #ccc;
      padding: 8px;
      border-radius: 6px;
      font-family: sans-serif;
    }

    #repaymentList > .buttons {
      display: flex;
      gap: 5px;
    }

    #repaymentList > button {
      font-size: 16px;
      padding: 4px 8px;
      cursor: pointer;
    }
  </style>
@endpush

@section('content')

    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h4>Loan Product | Manage Loan Product</h4>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"> <a href="{{url('')}}">
                            <svg class="stroke-icon">
                                <use href="{{url('assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                            </svg></a>
                        </li>
                        <li class="breadcrumb-item">Loan Product</li>
                        <li class="breadcrumb-item active">Manage Loan Product</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- button container -->
    <div class="card">
    <div class="card-body">
        <button class="btn btn-primary float-end btn-new"><i class = "fa fa-plus"></i> New Loan Product</button>
    </div>
    </div>


    <!-- table container -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id = "tblLoanProduct">
                    <thead class = "bg-default">
                        <tr>
                            <th class = "text-center" width = "120">No.</th>
                            <th class = "text-leftr" width = "150">Loan Type</th>
                            <th class = "text-leftr" width = "150">Loan Product</th>
                            <th>Description</th>
                            {{-- <th class = "text-center" width = "180">Amount</th> --}}
                            <th class = "text-center" width = "180">Status</th>
                            <th class = "text-center" width = "150"><i class = "fa fa-cog"></i></th> <!-- New column for Edit/Delete buttons -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('loan.product.modal')

@endsection

@push('js')
<script>
    $(document).ready(function () {
        const canEdit = {{ auth()->user()->can('edit-loan-product') ? 1 : 0 }};
        const canDelete = {{ auth()->user()->can('delete-loan-product') ? 1 : 0 }};

        const table = $('#tblLoanProduct').DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            stateSave: true,
            searching: true,
            ajax: {
                url: "{{ route('loan-product.index') }}",
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'loan_type.name', name: 'loan_type' },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'status', name: 'status', render: function(data, type, row) {
                    if (data == 1) {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-danger">Inactive</span>';
                    }
                } },
                {
                    data: null,
                    name: 'actions',
                    render: function(data, type, row) {
                        let buttons = '';
                        if (canEdit) {
                            buttons += `<button class="btn btn-primary btn-xs btnEdit" data-id="${row.id}"><i class="fa fa-pencil"></i></button> `;
                        }
                        if (canDelete) {
                            buttons += `<button class="btn btn-danger btn-xs" onclick="deleteData(${row.id})"><i class="fa fa-trash"></i></button>`;
                        }
                        return buttons;
                    }
                }
            ],
            columnDefs: [
                { className: "text-center", targets: [3,4] },
            ]
        });

        $(".btn-new").on("click", function() {
            showModal('modalLoanProduct')
            loadPrerequisites();
            renderList()
        })

        $("#modalLoanProduct .btnSubmit").click(function (e) {
            e.preventDefault();

            let form = $("#frmLoanProductModal")[0];

            if (!form.checkValidity()) {
                form.reportValidity();
            } else {

                //show loading screen
                $(".loader-wrapper").fadeIn("slow", function () {
                    $(this).show();
                });

                let formData = $("#frmLoanProductModal").serializeArray();

                formData.push({
                    name: "repayment_order",
                    value: JSON.stringify(items)
                });

                var id = $("#modalLoanProduct [name='id']").val();
                if(id) {
                    _method = 'PUT';
                    _url = `{{route('loan-product.update', ':id')}}`.replace(':id',id);
                } else {
                    _method = 'POST';
                    _url = `{{route('loan-product.store')}}`;
                }


                $.ajax({
                    method: _method,
                    url: _url,
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        if(response.status == 'success'){
                            hideModal('modalLoanProduct');
                            showToast.success(response.message)
                            table.ajax.reload();
                        }else{
                            showToast.error(response.message)
                        }
                    },
                    error: function (response) {
                        //hide loading screen
                        $(".loader-wrapper").fadeOut("slow", function () {
                            $(this).hide();
                        });
                        showToast.error(response.responseJSON.message)
                    }
                });
            }
        });

        $("#tblLoanProduct").on("click", ".btnEdit", function(){
            let id = $(this).attr("data-id");
            getLoanProduct(id)
            loadPrerequisites(id)
        })

        async function getLoanProduct(id) {
            try {
                let response = await fetch(`/loan-product/${id}`, {
                    method: "GET",
                });

                if (!response.ok) throw new Error("Network response was not ok");

                let data = await response.json();

                $("#modalLoanProduct [name='id']").val(data.id);
                $("#modalLoanProduct [name='name']").val(data.name);
                $("#modalLoanProduct [name='description']").val(data.description);
                $("#modalLoanProduct [name='loan_type_id']").val(data.loan_type_id);
                $("#modalLoanProduct [name='status']").val(data.status);
                showModal('modalLoanProduct')

                // Extract prerequisite IDs
                const selectedIds = data.prerequisite.map(p => p.id);

                // Uncheck all checkboxes first (optional reset)
                document.querySelectorAll('input[name^="prerequisites["]').forEach(cb => {
                    cb.checked = false;
                });

                // Check boxes that match the selected IDs
                selectedIds.forEach(id => {
                    const checkbox = document.getElementById(`prerequisites${id}`);
                    if (checkbox) checkbox.checked = true;
                });

                // Render repayment order

                const repaymentRaw = data.repayment_order;

                // Check if it's a string (e.g. "\"Principal\",\"Interest\"...")
                const repaymentOrder = typeof repaymentRaw === 'string'
                ? JSON.parse(repaymentRaw)
                : repaymentRaw; // already an array if Laravel is casting it

                const repaymentList = document.getElementById('repaymentList');
                repaymentList.innerHTML = ''; // clear list

                repaymentOrder.forEach((text, index) => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        ${text}
                        <div class="buttons">
                            <button type="button" class="btn btn-outline-primary btn-xs" onclick="moveUp(${index})">↑</button>
                            <button type="button" class="btn btn-outline-primary btn-xs" onclick="moveDown(${index})">↓</button>
                        </div>
                    `;
                    repaymentList.appendChild(li);
                });

            } catch (error) {
                console.error("Error fetching data:", error);
            }
        }

        function loadPrerequisites(id) {
            const loanProducts = @json($loan_products);

            const container = document.getElementById('prerequisitesContainer');
            container.innerHTML = '';
                        
            loanProducts.forEach(product => {
                if (product.id === parseInt(id)) return; // Skip if matched

                const div = document.createElement('div');
                div.innerHTML = `
                <input type="checkbox" name="prerequisites[${product.id}]" id="prerequisites${product.id}">
                <label for="prerequisites${product.id}">${product.name}</label>
                `;
                container.appendChild(div);
            });
        }
    });


    // function showModal(){
    //     $("#modalLoanProduct").modal("show");
    // }

    // function hideModal(){
    //     clearFormInputs();
    //     $("#modalLoanProduct").modal("hide");
    // }

    // function clearFormInputs() {
    //     $("#modalLoanProduct [name='id']").val("");
    //     $("#modalLoanProduct [name='name']").val("");
    //     $("#modalLoanProduct [name='description']").val("");
    //     $("#modalLoanProduct [name='status']").val("");
    // }

    function deleteData(id) {
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {

                $.ajax({
                    url: '/loan-product/' + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        showToast.success(response.message)
                        $('#tblLoanProduct').DataTable().ajax.reload();
                    }
                });

            }
        })
    }

    // Initial data array
    let initialItems = @json($repayment_order);
    let items = [...initialItems];

    // Render function
    function renderList() {
        const repaymentList = document.getElementById('repaymentList');
        repaymentList.innerHTML = ''; // Clear the list
        items.forEach((text, index) => {
        const li = document.createElement('li');
        li.innerHTML = `
            ${text}
            <div class="buttons">
                <button type="button" class="btn btn-outline-primary btn-xs" onclick="moveUp(${index})">↑</button>
                <button type="button" class="btn btn-outline-primary btn-xs" onclick="moveDown(${index})">↓</button>
            </div>
        `;
        repaymentList.appendChild(li);
        });
    }

    function moveUp(index) {
        if (index > 0) {
        [items[index - 1], items[index]] = [items[index], items[index - 1]];
        renderList();
        }
    }

    function moveDown(index) {
        if (index < items.length - 1) {
        [items[index + 1], items[index]] = [items[index], items[index + 1]];
        renderList();
        }
    }

</script>
@endpush
