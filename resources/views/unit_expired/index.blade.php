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

                                    <a href="{{ route('unitexpired.export') }}" class="btn btn-success mb-3 mb-lg-0"
                                        data-title="Export Unit" target="_blank" i="btnExport"><i
                                            class='bx bxs-share'></i>Export to
                                        Excel</a>
                                </div>
                                <div class="col-2">
                                    <select class="form-select" id="typeUnit" name="typeUnit">
                                        <option value="All">All Type</option>
                                        @foreach ($typeunit as $key => $value)
                                            <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select class="form-select" id="location" name="location">
                                        <option value="All">All location</option>
                                        @foreach ($location as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control datepicker" id="from" name="from">
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control datepicker" id="to" name="to">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body ">
                            <table id="table-data" class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Vehicle No.</th>
                                        <th>Chasis No.</th>
                                        <th>Plat No.</th>
                                        <th>Access Code</th>
                                        <th>PLR No.</th>
                                        <th>Banlaw No.</th>
                                        <th>Exp. Crane</th>
                                        <th>Exp. Fuel Issue</th>
                                        <th>Exp. TBST</th>
                                        <th>Exp. STNK</th>
                                        <th>Exp. Tax</th>
                                        <th>Exp. Commisioning</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                            <table>
                                <tr>
                                    <td colspan="14"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000000; background-color: #ff0000; width: 100px"></td>
                                    <td colspan="13">&nbsp; Masa berlaku kurang dari atau sama dengan 1 bulan</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000000; background-color: #ffff00;"></td>
                                    <td colspan="13">&nbsp; Masa berlaku antara 1 hingga 1.5 bulan</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #000000; background-color: #00ff00;"></td>
                                    <td colspan="13">&nbsp; Masa berlaku antara di atas 1.5 bulan</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end page wrapper -->

    @include('unit_expired.modal')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

    <script>
        const saveButton = document.getElementById('saveButton');
        var unitId = '';
        var locationId = '';
        var unitbrandId = '';
        var unitmodelId = '';
        $(document).ready(function() {
            var ajax = '{{ url()->current() }}';
            // var table = $('#table-data').DataTable({
            //     scrollCollapse: true,
            //     responsive: false,
            //     scrollX: true,
            //     "lengthMenu": [
            //         [10, 25, 50, 100, -1],
            //         [10, 25, 50, 100, "All"]
            //     ],
            //     "paging": true,
            //     "lengthChange": true,
            //     "searching": true,
            //     "ordering": true,
            //     "info": true,
            //     "autoWidth": false,
            //     "processing": true,
            //     "serverSide": true,
            //     "ajax": {
            //         url: ajax,
            //         data: function(d) {
            //             d.typeUnit = $('#typeUnit').val();
            //             d.location = $('#location').val();
            //             d.from = $('#from').val();
            //             d.to = $('#to').val();
            //         }
            //     },
            //     "columns": [{
            //             data: 'DT_RowIndex',
            //             name: 'DT_RowIndex',
            //             orderable: false,
            //             searchable: false,
            //             width: '10px',
            //             className: 'dt-center',
            //             targets: '_all'
            //         },
            //         {
            //             data: 'type',
            //             name: 'type',
            //             orderable: true,
            //             searchable: true,
            //         },
            //         {
            //             data: 'location',
            //             name: 'location',
            //             orderable: true,
            //             searchable: true,
            //         },
            //         {
            //             data: 'vehicle_no',
            //             name: 'vehicle_no',
            //             orderable: true,
            //             searchable: true,
            //         },
            //         {
            //             data: 'chassis_no',
            //             name: 'chassis_no',
            //             orderable: true,
            //             searchable: true,
            //         },
            //         {
            //             data: 'registration_no',
            //             name: 'registration_no',
            //             orderable: true,
            //             searchable: true,
            //         },
            //         {
            //             data: 'code_access',
            //             name: 'code_access',
            //             orderable: true,
            //             searchable: true,
            //         },
            //         {
            //             data: 'plr_no',
            //             name: 'plr_no',
            //             orderable: true,
            //             searchable: true,
            //         },
            //         {
            //             data: 'banlaw_no',
            //             name: 'banlaw_no',
            //             orderable: true,
            //             searchable: true,
            //         },
            //         {
            //             data: 'exp_crane',
            //             name: 'exp_crane',
            //             orderable: true,
            //             searchable: true,
            //             render: function(data, type, row) {
            //                 if (data) {
            //                     return dayjs(data).format('DD MMMM YYYY')
            //                 }
            //                 return "";
            //             }
            //         },
            //         {
            //             data: 'exp_fuel_issue',
            //             name: 'exp_fuel_issue',
            //             orderable: true,
            //             searchable: true,
            //             render: function(data, type, row) {
            //                 if (data) {
            //                     return dayjs(data).format('DD MMMM YYYY')
            //                 }
            //                 return "";
            //             }
            //         },
            //         {
            //             data: 'exp_tbst',
            //             name: 'exp_tbst',
            //             orderable: true,
            //             searchable: true,
            //             render: function(data, type, row) {
            //                 if (data) {
            //                     return dayjs(data).format('DD MMMM YYYY')
            //                 }
            //                 return "";
            //             }
            //         },
            //         {
            //             data: 'exp_stnk',
            //             name: 'exp_stnk',
            //             orderable: true,
            //             searchable: true,
            //             render: function(data, type, row) {
            //                 if (data) {
            //                     return dayjs(data).format('DD MMMM YYYY')
            //                 }
            //                 return "";
            //             }
            //         },
            //         {
            //             data: 'exp_tax',
            //             name: 'exp_tax',
            //             orderable: true,
            //             searchable: true,
            //             render: function(data, type, row) {
            //                 if (data) {
            //                     return dayjs(data).format('DD MMMM YYYY')
            //                 }
            //                 return "";
            //             }
            //         },
            //         {
            //             data: 'exp_comm',
            //             name: 'exp_comm',
            //             orderable: true,
            //             searchable: true,
            //             render: function(data, type, row) {
            //                 if (data) {
            //                     return dayjs(data).format('DD MMMM YYYY')
            //                 }
            //                 return "";
            //             }
            //         },
            //         {
            //             data: 'action',
            //             name: 'action',
            //             orderable: false,
            //             searchable: false,
            //             width: '100px',
            //             className: 'text-center',
            //             targets: '_all'
            //         }
            //     ],
            // });

            var table = $('#table-data').DataTable({
                scrollCollapse: true,
                responsive: false,
                scrollX: true,
                "lengthMenu": [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: ajax,
                    data: function(d) {
                        d.typeUnit = $('#typeUnit').val();
                        d.location = $('#location').val();
                        d.from = $('#from').val();
                        d.to = $('#to').val();
                    }
                },
                "createdRow": function(row, data, dataIndex) {
                    const getBgColor = (dateString) => {
                        if (!dateString) return null;
                        const targetDate = dayjs(dateString);
                        const now = dayjs();
                        const diffInDays = targetDate.diff(now, 'day');

                        // if (diffInDays <= 30) return {
                        //     bg: '#ff0000',
                        //     text: 'white'
                        // };
                        // if (diffInDays <= 45) return {
                        //     bg: '#ffff00',
                        //     text: 'black'
                        // };
                        // if (diffInDays <= 60) return {
                        //     bg: '#00ff00',
                        //     text: 'black'
                        // };

                        if (diffInDays <= 30) {
                            return {
                                bg: '#ff0000',
                                text: 'white'
                            }; // Merah
                        } else if (diffInDays <= 45) {
                            return {
                                bg: '#ffff00',
                                text: 'black'
                            }; // Kuning
                        } else {
                            return {
                                bg: '#00ff00',
                                text: 'black'
                            }; // Hijau
                        }
                        return null;
                    };
                    const dateFields = ['exp_crane', 'exp_fuel_issue', 'exp_tbst', 'exp_stnk',
                        'exp_tax', 'exp_comm'
                    ];
                    const startColIndex = 9;

                    dateFields.forEach((field, i) => {
                        const res = getBgColor(data[field]);
                        if (res) {
                            $('td', row).eq(startColIndex + i).css({
                                'background-color': res.bg,
                                'color': res.text,
                                'font-weight': 'bold'
                            });
                        }
                    });
                },
                "columns": [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'dt-center'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'vehicle_no',
                        name: 'vehicle_no'
                    },
                    {
                        data: 'chassis_no',
                        name: 'chassis_no'
                    },
                    {
                        data: 'registration_no',
                        name: 'registration_no'
                    },
                    {
                        data: 'code_access',
                        name: 'code_access'
                    },
                    {
                        data: 'plr_no',
                        name: 'plr_no'
                    },
                    {
                        data: 'banlaw_no',
                        name: 'banlaw_no'
                    },
                    {
                        data: 'exp_crane',
                        render: (d) => d ? dayjs(d).format('DD MMMM YYYY') : ''
                    },
                    {
                        data: 'exp_fuel_issue',
                        render: (d) => d ? dayjs(d).format('DD MMMM YYYY') : ''
                    },
                    {
                        data: 'exp_tbst',
                        render: (d) => d ? dayjs(d).format('DD MMMM YYYY') : ''
                    },
                    {
                        data: 'exp_stnk',
                        render: (d) => d ? dayjs(d).format('DD MMMM YYYY') : ''
                    },
                    {
                        data: 'exp_tax',
                        render: (d) => d ? dayjs(d).format('DD MMMM YYYY') : ''
                    },
                    {
                        data: 'exp_comm',
                        render: (d) => d ? dayjs(d).format('DD MMMM YYYY') : ''
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center'
                    }
                ],
            });

            $(document).on('click', '.editButton', function() {
                unitId = $(this).data('id');
                $('#modal-header').text('Edit Unit');
                $('#id').val(unitId);
                let url = '{{ route('unitexpired.show', ':_id') }}';
                url = url.replace(':_id', unitId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#modal-header').text('Edit Unit ' + response.data.vehicle_no);
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

            $("#location").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
                $('#table-data').DataTable().draw();
            });

            $(".datepicker").flatpickr();

            $("#from").on('change', function() {
                $('#table-data').DataTable().draw();
            });

            $("#to").on('change', function() {
                $('#table-data').DataTable().draw();
            });
        });

        $('#saveButton').on('click', function() {
            disableButton();
            var formData = new FormData($('#formModal').find('form')[0]);
            var url = '{{ route('unitexpired.update', ':_id') }}';
            url = url.replace(':_id', unitId);
            var type = 'POST';
            var title = "Saved!";
            title = "Updated!";
            formData.append('_method', 'PUT');
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
        });

        $('#formModal').on('hidden.bs.modal', function() {
            unitId = '';
            enableButton();
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
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
