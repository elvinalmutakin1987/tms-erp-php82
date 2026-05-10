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
                                        data-bs-toggle="modal" data-bs-target="#formModal" data-title="Add Payment"><i
                                            class='bx bxs-plus-square'></i>New</a>
                                </div>
                                <div class="col-4">
                                    <select class="form-select w-100" id="vendor" name="vendor">
                                        <option value="All">All Vendor</option>
                                    </select>
                                </div>
                                <div class="col-2">
                                    <select class="form-select select-top" id="_status" name="_status">
                                        <option value="All">All Status</option>
                                        <option value="Draft">Draft</option>
                                        <option value="Cancel">Cancel</option>
                                        <option value="Done">Done</option>
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
                                        <th>Payment Number</th>
                                        <th>Purchase Order Number</th>
                                        <th>Vendor</th>
                                        <th>Date</th>
                                        <th>Total</th>
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

    @include('purchase_order_payment.modal')

    @include('purchase_order_payment.modal-detail')
@endsection

@section('js')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2-custom.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

    <script>
        const saveButton1 = document.getElementById('saveButton1');
        const saveButton2 = document.getElementById('saveButton2');

        var paymentId = '';
        var orderId = '';
        var vendorId = '';
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
                        d.status = $('#_status').val();
                        d.date_start = $('#date_start').val();
                        d.date_end = $('#date_end').val();
                        d.vendor = $('#vendor').val();
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
                        data: 'payment_no',
                        name: 'payment_no',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'order_no',
                        name: 'order_no',
                        orderable: true,
                        searchable: true,
                    },
                    {
                        data: 'vendor',
                        name: 'vendor',
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
                        data: 'total',
                        name: 'total',
                        orderable: true,
                        searchable: true,
                        className: 'text-end',
                        render: function(data, type, row) {
                            return numbro(data ?? 0).format({
                                thousandSeparated: true,
                                mantissa: 0
                            });
                        }
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
                            } else if (data == 'Approved' || data == 'Received') {
                                return '<span class="badge bg-warning" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'Open') {
                                return '<span class="badge bg-primary" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'Approval') {
                                return '<span class="badge bg-info" style="font-size: 13px">' +
                                    data + '</span>';
                            } else if (data == 'Cancel') {
                                return '<span class="badge bg-danger" style="font-size: 13px">' +
                                    data + '</span>';
                            } else {
                                return '<span class="badge bg-secondary" style="font-size: 13px">' +
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
                paymentId = $(this).data('id');
                $('#modal-header').text('Edit Payment');
                $('#id').val(paymentId);
                let url = '{{ route('purchaseorderpayment.show', ':_id') }}';
                url = url.replace(':_id', paymentId);
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $("#divSignPath").css('display', 'block');
                        $('#modal-header').text('Edit Payment');

                        const poId = response.data.purchase_order_id;

                        initPurchaseOrderSelect2();

                        if (poId) {
                            setPurchaseOrderSelectedByAjax(poId);
                        }

                    },
                    error: function() {
                        alert('Error fetching data');
                    }
                });
            });

            $(document).on('click', '.detailButton', function() {
                $('#modal-detail-header').text('Detail Requisition');
                let url = '{{ route('purchaserequisition.get_detail', ':_id') }}';
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

            $(".select-top").select2({
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

            $('#vendor').select2({
                theme: "bootstrap-5",
                width: $('#vendor').data('width') ? $('#vendor').data('width') : ($('#vendor').hasClass(
                    'w-100') ? '100%' : 'style'),
                placeholder: 'All Vendor',
                allowClear: true,
                selectOnClose: false,
                minimumResultsForSearch: 0,
                ajax: {
                    url: '{{ route('purchaseorderpayment.get_client_vendor') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || data
                        };
                    },
                    cache: true
                }
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
                    let url = '{{ route('purchaseorderpayment.destroy', ':_id') }}';
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

        $('.saveButton').on('click', function() {
            disableButton();
            const status = $(this).val();
            const form = $('#formModal').find('form')[0];
            const formData = new FormData(form);

            formData.append('status', status);

            let url = '{{ route('purchaseorderpayment.store') }}';
            let type = 'POST';

            if (paymentId) {
                url = '{{ route('purchaseorderpayment.update', ':_id') }}'.replace(':_id', paymentId);
                formData.append('_method', 'PUT');
            }

            const submitForm = () => {
                $.ajax({
                    url,
                    type,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            title: response.title,
                            text: response.message,
                            icon: 'success',
                            timer: 5000,
                            willClose: () => {
                                $('#table-data').DataTable().ajax.reload(null, false);
                                form.reset();
                                paymentId = '';
                                $('#formModal').modal('hide');
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        const errorMessage = xhr.responseJSON?.message || error;
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage,
                        });
                        enableButton();
                    }
                });
            };

            if (status === 'Open') {
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#5156be',
                    cancelButtonColor: '#fd625e',
                    confirmButtonText: 'Yes, Save it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    result.isConfirmed ? submitForm() : enableButton();
                });
            } else {
                submitForm();
            }
        });

        $('#formModal').on('show.bs.modal', function() {
            var button = $('#openModalButton');
            var title = button.data('title');
            $('#formModal form')[0].reset();
            $('#modal-header').text(title);

            setTimeout(function() {
                const isEdit = paymentId != '';
                if (!isEdit) {
                    $.ajax({
                        url: '{{ route('purchaseorderpayment.get_prev_no') }}',
                        type: 'GET',
                        success: function(response) {
                            const titleText = 'Add Payment';
                            $('#modal-header').html(titleText + ' -&nbsp;<b>' +
                                response.payment_prev_no +
                                '</b>');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });

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
            paymentId = '';
            orderId = '';
            vendorId = '';
            enableButton();
            $("#request_token").val("");
            $('#tableItem tbody').empty();
        });

        $('#cancelButton').on('click', function() {
            $('#formModal').modal('hide');
        });

        $('#cancelDetailButton').on('click', function() {
            $('#formDetail').modal('hide');
            $('#modal-detail-body').html("");
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

            $('#purchase_order_id').select2({
                theme: "bootstrap-5",
                width: $('#purchase_order_id').data('width') ? $('#purchase_order_id').data('width') : ($(
                    '#purchase_order_id').hasClass(
                    'w-100') ? '100%' : 'style'),
                placeholder: '',
                allowClear: true,
                selectOnClose: false,
                ajax: {
                    url: '{{ route('purchaseorderpayment.get_purchase_order') }}',
                    dataType: 'json',
                    data: function(params) {
                        return {
                            term: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results || data
                        };
                    },
                    cache: true
                }
            }).on('select2:open', function() {
                setTimeout(function() {
                    $('.select2-container--open .select2-search__field').trigger('focus');
                    $('.select2-container--open').css('z-index', 1056);
                }, 0);
            }).on('select2:select', function(e) {
                const selectedData = e.params.data;
                $('#vendor_name').val(selectedData.vendor);
                $('#client_vendor_id').val(selectedData.client_vendor_id);
                var inv_date = selectedData.invoice_date ?
                    dayjs(selectedData.invoice_date) :
                    dayjs();

                // TOP: kalau null/kosong, dianggap 0 hari
                var topDays = selectedData.top ?
                    parseInt(selectedData.top) :
                    0;

                if (isNaN(topDays)) {
                    topDays = 0;
                }

                // due_date = invoice_date + top hari
                var due_date = inv_date.add(topDays, 'day');

                // isi input
                $('#invoice_date').val(inv_date.format('YYYY-MM-DD'));
                $('#due_date').val(due_date.format('YYYY-MM-DD'));
                $('#grand_total').val(numbro(selectedData.grand_total).format({
                    thousandSeparated: true,
                    mantissa: 0
                }));
                $('#balance').val(numbro(selectedData.balance).format({
                    thousandSeparated: true,
                    mantissa: 0
                }));
            });
        }

        function disableButton() {
            saveButton1.disabled = true;
            saveButton2.disabled = true;
        }

        function enableButton() {
            saveButton1.disabled = false;
            saveButton2.disabled = false;
        }

        const $total = $('#total_');

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

        $total.on('keydown', function(e) {
            textKeyDown(e);
        });

        $total.on('input', function(e) {
            textInput("total", e);
            checkTotalNotExceedBalance(e);
        });

        function cleanNumber(value) {
            if (value === null || value === undefined || value === '') {
                return 0;
            }

            return parseFloat(
                value.toString()
                .replace(/[^0-9.-]/g, '')
            ) || 0;
        }

        function checkTotalNotExceedBalance(e) {
            const el = e.target;

            const totalValue = Number(numbro.unformat($('#total').val())) || 0;
            const balanceValue = Number(numbro.unformat($('#balance').val())) || 0;

            if (totalValue > balanceValue) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Payment Exceeds Balance',
                    text: 'Please enter an amount less than or equal to the balance.',
                    confirmButtonText: 'OK'
                });

                // kembalikan tampilan input total_ ke sejumlah balance
                el.value = numbro(balanceValue).format({
                    thousandSeparated: true
                });

                // kembalikan value asli total ke balance juga
                $('#total').val(balanceValue);
            }
        }

        function setPurchaseOrderSelectedByAjax(poId) {
            if (!poId) {
                return;
            }

            const $purchase_order = $('#purchase_order_id');

            $.ajax({
                url: '{{ route('purchaseorderpayment.get_purchase_order') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: poId
                },
                success: function(response) {
                    let selectedData = null;

                    if (response.data) {
                        selectedData = response.data;
                    } else if (response.results && response.results.length > 0) {
                        selectedData = response.results[0];
                    } else if (Array.isArray(response) && response.length > 0) {
                        selectedData = response[0];
                    }

                    if (!selectedData) {
                        console.warn('Purchase Order tidak ditemukan dari AJAX Select2:', poId);
                        return;
                    }

                    const selectedId = selectedData.id;
                    const selectedText = selectedData.text;

                    const optionExists = $purchase_order.find('option').filter(function() {
                        return String(this.value) === String(selectedId);
                    }).length > 0;

                    if (!optionExists) {
                        const newOption = new Option(selectedText, selectedId, true, true);
                        $purchase_order.append(newOption);
                    }

                    $purchase_order.val(selectedId).trigger('change.select2');

                    $purchase_order.trigger({
                        type: 'select2:select',
                        params: {
                            data: selectedData
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error get selected purchase order:', error);
                }
            });
        }
    </script>
    <!--app JS-->
@endsection
