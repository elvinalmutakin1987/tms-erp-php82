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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Requisition"><i
                                            class='bx bxs-plus-square'></i>New</a>
                                </div>
                                <div class="col-2">
                                    <select class="form-select select-top" id="_status" name="_status">
                                        <option value="All">All Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Approval">Approval</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Received">Received</option>
                                        <option value="Cancel">Cancel</option>
                                        <option value="Done">Done</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select class="form-select select-top" id="_urgency" name="_urgency">
                                        <option value="All">All Urgency</option>
                                        <option value="P4">P4</option>
                                        <option value="P3">P3</option>
                                        <option value="P2">P2</option>
                                        <option value="P1">P1</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control datepicker" id="date_start" name="date_start"
                                        placeholder="Start Date">
                                </div>
                                <div class="col-2">
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
                            <table id="table-data" class="table" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Order Number</th>
                                        <th>Requisition Number</th>
                                        <th>Date</th>
                                        <th>Urgency </th>
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

    @include('purchase_order.modal')

    @include('purchase_order.modal-detail')

    @include('purchase_order.modal-monitoring')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const saveButton1 = document.getElementById('saveButton1');
        const saveButton2 = document.getElementById('saveButton2');
        const monitoringSaveButton1 = document.getElementById('monitoringSaveButton1');
        const monitoringSaveButton2 = document.getElementById('monitoringSaveButton2');

        var orderId = '';
        var requisitionId = '';

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
                "columns": [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '10px',
                        className: 'dt-center',
                        targets: '_all'
                    },
                    {
                        data: 'order_no',
                        name: 'order_no',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'requisition_no',
                        name: 'requisition_no',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'date',
                        name: 'date',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'urgency',
                        name: 'urgency',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == "P4") {
                                return '<span class="badge bg-success" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'P3') {
                                return '<span class="badge bg-primary" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'P2') {
                                return '<span class="badge bg-warning" style="font-size: 13px">' +
                                    data + '</span>';
                            } else {
                                return '<span class="badge bg-danger" style="font-size: 13px">' +
                                    data + '</span>';
                            }
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
                ],
            });

            $(document).on('click', '.editButton', function() {
                requisitionId = $(this).data('id');
                $('#modal-header').text('Edit Order');
                $('#id').val(orderId);
                let url = '{{ route('purchaseorder.show', ':_id') }}';
                url = url.replace(':_id', orderId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit Requisition');
                        $("#purchase_requisition_id").val(response.data.purchase_requisition_id)
                            .trigger(
                                'change');
                        $("#date").val(response.data.date);
                        $("#notes").val(response.data.notes);
                        $("#total").val(response.data.total);
                        $("#total_").val(numbro(response.data.total).format({
                            thousandSeparated: true,
                            mantissa: 0
                        }));
                        $("#tax").val(response.data.tax);
                        $("#tax_").val(numbro(response.data.tax).format({
                            thousandSeparated: true,
                            mantissa: 0
                        }));
                        $("#grand_total").val(response.data.grand_total);
                        $("#grand_total_").val(numbro(response.data.grand_total).format({
                            thousandSeparated: true,
                            mantissa: 0
                        }));
                        $("#urgency").val(response.data.urgency).trigger(
                            'change');
                        $("#request_token").val(response.data.request_token);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $(document).on('click', '.detailButton', function() {
                $('#modal-detail-header').text('Detail Order');
                let url = '{{ route('purchaseorder.get_detail', ':_id') }}';
                url = url.replace(':_id', $(this).data('id'));
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#modal-detail-body').html(response);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $(document).on('click', '.monitoringButton', function() {
                requisitionId = $(this).data('id');
                $('#modal-receive-header').text('Monitoring Order');
                $('#id').val(orderId);
                let url = '{{ route('purchaseorder.get_monitoring', ':_id') }}';
                url = url.replace(':_id', orderId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#divMonitoring').html(response);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
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

            $.ajax({
                url: '{{ route('purchaseorder.get_purchase_requisition') }}',
                type: 'GET',
                success: function(response) {
                    $('#purchase_requisition_id').empty();
                    $('#purchase_requisition_id').append(
                        '<option value="" selected disabled></option>');
                    $.each(response.data, function(index, purchase_requisition) {
                        $('#purchase_requisition_id').append('<option value="' +
                            purchase_requisition.id +
                            '">' +
                            purchase_requisition.requisition_no +
                            '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: error,
                    });
                }
            });

            gen_select2();
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
                    let url = '{{ route('purchaseorder.destroy', ':_id') }}';
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
                                text: errorMessage,
                            });
                        }
                    });
                }
            });
        }

        $('.saveButton').on('click', function() {
            disableButton();
            const status = $(this).val();
            const form = $('#formModal').find('form')[0];
            const formData = new FormData(form);

            formData.append('status', status);

            let url = '{{ route('purchaseorder.store') }}';
            let type = 'POST';

            if (orderId) {
                url = '{{ route('purchaseorder.update', ':_id') }}'.replace(':_id', orderId);
                formData.append('_method', 'PUT');
            }

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
                                orderId = '';
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
                    }
                });
            };

            if (status === 'Open') {
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    confirmButtonText: 'Yes, Save it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) submitForm();
                });
            } else {
                submitForm();
            }
        });

        $('.monitoringButton').on('click', function() {
            disableButton();
            const status = $(this).val();
            const formR = $('#formMonitoring').find('form')[0];
            const formData = new FormData(formR);

            formData.append('status', status);

            let url = '{{ route('purchaseorder.monitoring', ':_id') }}'.replace(':_id', orderId);
            let type = 'POST';

            formData.append('_method', 'PUT');

            const submitReceived = () => {
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
                                orderId = '';
                                $('#formMonitoring').modal('hide');
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
                    }
                });
            };

            if (status === 'Done') {
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    confirmButtonText: 'Yes, Save it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) submitReceived();
                });
            } else {
                submitReceived();
            }
        });

        $('#formModal').on('show.bs.modal', function() {
            var button = $('#openModalButton');
            var title = button.data('title');
            $('#formModal form')[0].reset();
            $('#modal-header').text(title);

            $("#div-table").html(`
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="visually">Loading...</span>
                    `);
            setTimeout(function() {
                const isEdit = orderId != '';
                const url = isEdit ?
                    '{{ route('purchaseorder.get_table_edit', ':_id') }}'.replace(':_id',
                        orderId) :
                    '{{ route('purchaseorder.get_table_add') }}';

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#div-table").html(response.html);

                        const titleText = isEdit ? 'Edit Order' : 'Add Order';
                        const number = isEdit ? response.order_no : response
                            .order_prev_no;

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

        $('#formModal').on('hidden.bs.modal', function() {
            orderId = '';
            requisitionId = '';
            enableButton();
            $("#request_token").val("");
            $('#div-table').html("");
            $("#purchase_requisition_id").val('').trigger('change');
            $("#total").val('');
            $("#total_").val('');
            $("#tax").val('');
            $("#tax_").val('');
            $("#grand_total").val('');
            $("#grand_total_").val('');
            enableButton();
        });

        $('#formMonitoring').on('hidden.bs.modal', function() {
            enableButton();
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').on('click', function() {
            $('#formDetail').modal('hide');
        });

        $('#cancelMonitoringButton').on('click', function() {
            $('#formMonitoring').modal('hide');
        });

        function gen_select2() {
            $('.select-select').each(function() {
                const $el = $(this);
                $el.select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#formModal'),
                    width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
                        'style'),
                    selectOnClose: false,
                    minimumResultsForSearch: 0,
                }).on('select2:close', function() {
                    $(this).blur();
                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                });
            });
        }

        function disableButton() {
            saveButton1.disabled = true;
            saveButton2.disabled = true;
            monitoringSaveButton1.disabled = true;
            monitoringSaveButton2.disabled = true;
        }

        function enableButton() {
            saveButton1.disabled = false;
            saveButton2.disabled = false;
            monitoringSaveButton1.disabled = false;
            monitoringSaveButton2.disabled = false;
        }

        $('#purchase_requisition_id').each(function() {
            const $el = $(this);
            $el.select2({
                theme: "bootstrap-5",
                dropdownParent: $('#formModal'),
                width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
                    'style'),
                selectOnClose: false,
                minimumResultsForSearch: 0,
            }).on('change', function() {
                $("#div-table").html(`
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="visually">Loading...</span>
                    `);
                const isEdit = orderId != '';
                const url = isEdit ?
                    '{{ route('purchaseorder.get_table_edit', ':_id') }}'.replace(':_id',
                        orderId) :
                    '{{ route('purchaseorder.get_table_add') }}';

                $.ajax({
                    url: url,
                    data: {
                        purchase_requisition_id: $(this).val()
                    },
                    type: 'GET',
                    success: function(response) {
                        $("#div-table").html(response.html);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
        });
    </script>
    <!--app JS-->
@endsection
