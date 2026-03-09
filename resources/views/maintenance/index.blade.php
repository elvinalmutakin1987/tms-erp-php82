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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Maintenance"><i
                                            class='bx bxs-plus-square'></i>New</a>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="unit" name="unit">
                                        <option value="All">All Unit</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="vendor" name="vendor">
                                        <option value="All">All Vendor</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="_status" name="_status">
                                        <option value="All">All Status</option>
                                        <option value="Open">Open</option>
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
                            <table id="table-data" class="table" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Maintenance Number</th>
                                        <th>Date</th>
                                        <th>Unit</th>
                                        <th>Start</th>
                                        <th>Finish</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th>Action</th>
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

    @include('maintenance.modal')

    @include('maintenance.modal-detail')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        var maintenanceId = '';
        var unitId = '';
        var vendorId = '';
        var status = '';
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
                        d.unit_id = $('#unit').val();
                        d.client_vendor_id = $('#vendor').val();
                        d.status = $('#_status').val();
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
                        data: 'maintenance_no',
                        name: 'maintenance_no',
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
                        data: 'unit',
                        name: 'unit',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'start',
                        name: 'start',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'finish',
                        name: 'finish',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'work_duration',
                        name: 'work_duration',
                        orderable: true,
                        searchable: true,
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
                            } else {
                                return '<span class="badge bg-danger" style="font-size: 13px">' +
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
                maintenanceId = $(this).data('id');
                $('#modal-header').text('Edit Maintenance');
                $('#id').val(maintenanceId);
                let url = '{{ route('maintenance.show', ':_id') }}';
                url = url.replace(':_id', maintenanceId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit Inspection');
                        $("#date").val(response.data.date);
                        $("#notes").val(response.data.notes);
                        $("#unit_id").val(response.data.unit_id).trigger('change');
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $(document).on('click', '.detailButton', function() {
                $('#modal-detail-header').text('Detail Inspection');
                let url = '{{ route('mechanicalinspection.get_detail', ':_id') }}';
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
            $(".timepicker").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 1
            });

            $("#unit").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
                $('#table-data').DataTable().draw();
            });

            $("#vendor").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
                $('#table-data').DataTable().draw();
            });


            $("#_status").select2({
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

            $.ajax({
                url: '{{ route('maintenance.get_unit_all') }}',
                type: 'GET',
                success: function(response) {
                    $('#unit').empty();
                    $('#unit').append('<option value="All">All Unit</option>');
                    $.each(response.data, function(index, unit) {
                        $('#unit').append('<option value="' + unit.id +
                            '">' +
                            unit.vehicle_no +
                            '</option>');
                    });
                    if (unitId != '') {
                        $("#unit").val(unitId).trigger('change');
                    }

                    $('#unit_id').empty();
                    $('#unit_id').append('<option value="All">All Unit</option>');
                    $.each(response.data, function(index, unit) {
                        $('#unit_id').append('<option value="' + unit.id +
                            '">' +
                            unit.vehicle_no +
                            '</option>');
                    });
                    if (unitId != '') {
                        $("#unit_id").val(unitId).trigger('change');
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

            $.ajax({
                url: '{{ route('maintenance.get_vendor_all') }}',
                type: 'GET',
                success: function(response) {
                    $('#vendor').empty();
                    $('#vendor').append('<option value="All">All Vendor</option>');
                    $.each(response.data, function(index, vendor) {
                        $('#vendor').append('<option value="' + vendor.id +
                            '">' +
                            vendor.name +
                            '</option>');
                    });
                    if (vendorId != '') {
                        $("#vendor").val(vendorId).trigger('change');
                    }

                    $('#client_vendor_id').empty();
                    $('#client_vendor_id').append('<option value="All">All Vendor</option>');
                    $.each(response.data, function(index, vendor) {
                        $('#client_vendor_id').append('<option value="' + vendor.id +
                            '">' +
                            vendor.name +
                            '</option>');
                    });
                    if (vendorId != '') {
                        $("#client_vendor_id").val(vendorId).trigger('change');
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

            $("#act").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
                $('#main_item_id').val('').trigger('change');
                let action = $("#act").val();
                $('#main_item_id').select2({
                    theme: "bootstrap-5",
                    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                        'w-100') ? '100%' : 'style',
                    allowClear: true,
                    ajax: {
                        url: '{{ route('maintenance.get_maintenance_item_by_action') }}',
                        dataType: 'json',
                        data: function(params) {
                            return {
                                term: params.term || '',
                                page: params.page || 1,
                                action: action
                            };
                        },
                        cache: true,
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
                    let url = '{{ route('maintenance.destroy', ':_id') }}';
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
            var url = '{{ route('maintenance.store') }}';
            var type = 'POST';
            if (maintenanceId != '') {
                url = '{{ route('maintenance.update', ':_id') }}';
                url = url.replace(':_id', maintenanceId);
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
                            maintenanceId = '';
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
            var tbody = $("#tableItem > tbody");
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
                if (maintenanceId != '') {
                    data = {
                        'form': 'edit',
                        'maintenance_id': maintenanceId
                    };
                    $.ajax({
                        url: '{{ route('maintenance.get_maintenance_item_list') }}',
                        data: data,
                        type: 'GET',
                        success: function(response) {
                            setTimeout(function() {
                                $('#tableItem tbody tr').not(':first').remove();
                                tbody.append(response);
                            }, 500);
                        },
                        error: function(xhr, status, error) {
                            console.log(error)
                        }
                    });
                } else {
                    $('#tableItem tbody tr').not(':first').remove();
                }
            }, 500);
        });

        $('#formModal').on('hidden.bs.modal', function() {
            maintenanceId = '';
            unitId = '';
            $('#tableItem tbody tr').not(':first').remove();
            $("#unit_id").val('All').trigger('change');
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').on('click', function() {
            $('#formDetail').modal('hide');
        });

        $('#addItemButton').on('click', function() {
            var tbody = $("#tableItem > tbody");
            var action = $("#act").val();
            var main_item_id = $("#main_item_id").val();
            var main_item = $("#main_item_id option:selected").text();
            var newRow = `
                <tr>
                    <td class="p-1 align-middle row-number">
                        #
                    </td>
                    <td class="p-1 align-middle">
                       <input type="text" class="form-control" id="action" name="action[]" readonly value="${action}">
                       
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="maintenance_item_id" name="maintenance_item_id[]" readonly value="${main_item_id}">
                       <input type="text" class="form-control" id="main_item" name="main_item[]" readonly value="${main_item}">
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
            $('#act').val("Repair").trigger('change');
            tbody.append(newRow);

            renumberRows();
        });

        $("#tableItem").on("click", ".delete-row", function() {
            $(this).closest("tr").remove();

            if ($(this).hasClass('fixed-row')) {
                return;
            }

            $(this).remove();
            renumberRows();
        });

        function renumberRows() {
            let no = 1;

            $('#tableItem > tbody > tr').each(function() {
                // row khusus tidak ikut nomor
                if ($(this).hasClass('fixed-row')) {
                    $(this).find('.row-number').text('');
                    return;
                }

                $(this).find('.row-number').text(no);
                no++;
            });
        }

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

        const $km_hm = $('#_km_hm');
        const $hour_meter = $('#_hour_meter');

        let isFmt = false;
        let userDecSep = null;

        function sanitize(s) {
            return (s ?? '').toString().replace(/[^0-9.,]/g, '');
        }

        function groupThousands(digits, sep) {
            digits = digits.replace(/^0+(?=\d)/, '');
            if (digits === '') digits = '0';
            return digits.replace(/\B(?=(\d{3})+(?!\d))/g, sep);
        }

        function countDigitsLeft(str, pos) {
            return (str.slice(0, pos).match(/\d/g) || []).length;
        }

        function caretByDigits(str, digitCount) {
            let c = 0;
            for (let i = 0; i < str.length; i++) {
                if (/\d/.test(str[i])) c++;
                if (c >= digitCount) return i + 1;
            }
            return str.length;
        }

        function textKeyDown(e) {
            if (e.ctrlKey || e.metaKey || e.altKey) return;

            const okNav = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Tab', 'Enter'];
            if (okNav.includes(e.key)) return;

            if (/^[0-9.,]$/.test(e.key)) return;

            e.preventDefault();
        }

        function textInput(key, e) {
            if (isFmt) return;
            isFmt = true;

            const el = e.target;
            const raw = el.value || '';
            const caretRaw = (typeof el.selectionStart === 'number') ? el.selectionStart : raw.length;

            const oe = e.originalEvent || e;
            const inserted = (oe && typeof oe.data === 'string') ? oe.data : '';

            const prevDecSep = userDecSep;
            const justTypedSep = (inserted === '.' || inserted === ',');

            const san = sanitize(raw);
            const leftSan = sanitize(raw.slice(0, caretRaw));
            const caretSan = leftSan.length;

            if (userDecSep && !san.includes(userDecSep)) userDecSep = null;

            const justSetDecSep = (!prevDecSep && justTypedSep);
            if (justSetDecSep) userDecSep = inserted;

            const digitsLeft = countDigitsLeft(san, caretSan);

            let intDigits = '';
            let fracDigits = '';
            let keepDec = false;

            if (userDecSep && san.includes(userDecSep)) {
                const pos = san.indexOf(userDecSep);
                keepDec = true;
                intDigits = san.slice(0, pos).replace(/[.,]/g, '');
                fracDigits = san.slice(pos + 1).replace(/[.,]/g, '');
                if (intDigits === '') intDigits = '0';
            } else {
                intDigits = san.replace(/[.,]/g, '');
            }

            const thousandsSep = userDecSep ? (userDecSep === ',' ? '.' : ',') : ',';

            const formattedInt = groupThousands(intDigits, thousandsSep);
            const formatted = keepDec ? (formattedInt + userDecSep + fracDigits) : formattedInt;

            el.value = formatted;

            if (typeof el.setSelectionRange === 'function') {
                if (justSetDecSep && keepDec) {
                    const decPosNew = formatted.indexOf(userDecSep);
                    const newCaret = decPosNew + 1;
                    el.setSelectionRange(newCaret, newCaret);
                } else {
                    const newCaret = caretByDigits(formatted, digitsLeft);
                    el.setSelectionRange(newCaret, newCaret);
                }
            }

            isFmt = false;

            $("#" + key).val(numbro.unformat(el.value));
        }

        $km_hm.on('keydown', function(e) {
            textKeyDown(e);
        });

        $km_hm.on('input', function(e) {
            textInput("km_hm", e);
        });

        $hour_meter.on('keydown', function(e) {
            textKeyDown(e);
        });

        $hour_meter.on('input', function(e) {
            textInput("hour_meter", e);
        });

        function hitungSelisihWaktu() {
            const start = document.getElementById('start').value.trim();
            const finish = document.getElementById('finish').value.trim();
            const durationField = document.getElementById('work_duration');

            if (start === '' || finish === '') {
                durationField.value = '';
                return;
            }

            const startClean = start.replace('.', ':');
            const finishClean = finish.replace('.', ':');

            const startParts = startClean.split(':').map(Number);
            const finishParts = finishClean.split(':').map(Number);

            if (
                startParts.length !== 2 || finishParts.length !== 2 ||
                isNaN(startParts[0]) || isNaN(startParts[1]) ||
                isNaN(finishParts[0]) || isNaN(finishParts[1])
            ) {
                durationField.value = '';
                return;
            }

            let startMinutes = startParts[0] * 60 + startParts[1];
            let finishMinutes = finishParts[0] * 60 + finishParts[1];

            if (finishMinutes < startMinutes) {
                finishMinutes += 24 * 60;
            }

            const diff = finishMinutes - startMinutes;
            const jam = String(Math.floor(diff / 60)).padStart(2, '0');
            const menit = String(diff % 60).padStart(2, '0');

            durationField.value = `${jam}:${menit}`;
        }

        document.getElementById('start').addEventListener('input', hitungSelisihWaktu);
        document.getElementById('finish').addEventListener('input', hitungSelisihWaktu);
        document.getElementById('start').addEventListener('change', hitungSelisihWaktu);
        document.getElementById('finish').addEventListener('change', hitungSelisihWaktu);
    </script>
    <!--app JS-->
@endsection
