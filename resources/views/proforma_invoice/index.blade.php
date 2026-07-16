@extends('partials.main')

@section('css')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content container-xxl">

            @include('partials.breadcrum')

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center mb-2">
                                <div class="col">
                                    <a href="javascript:;" id="openModalButton" class="btn btn-primary mb-3 mb-lg-0"
                                        data-bs-toggle="modal" data-bs-target="#formModal"
                                        data-title="Add Proforma Invoice"><i class='bx bxs-plus-square'></i>New</a>
                                    <a href="javascript:;" id="openModalButton" class="btn btn-info mb-3 mb-lg-0"
                                        data-bs-toggle="modal" data-bs-target="#formBulkModal"
                                        data-title="Add Requisition"><i class='bx bx-list-plus'></i>Bulk Generate</a>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col">
                                    <select class="form-select select-top" id="unit" name="unit">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select select-top" id="_status" name="_status">
                                        <option value="All">All Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Approval">Approval</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Cancel">Cancel</option>
                                        <option value="Done">Done</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select select-top" id="_month" name="_month">
                                        <option value="All">All Month</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select select-top" id="_year" name="_year">
                                        <option value="All">All Year</option>
                                        @for ($year = date('Y'); $year >= 2010; $year--)
                                            <option value="{{ $year }}"
                                                {{ request('_year') == $year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="table-data" class="table table-striped table-bordered" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>PI Number</th>
                                        <th>Contract Number</th>
                                        <th>Unit</th>
                                        <th>Type</th>
                                        <th>Periode</th>
                                        <th>Total</th>
                                        {{-- <th>Penalty</th>
                                        <th>Grand Total</th> --}}
                                        <th>Status</th>
                                        <th width="20">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end page wrapper -->
    @include('proforma_invoice.modal', $contract)

    @include('proforma_invoice.modal-edit')

    @include('proforma_invoice.modal-detail')

    @include('proforma_invoice.modal-bulk')

    @include('proforma_invoice.modal-update')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const saveButton = document.getElementById('saveButton');
        const saveUpdateButton = document.getElementById('saveUpdateButton');

        var proformaInvoiceId = '';
        var contractId = '';
        var unitId = '';

        $(document).ready(function() {
            var ajax = '{{ url()->current() }}';

            $('#table-data').DataTable({
                scrollCollapse: true,
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajax,
                    data: function(d) {
                        d.status = $('#_status').val();
                        d.unit_id = $('#unit').val() || '';
                        d.month = $('#_month').val();
                        d.year = $('#_year').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '10px',
                        className: 'dt-center',
                        targets: '_all'
                    },
                    {
                        data: 'proforma_no',
                        name: 'proforma_no',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'contract_no',
                        name: 'contract_no',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'unit',
                        name: 'unit',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'periode_',
                        name: 'periode_',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'total',
                        name: 'total',
                        orderable: true,
                        searchable: true,
                        className: 'text-end',
                        render: function(data, type, row) {
                            return numbro(data ?? 0).format({
                                thousandSeparated: true,
                                mantissa: 0
                            });
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == "Done") {
                                return '<span class="badge bg-success" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'Approved' || data == 'Received') {
                                return '<span class="badge bg-warning" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'Open') {
                                return '<span class="badge bg-primary" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'Approval') {
                                return '<span class="badge bg-info" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'Cancel') {
                                return '<span class="badge bg-danger" style="font-size: 13px">' +
                                    data + '</span>';
                            } else {
                                return '<span class="badge bg-secondary" style="font-size: 13px">' +
                                    data + '</span>';
                            }
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '100px',
                        className: 'text-center',
                        targets: '_all'
                    }
                ]
            });

            $(".datepicker").flatpickr({
                allowInput: true
            });

            initTopStatusSelect2();
            initUnitTopSelect2();
            gen_select2();
        });

        function initTopStatusSelect2() {
            $('.select-top').not('#unit').each(function() {
                const $el = $(this);

                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }

                $el.select2({
                    theme: "bootstrap-5",
                    width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
                        'style')
                });

                $el.off('change.topFilter').on('change.topFilter', function() {
                    $('#table-data').DataTable().draw();
                });
            });
        }

        function initUnitTopSelect2() {
            const $unit = $('#unit');

            if (!$unit.length) {
                return;
            }

            if ($unit.hasClass('select2-hidden-accessible')) {
                $unit.select2('destroy');
            }

            $unit.off('.unitTop');

            $unit.select2({
                theme: "bootstrap-5",
                width: $unit.data('width') ? $unit.data('width') : ($unit.hasClass('w-100') ? '100%' : 'style'),
                placeholder: 'All Unit',
                allowClear: true,
                selectOnClose: false,
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('proformainvoice.get_unit_all') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    },
                    cache: true
                }
            });

            $unit.val(null).trigger('change.select2');

            $unit.on('select2:open.unitTop', function() {
                setTimeout(function() {
                    $('.select2-container--open .select2-search__field').trigger('focus');
                }, 0);
            });

            $unit.on('change.unitTop', function() {
                $('#table-data').DataTable().draw();
            });
        }

        function closeTopSelect2BeforeModal() {
            const topSelects = ['#unit', '#_status'];

            topSelects.forEach(function(selector) {
                const $el = $(selector);

                if ($el.length && $el.hasClass('select2-hidden-accessible')) {
                    $el.select2('close');
                }
            });
        }

        function gen_select2() {
            $('.select-select').each(function() {
                const $el = $(this);

                if ($el.attr('id') === 'unit_id') {
                    return;
                }

                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }

                $el.select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#formModal'),
                    width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
                        'style'),
                    selectOnClose: false,
                    minimumResultsForSearch: 0,
                    placeholder: $el.attr('id') === 'contract_id' ? 'Choose Contract' : '',
                    allowClear: $el.attr('id') === 'contract_id'
                }).on('select2:close', function() {
                    $(this).blur();

                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                });

                if ($el.attr('id') === 'contract_id') {
                    $el.val(null).trigger('change.select2');
                }
            });
        }

        function initUnitSelect2() {
            const $unit = $('#unit_id');

            if (!$unit.length) {
                return;
            }

            const selectedValue = $unit.val();

            if ($unit.hasClass('select2-hidden-accessible')) {
                $unit.select2('destroy');
            }

            $unit.off('.unitModal');

            $unit.select2({
                theme: "bootstrap-5",
                dropdownParent: $('#formModal'),
                width: $unit.data('width') ? $unit.data('width') : ($unit.hasClass('w-100') ? '100%' : 'style'),
                placeholder: 'Choose Unit',
                allowClear: true,
                selectOnClose: false,
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route('proformainvoice.get_unit_all') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    },
                    cache: true
                }
            });

            if (selectedValue) {
                $unit.val(selectedValue).trigger('change.select2');
            }

            $unit.on('select2:open.unitModal', function() {
                setTimeout(function() {
                    const search = document.querySelector(
                        '.select2-container--open .select2-search__field');

                    if (search) {
                        search.focus({
                            preventScroll: true
                        });
                    }

                    $('.select2-container--open').css('z-index', 1056);
                }, 0);
            });

            $unit.on('change.unitModal', function() {
                unitId = $(this).val();
            });
        }

        let loadTableTimer = null;

        async function loadProformaInvoiceTable() {
            var contractId = $('#contract_id').val();
            var year = $('#year').val();
            var month = $('#month').val();

            if (!contractId) {
                $('#div-table').html('');
                return false;
            }

            const isAvailable = await checkProformaInvoice();

            if (isAvailable) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Proforma Invoice on this periode already created!"
                });
                proformaInvoiceId = '';
                $('#div-table').html('');
                return false;
            }

            $('#div-table').html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="visually-hidden">Loading...</span>
            `);

            clearTimeout(loadTableTimer);

            loadTableTimer = setTimeout(function() {
                const isEdit = proformaInvoiceId != '';

                const url = isEdit ?
                    '{{ route('proformainvoice.get_table_edit', ':_id') }}'.replace(':_id',
                        proformaInvoiceId) :
                    '{{ route('proformainvoice.get_table_add') }}';

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        proforma_invoice_id: proformaInvoiceId,
                        contract_id: contractId,
                        year: year,
                        month: month
                    },
                    success: function(response) {
                        if (response.doc_status == 1) {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Proforma Invoice on this periode already created!"
                            });
                            proformaInvoiceId = '';
                            $('#div-table').html('');
                            return false;
                        }

                        $('#div-table').html(response.html);
                        gen_select_pallet();

                        $('#modal-header').html(
                            'Add Proforma Invoice -&nbsp;<b>' + response.proforma_prev_no +
                            '</b>'
                        );
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);

                        $('#div-table').html(`
                    <div class="alert alert-danger mb-0">
                        Failed to load data.
                    </div>
                `);
                    }
                });
            }, 500);
        }

        function checkProformaInvoice() {
            var contractId = $('#contract_id').val();
            var year = $('#year').val();
            var month = $('#month').val();
            return $.ajax({
                    url: '{{ route('proformainvoice.check_proforma_invoice') }}',
                    type: 'GET',
                    data: {
                        contract_id: contractId,
                        year: year,
                        month: month
                    }
                })
                .then(function(response) {
                    if (response.status == true) {
                        return true;
                    }
                    return false;
                })
                .catch(function(xhr) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: xhr.responseJSON?.message || 'Failed to check proforma invoice.'
                    });

                    return false;
                });
        }

        $(document)
            .off('change.loadTable', '#contract_id, #month')
            .on('change.loadTable', '#contract_id, #month', function() {
                loadProformaInvoiceTable();
            });

        $(document)
            .off('input.loadTable change.loadTable', '#year')
            .on('input.loadTable change.loadTable', '#year', function() {
                loadProformaInvoiceTable();
            });

        $('#formModal').off('show.bs.modal.mainModal').on('show.bs.modal.mainModal', function(event) {
            closeTopSelect2BeforeModal();

            var button = $(event.relatedTarget);
            var title = button.data('title') || 'Add Proforma Invoice';

            $('#formModal form')[0].reset();
            $('#modal-header').text(title);
            if (proformaInvoiceId === '') {
                $.ajax({
                    url: '{{ route('gen_request_token') }}',
                    type: 'GET',
                    success: function(response) {
                        $('#request_token').val(response.data);
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: error
                        });
                    }
                });
            }
        });

        $('#formModal').off('shown.bs.modal.select2Modal').on('shown.bs.modal.select2Modal', function() {
            gen_select2();
            initUnitSelect2();

            $('#contract_id').val(null).trigger('change.select2');
            $('#unit_id').val(null).trigger('change.select2');
            $('#div-table').html('');
        });

        $('#formModal').off('hidden.bs.modal.mainModal').on('hidden.bs.modal.mainModal', function() {
            proformaInvoiceId = '';
            contractId = '';
            unitId = '';

            $("#request_token").val("");
            $("#div-table").html("");

            $("#contract_id").val(null).trigger('change.select2');
            $("#unit_id").val(null).trigger('change.select2');

            closeTopSelect2BeforeModal();
        });

        $('#formUpdate').off('hidden.bs.modal.mainModal').on('hidden.bs.modal.mainModal', function() {
            proformaInvoiceId = '';
            contractId = '';
            unitId = '';
        });

        $(document).off('click.editButton').on('click.editButton', '.editButton', function() {
            proformaInvoiceId = $(this).data('id');

            $('#modal-edit-header').text('Edit Proforma Invoice');
            $('#id').val(proformaInvoiceId);

            let url = '{{ route('proformainvoice.show', ':_id') }}';
            url = url.replace(':_id', proformaInvoiceId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#modal-edit-header').html(
                        'Edit Proforma Invoice -&nbsp;<b>' + response.proforma_no + '</b>'
                    );
                    $("#edit_contract_id").val(response.contract_id);
                    $("#edit_contract_no").val(response.contract_no);
                    $("#edit_year").val(response.year);
                    $("#edit_month").val(response.month);
                    $("#edit_month_name").val(response.month_name);
                    $('#div-table-edit').html(response.html);
                },
                error: function() {
                    alert('Error fetching data');
                }
            });
        });

        $(document).off('click.detailButton').on('click.detailButton', '.detailButton', function() {
            $('#modal-detail-header').text('Detail Proforma Invoice');

            let url = '{{ route('proformainvoice.get_detail', ':_id') }}';
            url = url.replace(':_id', $(this).data('id'));

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response, textStatus, xhr) {
                    const encodedJson = xhr.getResponseHeader('X-Json-Data');
                    const jsonData = JSON.parse(atob(encodedJson));
                    $('#modal-detail-header').html(
                        'Detail Proforma Invoice -&nbsp;<b>' + jsonData.proforma_no + '</b>'
                    );
                    $('#modal-detail-body').html(response);
                },
                error: function() {
                    alert('Error fetching data');
                }
            });
        });

        $(document).off('click.updateButton').on('click.updateButton', '.updateButton', function() {
            proformaInvoiceId = $(this).data('id');

            $('#modal-update-header').text('Update Progress');
            $('#id').val(proformaInvoiceId);

            let url = '{{ route('proformainvoice.show', ':_id') }}';
            url = url.replace(':_id', proformaInvoiceId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#modal-update-header').html(
                        'Update Progress -&nbsp;<b>' + response.proforma_no + '</b>'
                    );
                    $("#update_contract_no").val(response.contract_no);
                    $("#update_unit").val(response.unit);
                    $("#cut_off_date").val(response.proforma_invoice.cut_off_date);
                    $("#consolidation_date").val(response.proforma_invoice.consolidation_date);
                    $("#progress_claim_date").val(response.proforma_invoice.progress_claim_date);
                    $("#ops_received_date").val(response.proforma_invoice.ops_received_date);
                    $("#prof_inv_app_date").val(response.proforma_invoice.prof_inv_app_date);
                    $("#cic_request_date").val(response.proforma_invoice.cic_request_date);
                },
                error: function() {
                    alert('Error fetching data');
                }
            });
        });

        $('.saveButton').off('click.saveProforma').on('click.saveProforma', function() {
            const statusValue = $(this).val();
            const formElement = $('#formModal').find('form')[0];
            const formData = new FormData(formElement);

            let url = '{{ route('proformainvoice.store') }}';

            formData.append('status', statusValue);

            if (proformaInvoiceId !== '') {
                url = '{{ route('proformainvoice.update', ':_id') }}'
                    .replace(':_id', proformaInvoiceId);

                formData.append('_method', 'PUT');
            }

            function submitProformaInvoice() {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        Swal.fire({
                            title: response.title,
                            text: response.message,
                            icon: 'success',
                            timer: 5000,

                            willClose: function() {
                                $('#table-data')
                                    .DataTable()
                                    .ajax
                                    .reload(null, false);

                                $('#formModal form')[0].reset();

                                proformaInvoiceId = '';
                                contractId = '';
                                unitId = '';

                                $('#formModal').modal('hide');
                            }
                        });
                    },

                    error: function(xhr, status, error) {
                        enableButton();

                        const errorMessage =
                            xhr.responseJSON?.message ?? error;

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                    }
                });
            }

            if (statusValue === 'Open') {
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    confirmButtonText: 'Yes, process it!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        submitProformaInvoice();
                    } else {
                        enableButton();
                    }
                });
            } else {
                submitProformaInvoice();
            }
        });

        $('.saveEditButton').off('click.editProforma').on('click.editProforma', function() {
            const statusValue = $(this).val();

            const formData = new FormData($('#formEdit').find('form')[0]);

            let url = '{{ route('proformainvoice.store') }}';

            formData.append('status', statusValue);

            if (proformaInvoiceId !== '') {
                url = '{{ route('proformainvoice.update', ':_id') }}'
                    .replace(':_id', proformaInvoiceId);

                formData.append('_method', 'PUT');
            }

            function submitProformaInvoice() {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        Swal.fire({
                            title: response.title,
                            text: response.message,
                            icon: 'success',
                            timer: 5000,

                            willClose: function() {
                                $('#table-data')
                                    .DataTable()
                                    .ajax
                                    .reload(null, false);

                                $('#formEdit form')[0].reset();

                                proformaInvoiceId = '';
                                contractId = '';
                                unitId = '';

                                $('#formEdit').modal('hide');
                            }
                        });
                    },

                    error: function(xhr, status, error) {
                        enableButton();

                        const errorMessage =
                            xhr.responseJSON?.message ?? error;

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                    }
                });
            }

            if (statusValue === 'Open') {
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    confirmButtonText: 'Yes, update it!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        submitProformaInvoice();
                    } else {
                        enableButton();
                    }
                });
            } else {
                submitProformaInvoice();
            }
        });

        $('.saveUpdateButton').off('click.updateProforma').on('click.updateProforma', function() {
            const statusValue = $(this).val();
            const formElement = $('#formUpdate').find('form')[0];
            const formData = new FormData(formElement);

            let url = '{{ route('proformainvoice.store') }}';

            formData.append('status', statusValue);

            if (proformaInvoiceId !== '') {
                url = '{{ route('proformainvoice.update_progress', ':_id') }}'
                    .replace(':_id', proformaInvoiceId);

                formData.append('_method', 'PUT');
            }

            function submitProformaProgress() {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,

                    success: function(response) {
                        Swal.fire({
                            title: response.title,
                            text: response.message,
                            icon: 'success',
                            timer: 5000,

                            willClose: function() {
                                $('#table-data')
                                    .DataTable()
                                    .ajax
                                    .reload(null, false);

                                $('#formUpdate form')[0].reset();

                                proformaInvoiceId = '';
                                contractId = '';
                                unitId = '';

                                $('#formUpdate').modal('hide');
                            }
                        });
                    },

                    error: function(xhr, status, error) {
                        enableButton();

                        const errorMessage =
                            xhr.responseJSON?.message ?? error;

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                    }
                });
            }

            if (statusValue === 'Done') {
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    confirmButtonText: 'Yes, process it!',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        submitProformaProgress();
                    } else {
                        enableButton();
                    }
                });
            } else {
                submitProformaProgress();
            }
        });

        $('#cancelButton').off('click.cancelModal').on('click.cancelModal', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').off('click.cancelDetail').on('click.cancelDetail', function() {
            $('#formDetail').modal('hide');
        });

        $('#cancelEditButton').off('click.cancelEdit').on('click.cancelEdit', function() {
            $('#formEdit').modal('hide');
        });

        $('#cancelUpdateButton').off('click.cancelUpdate').on('click.cancelUpdate', function() {
            $('#formUpdate').modal('hide');
        });

        $("#_year").on('change', function() {
            $('#table-data').DataTable().draw();
        });

        $("#_month").on('change', function() {
            $('#table-data').DataTable().draw();
        });

        function delete_(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5156be',
                cancelButtonColor: '#fd625e',
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '{{ route('proformainvoice.destroy', ':_id') }}';
                    url = url.replace(':_id', id);

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success",
                                timer: 5000,
                                didOpen: () => {},
                                willClose: () => {
                                    $('#table-data').DataTable().ajax.reload(null, false);
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : error;

                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        }

        function disableButton() {
            if (saveButton) {
                saveButton.disabled = true;
                saveUpdateButton.disabled = true;
            }
        }

        function enableButton() {
            if (saveButton) {
                saveButton.disabled = false;
                saveUpdateButton.disabled = false;
            }
        }

        function gen_select_pallet() {
            $('.select-pallet').each(function() {
                const $el = $(this);

                // Hindari Select2 diinisialisasi dua kali
                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }

                $el.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $('#formModal'),
                    width: $el.data('width') ?
                        $el.data('width') : ($el.hasClass('w-100') ? '100%' : 'style'),
                    selectOnClose: false,
                    minimumResultsForSearch: 0
                }).on('select2:close', function() {
                    $(this).blur();

                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                });
            });
        }
    </script>
    <!--app JS-->
@endsection
