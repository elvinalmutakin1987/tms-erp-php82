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
                                        <option value="Open">Open</option>
                                        <option value="Send User">Send User</option>
                                        <option value="User Approval">User Approval</option>
                                        <option value="Custodian">Custodian Approval</option>
                                        <option value="Received">Received</option>
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
                                        <th>Prof. Invoice Number</th>
                                        <th>Contract Number</th>
                                        <th>Date</th>
                                        <th>Unit</th>
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

    @include('proforma_invoice.modal-bulk')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const saveButton = document.getElementById('saveButton');

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
                        d.date_start = $('#date_start').val();
                        d.date_end = $('#date_end').val();
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
                        data: 'date',
                        name: 'date',
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
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == "Done") {
                                return '<span class="badge bg-success" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'Open') {
                                return '<span class="badge bg-primary" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'Approval') {
                                return '<span class="badge bg-info" style="font-size: 13px">' +
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

            $("#date_start, #date_end").off('change.filterDate').on('change.filterDate', function() {
                $('#table-data').DataTable().draw();
            });
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

        // $('#contract_id').off('change.contract').on('change.contract', function() {
        //     var val = $(this).val();

        //     if (!val) {
        //         $("#div-table").html('');
        //         return;
        //     }

        //     $("#div-table").html(`
    //     <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    //     <span class="visually">Loading...</span>
    // `);

        //     setTimeout(function() {
        //         $.ajax({
        //             url: "{{ route('proformainvoice.get_table_add') }}",
        //             data: {
        //                 contract_id: val,
        //                 year: $("#year").val(),
        //                 month: $("#month").val()
        //             },
        //             type: 'GET',
        //             success: function(response) {
        //                 $("#div-table").html(response.html);
        //             },
        //             error: function(xhr, status, error) {
        //                 console.error('Error:', error);
        //             }
        //         });
        //     }, 500);
        // });

        let loadTableTimer = null;

        function loadProformaInvoiceTable() {
            var contractId = $('#contract_id').val();
            var year = $('#year').val();
            var month = $('#month').val();

            if (!contractId) {
                $('#div-table').html('');
                return;
            }

            $('#div-table').html(`
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        <span class="visually">Loading...</span>
    `);

            clearTimeout(loadTableTimer);

            loadTableTimer = setTimeout(function() {
                $.ajax({
                    url: "{{ route('proformainvoice.get_table_add') }}",
                    type: 'GET',
                    data: {
                        contract_id: contractId,
                        year: year,
                        month: month
                    },
                    success: function(response) {
                        $('#div-table').html(response.html);
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

            if (proformaInvoiceId == '') {
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

        $(document).off('click.editButton').on('click.editButton', '.editButton', function() {
            proformaInvoiceId = $(this).data('id');

            $('#modal-header').text('Edit Proforma Invoice');
            $('#id').val(proformaInvoiceId);

            let url = '{{ route('purchaserequisition.show', ':_id') }}';
            url = url.replace(':_id', proformaInvoiceId);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $("#divSignPath").css('display', 'block');
                    $('#modal-header').text('Edit Proforma Invoice');

                    $("#unit_id").val(response.data.unit_id).trigger('change');
                    $("#maintenance_id").val(response.data.maintenance_id).trigger('change');
                    $("#date").val(response.data.date);
                    $("#remarks").val(response.data.remarks);
                    $("#request_token").val(response.data.request_token);
                },
                error: function() {
                    alert('Error fetching data');
                }
            });
        });

        $(document).off('click.detailButton').on('click.detailButton', '.detailButton', function() {
            $('#modal-detail-header').text('Detail Proforma Invoice');

            let url = '{{ route('purchaserequisition.get_detail', ':_id') }}';
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

        $('.saveButton').off('click.saveProforma').on('click.saveProforma', function() {
            var formData = new FormData($('#formModal').find('form')[0]);
            var url = '{{ route('proformainvoice.store') }}';
            var type = 'POST';

            formData.append('status', $(this).val());

            if (proformaInvoiceId != '') {
                url = '{{ route('proformainvoice.update', ':_id') }}';
                url = url.replace(':_id', proformaInvoiceId);
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: type,
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire({
                        title: response.title,
                        text: response.message,
                        icon: "success",
                        timer: 5000,
                        didOpen: () => {},
                        willClose: () => {
                            $('#table-data').DataTable().ajax.reload(null, false);
                            $('#formModal form')[0].reset();

                            proformaInvoiceId = '';
                            contractId = '';
                            unitId = '';

                            $('#formModal').modal('hide');
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
        });

        $('#cancelButton').off('click.cancelModal').on('click.cancelModal', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').off('click.cancelDetail').on('click.cancelDetail', function() {
            $('#formDetail').modal('hide');
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
            }
        }

        function enableButton() {
            if (saveButton) {
                saveButton.disabled = false;
            }
        }
    </script>
    <!--app JS-->
@endsection
