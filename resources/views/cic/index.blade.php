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
                                    <a href="javascript:;" id="generateInvoiceButton" class="btn btn-success mb-3 mb-lg-0"
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Generate Invoice"><i
                                            class='bx bx-file'></i>Generate Invoice</a>
                                </div>
                                <div class="col">
                                    <select class="form-select select-top" id="_status" name="_status">
                                        <option value="All">All Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Approval">Approval</option>
                                        <option value="Approved">Approved</option>
                                        <option value="CIC Approval">CIC Approval</option>
                                        <option value="Invoicing">Invoicing</option>
                                        <option value="Cancel">Cancel</option>
                                        <option value="Done">Done</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control datepicker" id="date_start" name="date_start"
                                        placeholder="Start Date">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control datepicker" id="date_end" name="date_end"
                                        placeholder="End Date">
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
                                        <th>PI No.</th>
                                        <th>INV No.</th>
                                        <th>Contract No.</th>
                                        <th>CIC Number</th>
                                        <th>Type</th>
                                        <th>Periode</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th width="15%">Action</th>
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

    @include('cic.modal')

    @include('cic.modal-detail')

    @include('cic.modal-update')

    @include('cic.modal-create')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const saveButton = document.getElementById('saveButton');
        const saveCreateButton1 = document.getElementById('saveCreateButton1');
        const saveCreateButton2 = document.getElementById('saveCreateButton2');
        const saveUpdateButton1 = document.getElementById('saveUpdateButton1');
        const saveUpdateButton2 = document.getElementById('saveUpdateButton2');
        // const saveUpdateButton3 = document.getElementById('saveUpdateButton3');

        var proformaInvoiceId = '';
        var contractId = '';
        var unitId = '';

        $(document).ready(function() {
            var ajax = '{{ url()->current() }}';
            var table = $('#table-data').DataTable({
                scrollCollapse: true,
                responsive: true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: ajax,
                    data: function(d) {
                        d.status = $('#_status').val();
                        d.date_start = $('#date_start').val();
                        d.date_end = $('#date_end').val();
                        d.urgency = $('#_urgency').val();
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
                        data: 'invoice_no',
                        name: 'invoice_no',
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
                        data: 'cic_number',
                        name: 'cic_number',
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
                            } else if (data == 'Invoicing') {
                                return '<span class="badge bg-dark" style="font-size: 13px">' +
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

            $(".datepicker").flatpickr();

            $(".select-top").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
                $('#table-data').DataTable().draw();
            });

            $("#date_start").on('change', function() {
                $('#table-data').DataTable().draw();
            });

            $("#date_end").on('change', function() {
                $('#table-data').DataTable().draw();
            });

            gen_select2();
        });

        $('#generateInvoiceButton').on('click', function() {
            loadGenerateInvoice();
        });

        $('.saveButton').on('click', function() {
            disableButton();
            const status = $(this).val();
            const form = $('#formModal').find('form')[0];
            const formData = new FormData(form);
            formData.append('status', status);
            let type = 'POST';
            url = '{{ route('cic.store_generate_invoice') }}';
            formData.append('_method', 'POST');
            const submitForm = () => {
                $.ajax({
                    url,
                    type,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            title: response.title,
                            text: response.message,
                            icon: 'success',
                            timer: 5000,
                            willClose: () => {
                                $('#table-data').DataTable().ajax.reload(null, false);
                                form.reset();
                                $('#formModal').modal('hide');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        const errorMessage = xhr.responseJSON?.message || error;
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage,
                        });
                        enableButton();
                    }
                });
            };

            if (status === 'Unpaid') {
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    confirmButtonText: 'Yes, Save it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    result.isConfirmed ? submitForm() : enableButton();
                });
            } else {
                submitForm();
            }
        });

        $('#formModal').on('show.bs.modal', function() {
            var button = $('#openModalButton');
            var title = button.data('title');
            $('#formModal form')[0].reset();
            $('#modal-header').text("Generate Invoice");
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
        });

        $('#formModal').on('hidden.bs.modal', function() {
            proformaInvoiceId = '';
            const currentMonth = new Date().getMonth() + 1;
            $('#month').val(currentMonth).trigger('change');
            $('#div-table').html('');
            enableButton();
        });

        $('#formCreate').on('hidden.bs.modal', function() {
            proformaInvoiceId = '';
            contractId = '';
            unitId = '';
            enableButton();
        });

        $('#formUpdate').on('hidden.bs.modal', function() {
            proformaInvoiceId = '';
            contractId = '';
            unitId = '';
            enableButton();
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').off('click.cancelDetail').on('click.cancelDetail', function() {
            $('#formDetail').modal('hide');
        });

        $('#cancelUpdateButton').off('click.cancelUpdate').on('click.cancelUpdate', function() {
            $('#formUpdate').modal('hide');
        });

        $('#cancelCreateButton').off('click.cancelCreate').on('click.cancelCreate', function() {
            $('#formCreate').modal('hide');
        });

        $(document)
            .off('change.loadTable', '#month')
            .on('change.loadTable', '#month', function() {
                loadGenerateInvoice();
            });

        $(document)
            .off('input.loadTable change.loadTable', '#year')
            .on('input.loadTable change.loadTable', '#year', function() {
                loadGenerateInvoice();
            });

        $(document).off('click.detailButton').on('click.detailButton', '.detailButton', function() {
            $('#modal-detail-header').text('Detail Proforma Invoice');

            let url = '{{ route('cic.get_detail', ':_id') }}';
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

            let url = '{{ route('cic.show', ':_id') }}';
            url = url.replace(':_id', proformaInvoiceId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $('#modal-update-header').html(
                        'Update Progress -&nbsp;<b>' + response.proforma_no + '</b>'
                    );
                    $("#update_contract_no").val(response.contract_no);
                    $("#invoice_no").val(response.invoice_no);
                    $("#cut_off_date").val(response.proforma_invoice.cut_off_date);
                    $("#consolidation_date").val(response.proforma_invoice.consolidation_date);
                    $("#progress_claim_date").val(response.proforma_invoice.progress_claim_date);
                    $("#ops_received_date").val(response.proforma_invoice.ops_received_date);
                    $("#prof_inv_app_date").val(response.proforma_invoice.prof_inv_app_date);
                    $("#cic_request_date").val(response.proforma_invoice.cic_request_date);
                    $("#cic_created_date").val(response.proforma_invoice.cic_created_date);
                    $("#cic_received_date").val(response.proforma_invoice.cic_received_date);
                    $("#inv_date").val(response.proforma_invoice.inv_date);
                    $("#inv_create_date").val(response.proforma_invoice.inv_create_date);
                    $("#cic_send_date").val(response.proforma_invoice.cic_send_date);
                    $("#cic_ready_to_pick_date").val(response.proforma_invoice.cic_ready_to_pick_date);
                    $("#cic_pick_up_date").val(response.proforma_invoice.cic_pick_up_date);
                    $("#inv_send_date").val(response.proforma_invoice.inv_send_date);
                    $("#cic_number").val(response.proforma_invoice.cic_number);
                    $("#div-file").html(response.html);
                },
                error: function() {
                    alert('Error fetching data');
                }
            });
        });

        $('.saveUpdateButton').off('click.updateProforma').on('click.updateProforma', function() {
            const statusValue = $(this).val();
            const formElement = $('#formUpdate').find('form')[0];
            const formData = new FormData(formElement);

            let url = '{{ route('cic.store') }}';

            formData.append('status', statusValue);

            if (proformaInvoiceId !== '') {
                url = '{{ route('cic.update', ':_id') }}'
                    .replace(':_id', proformaInvoiceId);

                formData.append('_method', 'PUT');
            }

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
        });

        function gen_select2() {
            $('.select-select').each(function() {
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
                    placeholder: $el.attr('id') === 'month' ? 'Choose Month' : '',
                    allowClear: $el.attr('id') === 'month'
                }).on('select2:close', function() {
                    $(this).blur();

                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                });
            });
        }

        function create_invoice(id) {
            Swal.fire({
                title: 'Create Invoice',
                text: 'Choose Save or Draft.',
                icon: 'info',

                showDenyButton: true,
                showCancelButton: true,

                confirmButtonColor: '#198754',
                denyButtonColor: '#f1b44c',
                cancelButtonColor: '#fd625e',

                confirmButtonText: 'Save',
                denyButtonText: 'Draft',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                let submitType = null;

                if (result.isConfirmed) {
                    submitType = 'Open';
                } else if (result.isDenied) {
                    submitType = 'Draft';
                } else {
                    return;
                }

                let url = '{{ route('cic.create_invoice', ':_id') }}';
                url = url.replace(':_id', id);

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        id: id,
                        status: submitType,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: submitType === 'Draft' ?
                                'Invoice Draft Saved!' : 'Invoice Saved!',
                            text: response.message,
                            icon: 'success',
                            timer: 5000,
                            willClose: () => {
                                $('#table-data')
                                    .DataTable()
                                    .ajax
                                    .reload(null, false);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        const errorMessage = xhr.responseJSON?.message ?? error;

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                    }
                });
            });
        }

        let loadTableTimer = null;

        async function loadGenerateInvoice() {
            var year = $('#year').val();
            var month = $('#month').val();

            if (year === '' || month === '') {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Choose year & month!"
                });
                $('#div-table').html('');
                return false;
            }

            $('#div-table').html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="visually-hidden">Loading...</span>
            `);

            clearTimeout(loadTableTimer);

            loadTableTimer = setTimeout(function() {
                const url = '{{ route('cic.generate_invoice') }}';
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        year: year,
                        month: month
                    },
                    success: function(response) {
                        $('#div-table').html(response.html);

                        $('#modal-header').html('Generate Invoice');
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

        function delete_file(id) {
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
                    let url = '{{ route('cic.destroy_file', ':_id') }}';
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
                                    $('#div-file').html("");
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : error;
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: errorMessage,
                            });
                        }
                    });
                }
            });
        }

        function disableButton() {
            saveCreateButton1.disabled = true;
            saveCreateButton2.disabled = true;

            saveUpdateButton1.disabled = true;
            saveUpdateButton2.disabled = true;
            // saveUpdateButton3.disabled = true;
        }

        function enableButton() {
            saveCreateButton1.disabled = false;
            saveCreateButton2.disabled = false;

            saveUpdateButton1.disabled = false;
            saveUpdateButton2.disabled = false;
            // saveUpdateButton3.disabled = false;
        }
    </script>
    <!--app JS-->
@endsection
