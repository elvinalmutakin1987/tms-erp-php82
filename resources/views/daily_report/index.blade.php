@extends('partials.main')

@section('css')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />

    <style>
        #formModal .modal-content {
            max-height: calc(100vh - 1rem);
        }

        #formModal .modal-body {
            max-height: calc(100vh - 180px);
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        #formModal .modal-body table {
            min-width: 900px;
        }

        .select2-container--open {
            z-index: 1065 !important;
        }

        .flatpickr-calendar {
            z-index: 1066 !important;
        }
    </style>
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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Report"><i
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
                            <table id="table-data" class="table table-striped table-bordered" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Report Number</th>
                                        <th>Date</th>
                                        <th>Unit</th>
                                        <th>Type</th>
                                        <th>Total KM</th>
                                        <th>Duration</th>
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

    @include('daily_report.modal')

    @include('daily_report.modal-detail')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const saveButton = document.getElementById('saveButton');
        var reportId = '';
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
                        data: 'report_no',
                        name: 'report_no',
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
                        data: 'type',
                        name: 'type',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == "LCT") {
                                return '<span class="badge bg-success" style="font-size: 13px">' +
                                    data + '</span>';
                            } else {
                                return '<span class="badge bg-info" style="font-size: 13px">' +
                                    data + '</span>';
                            }
                        }
                    },
                    {
                        data: 'total_km_duration',
                        name: 'total_km_duration',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (row.type == "Non LCT") {
                                return numbro(data).format({
                                    thousandSeparated: true
                                });
                            }
                            return "";
                        }
                    },
                    {
                        data: 'total_km_duration',
                        name: 'total_km_duration',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (row.type == "LCT") {
                                return data;
                            }
                            return "";
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
                reportId = $(this).data('id');
                $('#modal-header').text('Edit Report');
                $('#id').val(reportId);
                let url = '{{ route('dailyreport.show', ':_id') }}';
                url = url.replace(':_id', reportId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit Report');
                        $("#date").val(response.data.date);
                        $("#unit_id").val(response.data.unit_id).trigger('change');
                        $("#shift").val(response.data.shift).trigger('change');
                        $("#remarks").val(response.data.remarks);
                        $('#request_token').val(response.data.request_token);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $(document).on('click', '.detailButton', function() {
                $('#modal-detail-header').text('Detail Report');
                let url = '{{ route('dailyreport.get_detail', ':_id') }}';
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

            $(".datepicker").flatpickr({
                allowInput: true
            });

            $("#date_start").on('change', function() {
                $('#table-data').DataTable().draw();
            });

            $("#date_end").on('change', function() {
                $('#table-data').DataTable().draw();
            });

            gen_select2();

            initUnitTopSelect2();
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
                    let url = '{{ route('dailyreport.destroy', ':_id') }}';
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
            var url = '{{ route('dailyreport.store') }}';
            var type = 'POST';
            if (reportId != '') {
                url = '{{ route('dailyreport.update', ':_id') }}';
                url = url.replace(':_id', reportId);
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
                            reportId = '';
                            $('#formModal').modal('hide');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    enableButton();
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

            $("#div-form").html(`
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="visually">Loading...</span>
                    `);
            setTimeout(function() {
                const isEdit = reportId != '';
                const url = isEdit ?
                    '{{ route('dailyreport.get_form_edit', ':_id') }}'.replace(':_id',
                        reportId) :
                    '{{ route('dailyreport.get_form_add') }}';
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#div-form").html(response.html);

                        requestAnimationFrame(function() {
                            initDailyReportAjaxForm(document.getElementById(
                                'div-form'));
                            initUnitSelect2();

                            const currentModalEl = document.getElementById('formModal');
                            if (currentModalEl && window.bootstrap) {
                                bootstrap.Modal.getOrCreateInstance(currentModalEl)
                                    .handleUpdate();
                            }
                        });

                        const titleText = isEdit ? 'Edit Report' : 'Add Report';
                        const number = isEdit ? response.report_no : response
                            .report_prev_no;

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
            reportId = '';
            unitId = '';
            enableButton();
            $('#request_token').val("");
            $('#div-form').html('');
            $("#unit_id")
                .val(null)
                .empty()
                .trigger('change');
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').on('click', function() {
            $('#formDetail').modal('hide');
            $('#modal-detail-body').html("");
        });

        function gen_select2() {
            // $('.select-select').each(function() {
            //     const $el = $(this);
            //     $el.select2({
            //             theme: "bootstrap-5",
            //             dropdownParent: $(
            //                 '#formModal'),
            //             width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
            //                 'style'),
            //             selectOnClose: false,
            //             minimumResultsForSearch: 0,
            //         })
            //         .on('select2:open', function() {
            //             setTimeout(function() {
            //                 const $search = $('.select2-container--open .select2-search__field');
            //                 $search.trigger('focus');
            //                 $('.select2-container--open').css('z-index', 1056);
            //             }, 0);
            //         });
            // });
        }

        function initUnitSelect2() {
            const $unit_id = $('#unit_id');

            if (!$unit_id.length) {
                return;
            }

            const selectedValue = $unit_id.val();

            if ($unit_id.hasClass('select2-hidden-accessible')) {
                $unit_id.select2('destroy');
            }

            $unit_id.off('.unit');

            $unit_id.select2({
                theme: "bootstrap-5",
                dropdownParent: $('#formModal'),
                width: $('#unit_id').data('width') ? $('#unit_id').data('width') : (
                    $(
                        '#unit_id').hasClass(
                        'w-100') ? '100%' : 'style'),
                placeholder: 'Choose Unit',
                allowClear: true,
                selectOnClose: false,
                ajax: {
                    url: '{{ route('dailyreport.get_unit_all') }}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    },
                    cache: true
                }
            }).on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-container--open .select2-search__field').trigger('focus');
                    $('.select2-container--open').css('z-index', 1056);
                }, 0);
            }).on('change', function() {
                const modalEl = document.getElementById('formModal');
                const modalBody = modalEl.querySelector('.modal-body');
                const lastScrollTop = modalBody ? modalBody.scrollTop : 0;

                $(this).select2('close');
                $(this).blur();

                if (document.activeElement) {
                    document.activeElement.blur();
                }

                $("#div-form").html(`
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="visually">Loading...</span>
                                `);
                var url = '{{ route('dailyreport.get_form_add') }}';
                if (reportId != '') {
                    url = '{{ route('dailyreport.get_form_edit', ':_id') }}'.replace(':_id',
                        reportId);
                }
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        unit_id: $(this).val()
                    },
                    success: function(response) {
                        $("#div-form").html(response.html);

                        requestAnimationFrame(function() {
                            initDailyReportAjaxForm(document.getElementById('div-form'));
                            initUnitSelect2();

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

            if (selectedValue) {
                $unit_id.val(selectedValue).trigger('change.select2');
            }

            $unit_id.on('select2:open.unit', function() {
                setTimeout(function() {
                    const search = document.querySelector(
                        '.select2-container--open .select2-search__field'
                    );

                    if (search) {
                        search.focus({
                            preventScroll: true
                        });
                    }

                    $('.select2-container--open').css('z-index', 1056);
                }, 0);
            });

            $unit_id.on('change.unit', function() {
                const unitId = $(this).val();
            });
        }

        function initUnitTopSelect2() {
            const $unit = $('#unit');

            if (!$unit.length) {
                return;
            }

            const selectedValue = $unit.val();

            if ($unit.hasClass('select2-hidden-accessible')) {
                $unit.select2('destroy');
            }

            $unit.off('.unit');

            $unit.select2({
                theme: "bootstrap-5",
                width: $('#unit').data('width') ? $('#unit').data('width') : (
                    $(
                        '#unit').hasClass(
                        'w-100') ? '100%' : 'style'),
                placeholder: 'All Unit',
                allowClear: true,
                selectOnClose: false,
                ajax: {
                    url: '{{ route('dailyreport.get_unit_all') }}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    },
                    cache: true
                }
            }).on('change', function() {
                $('#table-data').DataTable().draw();
            });

            if (selectedValue) {
                $unit.val(selectedValue).trigger('change.select2');
            }
        }



        function initDailyReportAjaxForm(root) {
            root = root || document.getElementById('div-form');

            if (!root) {
                return;
            }

            const modalEl = document.getElementById('formModal');
            const modalBody = modalEl ? modalEl.querySelector('.modal-body') : null;

            initAjaxSelect2(root, modalEl, modalBody);
            initAjaxDetailUnitSelect2(root, modalBody);
            initAjaxTimepicker(root, modalEl, modalBody);
            initAjaxNumberFormat(root);
            initAjaxUnitTable(root);
        }

        function initAjaxSelect2(root, modalEl, modalBody) {
            if (!window.jQuery || !$.fn.select2) {
                return;
            }

            $(root).find('.select-select').not('#_unit_id').each(function() {
                const $el = $(this);

                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }

                $el.off('.ajaxFormSelect');

                const allowClear = ['_item', '_uom_1', '_uom_2'].includes($el.attr('id'));

                $el.select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#formModal'),
                    width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
                        'style'),
                    placeholder: $el.data('placeholder') || 'Choose',
                    allowClear: allowClear,
                    selectOnClose: false,
                    minimumResultsForSearch: 0
                });

                $el.on('select2:open.ajaxFormSelect', function() {
                    const lastScrollTop = modalBody ? modalBody.scrollTop : 0;

                    setTimeout(function() {
                        const search = document.querySelector(
                            '.select2-container--open .select2-search__field');

                        if (search) {
                            search.focus({
                                preventScroll: true
                            });
                        }

                        $('.select2-container--open').css('z-index', 1065);

                        if (modalBody) {
                            modalBody.scrollTop = lastScrollTop;
                        }
                    }, 0);
                });
            });
        }

        function initAjaxDetailUnitSelect2(root, modalBody) {
            if (!window.jQuery || !$.fn.select2) {
                return;
            }

            const $unitDetail = $(root).find('#_unit_id');

            if (!$unitDetail.length) {
                return;
            }

            if ($unitDetail.hasClass('select2-hidden-accessible')) {
                $unitDetail.select2('destroy');
            }

            $unitDetail.off('.ajaxDetailUnit');
            $unitDetail.empty().append(new Option('', '', true, true));

            $unitDetail.select2({
                theme: "bootstrap-5",
                dropdownParent: $('#formModal'),
                width: $unitDetail.data('width') ? $unitDetail.data('width') : ($unitDetail.hasClass('w-100') ?
                    '100%' : 'style'),
                placeholder: 'Choose Unit',
                allowClear: true,
                selectOnClose: false,
                ajax: {
                    url: '{{ route('dailyreport.get_unit_all') }}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    },
                    cache: true
                }
            });

            $unitDetail.on('select2:open.ajaxDetailUnit', function() {
                const lastScrollTop = modalBody ? modalBody.scrollTop : 0;

                setTimeout(function() {
                    const search = document.querySelector(
                        '.select2-container--open .select2-search__field');

                    if (search) {
                        search.focus({
                            preventScroll: true
                        });
                    }

                    $('.select2-container--open').css('z-index', 1065);

                    if (modalBody) {
                        modalBody.scrollTop = lastScrollTop;
                    }
                }, 0);
            });
        }

        function initAjaxTimepicker(root, modalEl, modalBody) {
            if (typeof flatpickr === 'undefined') {
                return;
            }

            root.querySelectorAll('.timepicker').forEach(function(input) {
                if (input._flatpickr) {
                    input._flatpickr.destroy();
                }

                flatpickr(input, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    minuteIncrement: 1,
                    disableMobile: true,
                    allowInput: true,
                    appendTo: modalEl || document.body,
                    positionElement: input,
                    position: "below left",
                    onOpen: function(selectedDates, dateStr, instance) {
                        instance._scrollTop = modalBody ? modalBody.scrollTop : 0;
                    },
                    onClose: function(selectedDates, dateStr, instance) {
                        if (modalBody && typeof instance._scrollTop !== 'undefined') {
                            modalBody.scrollTop = instance._scrollTop;
                        }
                    },
                    onReady: function(selectedDates, dateStr, instance) {
                        instance.calendarContainer.style.zIndex = "1066";
                    }
                });
            });
        }

        function initAjaxNumberFormat(root) {
            let isFmt = false;
            let userDecSep = null;

            function sanitize(value) {
                return (value ?? '').toString().replace(/[^0-9.,]/g, '');
            }

            function groupThousands(digits, sep) {
                digits = digits.replace(/^0+(?=\d)/, '');

                if (digits === '') {
                    digits = '0';
                }

                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, sep);
            }

            function countDigitsLeft(str, pos) {
                return (str.slice(0, pos).match(/\d/g) || []).length;
            }

            function caretByDigits(str, digitCount) {
                let count = 0;

                for (let i = 0; i < str.length; i++) {
                    if (/\d/.test(str[i])) {
                        count++;
                    }

                    if (count >= digitCount) {
                        return i + 1;
                    }
                }

                return str.length;
            }

            function textKeyDown(e) {
                if (e.ctrlKey || e.metaKey || e.altKey) {
                    return;
                }

                const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Tab', 'Enter'];

                if (allowedKeys.includes(e.key)) {
                    return;
                }

                if (/^[0-9.,]$/.test(e.key)) {
                    return;
                }

                e.preventDefault();
            }

            function unformatNumber(value) {
                if (window.numbro) {
                    return numbro.unformat(value);
                }

                return sanitize(value).replace(/,/g, '');
            }

            function updateKmTotal() {
                const kmStart = document.getElementById('km_start');
                const kmFinish = document.getElementById('km_finish');
                const kmTotal = document.getElementById('km_total');
                const kmTotalDisplay = document.getElementById('_km_total');

                if (!kmStart || !kmFinish || !kmTotal || !kmTotalDisplay) {
                    return;
                }

                if (kmStart.value === '' || kmFinish.value === '') {
                    kmTotal.value = '';
                    kmTotalDisplay.value = '';
                    return;
                }

                const total = (parseFloat(kmFinish.value) || 0) - (parseFloat(kmStart.value) || 0);
                kmTotal.value = total;

                if (window.numbro) {
                    kmTotalDisplay.value = numbro(total).format({
                        thousandSeparated: true,
                        mantissa: 0
                    });
                } else {
                    kmTotalDisplay.value = total;
                }
            }

            function textInput(hiddenId, e) {
                if (isFmt) {
                    return;
                }

                isFmt = true;

                const el = e.target;
                const raw = el.value || '';
                const caretRaw = typeof el.selectionStart === 'number' ? el.selectionStart : raw.length;
                const inserted = e.inputType === 'insertText' && e.data ? e.data : '';
                const prevDecSep = userDecSep;
                const justTypedSep = inserted === '.' || inserted === ',';
                const san = sanitize(raw);
                const leftSan = sanitize(raw.slice(0, caretRaw));
                const caretSan = leftSan.length;

                if (userDecSep && !san.includes(userDecSep)) {
                    userDecSep = null;
                }

                if (!prevDecSep && justTypedSep) {
                    userDecSep = inserted;
                }

                const digitsLeft = countDigitsLeft(san, caretSan);
                let intDigits = '';
                let fracDigits = '';
                let keepDec = false;

                if (userDecSep && san.includes(userDecSep)) {
                    const pos = san.indexOf(userDecSep);
                    keepDec = true;
                    intDigits = san.slice(0, pos).replace(/[.,]/g, '');
                    fracDigits = san.slice(pos + 1).replace(/[.,]/g, '');

                    if (intDigits === '') {
                        intDigits = '0';
                    }
                } else {
                    intDigits = san.replace(/[.,]/g, '');
                }

                const thousandsSep = userDecSep ? (userDecSep === ',' ? '.' : ',') : ',';
                const formattedInt = groupThousands(intDigits, thousandsSep);
                const formatted = keepDec ? formattedInt + userDecSep + fracDigits : formattedInt;

                el.value = formatted;

                if (typeof el.setSelectionRange === 'function') {
                    if (!prevDecSep && justTypedSep && keepDec) {
                        const newCaret = formatted.indexOf(userDecSep) + 1;
                        el.setSelectionRange(newCaret, newCaret);
                    } else {
                        const newCaret = caretByDigits(formatted, digitsLeft);
                        el.setSelectionRange(newCaret, newCaret);
                    }
                }

                const hiddenInput = document.getElementById(hiddenId);

                if (hiddenInput) {
                    hiddenInput.value = unformatNumber(el.value);
                }

                updateKmTotal();
                isFmt = false;
            }

            const inputMap = [
                ['_value_1_', '_value_1'],
                ['_value_2_', '_value_2'],
                ['_refule_liter', 'refule_liter'],
                ['_refule_km', 'refule_km']
            ];

            inputMap.forEach(function(item) {
                const displayInput = root.querySelector('#' + item[0]);
                const hiddenId = item[1];

                if (!displayInput) {
                    return;
                }

                displayInput.onkeydown = textKeyDown;
                displayInput.oninput = function(e) {
                    textInput(hiddenId, e);
                };
            });
        }

        function initAjaxUnitTable(root) {
            const addButton = root.querySelector('#addUnitButton');
            const tableUnit = root.querySelector('#tableUnit');

            if (!addButton || !tableUnit) {
                return;
            }

            addButton.onclick = function() {
                const tbody = tableUnit.querySelector('tbody');
                const unitSelect = root.querySelector('#_unit_id');
                const itemSelect = root.querySelector('#_item');
                const uom1Select = root.querySelector('#_uom_1');
                const uom2Select = root.querySelector('#_uom_2');

                const unitId = unitSelect ? unitSelect.value : '';
                const unitSelectData = unitSelect && window.jQuery && $.fn.select2 ? $(unitSelect).select2('data') : [];
                const unitName = unitSelectData.length ? unitSelectData[0].text : (unitSelect && unitSelect.options[
                    unitSelect.selectedIndex] ? unitSelect.options[unitSelect.selectedIndex].text : '');
                const item = itemSelect ? itemSelect.value : '';
                const uom1 = uom1Select ? uom1Select.value : '';
                const uom2 = uom2Select ? uom2Select.value : '';
                const value1 = root.querySelector('#_value_1') ? root.querySelector('#_value_1').value : '';
                const value1Display = root.querySelector('#_value_1_') ? root.querySelector('#_value_1_').value : '';
                const value2 = root.querySelector('#_value_2') ? root.querySelector('#_value_2').value : '';
                const value2Display = root.querySelector('#_value_2_') ? root.querySelector('#_value_2_').value : '';
                const tr = document.createElement('tr');

                tr.innerHTML = `
                    <td class="p-1 align-middle row-number">#</td>
                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="detail_unit_id[]" readonly value="${escapeHtml(unitId)}">
                        <input type="text" class="form-control" name="unit_name[]" readonly value="${escapeHtml(unitName)}">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="text" class="form-control" name="item[]" readonly value="${escapeHtml(item)}">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="text" class="form-control" name="uom_1[]" readonly value="${escapeHtml(uom1)}">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="value_1[]" readonly value="${escapeHtml(value1)}">
                        <input type="text" class="form-control" name="value_1__[]" readonly value="${escapeHtml(value1Display)}">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="text" class="form-control" name="uom_2[]" readonly value="${escapeHtml(uom2)}">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="value_2[]" readonly value="${escapeHtml(value2)}">
                        <input type="text" class="form-control" name="value_2__[]" readonly value="${escapeHtml(value2Display)}">
                    </td>
                    <td class="text-center p-1 align-middle">
                        <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row"></button>
                    </td>
                `;

                tbody.appendChild(tr);

                resetTableUnitInputRow(root);

                renumberAjaxUnitRows(root);
            };

            tableUnit.onclick = function(e) {
                const deleteButton = e.target.closest('.delete-row');

                if (!deleteButton) {
                    return;
                }

                const row = deleteButton.closest('tr');

                if (row) {
                    row.remove();
                }

                renumberAjaxUnitRows(root);
            };
        }

        function resetTableUnitInputRow(root) {
            const resetSelects = ['#_unit_id', '#_item', '#_uom_1', '#_uom_2'];
            const resetInputs = ['#_value_1', '#_value_1_', '#_value_2', '#_value_2_'];

            resetSelects.forEach(function(selector) {
                const select = root.querySelector(selector);

                if (!select) {
                    return;
                }

                if (window.jQuery && $.fn.select2 && $(select).hasClass('select2-hidden-accessible')) {
                    $(select).val(null).trigger('change.select2');
                } else {
                    select.value = '';
                }
            });

            resetInputs.forEach(function(selector) {
                clearInputValue(root, selector);
            });
        }

        function renumberAjaxUnitRows(root) {
            let no = 13;

            root.querySelectorAll('#tableUnit > tbody > tr').forEach(function(row) {
                if (row.classList.contains('fixed-row')) {
                    const fixedNumberCell = row.querySelector('.row-number');

                    if (fixedNumberCell) {
                        fixedNumberCell.textContent = '';
                    }

                    return;
                }

                const numberCell = row.querySelector('.row-number');

                if (numberCell) {
                    numberCell.textContent = no;
                    no++;
                }
            });
        }

        function clearInputValue(root, selector) {
            const input = root.querySelector(selector);

            if (input) {
                input.value = '';
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function disableButton() {
            saveButton.disabled = true;
        }

        function enableButton() {
            saveButton.disabled = false;
        }

        initUnitSelect2();

        $('#formModal').off('shown.bs.modal.select2').on('shown.bs.modal.select2', function() {
            initUnitSelect2();
        });
    </script>
    <!--app JS-->
@endsection
