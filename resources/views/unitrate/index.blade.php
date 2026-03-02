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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Rate"><i
                                            class='bx bxs-plus-square'></i>New Rate</a>
                                </div>
                                <div class="col-4">
                                    <select class="form-select" id="client" name="client">
                                        <option value="All">All Client</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select class="form-select" id="contract" name="contract">
                                        <option value="All">All Contract</option>
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
                                        <th>Unit</th>
                                        <th>Contract Number</th>
                                        <th>Rate</th>
                                        <th>Target PA (%)</th>
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

    @include('unitrate.modal')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script>
        var unitrateId = '';
        var clientvendorId = '';
        var contractId = '';
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
                        d.client_vendor_id = $('#client').val();
                        d.contract_id = $('#contract').val();
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
                        data: 'vehicle_no',
                        name: 'vehicle_no',
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
                        data: 'rate',
                        name: 'rate',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return numbro(data).format({
                                thousandSeparated: true
                            })
                        }
                    },
                    {
                        data: 'target',
                        name: 'target',
                        orderable: true,
                        searchable: true,
                        render: function(data, type, row) {
                            return numbro(data).format({
                                thousandSeparated: true,
                                mantissa: 2
                            })
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
                unitrateId = $(this).data('id');
                $('#modal-header').text('Edit Rate');
                $('#id').val(unitrateId);
                let url = '{{ route('unitrate.show', ':_id') }}';
                url = url.replace(':_id', unitrateId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit Location');
                        $('#rate').val(response.data.rate);
                        $('#_rate').val(numbro(response.data.rate).format({
                            thousandSeparated: true
                        }));
                        $('#target').val(response.data.target);
                        $('#_target').val(numbro(response.data.target).format({
                            thousandSeparated: true,
                            mantissa: 2
                        }));
                        unitId = response.data.unit_id;
                        clientvendorId = response.data.contract.client_vendor_id;
                        contractId = response.data.contract_id;
                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $.ajax({
                url: '{{ route('unitrate.get_client_all') }}',
                type: 'GET',
                success: function(response) {
                    $('#client').empty();
                    $('#client').append('<option value="All">All Client</option>');
                    $.each(response.data, function(index, client) {
                        $('#client').append('<option value="' + client.id +
                            '">' +
                            client.name +
                            '</option>');
                    });
                    if (clientvendorId != '') {
                        $("#client").val(clientvendorId).trigger('change');
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

            $("#client_vendor_id").on('change', function() {
                get_contract();
            });

            $("#client").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
                get_filter_contract();
                $('#table-data').DataTable().draw();
            });

            $("#contract").select2({
                theme: "bootstrap-5",
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass(
                    'w-100') ? '100%' : 'style',
            }).on('change', function() {
                $('#table-data').DataTable().draw();
            });

            get_contract();

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
                    let url = '{{ route('unitrate.destroy', ':_id') }}';
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
            var url = '{{ route('unitrate.store') }}';
            var type = 'POST';
            if (unitrateId != '') {
                url = '{{ route('unitrate.update', ':_id') }}';
                url = url.replace(':_id', unitrateId);
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
                            locationId = '';
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

            $.ajax({
                url: '{{ route('unitrate.get_client_all') }}',
                type: 'GET',
                success: function(response) {
                    $('#client_vendor_id').empty();
                    $('#client_vendor_id').append('<option value="All">All Client</client>');
                    $.each(response.data, function(index, client) {
                        $('#client_vendor_id').append('<option value="' + client.id +
                            '">' +
                            client.name +
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

            $.ajax({
                url: '{{ route('unitrate.get_unit_all') }}',
                type: 'GET',
                success: function(response) {
                    $('#unit_id').empty();
                    $.each(response.data, function(index, unit) {
                        $('#unit_id').append('<option value="' + unit.id +
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

            setTimeout(function() {
                var data = {
                    'form': 'create'
                };
                if (unitrateId != '') {
                    data = {
                        'form': 'edit',
                        'unit_rate_id': unitrateId
                    };
                    if (clientvendorId != '') {
                        $("#client_vendor_id").val(clientvendorId).trigger('change');
                        get_contract();
                    }
                    if (unitId != '') {
                        $("#unit_id").val(unitId).trigger('change');
                    }
                }
            }, 500);

        });

        $('#formModal').on('hidden.bs.modal', function() {
            unitrateId = '';
            clientvendorId = '';
            contractId = '';
            unitId = '';
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

        function get_contract() {
            $.ajax({
                url: '{{ route('unitrate.get_contract') }}',
                data: {
                    client_vendor_id: $("#client_vendor_id").val()
                },
                type: 'GET',
                success: function(response) {
                    $('#contract_id').empty();
                    $.each(response.data, function(index, contract) {
                        $('#contract_id').append('<option value="' + contract.id +
                            '">' +
                            contract.contract_no +
                            '</client>');
                    });
                    if (contractId != '') {
                        $("#contract_id").val(contractId).trigger('change');
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

        function get_filter_contract() {
            $.ajax({
                url: '{{ route('unitrate.get_contract') }}',
                data: {
                    client_vendor_id: $("#client").val()
                },
                type: 'GET',
                success: function(response) {
                    $('#contract').empty();
                    $('#contract').append('<option value="All">All Contract</client>');
                    $.each(response.data, function(index, contract) {
                        $('#contract').append('<option value="' + contract.id +
                            '">' +
                            contract.contract_no +
                            '</client>');
                    });
                    if (contractId != '') {
                        $("#contract").val(contractId).trigger('change');
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

        const $rate = $('#_rate');
        const $target = $('#_target');

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

        $rate.on('keydown', function(e) {
            textKeyDown(e);
        });

        $rate.on('input', function(e) {
            textInput("rate", e);
        });

        $target.on('keydown', function(e) {
            textKeyDown(e);
        });

        $target.on('input', function(e) {
            textInput("target", e);
        });
    </script>
    <!--app JS-->
@endsection
