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

        #formModal {
            overflow: hidden !important;
        }

        #formModal .select2-container {
            width: 100% !important;
        }

        .select2-container--open {
            z-index: 1065 !important;
        }

        .select2-container--bootstrap-5 .select2-selection__clear,
        .select2-container--default .select2-selection__clear {
            cursor: pointer;
            margin-right: .5rem;
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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Maintenance"><i
                                            class='bx bxs-plus-square'></i>New</a>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="unit" name="unit">
                                        <option value="All">All Unit</option>
                                    </select>
                                </div>
                                {{-- <div class="col">
                                    <select class="form-select" id="vendor" name="vendor">
                                        <option value="All">All Vendor</option>
                                    </select>
                                </div> --}}
                                <div class="col">
                                    <select class="form-select" id="_status" name="_status">
                                        <option value="All">All Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Open">Open</option>
                                        <option value="Done">Done</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <select class="form-select" id="main_type" name="main_type">
                                        <option value="All">All Type</option>
                                        @foreach ($maintenance_type as $d)
                                            <option value="{{ $d['name'] }}">{{ $d['abbreviation'] }}</option>
                                        @endforeach
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
                            <table id="table-data" class="table table-striped table-bordered" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Maintenance Number</th>
                                        <th>Unit</th>
                                        <th>Date</th>
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

    @include('maintenance.modal', ['maintenance_type' => $maintenance_type])

    @include('maintenance.modal-detail', ['maintenance_type' => $maintenance_type])

    @include('maintenance.modal-cost', ['maintenance_type' => $maintenance_type])
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        const saveButton1 = document.getElementById('saveButton1');
        const saveButton2 = document.getElementById('saveButton2');

        let maintenanceId = '';
        let unitId = '';
        let clientVendorId = '';

        let pendingEditData = null;
        let pendingUnitData = null;
        let pendingVendorData = null;
        let pendingEditDataSeq = 0;

        let isClosingFormModal = false;
        let isHydratingForm = false;
        let modalLoadSeq = 0;

        let maintenanceTableRequest = null;
        let maintenanceShowRequest = null;
        let requestTokenRequest = null;


        function withAllowClear(options, $select) {
            const config = $.extend(true, {
                allowClear: true,
                selectOnClose: false
            }, options || {});

            if (!config.placeholder) {
                config.placeholder = $select && $select.length ? ($select.data('placeholder') || 'Choose') : 'Choose';
            }

            return config;
        }

        function enableSelect2AllowClearDefault() {
            if (!window.jQuery || !$.fn.select2) {
                return;
            }

            $.fn.select2.defaults.set('allowClear', true);
            $.fn.select2.defaults.set('selectOnClose', false);
        }

        $(document).ready(function() {
            enableSelect2AllowClearDefault();
            initMaintenanceDataTable();
            initPagePickers();
            initPageFilterSelect2();
            initModalSelect2();
            markInitialMaintenanceFixedRow();

            bindMaintenancePageEvents();
            bindMaintenanceModalEvents();
            bindMaintenanceSave();
            bindMaintenanceCost();
            bindMaintenanceItems();
            bindNumberFormatting();
            bindDurationCalculation();
        });

        function initMaintenanceDataTable() {
            const ajax = '{{ url()->current() }}';

            $('#table-data').DataTable({
                scrollCollapse: true,
                responsive: true,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'All']
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
                        d.type = $('#main_type').val() || 'All';
                        d.status = $('#_status').val() || 'All';
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
                        data: 'maintenance_no',
                        name: 'maintenance_no',
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
                        data: 'date',
                        name: 'date',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'start',
                        name: 'start',
                        orderable: true,
                        searchable: true,
                        render: function(data) {
                            return timeFormat(data);
                        }
                    },
                    {
                        data: 'finish',
                        name: 'finish',
                        orderable: true,
                        searchable: true,
                        render: function(data) {
                            return timeFormat(data);
                        }
                    },
                    {
                        data: 'work_duration',
                        name: 'work_duration',
                        orderable: true,
                        searchable: true,
                        render: function(data) {
                            return timeFormat(data);
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: true,
                        render: function(data) {
                            if (data === 'Done') {
                                return '<span class="badge bg-success" style="font-size: 13px">' + data +
                                    '</span>';
                            }

                            if (data === 'Open') {
                                return '<span class="badge bg-primary" style="font-size: 13px">' + data +
                                    '</span>';
                            }

                            return '<span class="badge bg-secondary" style="font-size: 13px">' + data +
                                '</span>';
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

        function initPagePickers() {
            $('.datepicker').each(function() {
                if (this._flatpickr) {
                    this._flatpickr.destroy();
                }

                flatpickr(this, {
                    allowInput: true
                });
            });

            $('.date-time').each(function() {
                if (this._flatpickr) {
                    this._flatpickr.destroy();
                }

                flatpickr(this, {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    minuteIncrement: 1,
                    disableMobile: true,
                    allowInput: true
                });
            });
        }

        function initPageFilterSelect2() {
            initAjaxPageSelect2('#unit', {
                placeholder: 'All Unit',
                allText: 'All Unit',
                url: '{{ route('maintenance.get_unit_all') }}',
                normalizer: normalizeUnitResults
            });

            initLocalPageSelect2('#_status');
            initLocalPageSelect2('#main_type');
        }

        function initAjaxPageSelect2(selector, options) {
            const $select = $(selector);

            if (!$select.length || !window.jQuery || !$.fn.select2) {
                return;
            }

            destroySelect2($select);
            $select.off('.maintenancePageFilter');

            ensureEmptyOption($select);
            ensureSelectOption($select, 'All', options.allText, true);

            $select.select2(withAllowClear({
                theme: 'bootstrap-5',
                width: select2Width($select),
                placeholder: options.placeholder,
                allowClear: true,
                selectOnClose: false,
                ajax: {
                    url: options.url,
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
                            results: options.normalizer(data, {
                                includeAll: params.page === 1
                            }),
                            pagination: {
                                more: getPaginationMore(data)
                            }
                        };
                    },
                    cache: true
                }
            }, $select));

            $select.on('select2:open.maintenancePageFilter', function() {
                fixSelect2SearchInputId($select);
            });

            $select.on('change.maintenancePageFilter', function() {
                if (!$(this).val()) {
                    $(this).val('All').trigger('change.select2');
                }

                drawMaintenanceTable();
            });

            if (!$select.val()) {
                $select.val('All').trigger('change.select2');
            }
        }

        function initLocalPageSelect2(selector) {
            const $select = $(selector);

            if (!$select.length || !window.jQuery || !$.fn.select2) {
                return;
            }

            destroySelect2($select);
            $select.off('.maintenancePageFilter');
            ensureEmptyOption($select);

            $select.select2(withAllowClear({
                theme: 'bootstrap-5',
                width: select2Width($select),
                placeholder: $select.data('placeholder') || $select.find('option[value!=""]:first').text() ||
                    'Choose',
                allowClear: true,
                selectOnClose: false
            }, $select));

            $select.on('select2:open.maintenancePageFilter', function() {
                fixSelect2SearchInputId($select);
            });

            $select.on('change.maintenancePageFilter', function() {
                // Semua filter lokal dibuat allowClear. Jika dikosongkan, query DataTable tetap fallback ke All.
                drawMaintenanceTable();
            });
        }

        function initModalSelect2(scope) {
            if (!window.jQuery || !$.fn.select2) {
                return;
            }

            const $scope = scope ? $(scope) : $('#formModal');

            if (!$scope.length) {
                return;
            }

            // Select utama modal: memakai AJAX, sama konsepnya seperti index blade 1.
            // Saat edit, value tetap bisa tampil karena hydrate akan menambahkan option terpilih secara manual.
            if ($scope.find('#unit_id').length) {
                initModalAjaxSelect2('#unit_id', {
                    placeholder: 'Choose Unit',
                    url: '{{ route('maintenance.get_unit_all') }}',
                    normalizer: normalizeUnitResults,
                    namespace: 'maintenanceUnit'
                });
            }

            if ($scope.find('#client_vendor_id').length) {
                initModalAjaxSelect2('#client_vendor_id', {
                    placeholder: 'Choose Vendor',
                    url: '{{ route('maintenance.get_vendor_all') }}',
                    normalizer: normalizeVendorResults,
                    namespace: 'maintenanceVendor'
                });
            }

            // Select lain bisa dibuat AJAX hanya dengan menambahkan data-select2-ajax-url/data-ajax-url/data-url
            // pada tag <select> di modal. Ini aman untuk option dinamis tanpa harus menulis init baru per field.
            initModalDataSourceSelect2($scope);

            $scope.find('.select-select').not('#unit_id, #client_vendor_id').not('.select-type')
                .not('[data-select2-ajax-url], [data-ajax-url], [data-source-url], [data-url], .select2-ajax')
                .each(function() {
                    initModalLocalSelect2($(this), {
                        placeholder: $(this).data('placeholder') || 'Choose',
                        namespace: 'maintenanceModalSelect'
                    });
                });

            $scope.find('.select-type')
                .not('[data-select2-ajax-url], [data-ajax-url], [data-source-url], [data-url], .select2-ajax')
                .each(function() {
                    initModalLocalSelect2($(this), {
                        placeholder: $(this).data('placeholder') || 'Maintenance Type',
                        namespace: 'maintenanceType'
                    });
                });
        }

        function initModalAjaxSelect2(selector, options) {
            const $select = selector && selector.jquery ? selector : $(selector);

            if (!$select.length || !options || !options.url) {
                return;
            }

            destroySelect2($select);
            $select.off('.' + options.namespace);
            ensureEmptyOption($select);

            $select.select2(withAllowClear({
                theme: 'bootstrap-5',
                dropdownParent: options.dropdownParent || $('#formModal'),
                width: select2Width($select),
                placeholder: options.placeholder || $select.data('placeholder') || 'Choose',
                allowClear: true,
                selectOnClose: false,
                minimumInputLength: Number(options.minimumInputLength || $select.data('minimumInputLength') ||
                    0),
                ajax: {
                    url: options.url,
                    dataType: 'json',
                    delay: Number(options.delay || 250),
                    data: function(params) {
                        const ajaxData = {
                            term: params.term || '',
                            q: params.term || '',
                            page: params.page || 1
                        };

                        const extraData = getSelect2ExtraAjaxData($select, options, params);

                        return $.extend({}, ajaxData, extraData);
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;

                        return {
                            results: options.normalizer(data, {
                                includeAll: false,
                                params: params,
                                $select: $select
                            }),
                            pagination: {
                                more: getPaginationMore(data)
                            }
                        };
                    },
                    cache: true
                }
            }, $select));

            $select.on('select2:open.' + options.namespace, function() {
                fixSelect2SearchInputId($select);
            });

            $select.on('select2:close.' + options.namespace, function() {
                releaseModalFocus(this);
                updateModalLayoutAndScroll();
            });

            $select.on('change.' + options.namespace, function() {
                if (isHydratingForm || isClosingFormModal) {
                    return;
                }

                if (this.id === 'unit_id') {
                    unitId = String($(this).val() || '');
                }

                if (this.id === 'client_vendor_id') {
                    clientVendorId = String($(this).val() || '');
                }
            });
        }

        function initModalDataSourceSelect2(scope) {
            const $scope = scope ? $(scope) : $('#formModal');

            $scope.find(
                    'select[data-select2-ajax-url], select[data-ajax-url], select[data-source-url], select[data-url], select.select2-ajax'
                )
                .not('#unit_id, #client_vendor_id')
                .each(function(index) {
                    const $select = $(this);
                    const url = getSelectAjaxUrl($select);

                    if (!url) {
                        return;
                    }

                    initModalAjaxSelect2($select, {
                        placeholder: $select.data('placeholder') || 'Choose',
                        url: url,
                        normalizer: function(data, normalizerOptions) {
                            return normalizeGenericSelect2Results(data, {
                                includeAll: false,
                                idField: $select.data('idField') || 'id',
                                textField: $select.data('textField') || 'text',
                                fallbackTextFields: String($select.data('fallbackTextFields') ||
                                        'name,code,title,label,text,id')
                                    .split(',')
                                    .map(function(item) {
                                        return item.trim();
                                    })
                                    .filter(Boolean)
                            });
                        },
                        namespace: 'maintenanceAjax_' + ($select.attr('id') || $select.attr('name') || index)
                            .replace(/[^a-zA-Z0-9_]/g, '_')
                    });
                });
        }

        function getSelectAjaxUrl($select) {
            return $select.data('select2AjaxUrl') || $select.data('ajaxUrl') || $select.data('sourceUrl') ||
                $select.data('url') || '';
        }

        function getSelect2ExtraAjaxData($select, options, params) {
            let extraData = {};

            if (typeof options.extraData === 'function') {
                extraData = options.extraData(params, $select) || {};
            } else if (options.extraData) {
                extraData = options.extraData;
            }

            const dependsOn = $select.data('dependsOn');
            if (dependsOn) {
                String(dependsOn).split(',').forEach(function(selector) {
                    selector = selector.trim();

                    if (!selector) {
                        return;
                    }

                    const $dependency = $(selector);
                    const key = selector.replace(/^#/, '').replace(/^\./, '');
                    extraData[key] = $dependency.val() || '';
                });
            }

            const extraParams = $select.data('extraParams');
            if (extraParams && typeof extraParams === 'object') {
                extraData = $.extend({}, extraData, extraParams);
            }

            return extraData;
        }

        function initModalLocalSelect2($select, options) {
            if (!$select.length) {
                return;
            }

            destroySelect2($select);
            $select.off('.' + options.namespace);
            ensureEmptyOption($select);

            $select.select2(withAllowClear({
                theme: 'bootstrap-5',
                dropdownParent: $('#formModal'),
                width: select2Width($select),
                placeholder: options.placeholder,
                allowClear: true,
                selectOnClose: false,
                minimumResultsForSearch: 0
            }, $select));

            $select.on('select2:open.' + options.namespace, function() {
                fixSelect2SearchInputId($select);
            });

            $select.on('select2:close.' + options.namespace, function() {
                releaseModalFocus(this);
            });
        }

        function bindMaintenancePageEvents() {
            $('#openModalButton').off('click.maintenanceAdd').on('click.maintenanceAdd', function() {
                maintenanceId = '';
                clearPendingMaintenanceEdit();
                $('#id').val('');
            });

            $(document).off('click.maintenanceEdit', '.editButton').on('click.maintenanceEdit', '.editButton',
                function() {
                    maintenanceId = String($(this).data('id') || '');
                    clearPendingMaintenanceEdit();
                    $('#id').val(maintenanceId);
                    $('#modal-header').text('Edit Maintenance');
                });

            $(document).off('click.maintenanceDetail', '.detailButton').on('click.maintenanceDetail',
                '.detailButton',
                function() {
                    $('#modal-detail-header').text('Detail Maintenance');

                    let url = '{{ route('maintenance.get_detail', ':_id') }}';
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

            $(document).off('click.maintenanceCost', '.costButton').on('click.maintenanceCost', '.costButton',
                function() {
                    maintenanceId = String($(this).data('id') || '');
                    $('#modal-cost-header').text('Cost Setting');

                    let url = '{{ route('maintenance.cost', ':_id') }}';
                    url = url.replace(':_id', maintenanceId);

                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            $('#modal-cost-body').html(response);
                        },
                        error: function() {
                            alert('Error fetching data');
                        }
                    });
                });

            $('#date_start, #date_end').off('change.maintenanceFilter').on('change.maintenanceFilter', function() {
                drawMaintenanceTable();
            });

            $('#cancelButton').off('click.maintenanceCancel').on('click.maintenanceCancel', function() {
                $('#formModal').modal('hide');
            });

            $('#cancelDetailButton').off('click.maintenanceCancelDetail').on('click.maintenanceCancelDetail',
                function() {
                    $('#formDetail').modal('hide');
                    $('#modal-detail-body').html('');
                });

            $('#cancelCostButton').off('click.maintenanceCancelCost').on('click.maintenanceCancelCost', function() {
                $('#formCost').modal('hide');
                maintenanceId = '';
            });
        }

        function bindMaintenanceModalEvents() {
            $('#formModal').off('show.bs.modal.maintenance').on('show.bs.modal.maintenance', function(e) {
                isClosingFormModal = false;

                const relatedButton = e.relatedTarget ? $(e.relatedTarget) : null;
                const relatedId = relatedButton && relatedButton.length ? relatedButton.data('id') : '';
                const isTriggeredByEdit = relatedButton && relatedButton.length && relatedButton.hasClass(
                    'editButton');
                const isTriggeredByAdd = relatedButton && relatedButton.length && relatedButton.attr('id') ===
                    'openModalButton';

                if (isTriggeredByEdit && relatedId) {
                    maintenanceId = String(relatedId);
                    clearPendingMaintenanceEdit();
                    $('#id').val(maintenanceId);
                }

                if (isTriggeredByAdd) {
                    maintenanceId = '';
                    clearPendingMaintenanceEdit();
                    $('#id').val('');
                }

                const currentSeq = ++modalLoadSeq;
                const isEdit = maintenanceId !== '';
                const title = isEdit ? 'Edit Maintenance' :
                    (relatedButton && relatedButton.data('title') ? relatedButton.data('title') :
                        'Add Maintenance');

                resetMaintenanceFormForOpen();
                $('#id').val(maintenanceId);
                $('#modal-header').text(title);
                ensureFormModalScrollable();

                loadInitialMaintenanceTable(currentSeq);

                if (isEdit) {
                    fetchMaintenanceEditData(currentSeq);
                } else {
                    generateRequestToken(currentSeq);
                }
            });

            $('#formModal').off('shown.bs.modal.maintenanceScroll').on('shown.bs.modal.maintenanceScroll',
                function() {
                    ensureFormModalScrollable();
                    updateModalLayoutAndScroll();
                    applyPendingMaintenanceEditData(modalLoadSeq);
                });

            $('#formModal').off('hide.bs.modal.maintenance').on('hide.bs.modal.maintenance', function() {
                isClosingFormModal = true;
                modalLoadSeq++;

                closeOpenSelect2();
                abortMaintenanceRequest('table');
                abortMaintenanceRequest('show');
                abortMaintenanceRequest('token');

                fixFormFieldsWithoutIdOrName(document.getElementById('formModal'));
            });

            $('#formModal').off('hidden.bs.modal.maintenance').on('hidden.bs.modal.maintenance', function() {
                const modalEl = document.getElementById('formModal');

                if (modalEl && document.activeElement && modalEl.contains(document.activeElement)) {
                    document.activeElement.blur();
                }

                maintenanceId = '';
                unitId = '';
                clientVendorId = '';
                clearPendingMaintenanceEdit();

                const form = $('#formModal').find('form')[0];
                if (form) {
                    form.reset();
                }

                $('#id').val('');
                $('#request_token').val('');
                resetMaintenanceItemRows();
                resetSelect2Silently('#unit_id', true);
                resetSelect2Silently('#client_vendor_id', true);
                resetSelect2Silently('#type', false);
                resetDataSourceSelect2Silently('#formModal');
                resetDataSourceSelect2Silently('#formModal');
                enableButton();
                fixFormFieldsWithoutIdOrName(modalEl);

                isClosingFormModal = false;
            });
        }

        function resetMaintenanceFormForOpen() {
            const form = $('#formModal').find('form')[0];

            if (form) {
                form.reset();
            }

            isHydratingForm = true;

            try {
                $('#id').val(maintenanceId);
                $('#divSignPath').css('display', maintenanceId ? 'block' : 'none');
                resetMaintenanceItemRows();
                resetSelect2Silently('#unit_id', true);
                resetSelect2Silently('#client_vendor_id', true);
                resetSelect2Silently('#type', false);

                setFlatpickrOrInputValue('#date', '');
                setFlatpickrOrInputValue('#start', '');
                setFlatpickrOrInputValue('#finish', '');

                setInputValue('#notes', '');
                setInputValue('#mechanic', '');
                setInputValue('#hour_meter', '');
                setInputValue('#_hour_meter', '');
                setInputValue('#km_hm', '');
                setInputValue('#_km_hm', '');
                setInputValue('#work_duration', '');
                setInputValue('#request_token', '');
            } finally {
                isHydratingForm = false;
            }
        }

        function loadInitialMaintenanceTable(currentSeq) {
            if (isClosingFormModal) {
                return;
            }

            abortMaintenanceRequest('table');
            showTableLoading(4);

            const isEdit = maintenanceId !== '';
            const url = isEdit ?
                '{{ route('maintenance.get_table_edit', ':_id') }}'.replace(':_id', maintenanceId) :
                '{{ route('maintenance.get_table_add') }}';

            maintenanceTableRequest = $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (isClosingFormModal || currentSeq !== modalLoadSeq) {
                        return;
                    }

                    renderMaintenanceItemRows(isEdit ? response.html : '');
                    initModalSelect2('#formModal');

                    const titleText = isEdit ? 'Edit Maintenance' : 'Add Maintenance';
                    const number = isEdit ? response.maintenance_no : response.maintenance_prev_no;

                    if (number) {
                        $('#modal-header').html(titleText + ' -&nbsp;<b>' + escapeHtml(number) + '</b>');
                    } else {
                        $('#modal-header').text(titleText);
                    }

                    renumberRows();
                    updateModalLayoutAndScroll();
                    fixFormFieldsWithoutIdOrName(document.getElementById('formModal'));

                    if (isEdit) {
                        applyPendingMaintenanceEditData(currentSeq);
                    }
                },
                error: function(xhr, ajaxStatus, error) {
                    if (ajaxStatus === 'abort') {
                        return;
                    }

                    console.error('Error:', error);
                    renderMaintenanceItemRows('');
                },
                complete: function() {
                    maintenanceTableRequest = null;
                }
            });
        }

        function fetchMaintenanceEditData(currentSeq) {
            if (!maintenanceId || isClosingFormModal) {
                return;
            }

            abortMaintenanceRequest('show');

            let url = '{{ route('maintenance.show', ':_id') }}';
            url = url.replace(':_id', maintenanceId);

            maintenanceShowRequest = $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if (isClosingFormModal || currentSeq !== modalLoadSeq) {
                        return;
                    }

                    pendingEditData = response.data || response || {};
                    pendingUnitData = response.unit || getDataValue(pendingEditData, ['unit', 'vehicle']) || {};
                    pendingVendorData = response.client_vendor || response.vendor ||
                        getDataValue(pendingEditData, ['client_vendor', 'vendor']) || {};
                    pendingEditDataSeq = currentSeq;

                    applyPendingMaintenanceEditData(currentSeq);
                },
                error: function(xhr, ajaxStatus) {
                    if (ajaxStatus === 'abort') {
                        return;
                    }

                    alert('Error fetching data');
                },
                complete: function() {
                    maintenanceShowRequest = null;
                }
            });
        }

        function applyPendingMaintenanceEditData(currentSeq) {
            if (!pendingEditData || isClosingFormModal || currentSeq !== modalLoadSeq ||
                pendingEditDataSeq !== currentSeq) {
                return;
            }

            requestAnimationFrame(function() {
                if (!pendingEditData || isClosingFormModal || currentSeq !== modalLoadSeq) {
                    return;
                }

                hydrateMaintenanceEditFields(pendingEditData, pendingUnitData, pendingVendorData);

                setTimeout(function() {
                    if (!pendingEditData || isClosingFormModal || currentSeq !== modalLoadSeq) {
                        return;
                    }

                    hydrateMaintenanceEditFields(pendingEditData, pendingUnitData, pendingVendorData);
                }, 75);
            });
        }

        function hydrateMaintenanceEditFields(data, unitData, vendorData) {
            if (!data || isClosingFormModal) {
                return false;
            }

            isHydratingForm = true;

            try {
                $('#divSignPath').css('display', 'block');
                $('#id').val(maintenanceId);

                setFlatpickrOrInputValue('#date', getDataValue(data, ['date', 'maintenance_date']));
                setInputValue('#notes', getDataValue(data, ['notes', 'note']));
                setInputValue('#mechanic', getDataValue(data, ['mechanic', 'mechanic_name']));

                const selectedUnitId = getDataValue(data, ['unit_id', 'unitId', 'unit.id', 'vehicle_id']);
                const selectedUnitText = getUnitTextFromData(data, unitData);

                if (selectedUnitId !== '') {
                    setSelect2Value('#unit_id', selectedUnitId, selectedUnitText || selectedUnitId);
                    unitId = String(selectedUnitId);
                }

                const selectedVendorId = getDataValue(data, [
                    'client_vendor_id',
                    'vendor_id',
                    'clientVendorId',
                    'client_vendor.id',
                    'vendor.id'
                ]);
                const selectedVendorText = getVendorTextFromData(data, vendorData);

                if (selectedVendorId !== '') {
                    setSelect2Value('#client_vendor_id', selectedVendorId,
                        selectedVendorText || selectedVendorId);
                    clientVendorId = String(selectedVendorId);
                }

                const selectedType = getDataValue(data, ['type', 'maintenance_type', 'type_name']);
                if (selectedType !== '') {
                    setSelect2Value('#type', selectedType, getExistingOptionText('#type', selectedType) ||
                        selectedType);
                }

                const hourMeter = getDataValue(data, ['hour_meter', 'hm']);
                const kmHm = getDataValue(data, ['km_hm', 'km', 'kilometer']);

                setInputValue('#hour_meter', hourMeter);
                setInputValue('#_hour_meter', formatNumber(hourMeter));
                setInputValue('#km_hm', kmHm);
                setInputValue('#_km_hm', formatNumber(kmHm));

                setFlatpickrOrInputValue('#start', dateTimeFormat(getDataValue(data, ['start', 'start_time'])));
                setFlatpickrOrInputValue('#finish', dateTimeFormat(getDataValue(data, ['finish', 'finish_time'])));
                setInputValue('#work_duration', timeFormat(getDataValue(data, [
                    'work_duration',
                    'duration'
                ])));
                setInputValue('#request_token', getDataValue(data, ['request_token', 'token']));
                hydrateDataSourceSelect2FromEditData(data);

                return true;
            } finally {
                isHydratingForm = false;
            }
        }

        function generateRequestToken(currentSeq) {
            abortMaintenanceRequest('token');

            requestTokenRequest = $.ajax({
                url: '{{ route('gen_request_token') }}',
                type: 'GET',
                success: function(response) {
                    if (isClosingFormModal || currentSeq !== modalLoadSeq) {
                        return;
                    }

                    $('#request_token').val(response.data);
                },
                error: function(xhr, ajaxStatus, error) {
                    if (ajaxStatus === 'abort') {
                        return;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error
                    });
                },
                complete: function() {
                    requestTokenRequest = null;
                }
            });
        }

        function bindMaintenanceSave() {
            $('.saveButton').off('click.maintenanceSave').on('click.maintenanceSave', function() {
                disableButton();

                const saveStatus = $(this).val();
                const form = $('#formModal').find('form')[0];

                if (!form) {
                    enableButton();
                    return;
                }

                const submitForm = function() {
                    const formData = new FormData(form);
                    formData.set('type', $('#type').val() || '');
                    formData.set('status', saveStatus);

                    let url = '{{ route('maintenance.store') }}';

                    if (maintenanceId) {
                        url = '{{ route('maintenance.update', ':_id') }}'.replace(':_id', maintenanceId);
                        formData.append('_method', 'PUT');
                    }

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: response.title,
                                text: response.message,
                                icon: 'success',
                                timer: 5000,
                                willClose: function() {
                                    $('#table-data').DataTable().ajax.reload(null,
                                        false);
                                    maintenanceId = '';
                                    $('#formModal').modal('hide');
                                }
                            });
                        },
                        error: function(xhr, ajaxStatus, error) {
                            const errorMessage = xhr.responseJSON ? xhr.responseJSON.message :
                                error;

                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: errorMessage
                            });
                            enableButton();
                        }
                    });
                };

                if (saveStatus === 'Open') {
                    Swal.fire({
                        title: 'Are you sure?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#5156be',
                        cancelButtonColor: '#fd625e',
                        confirmButtonText: 'Yes, Save it!',
                        cancelButtonText: 'Cancel'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            submitForm();
                        } else {
                            enableButton();
                        }
                    });
                    return;
                }

                submitForm();
            });
        }

        function bindMaintenanceCost() {
            $(document).off('click.maintenanceSaveCost', '.saveCostButton').on('click.maintenanceSaveCost',
                '.saveCostButton',
                function() {
                    const costStatus = $(this).val();

                    if (costStatus === 'Done') {
                        Swal.fire({
                            title: 'Are you sure?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#5156be',
                            cancelButtonColor: '#fd625e',
                            confirmButtonText: 'Yes, Done it!',
                            cancelButtonText: 'Cancel'
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                submitCost(costStatus);
                            }
                        });
                        return;
                    }

                    submitCost(costStatus);
                });

            $('#formCost').off('show.bs.modal.maintenanceCost').on('show.bs.modal.maintenanceCost', function(e) {
                const relatedButton = e.relatedTarget ? $(e.relatedTarget) : null;
                const title = relatedButton && relatedButton.data('title') ? relatedButton.data('title') :
                    'Cost Setting';

                $('#modal-cost-header').text(title);

                if (!maintenanceId) {
                    return;
                }

                $.ajax({
                    url: '{{ route('maintenance.get_maintenance_item_list') }}',
                    data: {
                        form: 'cost',
                        maintenance_id: maintenanceId
                    },
                    type: 'GET',
                    success: function(response) {
                        const $tbody = $('#formCost #tableItem > tbody');

                        if ($tbody.length) {
                            $tbody.children('tr').not(':first').remove();
                            $tbody.append(response);
                        }
                    },
                    error: function(xhr, ajaxStatus, error) {
                        console.error(error);
                    }
                });
            });

            $('#formCost').off('hidden.bs.modal.maintenanceCost').on('hidden.bs.modal.maintenanceCost', function() {
                maintenanceId = '';
                $('#formCost #request_token').val('');
                $('#formCost #tableItem > tbody > tr').not(':first').remove();
            });
        }

        function submitCost(costStatus) {
            const form = $('#formCost').find('form')[0];

            if (!form) {
                return;
            }

            const formData = new FormData(form);
            let url = '{{ route('maintenance.cost.store') }}';
            formData.append('status', costStatus);

            if (maintenanceId !== '') {
                url = '{{ route('maintenance.cost.update', ':_id') }}'.replace(':_id', maintenanceId);
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire({
                        title: response.title,
                        text: response.message,
                        icon: 'success',
                        timer: 5000,
                        willClose: function() {
                            $('#table-data').DataTable().ajax.reload(null, false);
                            form.reset();
                            maintenanceId = '';
                            $('#formCost').modal('hide');
                        }
                    });
                },
                error: function(xhr, ajaxStatus, error) {
                    const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : error;

                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errorMessage
                    });
                    enableButton();
                }
            });
        }

        function bindMaintenanceItems() {
            $('#addItemButton').off('click.maintenanceItem').on('click.maintenanceItem', function() {
                const $tbody = $('#formModal #tableItem > tbody');
                const action = $('#act').val() || '';
                const mainItemId = $('#main_item_id').val() || '';
                const mainItem = $('#main_item_id option:selected').text() || '';

                if (!mainItemId) {
                    return;
                }

                const newRow = `
                    <tr>
                        <td class="p-1 align-middle row-number">#</td>
                        <td class="p-1 align-middle">
                            <input type="text" class="form-control" name="action[]" readonly value="${escapeHtml(action)}">
                        </td>
                        <td class="p-1 align-middle">
                            <input type="hidden" class="form-control" name="maintenance_item_id[]" readonly value="${escapeHtml(mainItemId)}">
                            <input type="text" class="form-control" name="main_item[]" readonly value="${escapeHtml(mainItem)}">
                        </td>
                        <td class="text-center p-1 align-middle">
                            <div class="row row-cols-auto g-3">
                                <div class="col">
                                    <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row"></button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;

                $('#act').val('Repair').trigger('change');
                const $newRow = $(newRow);
                $tbody.append($newRow);
                initModalSelect2($newRow);
                renumberRows();
                updateModalLayoutAndScroll();
            });

            $('#formModal #tableItem').off('click.maintenanceDeleteRow', '.delete-row').on('click.maintenanceDeleteRow',
                '.delete-row',
                function() {
                    if ($(this).hasClass('fixed-row')) {
                        return;
                    }

                    $(this).closest('tr').remove();
                    renumberRows();
                    updateModalLayoutAndScroll();
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
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

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
                            title: 'Deleted!',
                            text: response.message,
                            icon: 'success',
                            timer: 5000,
                            willClose: function() {
                                $('#table-data').DataTable().ajax.reload(null, false);
                            }
                        });
                    },
                    error: function(xhr, ajaxStatus, error) {
                        const errorMessage = xhr.responseJSON ? xhr.responseJSON.message : error;

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                    }
                });
            });
        }

        function normalizeUnitResults(data, options = {}) {
            const results = normalizeAjaxRows(data).map(function(unit) {
                if (unit.id !== undefined && unit.text !== undefined) {
                    return unit;
                }

                return {
                    id: unit.id,
                    text: unit.vehicle_no || unit.unit_no || unit.name || unit.text || unit.id
                };
            }).filter(validSelect2Result);

            return prependAllOption(results, options.includeAll, 'All Unit');
        }

        function normalizeVendorResults(data, options = {}) {
            const results = normalizeAjaxRows(data).map(function(vendor) {
                if (vendor.id !== undefined && vendor.text !== undefined) {
                    return vendor;
                }

                return {
                    id: vendor.id,
                    text: vendor.name || vendor.vendor_name || vendor.company_name || vendor.text || vendor.id
                };
            }).filter(validSelect2Result);

            return prependAllOption(results, options.includeAll, 'All Vendor');
        }

        function normalizeGenericSelect2Results(data, options = {}) {
            const idField = options.idField || 'id';
            const textField = options.textField || 'text';
            const fallbackTextFields = options.fallbackTextFields || ['name', 'code', 'title', 'label', 'text', 'id'];

            const results = normalizeAjaxRows(data).map(function(item) {
                if (item.id !== undefined && item.text !== undefined) {
                    return item;
                }

                const id = getDataValue(item, [idField, 'id', 'value', 'code']);
                let text = getDataValue(item, [textField]);

                if (text === '') {
                    text = getDataValue(item, fallbackTextFields);
                }

                return {
                    id: id,
                    text: text || id
                };
            }).filter(validSelect2Result);

            return prependAllOption(results, options.includeAll, options.allText || 'All');
        }

        function normalizeAjaxRows(data) {
            if (data && Array.isArray(data.results)) {
                return data.results;
            }

            if (data && Array.isArray(data.data)) {
                return data.data;
            }

            if (Array.isArray(data)) {
                return data;
            }

            return [];
        }

        function validSelect2Result(item) {
            return item.id !== undefined && item.id !== null && item.text !== undefined && item.text !== null;
        }

        function prependAllOption(results, includeAll, text) {
            if (!includeAll) {
                return results;
            }

            const hasAll = results.some(function(item) {
                return String(item.id) === 'All';
            });

            if (!hasAll) {
                results.unshift({
                    id: 'All',
                    text: text
                });
            }

            return results;
        }

        function getPaginationMore(data) {
            return !!(data && data.pagination && data.pagination.more);
        }

        function getUnitTextFromData(data, unitData) {
            return getDataValue({
                data: data,
                relation: unitData
            }, [
                'relation.vehicle_no',
                'relation.unit_no',
                'relation.name',
                'data.unit.vehicle_no',
                'data.vehicle.vehicle_no',
                'data.vehicle_no',
                'data.unit_no',
                'data.unit_text',
                'data.unit_name'
            ]);
        }

        function getVendorTextFromData(data, vendorData) {
            return getDataValue({
                data: data,
                relation: vendorData
            }, [
                'relation.name',
                'relation.vendor_name',
                'relation.company_name',
                'data.client_vendor.name',
                'data.vendor.name',
                'data.vendor_name',
                'data.client_vendor_name'
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

            $input.val(value === undefined || value === null ? '' : value);
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
                } catch (error) {
                    // Nilai tetap ditulis langsung jika parser flatpickr tidak mengenali format backend.
                }
            }

            input.value = safeValue;
        }

        function setSelect2Value(selector, id, text, triggerCustomChange = false) {
            const $select = selector && selector.jquery ? selector : $(selector);

            if (!$select.length || id === undefined || id === null || id === '') {
                return;
            }

            ensureSelectOption($select, id, text || id, true);
            $select.val(String(id));

            // Untuk Select2 AJAX, option hasil pencarian belum ada saat edit.
            // Karena itu option terpilih dibuat manual lalu change.select2 dipicu agar label tampil.
            if (triggerCustomChange) {
                $select.trigger('change');
            } else if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change.select2');
            } else {
                $select.trigger('change');
            }
        }

        function resetSelect2Silently(selector, removeDynamicOptions) {
            const $select = selector && selector.jquery ? selector : $(selector);

            if (!$select.length) {
                return;
            }

            $select.val(null);

            if (removeDynamicOptions) {
                $select.find('option').not('[value=""]').remove();
                ensureEmptyOption($select);
            }

            if ($select.hasClass('select2-hidden-accessible')) {
                $select.trigger('change.select2');
            }
        }

        function resetDataSourceSelect2Silently(scope) {
            const $scope = scope ? $(scope) : $('#formModal');

            $scope.find(
                    'select[data-select2-ajax-url], select[data-ajax-url], select[data-source-url], select[data-url], select.select2-ajax'
                )
                .not('#unit_id, #client_vendor_id')
                .each(function() {
                    resetSelect2Silently($(this), true);
                });
        }

        function hydrateDataSourceSelect2FromEditData(data) {
            $('#formModal select[data-select2-ajax-url], #formModal select[data-ajax-url], #formModal select[data-source-url], #formModal select[data-url], #formModal select.select2-ajax')
                .not('#unit_id, #client_vendor_id')
                .each(function() {
                    const $select = $(this);
                    const idKeys = getSelectEditIdKeys($select);
                    const textKeys = getSelectEditTextKeys($select);
                    const selectedId = getDataValue(data, idKeys);

                    if (selectedId === '') {
                        return;
                    }

                    const selectedText = getDataValue(data, textKeys) || getExistingOptionText($select, selectedId) ||
                        selectedId;

                    setSelect2Value($select, selectedId, selectedText);
                });
        }

        function getSelectEditIdKeys($select) {
            const explicitKeys = $select.data('editIdKey') || $select.data('editIdKeys');

            if (explicitKeys) {
                return String(explicitKeys).split(',').map(function(key) {
                    return key.trim();
                }).filter(Boolean);
            }

            const name = String($select.attr('name') || '').replace(/\[\]$/, '');
            const id = $select.attr('id') || '';
            const keys = [];

            if (id) {
                keys.push(id, id + '_id');
            }

            if (name) {
                keys.push(name, name + '_id');
            }

            return keys;
        }

        function getSelectEditTextKeys($select) {
            const explicitKeys = $select.data('editTextKey') || $select.data('editTextKeys');

            if (explicitKeys) {
                return String(explicitKeys).split(',').map(function(key) {
                    return key.trim();
                }).filter(Boolean);
            }

            const name = String($select.attr('name') || '').replace(/\[\]$/, '');
            const id = $select.attr('id') || '';
            const textField = $select.data('textField') || 'text';
            const keys = [];

            if (id) {
                keys.push(id + '_text', id + '_name', id + '.' + textField, id + '.name');
            }

            if (name) {
                keys.push(name + '_text', name + '_name', name + '.' + textField, name + '.name');
            }

            keys.push(textField, 'text', 'name');

            return keys;
        }

        function ensureEmptyOption($select) {
            if (!$select.length) {
                return;
            }

            const hasEmptyOption = $select.find('option').filter(function() {
                return String(this.value) === '';
            }).length > 0;

            if (!hasEmptyOption) {
                $select.prepend(new Option('', '', false, false));
            }
        }

        function ensureSelectOption($select, id, text, selected) {
            const safeText = text || String(id);
            const $existingOption = $select.find('option').filter(function() {
                return String(this.value) === String(id);
            });

            if (!$existingOption.length) {
                $select.append(new Option(safeText, id, !!selected, !!selected));
                return;
            }

            if (safeText) {
                $existingOption.text(safeText);
            }

            if (selected) {
                $existingOption.prop('selected', true);
            }
        }

        function getExistingOptionText(selector, value) {
            const $select = selector && selector.jquery ? selector : $(selector);
            const $option = $select.find('option').filter(function() {
                return String(this.value) === String(value);
            }).first();

            return $option.length ? $option.text() : '';
        }

        function destroySelect2($select) {
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }
        }

        function select2Width($select) {
            return $select.data('width') ? $select.data('width') : ($select.hasClass('w-100') ? '100%' :
                'style');
        }

        function releaseModalFocus(element) {
            $(element).blur();

            if (document.activeElement) {
                document.activeElement.blur();
            }
        }

        function closeOpenSelect2() {
            $('.select2-hidden-accessible').each(function() {
                try {
                    $(this).select2('close');
                } catch (error) {}
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
                    field.id = 'maintenance_field_' + Date.now() + '_' + index;
                    index++;
                }
            });
        }

        function showTableLoading(colspan) {
            const $tbody = $('#formModal #tableItem > tbody');

            removeDynamicMaintenanceRows($tbody);
            $tbody.append(`
                <tr class="maintenance-loading-row">
                    <td colspan="${colspan}" class="text-center">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="visually-hidden">Loading...</span>
                    </td>
                </tr>
            `);
        }

        function renderMaintenanceItemRows(html) {
            const $tbody = $('#formModal #tableItem > tbody');

            removeDynamicMaintenanceRows($tbody);

            if (html) {
                const $newRows = $('<tbody>').html(html).find('tr');
                $tbody.append($newRows);
                initModalSelect2($newRows);
            }
        }

        function resetMaintenanceItemRows() {
            const $tbody = $('#formModal #tableItem > tbody');
            removeDynamicMaintenanceRows($tbody);
            renumberRows();
        }

        function markInitialMaintenanceFixedRow() {
            const $firstRow = $('#formModal #tableItem > tbody > tr').first();

            if ($firstRow.length) {
                $firstRow.addClass('fixed-row');
            }
        }

        function removeDynamicMaintenanceRows($tbody) {
            if (!$tbody || !$tbody.length) {
                return;
            }

            const $rows = $tbody.children('tr');
            const $fixedRows = $rows.filter('.fixed-row');

            $rows.filter('.maintenance-loading-row').remove();

            if ($fixedRows.length) {
                $tbody.children('tr').not('.fixed-row').remove();
                return;
            }

            $tbody.children('tr').remove();
        }

        function renumberRows() {
            let number = 1;

            $('#formModal #tableItem > tbody > tr').each(function(index) {
                if ($(this).hasClass('fixed-row')) {
                    $(this).find('.row-number').text('');
                    return;
                }

                $(this).find('.row-number').text(number);
                number++;
            });
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

        function abortMaintenanceRequest(type) {
            if (type === 'table' && maintenanceTableRequest) {
                maintenanceTableRequest.abort();
                maintenanceTableRequest = null;
            }

            if (type === 'show' && maintenanceShowRequest) {
                maintenanceShowRequest.abort();
                maintenanceShowRequest = null;
            }

            if (type === 'token' && requestTokenRequest) {
                requestTokenRequest.abort();
                requestTokenRequest = null;
            }
        }

        function clearPendingMaintenanceEdit() {
            pendingEditData = null;
            pendingUnitData = null;
            pendingVendorData = null;
            pendingEditDataSeq = 0;
            unitId = '';
            clientVendorId = '';
        }

        function drawMaintenanceTable() {
            if ($.fn.DataTable.isDataTable('#table-data')) {
                $('#table-data').DataTable().draw();
            }
        }

        function timeFormat(data) {
            if (!data) {
                return '';
            }

            const parts = String(data).split(':');

            if (parts.length < 2) {
                return '';
            }

            return parts[0] + ':' + parts[1];
        }

        function dateTimeFormat(data) {
            if (!data) {
                return '';
            }

            const value = String(data).trim();

            if (/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}$/.test(value)) {
                return value.substring(0, 16);
            }

            if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/.test(value)) {
                return value.replace('T', ' ').substring(0, 16);
            }

            if (/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}$/.test(value)) {
                return value;
            }

            return value;
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

        function escapeHtml(value) {
            return String(value === undefined || value === null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function bindNumberFormatting() {
            const $kmHm = $('#_km_hm');
            const $hourMeter = $('#_hour_meter');
            let isFormatting = false;
            let userDecimalSeparator = null;

            function sanitize(value) {
                return (value || '').toString().replace(/[^0-9.,]/g, '');
            }

            function groupThousands(digits, separator) {
                digits = digits.replace(/^0+(?=\d)/, '');

                if (digits === '') {
                    digits = '0';
                }

                return digits.replace(/\B(?=(\d{3})+(?!\d))/g, separator);
            }

            function countDigitsLeft(value, position) {
                return (value.slice(0, position).match(/\d/g) || []).length;
            }

            function caretByDigits(value, digitCount) {
                let count = 0;

                for (let index = 0; index < value.length; index++) {
                    if (/\d/.test(value[index])) {
                        count++;
                    }

                    if (count >= digitCount) {
                        return index + 1;
                    }
                }

                return value.length;
            }

            function textKeyDown(event) {
                if (event.ctrlKey || event.metaKey || event.altKey) {
                    return;
                }

                const navigationKeys = [
                    'Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Tab', 'Enter'
                ];

                if (navigationKeys.includes(event.key) || /^[0-9.,]$/.test(event.key)) {
                    return;
                }

                event.preventDefault();
            }

            function textInput(hiddenFieldId, event) {
                if (isFormatting) {
                    return;
                }

                isFormatting = true;

                const element = event.target;
                const raw = element.value || '';
                const rawCaret = typeof element.selectionStart === 'number' ? element.selectionStart : raw.length;
                const originalEvent = event.originalEvent || event;
                const inserted = originalEvent && typeof originalEvent.data === 'string' ? originalEvent.data : '';
                const previousDecimalSeparator = userDecimalSeparator;
                const justTypedSeparator = inserted === '.' || inserted === ',';
                const sanitized = sanitize(raw);
                const sanitizedLeft = sanitize(raw.slice(0, rawCaret));
                const sanitizedCaret = sanitizedLeft.length;

                if (userDecimalSeparator && !sanitized.includes(userDecimalSeparator)) {
                    userDecimalSeparator = null;
                }

                const justSetDecimalSeparator = !previousDecimalSeparator && justTypedSeparator;

                if (justSetDecimalSeparator) {
                    userDecimalSeparator = inserted;
                }

                const digitsLeft = countDigitsLeft(sanitized, sanitizedCaret);
                let integerDigits = '';
                let fractionDigits = '';
                let keepDecimal = false;

                if (userDecimalSeparator && sanitized.includes(userDecimalSeparator)) {
                    const position = sanitized.indexOf(userDecimalSeparator);
                    keepDecimal = true;
                    integerDigits = sanitized.slice(0, position).replace(/[.,]/g, '');
                    fractionDigits = sanitized.slice(position + 1).replace(/[.,]/g, '');

                    if (integerDigits === '') {
                        integerDigits = '0';
                    }
                } else {
                    integerDigits = sanitized.replace(/[.,]/g, '');
                }

                const thousandsSeparator = userDecimalSeparator ?
                    (userDecimalSeparator === ',' ? '.' : ',') : ',';
                const formattedInteger = groupThousands(integerDigits, thousandsSeparator);
                const formatted = keepDecimal ?
                    formattedInteger + userDecimalSeparator + fractionDigits : formattedInteger;

                element.value = formatted;

                if (typeof element.setSelectionRange === 'function') {
                    if (justSetDecimalSeparator && keepDecimal) {
                        const decimalPosition = formatted.indexOf(userDecimalSeparator) + 1;
                        element.setSelectionRange(decimalPosition, decimalPosition);
                    } else {
                        const newCaret = caretByDigits(formatted, digitsLeft);
                        element.setSelectionRange(newCaret, newCaret);
                    }
                }

                isFormatting = false;

                const numericValue = window.numbro ? numbro.unformat(element.value) :
                    element.value.replace(/,/g, '');
                $('#' + hiddenFieldId).val(numericValue);
            }

            $kmHm.off('.maintenanceNumber').on('keydown.maintenanceNumber', textKeyDown)
                .on('input.maintenanceNumber', function(event) {
                    textInput('km_hm', event);
                });

            $hourMeter.off('.maintenanceNumber').on('keydown.maintenanceNumber', textKeyDown)
                .on('input.maintenanceNumber', function(event) {
                    textInput('hour_meter', event);
                });
        }

        function bindDurationCalculation() {
            const startElement = document.getElementById('start');
            const finishElement = document.getElementById('finish');
            const durationElement = document.getElementById('work_duration');

            if (!startElement || !finishElement || !durationElement) {
                return;
            }

            function parseDateTime(value) {
                if (!value) {
                    return null;
                }

                value = String(value).trim();

                const match = value.match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})$/);

                if (!match) {
                    return null;
                }

                const year = Number(match[1]);
                const month = Number(match[2]) - 1;
                const day = Number(match[3]);
                const hour = Number(match[4]);
                const minute = Number(match[5]);
                const date = new Date(year, month, day, hour, minute, 0, 0);

                if (
                    date.getFullYear() !== year ||
                    date.getMonth() !== month ||
                    date.getDate() !== day ||
                    date.getHours() !== hour ||
                    date.getMinutes() !== minute
                ) {
                    return null;
                }

                return date;
            }

            function calculateDuration() {
                const start = startElement.value.trim();
                const finish = finishElement.value.trim();

                if (!start || !finish) {
                    durationElement.value = '';
                    return;
                }

                const startDate = parseDateTime(start);
                const finishDate = parseDateTime(finish);

                if (!startDate || !finishDate) {
                    durationElement.value = '';
                    return;
                }

                const differenceMs = finishDate.getTime() - startDate.getTime();

                if (differenceMs < 0) {
                    durationElement.value = '';
                    return;
                }

                const totalMinutes = Math.floor(differenceMs / 60000);
                const hours = String(Math.floor(totalMinutes / 60)).padStart(2, '0');
                const minutes = String(totalMinutes % 60).padStart(2, '0');

                durationElement.value = hours + ':' + minutes;
            }

            $(startElement).off('.maintenanceDuration')
                .on('input.maintenanceDuration change.maintenanceDuration', calculateDuration);
            $(finishElement).off('.maintenanceDuration')
                .on('input.maintenanceDuration change.maintenanceDuration', calculateDuration);
        }

        function disableButton() {
            if (saveButton1) {
                saveButton1.disabled = true;
            }

            if (saveButton2) {
                saveButton2.disabled = true;
            }
        }

        function enableButton() {
            if (saveButton1) {
                saveButton1.disabled = false;
            }

            if (saveButton2) {
                saveButton2.disabled = false;
            }
        }
    </script>
    <!--app JS-->
@endsection
