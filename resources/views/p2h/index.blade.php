@extends('partials.main')

@section('css')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet" />

    <style>
        #formModal .modal-dialog {
            height: calc(100vh - 1rem);
            max-height: calc(100vh - 1rem);
            margin-top: .5rem;
            margin-bottom: .5rem;
        }

        #formModal .modal-content {
            height: 100%;
            max-height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        #formModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto !important;
            overflow-x: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
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
                            <table id="table-data" class="table table-striped table-bordered" style="width:100%">
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

        let p2hId = '';
        let unitId = '';
        let pendingEditData = null;
        let unitData = null;
        let pendingEditDataSeq = 0;

        let isClosingFormModal = false;
        let isHydratingForm = false;
        let modalLoadSeq = 0;

        let tableAjaxRequest = null;
        let p2hShowRequest = null;
        let tokenAjaxRequest = null;
        let itemAjaxRequest = null;

        $(document).ready(function() {
            initP2hDataTable();
            initPageDatepicker();
            initUnitFilterSelect2();
            initModalSelect2();
            initModalUnitSelect2();

            bindP2hPageEvents();
            bindP2hModalEvents();
            bindP2hSave();
            bindKmFormatting();
        });

        function initP2hDataTable() {
            const ajax = '{{ url()->current() }}';

            $('#table-data').DataTable({
                scrollCollapse: true,
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajax,
                    data: function(d) {
                        d.unit_id = $('#unit').val() || 'All';
                        d.date_start = $('#date_start').val();
                        d.date_end = $('#date_end').val();
                    }
                },
                columns: [{
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
                        searchable: true
                    },
                    {
                        data: 'date',
                        name: 'date',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'unit',
                        name: 'unit',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'driver',
                        name: 'driver',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'shift',
                        name: 'shift',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'result',
                        name: 'result',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == 100) {
                                return '<span class="badge bg-success" style="font-size: 13px">Fit</span>';
                            }

                            return '<span class="badge bg-danger" style="font-size: 13px">' +
                                row.broken + ' broken</span>';
                        }
                    },
                    {
                        data: 'condition',
                        name: 'condition',
                        orderable: true,
                        searchable: true,
                        render: function(data) {
                            if (data >= 80) {
                                return '<span class="badge bg-success" style="font-size: 13px">' +
                                    data + '%</span>';
                            }

                            if (data >= 20) {
                                return '<span class="badge bg-warning" style="font-size: 13px">' +
                                    data + '%</span>';
                            }

                            return '<span class="badge bg-danger" style="font-size: 13px">' +
                                data + '%</span>';
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
                ]
            });
        }

        function bindP2hPageEvents() {
            $('#openModalButton').off('click.p2hAdd').on('click.p2hAdd', function() {
                p2hId = '';
                unitId = '';
                pendingEditData = null;
                pendingEditDataSeq = 0;
                $('#id').val('');
            });

            $(document).off('click.p2hEdit', '.editButton').on('click.p2hEdit', '.editButton', function() {
                p2hId = String($(this).data('id') || '');
                unitId = '';
                pendingEditData = null;
                pendingEditDataSeq = 0;

                $('#id').val(p2hId);
                $('#modal-header').text('Edit P2H');
            });

            $(document).off('click.p2hDetail', '.detailButton').on('click.p2hDetail', '.detailButton', function() {
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

            $("#date_start").off('change.p2hFilter').on('change.p2hFilter', function() {
                $('#table-data').DataTable().draw();
            });

            $("#date_end").off('change.p2hFilter').on('change.p2hFilter', function() {
                $('#table-data').DataTable().draw();
            });

            $('#cancelButton').off('click.p2hCancel').on('click.p2hCancel', function() {
                $('#formModal').modal('hide');
            });

            $('#cancelDetailButton').off('click.p2hCancelDetail').on('click.p2hCancelDetail', function() {
                $('#formDetail').modal('hide');
                $('#modal-detail-body').html("");
            });
        }

        function bindP2hModalEvents() {
            $('#formModal').off('show.bs.modal.p2h').on('show.bs.modal.p2h', function(e) {
                isClosingFormModal = false;

                // Penting: ambil ID langsung dari tombol pemicu modal.
                // Pada beberapa kondisi, Bootstrap membuka modal sebelum delegated click handler kita selesai.
                const relatedButton = e.relatedTarget ? $(e.relatedTarget) : null;
                const relatedId = relatedButton && relatedButton.length ? relatedButton.data('id') : '';
                const isTriggeredByEdit = relatedButton && relatedButton.length && relatedButton.hasClass(
                    'editButton');
                const isTriggeredByAdd = relatedButton && relatedButton.length && relatedButton.attr('id') ===
                    'openModalButton';

                if (isTriggeredByEdit && relatedId) {
                    p2hId = String(relatedId);
                    unitId = '';
                    pendingEditData = null;
                    pendingEditDataSeq = 0;
                    $('#id').val(p2hId);
                }

                if (isTriggeredByAdd) {
                    p2hId = '';
                    unitId = '';
                    pendingEditData = null;
                    pendingEditDataSeq = 0;
                    $('#id').val('');
                }

                const currentSeq = ++modalLoadSeq;
                const isEdit = p2hId !== '';
                const title = isEdit ? 'Edit P2H' : (relatedButton ? relatedButton.data('title') : 'Add P2H');

                resetP2hFormForOpen(isEdit);
                $('#id').val(p2hId);
                $('#modal-header').text(title || (isEdit ? 'Edit P2H' : 'Add P2H'));
                ensureFormModalScrollable();

                loadInitialP2hTable(currentSeq);

                if (isEdit) {
                    fetchP2hEditData(currentSeq);
                } else {
                    generateRequestToken();
                }
            });

            $('#formModal').off('shown.bs.modal.p2hScroll').on('shown.bs.modal.p2hScroll', function() {
                ensureFormModalScrollable();
                updateModalLayoutAndScroll();
                applyPendingP2hEditData(modalLoadSeq);
            });

            $('#formModal').off('hide.bs.modal.p2h').on('hide.bs.modal.p2h', function() {
                isClosingFormModal = true;
                modalLoadSeq++;

                closeOpenSelect2();

                abortAjaxRequest('table');
                abortAjaxRequest('show');
                abortAjaxRequest('token');
                abortAjaxRequest('item');

                fixFormFieldsWithoutIdOrName(document.getElementById('formModal'));
            });

            $('#formModal').off('hidden.bs.modal.p2h').on('hidden.bs.modal.p2h', function() {
                const modalEl = document.getElementById('formModal');

                if (modalEl && document.activeElement && modalEl.contains(document.activeElement)) {
                    document.activeElement.blur();
                }

                p2hId = '';
                unitId = '';
                pendingEditData = null;
                unitData = null;
                pendingEditDataSeq = 0;

                enableButton();

                $('#tableItem tbody').empty();

                const form = $('#formModal').find('form')[0];

                if (form) {
                    form.reset();
                }

                $('#request_token').val('');
                resetSelect2Silently('#unit_id');
                setSelectValue('#shift', 'Day');

                fixFormFieldsWithoutIdOrName(modalEl);

                isClosingFormModal = false;
            });
        }

        function bindP2hSave() {
            $('#saveButton').off('click.p2hSave').on('click.p2hSave', function() {
                disableButton();

                const form = $('#formModal').find('form')[0];
                const formData = new FormData(form);
                let url = '{{ route('p2h.store') }}';
                const type = 'POST';

                if (p2hId !== '') {
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

                                if (form) {
                                    form.reset();
                                }

                                p2hId = '';
                                $('#formModal').modal('hide');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        enableButton();

                        const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : error;

                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: errorMessage,
                        });
                    }
                });
            });
        }

        function initPageDatepicker() {
            $(".datepicker").each(function() {
                if (this._flatpickr) {
                    this._flatpickr.destroy();
                }

                flatpickr(this, {
                    allowInput: true
                });
            });
        }

        function initUnitFilterSelect2() {
            const $unit = $('#unit');

            if (!$unit.length || !window.jQuery || !$.fn.select2) {
                return;
            }

            if ($unit.hasClass('select2-hidden-accessible')) {
                $unit.select2('destroy');
            }

            $unit.off('.unitFilter');

            ensureEmptyOption($unit);
            ensureSelectOption($unit, 'All', 'All Unit', true);

            $unit.select2({
                theme: "bootstrap-5",
                width: $unit.data('width') ? $unit.data('width') : ($unit.hasClass('w-100') ? '100%' : 'style'),
                placeholder: 'All Unit',
                allowClear: true,
                selectOnClose: false,
                ajax: {
                    url: '{{ route('p2h.get_unit_all') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            q: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        const results = normalizeUnitResults(data, {
                            includeAll: params.page === 1
                        });

                        return {
                            results: results,
                            pagination: {
                                more: !!(data && data.pagination && data.pagination.more)
                            }
                        };
                    },
                    cache: true
                }
            });

            $unit.on('select2:open.unitFilter', function() {
                fixSelect2SearchInputId($unit);
            });

            $unit.on('change.unitFilter', function() {
                if (!$(this).val()) {
                    $(this).val('All').trigger('change.select2');
                }

                $('#table-data').DataTable().draw();
            });

            if (!$unit.val()) {
                $unit.val('All').trigger('change.select2');
            }
        }

        function initModalSelect2() {
            if (!window.jQuery || !$.fn.select2) {
                return;
            }

            $('.select-select').not('#unit_id').each(function() {
                const $el = $(this);

                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }

                $el.off('.modalSelect');

                ensureEmptyOption($el);

                $el.select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#formModal'),
                    width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
                        'style'),
                    placeholder: $el.data('placeholder') || 'Choose',
                    allowClear: true,
                    selectOnClose: false,
                    minimumResultsForSearch: 0,
                });

                $el.on('select2:open.modalSelect', function() {
                    fixSelect2SearchInputId($el);
                });

                $el.on('select2:close.modalSelect', function() {
                    $(this).blur();

                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                });
            });
        }

        function initModalUnitSelect2() {
            const $unitId = $('#unit_id');

            if (!$unitId.length || !window.jQuery || !$.fn.select2) {
                return;
            }

            if ($unitId.hasClass('select2-hidden-accessible')) {
                $unitId.select2('destroy');
            }

            $unitId.off('.modalUnit');
            ensureEmptyOption($unitId);

            $unitId.select2({
                theme: "bootstrap-5",
                dropdownParent: $('#formModal'),
                width: $unitId.data('width') ? $unitId.data('width') : ($unitId.hasClass('w-100') ? '100%' :
                    'style'),
                placeholder: 'Choose Unit',
                allowClear: true,
                selectOnClose: false,
                ajax: {
                    url: '{{ route('p2h.get_unit_all') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            q: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: normalizeUnitResults(data, {
                                includeAll: false
                            }),
                            pagination: {
                                more: !!(data && data.pagination && data.pagination.more)
                            }
                        };
                    },
                    cache: true
                }
            });

            $unitId.on('select2:open.modalUnit', function() {
                fixSelect2SearchInputId($unitId);
            });

            $unitId.on('select2:close.modalUnit', function() {
                $(this).blur();

                if (document.activeElement) {
                    document.activeElement.blur();
                }
            });

            $unitId.on('change.modalUnit', function() {
                if (isClosingFormModal || isHydratingForm) {
                    return;
                }

                const modalEl = document.getElementById('formModal');

                if (!modalEl || !modalEl.classList.contains('show')) {
                    return;
                }

                const selectedUnitId = $(this).val() || '';

                if (String(selectedUnitId) === String(unitId)) {
                    return;
                }

                unitId = String(selectedUnitId);

                // Tutup dropdown Select2 dan lepaskan focus agar scroll modal tidak tertahan
                // setelah tbody table diganti oleh AJAX.
                try {
                    $unitId.select2('close');
                } catch (e) {}

                if (document.activeElement) {
                    document.activeElement.blur();
                }

                const modalBody = modalEl.querySelector('.modal-body');
                const lastScrollTop = modalBody ? modalBody.scrollTop : 0;

                loadP2hItemsByUnit(selectedUnitId, lastScrollTop);
            });
        }

        function resetP2hFormForOpen(isEdit) {
            const form = $('#formModal').find('form')[0];

            if (form) {
                form.reset();
            }

            $('#id').val(p2hId);
            $('#tableItem tbody').empty();

            isHydratingForm = true;

            resetSelect2Silently('#unit_id');
            setSelectValue('#shift', 'Day');

            $('#driver').val('');
            setFlatpickrOrInputValue('#date', '');
            $('#km_start').val('');
            $('#_km_start').val('');
            $('#km_finish').val('');
            $('#_km_finish').val('');
            $('#request_token').val('');

            isHydratingForm = false;
        }

        function loadInitialP2hTable(currentSeq) {
            if (isClosingFormModal) {
                return;
            }

            abortAjaxRequest('table');

            const isEdit = p2hId !== '';

            showTableLoading(5);

            let url = isEdit ?
                '{{ route('p2h.get_table_edit', ':_id') }}'.replace(':_id', p2hId) :
                '{{ route('p2h.get_table_add') }}';

            tableAjaxRequest = $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (isClosingFormModal || currentSeq !== modalLoadSeq) {
                        return;
                    }

                    $("#tableItem > tbody").html(response.html);

                    const titleText = isEdit ? 'Edit P2H' : 'Add P2H';
                    const number = isEdit ? response.p2h_no : response.p2h_prev_no;

                    if (number) {
                        $('#modal-header').html(titleText + ' -&nbsp;<b>' + number + '</b>');
                    } else {
                        $('#modal-header').text(titleText);
                    }

                    updateModalLayoutAndScroll();
                    fixFormFieldsWithoutIdOrName(document.getElementById('formModal'));

                    if (isEdit) {
                        applyPendingP2hEditData(currentSeq);

                    }
                },
                error: function(xhr, status, error) {
                    if (status === 'abort') {
                        return;
                    }

                    console.error('Error:', error);
                },
                complete: function() {
                    tableAjaxRequest = null;
                }
            });
        }

        function fetchP2hEditData(currentSeq) {
            if (!p2hId || isClosingFormModal) {
                return;
            }

            abortAjaxRequest('show');

            let url = '{{ route('p2h.show', ':_id') }}';
            url = url.replace(':_id', p2hId);

            p2hShowRequest = $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (isClosingFormModal || currentSeq !== modalLoadSeq) {
                        return;
                    }

                    pendingEditData = response.data || response || {};
                    unitData = response.unit || response || {};
                    pendingEditDataSeq = currentSeq;
                    applyPendingP2hEditData(currentSeq, unitData);
                },
                error: function(xhr, status, error) {
                    if (status === 'abort') {
                        return;
                    }

                    alert('Error fetching data');
                },
                complete: function() {
                    p2hShowRequest = null;
                }
            });
        }

        function applyPendingP2hEditData(currentSeq, unitData) {
            if (!pendingEditData || isClosingFormModal || currentSeq !== modalLoadSeq) {
                return;
            }

            requestAnimationFrame(function() {
                if (!pendingEditData || isClosingFormModal || currentSeq !== modalLoadSeq) {
                    return;
                }

                hydrateP2hEditFields(pendingEditData, unitData);

                // Retry once after Bootstrap/Select2/flatpickr finish layout work.
                setTimeout(function() {
                    if (!pendingEditData || isClosingFormModal || currentSeq !== modalLoadSeq) {
                        return;
                    }

                    hydrateP2hEditFields(pendingEditData, unitData);
                }, 75);
            });
        }

        function hydrateP2hEditFields(data, unitData) {
            if (!data || isClosingFormModal) {
                return false;
            }

            isHydratingForm = true;

            try {
                $('#divSignPath').css('display', 'block');
                $('#modal-header').text('Edit P2H');
                $('#id').val(p2hId);

                setInputValue('#driver', getDataValue(data, ['driver', 'driver_name', 'operator_name']));
                setFlatpickrOrInputValue('#date', getDataValue(data, ['date', 'p2h_date']));

                const selectedUnitId = getDataValue(data, ['unit_id', 'unitId', 'unit.id', 'vehicle_id']);
                const selectedUnitText = getUnitTextFromData(data, unitData);

                if (selectedUnitId) {
                    // Value tetap memakai unit_id, tetapi label yang ditampilkan harus vehicle_no.
                    // Jika backend belum mengirim vehicle_no, fallback terakhir tetap id agar form tidak kosong.
                    setSelect2Value('#unit_id', selectedUnitId, selectedUnitText || selectedUnitId, false);
                    unitId = String(selectedUnitId);
                }

                const shiftValue = getDataValue(data, ['shift', 'shift_name']);
                if (shiftValue !== undefined && shiftValue !== null && shiftValue !== '') {
                    setSelect2Value('#shift', shiftValue, shiftValue, false);
                }

                const kmStart = getDataValue(data, ['km_start', 'start_km']);
                const kmFinish = getDataValue(data, ['km_finish', 'finish_km', 'end_km']);

                setInputValue('#km_start', kmStart);
                setInputValue('#_km_start', formatNumber(kmStart));

                setInputValue('#km_finish', kmFinish);
                setInputValue('#_km_finish', formatNumber(kmFinish));

                setInputValue('#request_token', getDataValue(data, ['request_token', 'token']));

                return true;
            } finally {
                isHydratingForm = false;
            }
        }

        function loadP2hItemsByUnit(selectedUnitId, lastScrollTop) {
            if (isClosingFormModal) {
                return;
            }

            abortAjaxRequest('item');

            showTableLoading(5);

            let url = '{{ route('p2h.get_p2h_item') }}';
            const requestData = {
                unit_id: selectedUnitId
            };

            if (!selectedUnitId || selectedUnitId === 'All') {
                url = '{{ route('p2h.get_table_add') }}';
            }

            itemAjaxRequest = $.ajax({
                url: url,
                type: 'GET',
                data: requestData,
                success: function(response) {
                    if (isClosingFormModal) {
                        return;
                    }

                    $("#tableItem > tbody").html(response.html);

                    requestAnimationFrame(function() {
                        updateModalLayoutAndScroll(lastScrollTop);
                        fixFormFieldsWithoutIdOrName(document.getElementById('formModal'));
                    });
                },
                error: function(xhr, status, error) {
                    if (status === 'abort') {
                        return;
                    }

                    console.error('Error:', error);
                    $("#tableItem > tbody").empty();
                },
                complete: function() {
                    itemAjaxRequest = null;
                }
            });
        }

        function generateRequestToken() {
            abortAjaxRequest('token');

            tokenAjaxRequest = $.ajax({
                url: '{{ route('gen_request_token') }}',
                type: 'GET',
                success: function(response) {
                    if (isClosingFormModal) {
                        return;
                    }

                    $('#request_token').val(response.data);
                },
                error: function(xhr, status, error) {
                    if (status === 'abort') {
                        return;
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: error,
                    });
                },
                complete: function() {
                    tokenAjaxRequest = null;
                }
            });
        }

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
                            const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : error;

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

        function normalizeUnitResults(data, options = {}) {
            const includeAll = !!options.includeAll;
            let rows = [];

            if (data && Array.isArray(data.results)) {
                rows = data.results;
            } else if (data && Array.isArray(data.data)) {
                rows = data.data;
            } else if (Array.isArray(data)) {
                rows = data;
            }

            const results = rows.map(function(unit) {
                if (unit.id !== undefined && unit.text !== undefined) {
                    return unit;
                }

                return {
                    id: unit.id,
                    text: unit.vehicle_no || unit.unit_no || unit.name || unit.text || unit.id
                };
            }).filter(function(unit) {
                return unit.id !== undefined && unit.id !== null && unit.text !== undefined && unit.text !== null;
            });

            if (includeAll) {
                const hasAll = results.some(function(unit) {
                    return String(unit.id) === 'All';
                });

                if (!hasAll) {
                    results.unshift({
                        id: 'All',
                        text: 'All Unit'
                    });
                }
            }

            return results;
        }

        function getUnitTextFromData(data, unitData) {
            if (!data) {
                return '';
            }
            // console.log(unitData);
            // Prioritas utama saat edit: label Select2 harus memakai vehicle_no dari relasi unit/vehicle.
            return getDataValue(unitData, [
                'unit.vehicle_no',
                'vehicle.vehicle_no',
                'unit_data.vehicle_no',
                'unit.unit_no',
                'unit.name',
                'vehicle_no',
                'unit_no',
                'unit_text',
                'unit_name'
            ]);
        }

        function getDataValue(data, keys) {
            for (const key of keys) {
                const parts = String(key).split('.');
                let value = data;

                for (const part of parts) {
                    if (value === undefined || value === null) {
                        value = undefined;
                        break;
                    }

                    value = value[part];
                }

                if (value !== undefined && value !== null) {
                    return value;
                }
            }

            return '';
        }

        function setInputValue(selector, value) {
            const $input = $(selector);

            if (!$input.length) {
                return;
            }

            if (value === undefined || value === null) {
                value = '';
            }

            $input.val(value);
        }

        function ensureEmptyOption($select) {
            if (!$select.length) {
                return;
            }

            const hasEmpty = $select.find('option').filter(function() {
                return String(this.value) === '';
            }).length > 0;

            if (!hasEmpty) {
                $select.prepend(new Option('', '', false, false));
            }
        }

        function setSelect2Value(selector, id, text, triggerCustomChange = false) {
            const $select = $(selector);

            if (!$select.length || id === undefined || id === null || id === '') {
                return;
            }

            ensureSelectOption($select, id, text || id, true);
            $select.val(String(id));

            // Untuk Select2 AJAX, trigger change penuh paling stabil untuk refresh label.
            // Handler custom tetap aman karena saat hydrate, isHydratingForm bernilai true.
            if (triggerCustomChange) {
                $select.trigger('change');
            } else if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change');
            } else {
                $select.trigger('change');
            }
        }

        function setSelectValue(selector, value) {
            const $select = $(selector);

            if (!$select.length) {
                return;
            }

            if (value === undefined || value === null) {
                value = '';
            }

            if (value !== '' && !$select.find('option').filter(function() {
                    return String(this.value) === String(value);
                }).length) {
                $select.append(new Option(value, value, true, true));
            }

            $select.val(value);

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change');
            }
        }

        function resetSelect2Silently(selector) {
            const $select = $(selector);

            if (!$select.length) {
                return;
            }

            $select.val(null);

            if (selector === '#unit_id') {
                $select.find('option').not('[value=""]').remove();
                ensureEmptyOption($select);
            }

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change.select2');
            }
        }

        function ensureSelectOption($select, id, text, selected) {
            const safeText = text || '';
            const $existingOption = $select.find('option').filter(function() {
                return String(this.value) === String(id);
            });

            if (!$existingOption.length) {
                const option = new Option(safeText || id, id, !!selected, !!selected);
                $select.append(option);
                return;
            }

            // Penting untuk Select2 AJAX: jika option sebelumnya pernah dibuat dengan text=id,
            // update ulang label-nya agar tampil vehicle_no, bukan unit_id.
            if (safeText) {
                $existingOption.text(safeText);
            }

            if (selected) {
                $existingOption.prop('selected', true);
            }
        }

        function setFlatpickrOrInputValue(selector, value) {
            const input = document.querySelector(selector);

            if (!input) {
                return;
            }

            const safeValue = value || '';

            if (input._flatpickr) {
                try {
                    if (safeValue) {
                        input._flatpickr.setDate(safeValue, false);
                    } else {
                        input._flatpickr.clear();
                    }
                } catch (e) {
                    // Fallback kalau format tanggal dari backend tidak cocok dengan parser flatpickr.
                }
            }

            input.value = safeValue;
        }

        function formatNumber(value) {
            if (value === undefined || value === null || value === '') {
                return '';
            }

            if (window.numbro) {
                return numbro(value).format({
                    thousandSeparated: true
                });
            }

            return value;
        }

        function closeOpenSelect2() {
            $('.select2-hidden-accessible').each(function() {
                try {
                    $(this).select2('close');
                } catch (e) {}
            });

            if (document.activeElement) {
                document.activeElement.blur();
            }
        }

        function fixSelect2SearchInputId($select) {
            const selectId = $select.attr('id') || ('select2_' + Date.now());

            setTimeout(function() {
                $('.select2-container--open .select2-search__field').each(function(index) {
                    if (!this.id) {
                        this.id = selectId + '_search_' + index;
                    }

                    if (!this.getAttribute('aria-label')) {
                        this.setAttribute('aria-label', 'Search ' + selectId);
                    }
                });
            }, 0);
        }

        function fixFormFieldsWithoutIdOrName(root) {
            if (!root) {
                return;
            }

            let index = 0;

            root.querySelectorAll('input, select, textarea').forEach(function(field) {
                if (!field.id && !field.name) {
                    field.id = 'p2h_field_' + Date.now() + '_' + index;
                    index++;
                }
            });
        }

        function showTableLoading(colspan) {
            $("#tableItem > tbody").html(`
                <tr>
                    <td colspan="${colspan}" class="text-center">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="visually-hidden">Loading...</span>
                    </td>
                </tr>
            `);
        }

        function ensureFormModalScrollable() {
            const modalEl = document.getElementById('formModal');

            if (!modalEl) {
                return;
            }

            const modalDialog = modalEl.querySelector('.modal-dialog');
            const modalContent = modalEl.querySelector('.modal-content');
            const modalBody = modalEl.querySelector('.modal-body');

            if (modalDialog) {
                modalDialog.style.height = 'calc(100vh - 1rem)';
                modalDialog.style.maxHeight = 'calc(100vh - 1rem)';
                modalDialog.style.marginTop = '.5rem';
                modalDialog.style.marginBottom = '.5rem';
            }

            if (modalContent) {
                modalContent.style.height = '100%';
                modalContent.style.maxHeight = '100%';
                modalContent.style.minHeight = '0';
                modalContent.style.display = 'flex';
                modalContent.style.flexDirection = 'column';
            }

            if (modalBody) {
                modalBody.style.flex = '1 1 auto';
                modalBody.style.minHeight = '0';
                modalBody.style.maxHeight = '';
                modalBody.style.overflowY = 'auto';
                modalBody.style.overflowX = 'auto';
                modalBody.style.overscrollBehavior = 'contain';
                modalBody.style.webkitOverflowScrolling = 'touch';
                modalBody.style.pointerEvents = 'auto';
            }
        }

        function updateModalLayoutAndScroll(lastScrollTop) {
            const modalEl = document.getElementById('formModal');

            ensureFormModalScrollable();

            const modalBody = modalEl ? modalEl.querySelector('.modal-body') : null;

            if (modalEl && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modalEl).handleUpdate();
            }

            if (!modalBody) {
                return;
            }

            // Jangan toggle overflow menjadi hidden karena dapat membuat scroll modal tertahan
            // setelah unit_id berubah dan isi table dirender ulang.
            modalBody.style.overflowY = 'auto';
            modalBody.style.pointerEvents = 'auto';

            requestAnimationFrame(function() {
                if (typeof lastScrollTop !== 'undefined') {
                    modalBody.scrollTop = lastScrollTop;
                }

                if (modalEl && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).handleUpdate();
                }
            });
        }

        function abortAjaxRequest(type) {
            if (type === 'table' && tableAjaxRequest) {
                tableAjaxRequest.abort();
                tableAjaxRequest = null;
            }

            if (type === 'show' && p2hShowRequest) {
                p2hShowRequest.abort();
                p2hShowRequest = null;
            }

            if (type === 'token' && tokenAjaxRequest) {
                tokenAjaxRequest.abort();
                tokenAjaxRequest = null;
            }

            if (type === 'item' && itemAjaxRequest) {
                itemAjaxRequest.abort();
                itemAjaxRequest = null;
            }
        }

        function bindKmFormatting() {
            const $kmStart = $('#_km_start');
            const $kmFinish = $('#_km_finish');

            let isFmt = false;
            let userDecSep = null;

            function sanitize(s) {
                return (s ?? '').toString().replace(/[^0-9.,]/g, '');
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
                let c = 0;

                for (let i = 0; i < str.length; i++) {
                    if (/\d/.test(str[i])) {
                        c++;
                    }

                    if (c >= digitCount) {
                        return i + 1;
                    }
                }

                return str.length;
            }

            function textKeyDown(e) {
                if (e.ctrlKey || e.metaKey || e.altKey) {
                    return;
                }

                const okNav = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Tab', 'Enter'];

                if (okNav.includes(e.key)) {
                    return;
                }

                if (/^[0-9.,]$/.test(e.key)) {
                    return;
                }

                e.preventDefault();
            }

            function textInput(key, e) {
                if (isFmt) {
                    return;
                }

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

                if (userDecSep && !san.includes(userDecSep)) {
                    userDecSep = null;
                }

                const justSetDecSep = (!prevDecSep && justTypedSep);

                if (justSetDecSep) {
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

                $("#" + key).val(window.numbro ? numbro.unformat(el.value) : sanitize(el.value).replace(/,/g, ''));

                isFmt = false;
            }

            $kmStart.off('.kmFormat').on('keydown.kmFormat', textKeyDown);
            $kmStart.on('input.kmFormat', function(e) {
                textInput("km_start", e);
            });

            $kmFinish.off('.kmFormat').on('keydown.kmFormat', textKeyDown);
            $kmFinish.on('input.kmFormat', function(e) {
                textInput("km_finish", e);
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
