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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add User"><i
                                            class='bx bxs-plus-square'></i>New User</a>
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
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Email</th>
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

    @include('user.modal')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <!--Password show & hide js -->
    <script>
        $(document).ready(function() {
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });
            $("#show_hide_password_confirmation a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password_confirmation input').attr("type") == "text") {
                    $('#show_hide_password_confirmation input').attr('type', 'password');
                    $('#show_hide_password_confirmation i').addClass("bx-hide");
                    $('#show_hide_password_confirmation i').removeClass("bx-show");
                } else if ($('#show_hide_password_confirmation input').attr("type") == "password") {
                    $('#show_hide_password_confirmation input').attr('type', 'text');
                    $('#show_hide_password_confirmation i').removeClass("bx-hide");
                    $('#show_hide_password_confirmation i').addClass("bx-show");
                }
            });
        });
    </script>

    <script>
        var userId = '';
        var roleId = '';
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
                        data: 'username',
                        name: 'username',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'role',
                        name: 'role',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'email',
                        name: 'email',
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
                userId = $(this).data('id');
                $('#modal-header').text('Edit User');
                $('#id').val(userId);
                let url = '{{ route('user.show', ':_id') }}';
                url = url.replace(':_id', userId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#username').prop('readonly', true);
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit User');
                        $('#username').val(response.data.username);
                        $('#name').val(response.data.name);
                        $('#email').val(response.data.email);
                        roleId = response.role_id;
                        if (response.data.sign_path != null) {
                            var imageUrl = response.sign_path;
                            $('#divSignPath').html('<img src="' + imageUrl +
                                '" alt="Signature Image" style="max-width: 100%; height: auto;"/>'
                            );
                        }
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });
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
                    let url = '{{ route('user.destroy', ':_id') }}';
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
            var url = '{{ route('user.store') }}';
            var type = 'POST';
            if (userId != '') {
                url = '{{ route('user.update', ':_id') }}';
                url = url.replace(':_id', userId);
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
            $('#username').prop('readonly', false);
            $('#deleteSignButton').hide();
            setTimeout(function() {
                var data = {
                    'form': 'create'
                };
                if (userId != '') {
                    data = {
                        'form': 'edit',
                        'user_id': userId
                    };
                    $('#deleteSignButton').show();
                }
                $.ajax({
                    url: '{{ route('user.get_role_all') }}',
                    type: 'GET',
                    success: function(response) {
                        $('#role_id').empty();
                        $.each(response.data, function(index, role) {
                            $('#role_id').append('<option value="' + role.id + '">' +
                                role.name +
                                '</option>');
                        });
                        if (roleId != '') {
                            $("#role_id").val(roleId).trigger('change');
                        }
                        gen_select2();
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
            }, 500);
        });

        $('#formModal').on('hidden.bs.modal', function() {
            userId = '';
            roleId = '';
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        function gen_select2() {
            $('#role_id').each(function() {
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

        $('#deleteSignButton').on('click', function() {
            var url = '{{ route('user.delete_sign', ':_id') }}';
            url = url.replace(':_id', userId);
            var type = 'DELETE';
            $.ajax({
                url: url,
                type: type,
                success: function(response) {
                    $('#divSignPath').html('');
                    $('#divSignPath').css('display', 'none');
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
    </script>
    <!--app JS-->
@endsection
