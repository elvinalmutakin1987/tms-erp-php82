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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Contract"><i
                                            class='bx bxs-plus-square'></i>New</a>
                                </div>
                                <div class="col-4">
                                    <select class="form-select select-select" id="client" name="client">
                                        <option value="All">All Client</option>
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
                                        <th>Client</th>
                                        <th>Contract Number</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
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

    @include('contract.modal')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        var contractId = '';
        var clientId = '';
        var clientVendorId = '';
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
                        d.service_id = $('#client').val();
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
                        data: 'client',
                        name: 'client',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'contract_no',
                        name: 'contract_no',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            if (data == 'Active') {
                                return '<span class="badge bg-success" style="font-size: 13px">' +
                                    data +
                                    '</span>';
                            } else {
                                return '<span class="badge bg-danger" style="font-size: 13px">' +
                                    data +
                                    '</span>';
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
                contractId = $(this).data('id');
                $('#modal-header').text('Edit Contract');
                $('#id').val(contractId);
                let url = '{{ route('contract.show', ':_id') }}';
                url = url.replace(':_id', contractId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit Contract');
                        $('#contract_no').val(response.data.contract_no);
                        $('#start_date').val(response.data.start_date);
                        $('#end_date').val(response.data.end_date);
                        $('#_value').val(response.data.value ? numbro(response.data.value)
                            .format({
                                thousandSeparated: true,
                            }) : '');
                        $('#value').val(response.data.value);
                        $('#notes').val(response.data.notes);
                        $('#service_id').val(response.data.service_id).trigger('change');
                        $('#client_vendor_id').val(response.data.client_vendor_id).trigger(
                            'change');
                        clientVendorId = response.data.client_vendor_id;
                        serviceId = response.data.service_id;

                        var newRow = $(response.html_item);
                        var newRow1 = $(response.html_target);
                        var newRow2 = $(response.html_fmf);
                        $("#tbody_tableItem tr").eq(0).after(newRow);
                        $("#tbody_tableTarget tr").eq(0).after(newRow1);
                        $("#tbody_tableFmf tr").eq(0).after(newRow2);
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $(document).on('click', '.changeStatusButton', function() {
                let id = $(this).data('id');
                let _status = $(this).data('status');
                let title = 'Are you want to deactivate?';
                let confirmButton = 'Yes, deactivate it!';
                if (_status == 'Deactive') {
                    title = 'Are you want to activate?';
                    confirmButton = 'Yes, activate it!'
                }
                Swal.fire({
                    title: title,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    confirmButtonText: confirmButton,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let url = '{{ route('contract.update_status', ':_id') }}';
                        url = url.replace(':_id', id);
                        $.ajax({
                            url: url,
                            type: 'PUT',
                            data: {
                                id: id,
                                status: _status,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: "Status updated!",
                                    text: response.message,
                                    icon: "success",
                                    timer: 5000,
                                    didOpen: () => {},
                                    willClose: () => {
                                        $('#table-data').DataTable().ajax
                                            .reload(null, false);
                                    }
                                });
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
                    }
                });
            });

            $.ajax({
                url: '{{ route('contract.get_client_all') }}',
                type: 'GET',
                success: function(response) {
                    $('#client').empty();
                    $('#client').append('<option value="All">All Client</client>');
                    $.each(response.data, function(index, client) {
                        $('#client').append('<option value="' + client.id +
                            '">' +
                            client.name +
                            '</client>');
                    });
                    if (clientId != '') {
                        $("#client").val(clientId).trigger('change');
                    }

                    $('#client_vendor_id').empty();
                    $.each(response.data, function(index, client) {
                        $('#client_vendor_id').append('<option value="' + client.id +
                            '">' +
                            client.name +
                            '</client>');
                    });
                    if (clientVendorId != '') {
                        $("#client_vendor_id").val(clientVendorId).trigger('change');
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
                url: '{{ route('contract.get_unit_all') }}',
                type: 'GET',
                success: function(response) {
                    $('#unit').empty();
                    $.each(response.data, function(index, unit) {
                        $('#unit').append('<option value="' + unit.id +
                            '">' +
                            unit.vehicle_no +
                            '</client>');
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

            $('#service_id').val('').trigger('change');
            $.ajax({
                url: '{{ route('contract.get_service_all') }}',
                type: 'GET',
                success: function(response) {
                    $('#service_id').empty();
                    $('#service_id').append(
                        '<option value="" selected disabled></option>');
                    $.each(response.data, function(index, service) {
                        $('#service_id').append('<option value="' + service.id + '">' + service
                            .name + '</option>');
                    });

                    if (serviceId != '') {
                        $("#service_id").val(serviceId).trigger('change');
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

            $("#client").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
                $('#table-data').DataTable().draw();
            });

            $("#unit").select2({
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
                    let url = '{{ route('contract.destroy', ':_id') }}';
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
            var url = '{{ route('contract.store') }}';
            var type = 'POST';
            if (contractId != '') {
                url = '{{ route('contract.update', ':_id') }}';
                url = url.replace(':_id', contractId);
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
                            contractId = '';
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
            contractId = '';
            clientId = '';
            clientVendorId = '';
            serviceId = '';
            $('#tableTarget tbody tr').not(':first').remove();
            $('#tableItem tbody tr').not(':first').remove();
            $('#tableFmf tbody tr').not(':first').remove();
            $('#service_id').val(null).trigger('change');
            $('#client_vendor_id').prop('selectedIndex', -1);
            // $('#client_vendor_id').val(null).trigger('change');
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

        const $value = $('#_value');
        const $target = $('#_target_');
        const $rate = $('#_rate_t_');
        const $price = $('#_price_');
        const $fmf_value = $('#_fmf_value');

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

        $value.on('keydown', function(e) {
            textKeyDown(e);
        });

        $value.on('input', function(e) {
            textInput("value", e);
        });

        $target.on('keydown', function(e) {
            textKeyDown(e);
        });

        $target.on('input', function(e) {
            textInput("_target", e);
        });

        $price.on('keydown', function(e) {
            textKeyDown(e);
        });

        $price.on('input', function(e) {
            textInput("price_", e);
        });

        $fmf_value.on('keydown', function(e) {
            textKeyDown(e);
        });

        $fmf_value.on('input', function(e) {
            textInput("fmf_value", e);
        });

        $rate.on('keydown', function(e) {
            textKeyDown(e);
        });

        $rate.on('input', function(e) {
            textInput("_rate_t", e);
        });

        $('#addItemButton').on('click', function() {
            var tbody = $("#tableItem > tbody");
            var item_no = $("#_item_no").val();
            var description = $("#_description").val();
            var rate = $("#_rate_t").val();
            var _rate = $("#_rate_t_").val();
            var newRow = `
                <tr>
                    <td class="p-1 align-middle row-number">
                        #
                    </td>
                    <td class="p-1 align-middle">
                       <input type="text" class="form-control" id="item_no" name="item_no[]" readonly value="${item_no}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="text" class="form-control" id="service_item" name="service_item[]" readonly value="${description}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="rate" name="rate[]" readonly value="${rate}">
                       <input type="text" class="form-control" id="_rate" name="_rate[]" readonly value="${_rate}">
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
            $("#_item_no").val('');
            $("#_description").val('');
            $("#_rate_t").val('');
            $("#_rate_t_").val('');
            tbody.append(newRow);
            renumberRows('item');
        });

        $('#addTargetButton').on('click', function() {
            var tbody = $("#tableTarget > tbody");
            var unit_id = $("#unit").val();
            var unit_name = $("#unit option:selected").text();
            var target = $("#target_").val();
            var _target = $("#_target_").val();
            var price = $("#price_").val();
            var _price = $("#_price_").val();
            var newRow = `
                <tr>
                    <td class="p-1 align-middle row-number">
                        #
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="unit_id" name="unit_id[]" readonly value="${unit_id}">
                       <input type="text" class="form-control" id="unit_name" name="unit_name[]" readonly value="${unit_name}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="target" name="target[]" readonly value="${target}">
                       <input type="text" class="form-control" id="_target" name="_target[]" readonly value="${_target}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="price" name="price[]" readonly value="${price}">
                       <input type="text" class="form-control" id="_price" name="_price[]" readonly value="${_price}">
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
            $("#target_").val('');
            $("#_target_").val('');
            $("#price_").val('');
            $("#_price_").val('');
            $("#unit").val('').trigger('change');
            tbody.append(newRow);
            renumberRows('target');
        });

        $('#addFmfButton').on('click', function() {
            var tbody = $("#tableFmf > tbody");
            var year = $("#_year").val();
            var fmf_value = $("#fmf_value").val();
            var _fmf_value = $("#_fmf_value").val();
            var newRow = `
                <tr>
                    <td class="p-1 align-middle row-number">
                        #
                    </td>
                    <td class="p-1 align-middle">
                       <input type="text" class="form-control" id="year" name="year[]" readonly value="${year}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="value_fmf" name="value_fmf[]" readonly value="${fmf_value}">
                       <input type="text" class="form-control" id="_value_fmf" name="_value_fmf[]" readonly value="${_fmf_value}">
                    </td>
                    <td class="text-center p-1 align-middle">
                        <div class="row row-cols-auto g-3">
                            <div class="col">
                                <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row  "
                                    id="removeFmfButton"></button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            $("#_year").val('');
            $("#fmf_value").val('');
            $("#_fmf_value").val('');
            tbody.append(newRow);
            renumberRows("fmf");
        });

        $("#tableTarget").on("click", ".delete-row", function() {
            $(this).closest("tr").remove();

            if ($(this).hasClass('fixed-row')) {
                return;
            }

            $(this).remove();
            renumberRows('target');
        });

        $("#tableItem").on("click", ".delete-row", function() {
            $(this).closest("tr").remove();

            if ($(this).hasClass('fixed-row')) {
                return;
            }

            $(this).remove();
            renumberRows('item');
        });

        $("#tableFmf").on("click", ".delete-row", function() {
            $(this).closest("tr").remove();

            if ($(this).hasClass('fixed-row')) {
                return;
            }

            $(this).remove();
            renumberRows('fmf');
        });

        function renumberRows(table) {
            let no = 1;
            let tableName = 'tableTarget';
            if (table == 'item') {
                tableName = 'tableItem';
            } else if (table == 'fmf') {
                tableName = 'tableFmf';
            }
            $('#' + tableName + ' > tbody > tr').each(function() {
                if ($(this).hasClass('fixed-row')) {
                    $(this).find('.row-number').text('');
                    return;
                }

                $(this).find('.row-number').text(no);
                no++;
            });
        }
    </script>
    <!--app JS-->
@endsection
