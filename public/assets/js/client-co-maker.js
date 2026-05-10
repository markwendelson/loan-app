$(function () {
    const routeIs = window.location.pathname;

    const table = $('#comaker_table').DataTable({
        dom: 't',
        paging: false,
        info: false,
        ordering: false,
        processing: true,
        serverSide: true,
        searching: true,
        deferLoading: 0, // Prevent initial load
        ajax: {
            url: '/ajax/clients',
            data: function (d) {
                // Only load if there's a search value
                var searchValue = $('#comaker_search').val();
                if (!searchValue || searchValue.length === 0) {
                    $(".comaker-search-section .table-responsive").addClass("d-none");

                    return false; // Cancel the request
                }

                var $borrower = $('#borrower_id');
                // Check element exists AND has value
                if ($borrower.length && $borrower.val()) {
                    d.borrower_id = $borrower.val();
                }

                $(".comaker-search-section .table-responsive").removeClass("d-none");
            }
        },
        columns: [
            {
                data: 'id',
                name: 'id',
                render: function (data, type, row) {
                    let badgeClass = row.status == 1 ? 'badge-success' : 'badge-danger';
                    let statusLabel = row.status == 1 ? 'Active' : 'Inactive'; // optional label
                    let client_code = row.client_code;
                    let client_name = row.details.fullname;
                    let client_address = row.details.present_full_address;

                    return `
                        <div class="d-flex gap-2 justify-content-between">
                            <div>
                                <div><strong>${client_code}</strong></div>
                                <div>${client_name}</div>
                                <div>${client_address}</div>
                                <div><span class="badge ${badgeClass}">${statusLabel}</span></div>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary action-button register-comaker" data-id="${row.id}" data-name="${client_name}" data-address="${client_address}">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                        `;
                }
            },
        ],

    });

    $('#comaker_search').on('keyup', function () {
        const searchValue = this.value;

        // Show table only if there's search text
        if (searchValue.length > 0) {
            $('#comaker_table_wrapper').show();
            table.search(searchValue).draw();
        } else {
            $('#comaker_table_wrapper').hide();
            table.search('').draw();
        }
    });

    $('#clear_comaker_search').on('click', function() {
        $('#comaker_search').val('');
        $('#comaker_table_wrapper').hide();
        table.search('').draw();
    });

    table.on('click', '.register-comaker', function () {
        const id = 'tmp_' + $(this).data('id');
        const borrower_id = $("#client_id").val() || $('#borrower_id').val();
        const loan_id = $("#principal_loan_id").val() || '';
        const name = $(this).data('name');
        const address = $(this).data('address');

        if ($(`#client_comaker tbody tr[data-id="${id}"]`).length === 0) {
            let newRow = '';
            if (routeIs.includes('/loan/create')) {
                newRow = `
                <tr data-id="${id}" data-client-id="${borrower_id}" data-loan-id="${loan_id}">
                    <td><input type="hidden" name="client_comaker[]" value="${id}">${name}</td>
                    <td>${address}</td>
                    <td class="text-center" width="10">
                        <button class="btn btn-sm btn-danger btn-remove" data-id="${id}" data-client-id="${borrower_id}" data-loan-id="${loan_id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
            } else {
                newRow = `
                <tr data-id="${id}" data-client-id="${borrower_id}" data-loan-id="${loan_id}">
                    <td><input type="hidden" name="client_comaker[]" value="${id}">${name}</td>
                    <td>${address}</td>
                    <td></td>
                    <td class="text-center" width="10">
                        <button class="btn btn-sm btn-danger btn-remove" data-id="${id}" data-client-id="${borrower_id}" data-loan-id="${loan_id}">
                            <i class="fa fa-trash"></i>
                        </button>
                        <button class="btn btn-sm btn-success btn-save-co-maker" data-id="${id}">
                            <i class="fa fa-check"></i>
                        </button>
                    </td>
                </tr>
                `;
            }

            $('#client_comaker tbody').append(newRow);
        } else {
            showToast.error('Client already added.');
        }
    });

    $('#client_comaker').on('click', '.btn-remove', function () {
        const rowId = $(this).data('id');
        const clientId = $(this).data('client-id');
        const loanId = $(this).data('loan-id');

        const $row = $(`#client_comaker tbody tr[data-id="${rowId}"][data-client-id="${clientId}"][data-loan-id="${loanId}"]`);
        if (String(rowId).includes('tmp_')) {
            console.log($row)
            $row.remove();
            return;
        }

        swal({
            title: 'Remove Co-Maker?',
            text: 'Are you sure you want to remove this co-maker?',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((result) => {
            if (result) {
                $.ajax({
                    url: '/ajax/co-maker',
                    method: 'DELETE',
                    data: {
                        co_maker_id: clientId,
                        loan_id: loanId,
                        client_id: rowId,
                        _token: $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function (response) {
                        $row.remove();
                        showToast.success(response.message);
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message ?? 'Failed to remove co-maker.';
                        showToast.error(msg);
                    }
                });
            }
        });
    });

    $('#client_comaker').on('click', '.btn-save-co-maker', function () {
        const $btn = $(this);
        const $row = $btn.closest('tr');
        const rawId = $btn.data('id');
        const coMakerId = String(rawId).replace('tmp_', '');
        const borrowerId = $('#client_id').val();
        const loanId = $('#principal_loan_id').val();

        $.ajax({
            url: '/ajax/co-maker',
            method: 'POST',
            data: {
                co_maker_id: coMakerId,
                borrower_id: borrowerId,
                loan_id: loanId,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                $btn.remove(); // remove save button
                $row.attr('data-id', coMakerId); // strip tmp_ from row id
                showToast.success(response.message);
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message ?? 'Failed to save co-maker.';
                showToast.error(msg);
            }
        });
    })
})