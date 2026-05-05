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
                                        <option value="Open" selected>Open</option>
                                        <option value="Created">Created</option>
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
                            <table id="table-data" class="table" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10">No</th>
                                        <th width="15%">Requisition Number</th>
                                        <th width="15%">Department</th>
                                        <th>Quotation File</th>
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

    @include('request_quotation.modal')

    @include('request_quotation.modal-detail')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const saveButton = document.getElementById('saveButton');

        var requisitionId = '';
        var maintenanceId = '';
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
                        d.department = $('#depart').val();
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
                        data: 'department',
                        name: 'department',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'quotation_file',
                        name: 'quotation_file',
                        orderable: false,
                        searchable: false,
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
                var button = $('#editButton');
                var title = button.data('title');
                requisitionId = $(this).data('id');
                $('#modal-header').text('Upload Quotation');
                $('#id').val(requisitionId);
                let url = '{{ route('requestquotation.get_purchase_requisition', ':_id') }}';
                url = url.replace(':_id', requisitionId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').html('Upload Quotation -&nbsp;<b>' + response.data
                            .requisition_no + '</b>');
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

            $('#client_vendor_id').select2({
                theme: "bootstrap-5",
                dropdownParent: $('#formModal'),
                width: '100%',
                selectOnClose: false,
                ajax: {
                    url: '{{ route('requestquotation.get_client_vendor') }}',
                    dataType: 'json',
                    delay: 250,
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
                    const $search = $('.select2-container--open .select2-search__field');
                    $search.trigger('focus');
                    $('.select2-container--open').css('z-index', 1056);
                }, 0);
            });
        });

        $('.saveButton').on('click', function() {
            disableButton();
            const status = $(this).val();
            const form = $('#formModal').find('form')[0];
            const formData = new FormData(form);
            let url = '{{ route('requestquotation.quotation', ':_id') }}'.replace(':_id', requisitionId);
            formData.append('_method', 'PUT');
            let type = 'POST';
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
                    result.isConfirmed ? submitForm() : enableButton();
                });
            } else {
                submitForm();
            }
        });

        $(document).on('click', '.detailButton', function() {
            $('#modal-detail-header').text('Detail Requisition');
            let url = '{{ route('requestquotation.get_detail', ':_id') }}';
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

        $('#formModal').on('show.bs.modal', function() {
            var button = $('#openModalButton');
            var title = button.data('title');
            $('#formModal form')[0].reset();
            $('#modal-header').text(title);

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
        });

        $('#formModal').on('hidden.bs.modal', function() {
            requisitionId = '';
            enableButton();
            $("#request_token").val("");
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

        function disableButton() {
            saveButton.disabled = true;
        }

        function enableButton() {
            saveButton.disabled = false;
        }

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
                    let url = '{{ route('requestquotation.destroy', ':_id') }}';
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

        function create_(id) {
            Swal.fire({
                title: 'Are you sure to create Purchase Order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5156be',
                cancelButtonColor: '#fd625e',
                confirmButtonText: 'Yes, Create it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '{{ route('requestquotation.create_purchase_order', ':_id') }}';
                    url = url.replace(':_id', id);
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: "Created!",
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
    </script>
    <!--app JS-->
@endsection
