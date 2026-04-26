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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add P2H"><i
                                            class='bx bxs-plus-square'></i>New</a>
                                </div>
                                <div class="col-2">
                                    <select class="form-select" id="unit" name="unit">
                                        <option value="All">All Unit</option>
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
                                        <th>P2H Number</th>
                                        <th>Date</th>
                                        <th>Unit</th>
                                        <th>Driver</th>
                                        <th>Shift</th>
                                        <th>Result</th>
                                        <th>Condition</th>
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

    @include('p2h.modal')

    @include('p2h.modal-detail')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const saveButton = document.getElementById('saveButton');
        var p2hId = '';
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
                        d.unit_id = $('#unit').val();
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
                        data: 'p2h_no',
                        name: 'p2h_no',
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
                        data: 'driver',
                        name: 'driver',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'shift',
                        name: 'shift',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'result',
                        name: 'result',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == 100) {
                                return '<span class="badge bg-success" style="font-size: 13px">Fit</span>';
                            } else {
                                return '<span class="badge bg-danger" style="font-size: 13px">' +
                                    row.broken + ' broken</span>';
                            }
                        }
                    },
                    {
                        data: 'condition',
                        name: 'condition',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data >= 80) {
                                return '<span class="badge bg-success" style="font-size: 13px">' +
                                    data + '%</span>';
                            } else if (data >= 60) {
                                return '<span class="badge bg-danger" style="font-size: 13px">' +
                                    data + '%</span>';
                            } else if (data >= 40) {
                                return '<span class="badge bg-danger" style="font-size: 13px">' +
                                    data + '%</span>';
                            } else if (data >= 20) {
                                return '<span class="badge bg-warning" style="font-size: 13px">' +
                                    data + '%</span>';
                            } else {
                                return '<span class="badge bg-danger" style="font-size: 13px">' +
                                    data + '%</span>';
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
                p2hId = $(this).data('id');
                $('#modal-header').text('Edit P2H');
                $('#id').val(p2hId);
                let url = '{{ route('p2h.show', ':_id') }}';
                url = url.replace(':_id', p2hId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit P2H');
                        $("#driver").val(response.data.driver);
                        $("#date").val(response.data.date);
                        $("#unit_id").val(response.data.unit_id).trigger('change');
                        $("#shift").val(response.data.shift).trigger('change');
                        $('#km_start').val(response.data.km_start);
                        $('#_km_start').val(numbro(response.data.km_start).format({
                            thousandSeparated: true
                        }));
                        $('#km_finish').val(response.data.km_finish);
                        $('#_km_finish').val(numbro(response.data.km_finish).format({
                            thousandSeparated: true
                        }));
                        $("#request_token").val(response.data.request_token);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $(document).on('click', '.detailButton', function() {
                $('#modal-detail-header').text('Detail P2H');
                let url = '{{ route('p2h.get_detail', ':_id') }}';
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

            $("#unit").select2({
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
                url: '{{ route('p2h.get_unit_all') }}',
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
                    let url = '{{ route('p2h.destroy', ':_id') }}';
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
            var url = '{{ route('p2h.store') }}';
            var type = 'POST';
            if (p2hId != '') {
                url = '{{ route('p2h.update', ':_id') }}';
                url = url.replace(':_id', p2hId);
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
                            p2hId = '';
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
                const isEdit = p2hId != '';
                const url = isEdit ?
                    '{{ route('p2h.get_table_edit', ':_id') }}'.replace(':_id', p2hId) :
                    '{{ route('p2h.get_table_add') }}';

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#tableItem > tbody").html(response.html);

                        const titleText = isEdit ? 'Edit P2H' : 'Add P2H';
                        const number = isEdit ? response.p2h_no : response.p2h_prev_no;

                        $('#modal-header').html(titleText + ' -&nbsp;<b>' + number + '</b>');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });

                if (!isEdit) {
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
            }, 500);
        });

        $('#formModal').on('hidden.bs.modal', function() {
            p2hId = '';
            unitId = '';
            enableButton();
            $("#request_token").val();
            $('#tableItem tbody').empty();
            $("#unit_id").val('All').trigger('change');
            $("#shift").val('Day').trigger('change');
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

        const $km_start = $('#_km_start');
        const $km_finish = $('#_km_finish');

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

        $km_start.on('keydown', function(e) {
            textKeyDown(e);
        });

        $km_start.on('input', function(e) {
            textInput("km_start", e);
        });

        $km_finish.on('keydown', function(e) {
            textKeyDown(e);
        });

        $km_finish.on('input', function(e) {
            textInput("km_finish", e);
        });

        $("#unit_id").on('change', function() {
            const modalEl = document.getElementById('formModal');
            const modalBody = modalEl.querySelector('.modal-body');
            const lastScrollTop = modalBody ? modalBody.scrollTop : 0;

            $(this).select2('close');
            $(this).blur();

            if (document.activeElement) {
                document.activeElement.blur();
            }

            const $tbody = $("#tableItem > tbody");
            $tbody.html(`
                <tr>
                    <td colspan="5" class="text-center">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="visually-hidden">Loading...</span>
                    </td>
                </tr>
            `);
            let url = '{{ route('p2h.get_p2h_item') }}';
            if ($(this).val() == 'All') {
                url = '{{ route('p2h.get_table_add') }}';
            }
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    unit_id: $(this).val()
                },
                success: function(response) {
                    $tbody.html(response.html);

                    requestAnimationFrame(function() {
                        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modalInstance.handleUpdate();

                        if (modalBody) {
                            modalBody.scrollTop = lastScrollTop;
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    $tbody.empty();
                }
            });
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
