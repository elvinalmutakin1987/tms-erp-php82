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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Unit"><i
                                            class='bx bxs-plus-square'></i>New</a>
                                </div>
                                <div class="col-2">
                                    <select class="form-select" id="typeUnit" name="typeUnit">
                                        <option value="All">All Type</option>
                                        @foreach ($typeunit as $key => $value)
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
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th>Plate Number</th>
                                        <th>Location</th>
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

    @include('unit.modal')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const saveButton = document.getElementById('saveButton');
        var unitId = '';
        var locationId = '';
        var unitbrandId = '';
        var unitmodelId = '';
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
                        d.typeUnit = $('#typeUnit').val();
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
                        data: 'brand',
                        name: 'brand',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'model',
                        name: 'model',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'registration_no',
                        name: 'registration_no',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'location',
                        name: 'location',
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
                unitId = $(this).data('id');
                $('#modal-header').text('Edit Unit');
                $('#id').val(unitId);
                let url = '{{ route('unit.show', ':_id') }}';
                url = url.replace(':_id', unitId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#modal-header').text('Edit Unit');
                        unitbrandId = response.data.unit_brand_id;
                        unitmodelId = response.data.unit_model_id;
                        locationId = response.data.location_id;
                        $("#type").val(response.data.type).trigger('change');
                        $("#registration_no").val(response.data.registration_no);
                        $("#vehicle_no").val(response.data.vehicle_no);
                        $("#cetificate_no").val(response.data.cetificate_no);
                        $("#mechine_no").val(response.data.mechine_no);
                        $("#chassis_no").val(response.data.chassis_no);
                        $("#code_access").val(response.data.code_access);
                        $("#plr_no").val(response.data.plr_no);
                        $("#exp_crane").val(response.data.exp_crane);
                        $("#exp_fuel_issue").val(response.data.exp_fuel_issue);
                        $("#exp_tbst").val(response.data.exp_tbst);
                        $("#exp_pass_road_1").val(response.data.exp_pass_road_1);
                        $("#exp_stnk").val(response.data.exp_stnk);
                        $("#exp_tax").val(response.data.exp_tax);
                        $("#exp_comm").val(response.data.exp_comm);
                        $("#description").val(response.data.description);
                        $("#request_token").val(response.data.request_token);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $("#typeUnit").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
                $('#table-data').DataTable().draw();
            });

            $(".datepicker").flatpickr();

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
                    let url = '{{ route('unit.destroy', ':_id') }}';
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
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: error,
                            });
                        }
                    });
                }
            });
        }

        $('#saveButton').on('click', function() {
            disableButton();
            var formData = new FormData($('#formModal').find('form')[0]);
            var url = '{{ route('unit.store') }}';
            var type = 'POST';
            var title = "Saved!";
            if (unitId != '') {
                url = '{{ route('unit.update', ':_id') }}';
                url = url.replace(':_id', unitId);
                title = "Updated!";
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
                        title: title,
                        text: response.message,
                        icon: "success",
                        timer: 5000,
                        didOpen: () => {},
                        willClose: () => {
                            $('#table-data').DataTable().ajax.reload(null, false);
                            $('#formModal form')[0].reset();
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

            setTimeout(function() {
                var data = {
                    'form': 'create'
                };
                if (unitId != '') {
                    data = {
                        'form': 'edit',
                        'unit_id': unitId
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
                    url: '{{ route('unit.get_location_all') }}',
                    type: 'GET',
                    success: function(response) {
                        $('#location_id').empty();
                        $('#location_id').append(
                            '<option value="" selected disabled></option>');
                        $.each(response.data, function(index, location) {
                            $('#location_id').append('<option value="' + location.id +
                                '">' +
                                location.name +
                                '</option>');
                        });
                        if (locationId != '') {
                            $("#location_id").val(locationId).trigger('change');
                        }
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

                $.ajax({
                    url: '{{ route('unit.get_brand_all') }}',
                    type: 'GET',
                    success: function(response) {
                        $('#unit_brand_id').empty();
                        $('#unit_brand_id').append(
                            '<option value="" selected disabled></option>');
                        $.each(response.data, function(index, brand) {
                            $('#unit_brand_id').append('<option value="' + brand.id +
                                '">' +
                                brand.name +
                                '</option>');
                        });
                        if (unitbrandId != '') {
                            $("#unit_brand_id").val(unitbrandId).trigger('change');
                        }
                        get_unit_model($("#unit_brand_id").val());
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
            unitId = '';
            locationId = '';
            unitbrandId = '';
            unitmodelId = '';
            enableButton();
            $("#request_token").val("");
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

        function get_unit_model(unit_brand_id) {
            $.ajax({
                url: '{{ route('unit.get_model_all') }}',
                data: {
                    'unit_brand_id': unit_brand_id
                },
                type: 'GET',
                success: function(response) {
                    $('#unit_model_id').empty();
                    $('#unit_model_id').append('<option value="" selected disabled></option>');
                    $.each(response.data, function(index, brand) {
                        $('#unit_model_id').append('<option value="' + brand.id +
                            '">' +
                            brand.desc +
                            '</option>');
                    });
                    if (unitmodelId != '') {
                        $("#unit_model_id").val(unitmodelId).trigger('change');
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
        }

        $("#unit_brand_id").select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                'w-100') ? '100%' : 'style',
        }).on('change', function() {
            get_unit_model($(this).val());
        });

        function disableButton() {
            saveButton.disabled = true;
        }

        function enableButton() {
            saveButton.disabled = false;
        }
    </script>
    <!--app JS-->
@endsection
