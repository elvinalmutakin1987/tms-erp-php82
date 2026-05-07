@php
    use Illuminate\Support\Number;
@endphp

<table class="table mb-0" id="tableItem">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:3%">#</th>
            <th scope="col" style="width:9%">Type</th>
            <th scope="col">Description</th>
            <th scope="col">Alter Name</th>
            <th scope="col" style="width:9%">Uom</th>
            <th scope="col" style="width:9%">Qty</th>
            <th scope="col" style="width:10%">Price</th>
            <th scope="col" style="width:10%">Discount</th>
            <th scope="col" style="width:10%">Amount</th>
            <th scope="col" style="width:2%">Action</th>
        </tr>
    </thead>

    <tbody id="tbody">
        <tr class="fixed-row">
            <td class="p-1 align-middle"></td>

            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_type" name="_type">
                    <option value="Good">Good</option>
                    <option value="Service">Service</option>
                </select>
            </td>

            <td class="p-1 align-middle">
                <input type="text" class="form-control" id="_description" name="_description">
            </td>

            <td class="p-1 align-middle">
                <input type="text" class="form-control" id="_desc_vendor" name="_desc_vendor">
            </td>

            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_uom" name="_uom">
                    @foreach ($uom as $d => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
            </td>

            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_qty" name="_qty">
                <input type="text" class="form-control" id="_qty_" name="_qty_" style="text-align: right;">
            </td>

            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_price" name="_price">
                <input type="text" class="form-control" id="_price_" name="_price_" style="text-align: right;">
            </td>

            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_discount_item" name="_discount_item">
                <input type="text" class="form-control" id="_discount_item_" name="_discount_item_"
                    style="text-align: right;">
            </td>

            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_amount" name="_amount" readonly>
                <input type="text" class="form-control" id="_amount_" name="_amount_" readonly
                    style="text-align: right;">
            </td>

            <td class="p-1 align-middle" style="width:2%">
                <div class="row row-cols-auto g-3">
                    <div class="col">
                        <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1"
                            id="addItemButton"></button>
                    </div>
                </div>
            </td>
        </tr>

        @if ($purchase_requisition)
            @foreach ($purchase_requisition->purchase_requisition_detail as $d)
                <tr>
                    <td class="p-1 align-middle row-number">
                        {{ $loop->iteration }}
                    </td>

                    <td class="p-1 align-middle">
                        <select class="form-select select-select detail-type" name="type[]">
                            <option value="Good" {{ ($d->type ?? 'Good') == 'Good' ? 'selected' : '' }}>
                                Good
                            </option>
                            <option value="Service" {{ ($d->type ?? '') == 'Service' ? 'selected' : '' }}>
                                Service
                            </option>
                        </select>
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control order" name="order[]"
                            value="{{ $loop->iteration }}">
                        <input type="text" class="form-control" name="description[]" readonly
                            value="{{ $d->description }}">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="text" class="form-control" name="desc_vendor[]"
                            value="{{ $d->desc_vendor }}">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="text" class="form-control" name="uom[]" readonly
                            value="{{ $d->uom }}">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="qty[]" readonly
                            value="{{ $d->qty }}">
                        <input type="text" class="form-control" name="__qty[]" readonly
                            value="{{ $d->qty ? Number::format($d->qty, precision: 0) : '' }}"
                            style="text-align: right;">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="price[]" readonly
                            value="{{ $d->price }}">
                        <input type="text" class="form-control" name="__price[]" readonly
                            value="{{ $d->price ? Number::format($d->price, precision: 0) : '' }}"
                            style="text-align: right;">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="discount_item[]" readonly
                            value="{{ $d->discount_item }}">
                        <input type="text" class="form-control" name="__discount_item[]" readonly
                            value="{{ $d->discount_item ? Number::format($d->discount_item, precision: 0) : '' }}"
                            style="text-align: right;">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="amount[]" readonly
                            value="{{ $d->amount }}">
                        <input type="text" class="form-control" name="__amount[]" readonly
                            value="{{ $d->amount ? Number::format($d->amount, precision: 0) : '' }}"
                            style="text-align: right;">
                    </td>

                    <td class="text-center p-1 align-middle">
                        <div class="row row-cols-auto g-3">
                            <div class="col">
                                <button type="button"
                                    class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row"></button>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>

    <tfoot>
        <tr>
            <td scope="col" colspan="8" class="text-end p-1 align-middle">
                <b>Total</b>
            </td>
            <td scope="col" class="p-1 align-middle">
                <input type="hidden" id="total" name="total" readonly
                    value="{{ $purchase_requisition?->total ?? '' }}">
                <input type="text" class="form-control" id="total_" name="total_" readonly
                    value="{{ $purchase_requisition ? Number::format($purchase_requisition->total, precision: 0) : '' }}"
                    style="text-align: right;">
            </td>
            <td scope="col" class="p-1 align-middle"></td>
        </tr>

        <tr>
            <td scope="col" colspan="8" class="text-end p-1 align-middle">
                <b>Discount</b>
            </td>
            <td scope="col" class="p-1 align-middle">
                <input type="hidden" id="discount" name="discount" readonly
                    value="{{ $purchase_requisition?->discount ?? '' }}">
                <input type="text" class="form-control" id="discount_" name="discount_" readonly
                    value="{{ $purchase_requisition?->discount ? Number::format($purchase_requisition->discount, precision: 0) : '' }}"
                    style="text-align: right;">
            </td>
            <td scope="col" class="p-1 align-middle"></td>
        </tr>

        <tr>
            <td scope="col" colspan="8" class="text-end p-1 align-middle">
                <b id='text-tax'>Tax ({{ $taxable }})</b>
            </td>
            <td scope="col" class="p-1 align-middle">
                <input type="hidden" id="tax" name="tax" readonly
                    value="{{ $purchase_requisition?->tax ?? '' }}">
                <input type="text" class="form-control" id="tax_" name="tax_" readonly
                    value="{{ $purchase_requisition ? Number::format($purchase_requisition->tax, precision: 0) : '' }}"
                    style="text-align: right;">
            </td>
            <td scope="col" class="p-1 align-middle"></td>
        </tr>

        <tr>
            <td scope="col" colspan="8" class="text-end p-1 align-middle">
                <b>Grand Total</b>
            </td>
            <td scope="col" class="p-1 align-middle">
                <input type="hidden" id="grand_total" name="grand_total" readonly
                    value="{{ $purchase_requisition?->grand_total ?? '' }}">
                <input type="text" class="form-control" id="grand_total_" name="grand_total_" readonly
                    value="{{ $purchase_requisition ? Number::format($purchase_requisition->grand_total, precision: 0) : '' }}"
                    style="text-align: right;">
            </td>
            <td scope="col" class="p-1 align-middle"></td>
        </tr>
    </tfoot>
</table>

<script>
    (() => {
        const tax_ = {{ $system_setting['tax'] }};
        const modalEl = document.querySelector('#formModal');
        const modalBody = document.querySelector('#formModal .modal-body');

        function initSelect2($scope = $(document)) {
            $scope.find('.select-select').each(function() {
                const $el = $(this);

                if ($el.hasClass('select2-hidden-accessible')) {
                    $el.select2('destroy');
                }

                $el.select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#formModal'),
                    width: $el.data('width') ?
                        $el.data('width') : ($el.hasClass('w-100') ? '100%' : 'style'),
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

        initSelect2($('#tableItem'));

        const $qty = $('#_qty_');
        const $price = $('#_price_');
        const $discount_item = $('#_discount_item_');

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

            const okNav = [
                'Backspace',
                'Delete',
                'ArrowLeft',
                'ArrowRight',
                'Home',
                'End',
                'Tab',
                'Enter'
            ];

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
            const caretRaw = typeof el.selectionStart === 'number' ?
                el.selectionStart :
                raw.length;

            const oe = e.originalEvent || e;
            const inserted = oe && typeof oe.data === 'string' ?
                oe.data :
                '';

            const prevDecSep = userDecSep;
            const justTypedSep = inserted === '.' || inserted === ',';

            const san = sanitize(raw);
            const leftSan = sanitize(raw.slice(0, caretRaw));
            const caretSan = leftSan.length;

            if (userDecSep && !san.includes(userDecSep)) {
                userDecSep = null;
            }

            const justSetDecSep = !prevDecSep && justTypedSep;

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

            const thousandsSep = userDecSep ?
                (userDecSep === ',' ? '.' : ',') :
                ',';

            const formattedInt = groupThousands(intDigits, thousandsSep);
            const formatted = keepDec ?
                formattedInt + userDecSep + fracDigits :
                formattedInt;

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

            $('#' + key).val(numbro.unformat(el.value));
        }

        $qty.on('keydown', function(e) {
            textKeyDown(e);
        });

        $qty.on('input', function(e) {
            textInput('_qty', e);
            calculateAmount();
        });

        $price.on('keydown', function(e) {
            textKeyDown(e);
        });

        $price.on('input', function(e) {
            textInput('_price', e);
            calculateAmount();
        });

        $discount_item.on('keydown', function(e) {
            textKeyDown(e);
        });

        $discount_item.on('input', function(e) {
            textInput('_discount_item', e);
            calculateAmount();
        });

        $('#addItemButton').on('click', function() {
            const tbody = $('#tableItem > tbody');

            const type = $('#_type').val();
            const description = $('#_description').val();
            const desc_vendor = $('#_desc_vendor').val();
            const uom = $('#_uom').val();

            const _qty = $('#_qty').val();
            const _qty_ = $('#_qty_').val();

            const _price = $('#_price').val();
            const _price_ = $('#_price_').val();

            const _discount_item = $('#_discount_item').val();
            const _discount_item_ = $('#_discount_item_').val();

            const _amount = $('#_amount').val();
            const _amount_ = $('#_amount_').val();

            const newRow = `
                <tr>
                    <td class="p-1 align-middle row-number">
                        #
                    </td>

                    <td class="p-1 align-middle">
                        <select class="form-select select-select detail-type" name="type[]">
                            <option value="Good" ${type == 'Good' ? 'selected' : ''}>Good</option>
                            <option value="Service" ${type == 'Service' ? 'selected' : ''}>Service</option>
                        </select>
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control order" name="order[]">
                        <input type="text" class="form-control" name="description[]" readonly value="${description}">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="text" class="form-control" name="desc_vendor[]" readonly value="${desc_vendor}">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="text" class="form-control" name="uom[]" readonly value="${uom}">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="qty[]" readonly value="${_qty}">
                        <input type="text" class="form-control" name="__qty[]" readonly value="${_qty_}" style="text-align: right;">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="price[]" readonly value="${_price}">
                        <input type="text" class="form-control" name="__price[]" readonly value="${_price_}" style="text-align: right;">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="discount_item[]" readonly value="${_discount_item}">
                        <input type="text" class="form-control" name="__discount_item[]" readonly value="${_discount_item_}" style="text-align: right;">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" name="amount[]" readonly value="${_amount}">
                        <input type="text" class="form-control" name="__amount[]" readonly value="${_amount_}" style="text-align: right;">
                    </td>

                    <td class="text-center p-1 align-middle">
                        <div class="row row-cols-auto g-3">
                            <div class="col">
                                <button type="button"
                                    class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row"></button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;

            const $newRow = $(newRow);

            tbody.append($newRow);

            initSelect2($newRow);

            $('#_qty').val('');
            $('#_qty_').val('');

            $('#_price').val('');
            $('#_price_').val('');

            $('#_discount_item').val('');
            $('#_discount_item_').val('');

            $('#_amount').val('');
            $('#_amount_').val('');

            $('#_description').val('');
            $('#_desc_vendor').val('');

            window.initPurchaseOrderItemTable = function() {
                renumberRows();
                calculateTotal();
            };

            window.initPurchaseOrderItemTable();
        });

        function renumberRows() {
            let no = 1;

            $('#tableItem > tbody > tr').each(function() {
                if ($(this).hasClass('fixed-row')) {
                    $(this).find('.row-number').text('');
                    $(this).find('.order').val('');
                    return;
                }

                $(this).find('.row-number').text(no);
                $(this).find('.order').val(no);

                no++;
            });
        }

        $('#tableItem').on('click', '.delete-row', function() {
            $(this).closest('tr').remove();

            window.initPurchaseOrderItemTable = function() {
                renumberRows();
                calculateTotal();
            };

            window.initPurchaseOrderItemTable();
        });

        function calculateAmount() {
            const qty = parseFloat($('#_qty').val()) || 0;
            const price = parseFloat($('#_price').val()) || 0;
            const discount_item = parseFloat($('#_discount_item').val()) || 0;

            const amount = (qty * price) - discount_item;

            $("#_discount_item").val(discount_item);
            $("#_discount_item_").val(numbro(discount_item).format({
                thousandSeparated: true,
                mantissa: 0
            }));

            $('#_amount').val(amount);
            $('#_amount_').val(numbro(amount).format({
                thousandSeparated: true,
                mantissa: 0
            }));
        }

        function calculateTotal() {
            let subtotal = 0;
            let discount = 0;

            $('input[name="amount[]"]').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });

            $('input[name="discount_item[]"]').each(function() {
                discount += parseFloat($(this).val()) || 0;
            });

            const total = subtotal;

            let tax = 0;
            let grandTotal = 0;

            if (total > 0) {
                if (window.poState.taxable == 'PKP') {
                    tax = tax_ / 100 * total;
                }

                grandTotal = total + tax;
            }

            $('#total').val(total || 0);
            $('#total_').val(total ? numbro(total).format({
                thousandSeparated: true,
                mantissa: 0
            }) : 0);

            $('#discount').val(discount || 0);
            $('#discount_').val(discount ? numbro(discount).format({
                thousandSeparated: true,
                mantissa: 0
            }) : 0);

            $('#tax').val(tax || 0);
            $('#tax_').val(tax ? numbro(tax).format({
                thousandSeparated: true,
                mantissa: 0
            }) : 0);

            $('#grand_total').val(grandTotal || 0);
            $('#grand_total_').val(grandTotal ? numbro(grandTotal)
                .format({
                    thousandSeparated: true,
                    mantissa: 0
                }) : 0);
        }

        window.initPurchaseOrderItemTable = function() {
            renumberRows();
            calculateTotal();

        };

        window.initPurchaseOrderItemTable();
    })();
</script>
