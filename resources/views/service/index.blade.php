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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Service"><i
                                            class='bx bxs-plus-square'></i>New</a>
                                </div>
                                <div class="col-3">
                                    <select class="form-select" id="typeSevice" name="typeSevice">
                                        <option value="All">All Type</option>
                                        @foreach ($servicetype as $key => $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
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

    @include('service.modal')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script>
        var serviceId = '';
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
                        d.type = $('#typeSevice').val();
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
                serviceId = $(this).data('id');
                $('#modal-header').text('Edit Service');
                $('#id').val(serviceId);
                let url = '{{ route('service.show', ':_id') }}';
                url = url.replace(':_id', serviceId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit Service');
                        $('#name').val(response.data.name);
                        $('#type').val(response.data.type);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $("#typeSevice").select2({
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
                    let url = '{{ route('service.destroy', ':_id') }}';
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
            var url = '{{ route('service.store') }}';
            var type = 'POST';
            if (serviceId != '') {
                url = '{{ route('service.update', ':_id') }}';
                url = url.replace(':_id', serviceId);
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
                            serviceId = '';
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

            setTimeout(function() {
                var data = {
                    'form': 'create'
                };
                if (serviceId != '') {
                    data = {
                        'form': 'edit',
                        'service_id': serviceId
                    };
                    $.ajax({
                        url: '{{ route('service.get_service_item_list') }}',
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
            serviceId = '';
            $('#tableStep tbody tr').not(':first').remove();
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        $('#addItemButton').on('click', function() {
            var tbody = $("#tableStep > tbody");
            var item_no = $("#txt_item_no").val();
            var item_des = $("#txt_item_des").val();
            var newRow = `
                <tr>
                    <td class="p-1 align-middle row-number">
                        #
                    </td>
                    <td class="p-1 align-middle">
                       <input type="text" class="form-control" id="item_no" name="item_no[]" readonly value="${item_no}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="text" class="form-control" id="item_des" name="item_des[]" readonly value="${item_des}">
                    </td>
                    <td class="text-center p-1 align-middle">
                        <div class="row row-cols-auto g-3">
                            <div class="col">
                                <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row  "
                                                        id="removeItemButton"></button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            $("#txt_item_no").val('');
            $("#txt_item_des").val('');
            tbody.append(newRow);

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

        function gen_select2() {
            $('#type').each(function() {
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
