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
                            <div class="row align-items-center">
                                <div class="col">
                                    <a href="javascript:;" id="openModalButton" class="btn btn-primary mb-3 mb-lg-0"
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Approval Flow"><i
                                            class='bx bxs-plus-square'></i>New</a>
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
                                        <th>Name</th>
                                        <th>Model</th>
                                        <th>Step Total</th>
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

    @include('approvalflow.modal')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>

    <script>
        var approvalFlowId = '';
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
                "ajax": ajax,
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
                        data: 'name',
                        name: 'name',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'approvable_model',
                        name: 'approvable_model',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'step_total',
                        name: 'step_total',
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

            $(document).on('click', '.editButton', function() {
                approvalFlowId = $(this).data('id');
                $('#modal-header').text('Edit Approval Flow');
                $('#id').val(approvalFlowId);
                let url = '{{ route('approval_flow.show', ':_id') }}';
                url = url.replace(':_id', approvalFlowId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#username').prop('readonly', true);
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit User');
                        $('#name').val(response.data.name);
                        $('#approvable_model').val(response.data.approvable_model);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
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
                    let url = '{{ route('approval_flow.destroy', ':_id') }}';
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

        $('#saveButton').on('click', function() {
            var formData = new FormData($('#formModal').find('form')[0]);
            var url = '{{ route('approval_flow.store') }}';
            var type = 'POST';
            if (approvalFlowId != '') {
                url = '{{ route('approval_flow.update', ':_id') }}';
                url = url.replace(':_id', approvalFlowId);
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
                            userId = '';
                            $('#formModal').modal('hide');
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
        });

        $('#formModal').on('show.bs.modal', function() {
            var button = $('#openModalButton');
            var title = button.data('title');
            $('#formModal form')[0].reset();
            $('#modal-header').text(title);
            var tbody = $("#tableStep > tbody");
            tbody.append(`
                    <tr>
                        <td colspan="5">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="visually">Loading...</span>
                        </td>
                    </tr>
                    `);
            $.ajax({
                url: '{{ route('approval_flow.get_user_all') }}',
                type: 'GET',
                success: function(response) {
                    $('#approver_id').empty();
                    $.each(response.data, function(index, role) {
                        $('#approver_id').append('<option value="' + role.id +
                            '">' +
                            role.name +
                            '</option>');
                    });
                    if (approvalFlowId != '') {
                        $("#approver_id").val(approvalFlowId).trigger('change');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: error,
                    });
                }
            });

            setTimeout(function() {
                var data = {
                    'form': 'create'
                };
                if (approvalFlowId != '') {
                    data = {
                        'form': 'edit',
                        'approval_flow_id': approvalFlowId
                    };
                    $.ajax({
                        url: '{{ route('approval_flow.get_step_list') }}',
                        data: data,
                        type: 'GET',
                        success: function(response) {
                            setTimeout(function() {
                                $('#tableStep tbody tr').not(':first').remove();
                                tbody.append(response);
                            }, 500);
                        },
                        error: function(xhr, status, error) {
                            console.log(error)
                        }
                    });
                } else {
                    $('#tableStep tbody tr').not(':first').remove();
                }
            }, 500);
        });

        $('#formModal').on('hidden.bs.modal', function() {
            approvalFlowId = '';
            $('#tableStep tbody tr').not(':first').remove();
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
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

        $('#addStepButton').on('click', function() {
            var tbody = $("#tableStep > tbody");
            var user_id = $("#approver_id").val();
            var user_name = $("#approver_id option:selected").text();
            var order = $("#txt_order").val();
            var action = $("#slc_action option:selected").text();

            var newRow = `
                <tr class="row-number">
                    <td class="p-1 align-middle row-number">
                        #
                    </td>
                    <td class="p-1 align-middle">
                       <input type="text" class="form-control" id="username" name="username" readonly value="${user_name}">
                       <input type="hidden" class="form-control" id="user_id" name="user_id[]" value="${user_id}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="text" class="form-control" id="action" name="action[]" readonly value="${action}">
                    </td>
                    <td class="p-1 align-middle"> 
                        <input type="number" class="form-control" id="order" name="order[]" value="${order}">
                    </td>
                    <td class="text-center p-1 align-middle">
                        <div class="row row-cols-auto g-3">
                            <div class="col">
                                <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row  "
                                                        id="removeStepButton"></button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            $("#txt_order").val('');
            tbody.append(newRow);
            gen_select2();

            renumberRows();
        });

        function renumberRows() {
            let no = 1;

            $('#tableStep > tbody > tr').each(function() {
                // row khusus tidak ikut nomor
                if ($(this).hasClass('fixed-row')) {
                    $(this).find('.row-number').text('');
                    return;
                }

                $(this).find('.row-number').text(no);
                no++;
            });
        }

        $("#tableStep").on("click", ".delete-row", function() {
            $(this).closest("tr").remove();

            if ($(this).hasClass('fixed-row')) {
                return;
            }

            $(this).remove();
            renumberRows();
        });
    </script>
    <!--app JS-->
@endsection
