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

                            <div class="row align-items-center">
                                <div class="col">
                                    <a href="javascript:;" id="openModalButton" class="btn btn-primary mb-3 mb-lg-0"
                                        data-bs-toggle="modal" data-bs-target="#formModal"
                                        data-title="Add Proforma Invoice"><i class='bx bxs-plus-square'></i>New</a>
                                </div>
                                <div class="col">
                                    <select class="form-select select-top" id="_status" name="_status">
                                        <option value="All">All Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Approval">Approval</option>
                                        <option value="Approved">Approved</option>
                                        <option value="CIC Approval">CIC Approval</option>
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
                                        <th>Invoice No.</th>
                                        <th>Client</th>
                                        <th>Periode</th>
                                        <th>Grand Total</th>
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
    @include('invoice.modal')

    @include('invoice.modal-detail')

    @include('invoice.modal-edit')

    @include('invoice.modal-update')
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

        var invoiceId = '';
        window.invoiceState = {
            taxable: null,
            termofpayment: null
        };
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
                        data: 'invoice_no',
                        name: 'invoice_no',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'client',
                        name: 'client',
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
                        data: 'grand_total',
                        name: 'grand_total',
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
                            } else if (data == 'Approved') {
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
            gen_select2();

            // #client_vendor_id diinisialisasi ketika modal sudah tampil.
            // Ini mencegah konflik dengan select2-custom.js dan ukuran dropdown modal.
        });

        function initTopStatusSelect2() {
            $('.select-top').each(function() {
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

        function gen_select2() {
            $('.select-select')
                .not('#client_vendor_id')
                .each(function() {
                    const $el = $(this);

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
                        placeholder: $el.attr('id') === 'client_vendor_id' ? 'Choose Client' : '',
                        allowClear: $el.attr('id') === 'client_vendor_id'
                    }).on('select2:close', function() {
                        $(this).blur();

                        if (document.activeElement) {
                            document.activeElement.blur();
                        }
                    });

                });
        }

        function normalizeClientVendorResponse(response) {
            function findArray(value, depth = 0) {
                if (depth > 5 || value === null || value === undefined) {
                    return [];
                }

                if (Array.isArray(value)) {
                    return value;
                }

                if (typeof value !== 'object') {
                    return [];
                }

                const preferredKeys = [
                    'results', 'data', 'items', 'clients', 'vendors',
                    'client_vendors', 'clientVendors', 'rows'
                ];

                for (const key of preferredKeys) {
                    if (Object.prototype.hasOwnProperty.call(value, key)) {
                        const found = findArray(value[key], depth + 1);
                        if (found.length) {
                            return found;
                        }
                    }
                }

                for (const child of Object.values(value)) {
                    const found = findArray(child, depth + 1);
                    if (found.length) {
                        return found;
                    }
                }

                return [];
            }

            return findArray(response)
                .map(function(item) {
                    if (item === null || item === undefined) {
                        return null;
                    }

                    if (typeof item !== 'object') {
                        return {
                            id: String(item),
                            text: String(item)
                        };
                    }

                    const id = item.id ??
                        item.value ??
                        item.client_vendor_id ??
                        item.vendor_id ??
                        item.client_id ??
                        item.code;

                    const text = item.text ??
                        item.label ??
                        item.name ??
                        item.client_vendor ??
                        item.client_vendor_name ??
                        item.client_name ??
                        item.vendor_name ??
                        item.company_name ??
                        item.description;

                    if (id === null || id === undefined || text === null || text === undefined) {
                        console.warn('Baris get_client_vendor tidak dapat dipetakan:', item);
                        return null;
                    }

                    return {
                        id: String(id),
                        text: String(text),
                        raw: item
                    };
                })
                .filter(Boolean);
        }

        function initClientVendorSelect2(triggerTaxable = true) {
            const $modal = $('#formModal');
            const $client = $modal.find('select#client_vendor_id').first();

            if (!$client.length) {
                console.error('#client_vendor_id tidak ditemukan di dalam #formModal.');
                return;
            }

            if (typeof $.fn.select2 !== 'function') {
                console.error('Library Select2 belum termuat.');
                return;
            }

            let taxableTimer = null;

            // Pisahkan field ini dari initializer Select2 global.
            $client.removeClass('select-select');
            $client.off('.clientVendor');

            if ($client.hasClass('select2-hidden-accessible')) {
                $client.select2('destroy');
            }

            $client
                .prop('disabled', false)
                .removeAttr('disabled multiple data-close-on-select data-select-on-close')
                .prop('multiple', false)
                .removeData('closeOnSelect')
                .removeData('selectOnClose');

            $client.select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $modal,
                placeholder: 'Choose Client',
                allowClear: true,
                closeOnSelect: true,
                selectOnClose: false,
                minimumResultsForSearch: 0
            });

            $client.on('select2:open.clientVendor', function() {
                $('.select2-container--open').css('z-index', 1060);
            });

            $client.on('select2:select.clientVendor', function(event) {
                const selected = event.params?.data;
                const clientId = selected?.id ?? $client.val();

                if (!clientId || !triggerTaxable) {
                    return;
                }

                clearTimeout(taxableTimer);
                taxableTimer = setTimeout(function() {
                    if (typeof window.loadClientVendorTaxable === 'function') {
                        window.loadClientVendorTaxable(clientId);
                    } else if (typeof loadClientVendorTaxable === 'function') {
                        loadClientVendorTaxable(clientId);
                    } else {
                        console.warn('loadClientVendorTaxable() tidak ditemukan.');
                    }
                }, 100);
            });

            $client.on('select2:clear.clientVendor', function() {
                clearTimeout(taxableTimer);
                window.invoiceState = window.invoiceState || {};
                window.invoiceState.taxable = null;
                $('#check_tax').prop('checked', false);
            });
        }

        function loadClientVendorTaxable(clientId, invoiceTax) {
            if (!clientId) {
                window.invoiceState.taxable = null;
                $('#check_tax').prop('checked', false);
                $(document).trigger('invoice:taxableChanged');
                return;
            }

            let url = '{{ route('invoice.get_client_vendor_by_id', ':_id') }}';
            url = url.replace(':_id', clientId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    window.invoiceState.taxable = response.data.taxable;

                    $('#check_tax').prop(
                        'checked',
                        window.invoiceState.taxable === 'PKP'
                    );

                    const taxValue = parseFloat(invoiceTax) || 0;
                    if (taxValue === 0) {
                        window.poState.taxable = 'Non PKP';
                        $('#check_tax').prop('checked', false);
                    } else {
                        window.poState.taxable = 'PKP';
                        $('#check_tax').prop('checked', true);
                    }

                    $(document).trigger('po:taxableChanged');
                    initItemTableAfterAjax();
                },
                error: function(xhr, status, error) {
                    window.poState.taxable = null;
                    $('#check_tax').prop('checked', false);
                    $(document).trigger('invoice:taxableChanged');
                    console.error('Error get vendor taxable:', error);
                }
            });
        }

        function loadClientVendorSelect2(triggerTaxable = false) {
            const $modal = $('#formModal');
            const $client = $modal.find('select#client_vendor_id').first();
            const endpoint = @json(route('invoice.get_client_vendor'));

            if (!$client.length) {
                console.error('#client_vendor_id tidak ditemukan saat modal dibuka.');
                return $.Deferred().reject().promise();
            }

            // Cegah initializer global mengambil alih sebelum request selesai.
            $client.removeClass('select-select');

            if ($client.hasClass('select2-hidden-accessible')) {
                $client.select2('destroy');
            }

            $client
                .prop('disabled', true)
                .empty()
                .append(new Option('Loading client...', '', true, false));

            console.debug('Request get_client_vendor:', endpoint);

            return $.ajax({
                url: endpoint,
                method: 'GET',
                dataType: 'json',
                cache: false,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                data: {
                    term: '',
                    q: '',
                    search: '',
                    page: 1
                }
            }).done(function(response) {
                console.debug('Response get_client_vendor:', response);

                const items = normalizeClientVendorResponse(response);
                $client.empty().append(new Option('', '', false, false));

                items.forEach(function(item) {
                    const option = new Option(item.text, item.id, false, false);
                    $(option).data('raw', item.raw || item);
                    $client.append(option);
                });

                if (!items.length) {
                    console.error(
                        'Endpoint berhasil dipanggil, tetapi tidak ada data yang dapat dipetakan.',
                        response
                    );
                    $client.append(new Option('No client data found', '', false, false));
                }

                initClientVendorSelect2(triggerTaxable);
                $client.val(null).trigger('change.select2');

                console.debug('Jumlah client yang dimuat:', items.length);
            }).fail(function(xhr, status, error) {
                console.error('Request get_client_vendor gagal:', {
                    url: endpoint,
                    httpStatus: xhr.status,
                    status: status,
                    error: error,
                    contentType: xhr.getResponseHeader('Content-Type'),
                    response: xhr.responseText
                });

                $client
                    .empty()
                    .append(new Option('Failed to load client', '', false, false))
                    .prop('disabled', true);
            });
        }

        let loadTableTimer = null;

        async function loadInvoiceTable() {
            var year = $('#year').val();
            var month = $('#month').val();

            $('#div-table').html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="visually-hidden">Loading...</span>
            `);

            clearTimeout(loadTableTimer);

            loadTableTimer = setTimeout(function() {
                const isEdit = invoiceId != '';

                const url = isEdit ?
                    '{{ route('invoice.get_table_edit', ':_id') }}'.replace(':_id',
                        invoiceId) :
                    '{{ route('invoice.get_table_add') }}';

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        invoice_id: invoiceId,
                        year: year,
                        month: month
                    },
                    success: function(response) {
                        $('#div-table').html(response.html);

                        const titleText = isEdit ? 'Edit Invoice' : 'Add Invoice';
                        const number = isEdit ? response.invoice_no : response
                            .invoice_prev_no;

                        $('#modal-header').html(titleText + ' -&nbsp;<b>' + number + '</b>');
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

        $(document)
            .off('change.loadTable', '#month')
            .on('change.loadTable', '#month', function() {
                loadInvoiceTable();
            });

        $(document)
            .off('input.loadTable change.loadTable', '#year')
            .on('input.loadTable change.loadTable', '#year', function() {
                loadInvoiceTable();
            });

        $('#formModal').off('show.bs.modal.mainModal').on('show.bs.modal.mainModal', function(event) {
            var button = $('#openModalButton');
            var title = button.data('title');
            $('#modal-header').text(title);

            $("#div-table").html(`
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="visually">Loading...</span>
                    `);
            setTimeout(function() {
                const isEdit = invoiceId != '';
                const url = isEdit ?
                    '{{ route('invoice.get_table_edit', ':_id') }}'.replace(':_id',
                        invoiceId) :
                    '{{ route('invoice.get_table_add') }}';
                $.ajax({
                    url: url,
                    data: {
                        invoice_id: invoiceId
                    },
                    type: 'GET',
                    success: function(response) {
                        $("#div-table").html(response.html);

                        setTimeout(function() {
                            if (typeof window.initInvoiceItemTable ===
                                'function') {
                                window.initInvoiceItemTable();
                            }
                        }, 0);

                        const titleText = isEdit ? 'Edit Invoice' : 'Add Invoice';
                        const number = isEdit ? response.invoice_no : response
                            .invoice_prev_no;

                        $('#modal-header').html(titleText + ' -&nbsp;<b>' + number + '</b>');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });

                if (!isEdit) {
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
                                text: error,
                            });
                        }
                    });
                }
            }, 500);
        });

        $('#formModal').off('hidden.bs.modal.mainModal').on('hidden.bs.modal.mainModal', function() {
            const $client = $('#client_vendor_id');

            $client
                .val(null)
                .empty()
                .trigger('change.select2');

            window.invoiceState = window.invoiceState || {};
            window.invoiceState.taxable = null;
            $('#check_tax').prop('checked', false);
        });

        $('#formUpdate').off('hidden.bs.modal.mainModal').on('hidden.bs.modal.mainModal', function() {
            invoiceId = '';
            contractId = '';
        });

        $(document).off('click.editButton').on('click.editButton', '.editButton', function() {
            invoiceId = $(this).data('id');

            $('#modal-edit-header').text('Edit Proforma Invoice');
            $('#id').val(invoiceId);

            let url = '{{ route('invoice.show', ':_id') }}';
            url = url.replace(':_id', invoiceId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#modal-edit-header').html(
                        'Edit Proforma Invoice -&nbsp;<b>' + response.invoice_no + '</b>'
                    );
                    $("#client_vendor_id").val(response.data.client_vendor_id).trigger('change');
                    $("#date").val(response.data.date);
                    $('#div-table-edit').html(response.html);
                },
                error: function() {
                    alert('Error fetching data');
                }
            });
        });

        $(document).off('click.detailButton').on('click.detailButton', '.detailButton', function() {
            $('#modal-detail-header').text('Detail Invoice');
            $('#modal-detail-body').html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="visually-hidden">Loading...</span>
            `);
            let url = '{{ route('invoice.get_detail', ':_id') }}';
            url = url.replace(':_id', $(this).data('id'));

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response, textStatus, xhr) {
                    const encodedJson = xhr.getResponseHeader('X-Json-Data');
                    const jsonData = JSON.parse(atob(encodedJson));
                    $('#modal-detail-header').html(
                        'Detail Invoice -&nbsp;<b>' + jsonData.invoice_no + '</b>'
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

            let url = '{{ route('invoice.store') }}';

            formData.append('status', statusValue);

            if (invoiceId !== '') {
                url = '{{ route('invoice.update', ':_id') }}'
                    .replace(':_id', invoiceId);

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

                                invoiceId = '';

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

            if (statusValue === 'Done' || statusValue === 'CIC Approval') {
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

        $('#cancelUpdateButton').off('click.cancelUpdate').on('click.cancelUpdate', function() {
            $('#formUpdate').modal('hide');
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
                    let url = '{{ route('invoice.destroy', ':_id') }}';
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
                                    $('#table-data').DataTable().ajax.reload(null,
                                        false);
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

        $('#formModal').off('shown.bs.modal.select2Invoice').on('shown.bs.modal.select2Invoice', function() {
            loadClientVendorSelect2(false);
        });

        $(document).on('change', '#check_tax', function() {
            window.invoiceState = window.invoiceState || {};
            let isChecked = $(this).is(':checked');
            if (isChecked) {
                window.invoiceState.taxable = 'PKP';
            } else {
                window.invoiceState.taxable = 'Non PKP';
            }
            console.log('Taxable:', window.invoiceState.taxable);
            $(document).trigger('invoice:taxableChanged');
        });
    </script>
    <!--app JS-->
@endsection
