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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Client"
                                        data-type="Client"><i class='bx bxs-plus-square'></i>New Client </a>
                                    <a href="javascript:;" id="openModalButton2" class="btn btn-secondary mb-3 mb-lg-0"
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Vendor"
                                        data-type="Vendor"><i class='bx bxs-plus-square'></i>New Vendor </a>
                                </div>
                                <div class="col-2">
                                    <select class="form-select" id="typeClientVendor" name="typeClientVendor">
                                        <option value="All">All Type</option>
                                        <option value="Client">Client</option>
                                        <option value="Vendor">Vendor</option>
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
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>PIC</th>
                                        <th>Phone</th>
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

    @include('clientvendor.modal')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script>
        const saveButton = document.getElementById('saveButton');

        var clientvendorId = '';
        var locationId = '';
        var type = '';
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
                        data: 'name',
                        name: 'name',
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
                        data: 'pic',
                        name: 'pic',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'phone',
                        name: 'phone',
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
                clientvendorId = $(this).data('id');
                $('#modal-header').text('Edit ' + this.dataset.type);
                type = this.dataset.type;
                $('#id').val(clientvendorId);
                let url = '{{ route('clientvendor.show', ':_id') }}';
                url = url.replace(':_id', clientvendorId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit ' + type);
                        $('#name').val(response.data.name);
                        $("#location_id").val(response.data.location_id).trigger('change');
                        $('#address').val(response.data.address);
                        $('#email').val(response.data.email);
                        $('#pic').val(response.data.pic);
                        $('#phone').val(response.data.phone);
                        $('#top').val(response.data.top);
                        // type = response.data.type;
                        $('#type').val(type);
                        $('#divLocation').hide();
                        if (type == 'Client') {
                            $('#divLocation').show();
                        }
                        $('#bank').val(response.data.bank).trigger('change');
                        $('#bank_account').val(response.data.bank_account);
                        $('#taxable').val(response.data.taxable).trigger('change');
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $("#typeClientVendor").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
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
                    let url = '{{ route('clientvendor.destroy', ':_id') }}';
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
            disableButton();
            var formData = new FormData($('#formModal').find('form')[0]);
            var url = '{{ route('clientvendor.store') }}';
            var typeAjax = 'POST';
            $('#type').val(type);
            if (clientvendorId != '') {
                url = '{{ route('clientvendor.update', ':_id') }}';
                url = url.replace(':_id', clientvendorId);
                formData.append('_method', 'PUT');
            }
            $.ajax({
                url: url,
                type: typeAjax,
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
                            clientvendorId = '';
                            type = '';
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

        $('#openModalButton2').on('click', function() {
            var button = $('#openModalButton2');
            var title = button.data('title');
            $('#modal-header').text(title);
            type = 'Vendor';
            $('#type').val(type);
            $('#divLocation').hide();
        });

        $('#formModal').on('hidden.bs.modal', function() {
            locationId = '';
            clientvendorId = '';
            enableButton();
            $('#request_token').val("");
            $("#type").val(type).trigger('change');
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

        function disableButton() {
            saveButton.disabled = true;
        }

        function enableButton() {
            saveButton.disabled = false;
        }
    </script>
    <!--app JS-->
@endsection
