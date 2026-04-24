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
                                    <select class="form-select select-top" id="depart" name="depart">
                                        <option value="All">All Department</option>
                                        @foreach ($department as $key => $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select class="form-select select-top" id="_status" name="_status">
                                        <option value="All">All Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Approval">Approval</option>
                                        <option value="Open">Open</option>
                                        <option value="Cancel">Cancel</option>
                                        <option value="Done">Done</option>
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
                                        <th>Requisition Number</th>
                                        <th>Date</th>
                                        <th>Department</th>
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

    @include('purchase_requisition_general.modal')

    @include('purchase_requisition_general.modal-detail')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
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
                        d.department = $('#depart').val();
                        d.date_start = $('#date_start').val();
                        d.date_end = $('#date_end').val();
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
                        data: 'department',
                        name: 'department',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == "Done" || data == "Approved") {
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
                ],
            });

            $(document).on('click', '.editButton', function() {
                requisitionId = $(this).data('id');
                $('#modal-header').text('Edit Requisition');
                $('#id').val(requisitionId);
                let url = '{{ route('purchaserequisitiongeneral.show', ':_id') }}';
                url = url.replace(':_id', requisitionId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit Requisition');
                        $("#date").val(response.data.date);
                        $("#notes").val(response.data.notes);
                        $("#department").val(response.data.department).trigger('change');
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $(document).on('click', '.detailButton', function() {
                $('#modal-detail-header').text('Detail Requisition');
                let url = '{{ route('purchaserequisitiongeneral.get_detail', ':_id') }}';
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
                    let url = '{{ route('purchaserequisitiongeneral.destroy', ':_id') }}';
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
            const status = $(this).val();
            const form = $('#formModal').find('form')[0];
            const formData = new FormData(form);

            formData.append('status', status);

            let url = '{{ route('purchaserequisitiongeneral.store') }}';
            let type = 'POST';

            if (requisitionId) {
                url = '{{ route('purchaserequisitiongeneral.update', ':_id') }}'.replace(':_id', requisitionId);
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
                                requisitionId = '';
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


        $('#formModal').on('show.bs.modal', function() {
            var button = $('#openModalButton');
            var title = button.data('title');
            $('#formModal form')[0].reset();
            $('#modal-header').text(title);

            var tbody = $("#tableItem > tbody");
            tbody.append(`
                    <tr>
                        <td colspan="6">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="visually">Loading...</span>
                        </td>
                    </tr>
                    `);
            setTimeout(function() {
                const isEdit = requisitionId != '';
                const url = isEdit ?
                    '{{ route('purchaserequisitiongeneral.get_table_edit', ':_id') }}'.replace(':_id',
                        requisitionId) :
                    '{{ route('purchaserequisitiongeneral.get_table_add') }}';

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#tableItem > tbody").html(response.html);

                        const titleText = isEdit ? 'Edit Requisition' : 'Add Requisition';
                        const number = isEdit ? response.requisition_no : response
                            .requisition_prev_no;

                        $('#modal-header').html(titleText + ' -&nbsp;<b>' + number + '</b>');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }, 500);
        });

        $('#formModal').on('hidden.bs.modal', function() {
            requisitionId = '';
            $('#tableItem tbody').empty();
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').on('click', function() {
            $('#formDetail').modal('hide');
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
    </script>
    <!--app JS-->
@endsection
