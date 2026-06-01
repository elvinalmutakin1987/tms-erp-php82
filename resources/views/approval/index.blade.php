@extends('partials.main')

@section('css')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
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
                            <table id="table-data" class="table" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Type</th>
                                        <th>Number</th>
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

    @include('approval.modal')

    @include('approval.modal-detail')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script>
        const saveButton = document.getElementById('saveButton');
        var approvableId = '';
        var type = '';
        var status = '';
        var model = '';
        var procId = '';
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
                        d.type = $('#typeClientVendor').val();
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
                        data: 'type',
                        name: 'type',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'number',
                        name: 'number',
                        orderable: true,
                        searchable: true,
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

            // $(document).on('click', '.detailButton', function() {
            //     $('#modal-detail-header').text('Detail Report');
            //     let id = $(this).data('id');
            //     let model = $(this).data('model');
            //     let url =
            //         '{{ route('approval.get_detail', ['approvable_model' => ':_approvable_model', 'id' => ':_id']) }}';
            //     url = url.replace(':_approvable_model', model).replace(':_id', id);
            //     $.ajax({
            //         url: url,
            //         type: 'GET',
            //         success: function(response) {
            //             $('#modal-detail-body').html(response);
            //         },
            //         error: function() {
            //             alert('Error fetching data');
            //         }
            //     });
            // });

            $(document).on('click', '.detailButton', function() {
                $('#modal-detail-header').text('Detail');
                approvableId = $(this).data('id');
                procId = $(this).data('procid');
                model = $(this).data('model');
                $('#approveButton').data('id', procId);
                $('#rejectButton').data('id', procId);
                $('#approveButton').data('model', model);
                $('#rejectButton').data('model', model);
                let url =
                    '{{ route('approval.get_detail', ['approvable_model' => ':_approvable_model', 'id' => ':_id']) }}';
                url = url.replace(':_approvable_model', model).replace(':_id', approvableId);
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

            gen_select2();
        });

        $(document).on('click', '.saveButton', function() {
            status = $(this).val();
            model = $(this).data('model');
            if (status === 'Approve') {
                approve(procId);
            } else if (status === 'Reject') {
                reject(procId);
            }
        });

        $('#formModal').on('show.bs.modal', function() {
            $('#formModal form')[0].reset();
            setTimeout(function() {
                var data = {
                    'form': 'create'
                };
                if (clientvendorId != '') {
                    data = {
                        'form': 'edit',
                        'client_vendor_id': clientvendorId
                    };
                } else {
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
                $.ajax({
                    url: '{{ route('clientvendor.get_location_all') }}',
                    type: 'GET',
                    success: function(response) {
                        $('#location_id').empty();
                        $.each(response.data, function(index, location) {
                            $('#location_id').append('<option value="' +
                                location.id +
                                '">' +
                                location.name +
                                '</option>');
                        });
                        if (locationId != '') {
                            $("#location_id").val(locationId).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON ? xhr.responseJSON
                            .message : error;
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: errorMessage,
                        });
                    }
                });
            }, 500);
        });


        $('#openModalButton').on('click', function() {
            var button = $('#openModalButton');
            var title = button.data('title');
            $('#modal-header').text(title);
            type = 'Client';
            $('#type').val(type);
            $('#divLocation').show();
        });

        $('#formModal').on('hidden.bs.modal', function() {
            approvalProcessId = '';
            procId = '';
            status = '';
            model = '';
            enableButton();
            $('#request_token').val("");
            $("#type").val(type).trigger('change');
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').on('click', function() {
            $('#formDetail').modal('hide');
            $('#modal-detail-body').html("");
        });

        function gen_select2() {
            $('.select-select').each(function() {
                const $el = $(this);
                $el.select2({
                        theme: "bootstrap-5",
                        dropdownParent: $(
                            '#formModal'),
                        width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
                            'style'),
                        selectOnClose: false,
                        minimumResultsForSearch: 0,
                    })
                    .on('select2:open', function() {
                        setTimeout(function() {
                            const $search = $('.select2-container--open .select2-search__field');
                            $search.trigger('focus');
                            $('.select2-container--open').css('z-index', 1056);
                        }, 0);
                    });
            });
        }

        function disableButton() {
            saveButton.disabled = true;
        }

        function enableButton() {
            saveButton.disabled = false;
        }

        function approve(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to approve this item.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '{{ route('approval.approve', ':_id') }}';
                    url = url.replace(':_id', id);
                    $.ajax({
                        url: url,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                response.title,
                                response.message,
                                'success'
                            );
                            $('#table-data').DataTable().ajax.reload(null, false);
                            $('#formDetail').modal('hide');
                            $('#modal-detail-body').html("");
                        },
                        error: function(xhr, status, error) {
                            var errorMessage = xhr.responseJSON ? xhr.responseJSON
                                .message : error;
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

        function reject(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to reject this item.",
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Enter reject reason',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Reason is required!';
                    }
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let url = '{{ route('approval.reject', ':_id') }}';
                    url = url.replace(':_id', id);
                    $.ajax({
                        url: url,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            reason: result.value
                        },
                        success: function(response) {
                            Swal.fire(
                                response.title,
                                response.message,
                                'success'
                            );

                            $('#table-data').DataTable().ajax.reload(null, false);
                            $('#formDetail').modal('hide');
                            $('#modal-detail-body').html("");
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
