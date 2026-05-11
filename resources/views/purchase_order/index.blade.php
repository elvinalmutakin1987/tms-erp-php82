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
                                        <option value="All">All Abbreviation</option>
                                        <option value="P5">LP</option>
                                        <option value="P4">MP</option>
                                        <option value="P3">HP</option>
                                        <option value="P2">U</option>
                                        <option value="P1">TU</option>
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
                                        <th>Vendor</th>
                                        <th>Date</th>
                                        <th>Grand Total</th>
                                        <th>Abbreviation </th>
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
        const monitoringSaveButton = document.getElementById('monitoringSaveButton');

        var orderId = '';
        var requisitionId = '';
        window.poState = {
            taxable: null
        };
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
                        data: 'vendor',
                        name: 'vendor',
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
                        data: 'urgency',
                        name: 'urgency',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == "P5") {
                                return '<span class="badge bg-success" style="font-size: 13px">LP</span>';
                            } else if (data == "P4") {
                                return '<span class="badge bg-info" style="font-size: 13px">MP</span>';
                            } else if (data == 'P3') {
                                return '<span class="badge bg-primary" style="font-size: 13px">HP</span>';
                            } else if (data == 'P2') {
                                return '<span class="badge bg-warning" style="font-size: 13px">U</span>';
                            } else {
                                return '<span class="badge bg-danger" style="font-size: 13px">TU</span>';
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
                orderId = $(this).data('id');
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

                        $("#purchase_requisition_id")
                            .val(response.data.purchase_requisition_id)
                            .trigger('change.select2');


                        const vendorId = response.data.client_vendor_id;
                        const vendorText = response.vendor ? response.vendor.name :
                            `Vendor #${vendorId}`;

                        const $vendor = $("#client_vendor_id");

                        initClientVendorSelect2(false);

                        if (vendorId) {
                            const optionExists = $vendor.find('option').filter(function() {
                                return String(this.value) === String(vendorId);
                            }).length > 0;

                            if (!optionExists) {
                                const newOption = new Option(vendorText, vendorId, true, true);
                                $vendor.append(newOption);
                            }

                            $vendor.val(vendorId).trigger('change.select2');

                            loadClientVendorTaxable(vendorId);
                        }

                        $("#date").val(response.data.date);
                        $("#notes").val(response.data.notes);

                        $("#urgency")
                            .val(response.data.urgency)
                            .trigger('change');

                        $("#request_token").val(response.data.request_token);

                        $("#div-file").html(response.html);

                        requisitionId = response.data.purchase_requisition_id;
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

            // $(document).on('click', '.monitoringButton', function() {
            //     requisitionId = $(this).data('id');
            //     $('#modal-receive-header').text('Monitoring Order');
            //     $('#id').val(orderId);
            //     let url = '{{ route('purchaseorder.get_monitoring', ':_id') }}';
            //     url = url.replace(':_id', orderId);
            //     $.ajax({
            //         url: url,
            //         type: 'GET',
            //         success: function(response) {
            //             $('#divMonitoring').html(response);
            //         },
            //         error: function() {
            //             alert('Error fetching data');
            //         }
            //     });
            // });

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

            // $.ajax({
            //     url: '{{ route('purchaseorder.get_purchase_requisition') }}',
            //     type: 'GET',
            //     success: function(response) {
            //         $('#purchase_requisition_id').empty();
            //         $('#purchase_requisition_id').append(
            //             '<option value="">Direct PO</option>');
            //         $.each(response.data, function(index, purchase_requisition) {
            //             $('#purchase_requisition_id').append('<option value="' +
            //                 purchase_requisition.id +
            //                 '">' +
            //                 purchase_requisition.requisition_no +
            //                 '</option>');
            //         });
            //     },
            //     error: function(xhr, status, error) {
            //         Swal.fire({
            //             icon: "error",
            //             title: "Oops...",
            //             text: error,
            //         });
            //     }
            // });

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

        function close_(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5156be',
                cancelButtonColor: '#fd625e',
                confirmButtonText: 'Yes, Close it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '{{ route('purchaseorder.close', ':_id') }}';
                    url = url.replace(':_id', id);
                    $.ajax({
                        url: url,
                        type: 'PUT',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Closed!",
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
                        enableButton();
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
                    // if (result.isConfirmed) submitForm();
                    result.isConfirmed ? submitForm() : enableButton();
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
                    // if (result.isConfirmed) submitReceived();
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
                    data: {
                        purchase_requisition_id: requisitionId
                    },
                    type: 'GET',
                    success: function(response) {
                        $("#div-table").html(response.html);

                        setTimeout(function() {
                            if (typeof window.initPurchaseOrderItemTable ===
                                'function') {
                                window.initPurchaseOrderItemTable();
                            }
                        }, 0);

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
            window.poState.taxable = null;
        });

        $('#formMonitoring').on('hidden.bs.modal', function() {
            enableButton();
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').on('click', function() {
            $('#formDetail').modal('hide');
            $('#modal-detail-body').html("");
        });

        $('#cancelMonitoringButton').on('click', function() {
            $('#formMonitoring').modal('hide');
            $('#divReceive').html("");
        });

        function gen_select2() {
            $('.select-select')
                .not('#purchase_requisition_id, #client_vendor_id')
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
            monitoringSaveButton.disabled = true;
        }

        function enableButton() {
            saveButton1.disabled = false;
            saveButton2.disabled = false;
            monitoringSaveButton.disabled = false;
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
                    let url = '{{ route('purchaseorder.destroy_file', ':_id') }}';
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

        function initItemTableAfterAjax() {
            if (typeof window.initPurchaseOrderItemTable === 'function') {
                window.initPurchaseOrderItemTable();
            }
        }

        function loadItemTable() {
            $("#div-table").html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="visually">Loading...</span>
            `);

            const isEdit = orderId != '';

            const url = '{{ route('purchaseorder.get_table_add') }}';

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    purchase_requisition_id: requisitionId,
                    client_vendor_id: $('#client_vendor_id').val(),
                },
                success: function(response) {
                    $("#div-table").html(response.html);

                    setTimeout(function() {
                        initItemTableAfterAjax();

                        initClientVendorSelect2(false);
                    }, 0);
                },
                error: function(xhr, status, error) {
                    console.error('Error load item table:', error);
                }
            });
        }

        function loadClientVendorTaxable(vendorId) {
            if (!vendorId) {
                window.poState.taxable = null;
                $("#text-tax").text("Tax");
                initItemTableAfterAjax();
                return;
            }

            let url = '{{ route('purchaseorder.get_client_vendor_by_id', ':_id') }}';
            url = url.replace(':_id', vendorId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    window.poState.taxable = response.data.taxable;

                    $("#text-tax").text(`Tax (${window.poState.taxable})`);

                    initItemTableAfterAjax();
                },
                error: function(xhr, status, error) {
                    console.error('Error get vendor taxable:', error);
                }
            });
        }

        function initPurchaseRequisitionSelect2() {
            // const $requisition = $('#purchase_requisition_id');

            // if (!$requisition.length) {
            //     return;
            // }

            // if ($requisition.hasClass('select2-hidden-accessible')) {
            //     $requisition.select2('destroy');
            // }

            // $requisition.off('.purchaseRequisition');

            // $requisition.select2({
            //     theme: "bootstrap-5",
            //     dropdownParent: $('#formModal'),
            //     width: $requisition.data('width') ?
            //         $requisition.data('width') : ($requisition.hasClass('w-100') ? '100%' : 'style'),
            //     selectOnClose: false,
            //     minimumResultsForSearch: 0,
            // });

            // $requisition.on('change.purchaseRequisition', function() {
            //     requisitionId = $(this).val();
            //     loadItemTable();
            // });

            const $purchase_requisition = $('#purchase_requisition_id');

            if (!$purchase_requisition.length) {
                return;
            }

            const selectedValue = $purchase_requisition.val();

            if ($purchase_requisition.hasClass('select2-hidden-accessible')) {
                $purchase_requisition.select2('destroy');
            }

            $purchase_requisition.off('.clientVendor');

            $purchase_requisition.select2({
                theme: "bootstrap-5",
                width: $('#purchase_requisition_id').data('width') ? $('#purchase_requisition_id').data('width') : (
                    $(
                        '#purchase_requisition_id').hasClass(
                        'w-100') ? '100%' : 'style'),
                placeholder: 'Direct PO',
                allowClear: true,
                selectOnClose: false,
                ajax: {
                    url: '{{ route('purchaseorder.get_purchase_requisition') }}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || data
                        };
                    },
                    cache: true
                }
            }).on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-container--open .select2-search__field').trigger('focus');
                    $('.select2-container--open').css('z-index', 1056);
                }, 0);
            });

            if (selectedValue) {
                $purchase_requisition.val(selectedValue).trigger('change.select2');
            }

            $purchase_requisition.on('select2:open.purchaseRequisition', function() {
                setTimeout(function() {
                    const search = document.querySelector(
                        '.select2-container--open .select2-search__field'
                    );

                    if (search) {
                        search.focus({
                            preventScroll: true
                        });
                    }

                    $('.select2-container--open').css('z-index', 1056);
                }, 0);
            });

            $purchase_requisition.on('change.purchaseRequisition', function() {
                requisitionId = $(this).val();
                loadItemTable();
            });
        }

        function initClientVendorSelect2(triggerTaxable = true) {
            const $vendor = $('#client_vendor_id');

            if (!$vendor.length) {
                return;
            }

            const selectedValue = $vendor.val();

            if ($vendor.hasClass('select2-hidden-accessible')) {
                $vendor.select2('destroy');
            }

            $vendor.off('.clientVendor');

            $vendor.select2({
                theme: "bootstrap-5",
                width: $('#client_venodr_id').data('width') ? $('#client_venodr_id').data('width') : (
                    $(
                        '#client_venodr_id').hasClass(
                        'w-100') ? '100%' : 'style'),
                placeholder: '',
                allowClear: true,
                selectOnClose: false,
                ajax: {
                    url: '{{ route('purchaseorder.get_client_vendor') }}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || data
                        };
                    },
                    cache: true
                }
            }).on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-container--open .select2-search__field').trigger('focus');
                    $('.select2-container--open').css('z-index', 1056);
                }, 0);
            });

            if (selectedValue) {
                $vendor.val(selectedValue).trigger('change.select2');
            }

            $vendor.on('select2:open.clientVendor', function() {
                setTimeout(function() {
                    const search = document.querySelector(
                        '.select2-container--open .select2-search__field'
                    );

                    if (search) {
                        search.focus({
                            preventScroll: true
                        });
                    }

                    $('.select2-container--open').css('z-index', 1056);
                }, 0);
            });

            $vendor.on('change.clientVendor', function() {
                const vendorId = $(this).val();
                loadClientVendorTaxable(vendorId);
            });

            if (triggerTaxable && selectedValue) {
                loadClientVendorTaxable(selectedValue);
            }
        }
        initPurchaseRequisitionSelect2();
        initClientVendorSelect2(false);

        $('#formModal').off('shown.bs.modal.select2PO').on('shown.bs.modal.select2PO', function() {
            initPurchaseRequisitionSelect2();
            initClientVendorSelect2(false);
        });
    </script>
    <!--app JS-->
@endsection
