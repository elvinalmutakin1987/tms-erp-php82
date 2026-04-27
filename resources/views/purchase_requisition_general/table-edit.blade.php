@php
    use Illuminate\Support\Number;
@endphp
<tr class="fixed-row">
    <td class="p-1 align-middle">

    </td>
    <td class="p-1 align-middle">
        <input type="text" class="form-control" id="_description" name="_description">
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
        <input type="text" class="form-control" id="_qty_" name="_qty_">
    </td>
    <td class="p-1 align-middle">
        <input type="hidden" class="form-control" id="_price" name="_price">
        <input type="text" class="form-control" id="_price_" name="_price_">
    </td>
    <td class="p-1 align-middle">
        <input type="hidden" class="form-control" id="_amount" name="_amount" readonly>
        <input type="text" class="form-control" id="_amount_" name="_amount_" readonly>
    </td>
    <td class="p-1 align-middle" style="width:2%">
        <div class="row row-cols-auto g-3">
            <div class="col">
                <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1" id="addItemButton"></button>
            </div>
        </div>
    </td>
</tr>

@foreach ($purchase_requisition_detail as $d)
    <tr>
        <td class="p-1 align-middle row-number">
            {{ $loop->iteration }}
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control order" id="order" name="order[]"
                value="{{ $loop->iteration }}">
            <input type="text" class="form-control" id="description" name="description[]" readonly
                value="{{ $d->description }}">
        </td>
        <td class="p-1 align-middle">
            <input type="text" class="form-control" id="uom" name="uom[]" readonly
                value="{{ $d->uom }}">
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control" id="qty" name="qty[]" readonly
                value="{{ $d->qty }}">
            <input type="text" class="form-control" id="__qty" name="__qty[]" readonly
                value="{{ $d->qty ? Number::format($d->qty, precision: 0) : '' }}">
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control" id="price" name="price[]" readonly
                value="{{ $d->price }}">
            <input type="text" class="form-control" id="__price" name="__price[]" readonly
                value="{{ $d->price ? Number::format($d->price, precision: 0) : '' }}">
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control" id="amount" name="amount[]" readonly
                value="{{ $d->amount }}">
            <input type="text" class="form-control" id="__amount" name="__amount[]" readonly
                value="{{ $d->amount ? Number::format($d->amount, precision: 0) : '' }}">
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
@endforeach

<script>
    (() => {
        const tax_ = {{ $system_setting['tax'] }};
        const modalEl = document.querySelector('#formModal');
        const modalBody = document.querySelector('#formModal .modal-body');

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

        const $qty = $('#_qty_');
        const $price = $('#_price_');

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

        $qty.on('keydown', function(e) {
            textKeyDown(e);
        });

        $qty.on('input', function(e) {
            textInput("_qty", e);
        });

        $price.on('keydown', function(e) {
            textKeyDown(e);
        });

        $price.on('input', function(e) {
            textInput("_price", e);
            calculateAmount();
        });

        $('#addItemButton').on('click', function() {
            var tbody = $("#tableItem > tbody");
            var description = $("#_description").val();
            var uom = $("#_uom").val();
            var _qty = $("#_qty").val();
            var _qty_ = $("#_qty_").val();
            var _price = $("#_price").val();
            var _price_ = $("#_price_").val();
            var _amount = $("#_amount").val();
            var _amount_ = $("#_amount_").val();
            var newRow = `
                <tr>
                    <td class="p-1 align-middle row-number">
                        #
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control order" id="order" name="order[]">
                       <input type="text" class="form-control" id="description" name="description[]" readonly value="${description}">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="text" class="form-control" id="uom" name="uom[]" readonly value="${uom}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="qty" name="qty[]" readonly value="${_qty}">
                       <input type="text" class="form-control" id="__qty" name="__qty[]" readonly value="${_qty_}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="price" name="price[]" readonly value="${_price}">
                       <input type="text" class="form-control" id="__price" name="__price[]" readonly value="${_price_}">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="amount" name="amount[]" readonly value="${_amount}">
                       <input type="text" class="form-control" id="__amount" name="__amount[]" readonly value="${_amount_}">
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
            $("#_qty").val('');
            $("#_qty_").val('');
            $("#_price").val('');
            $("#_price_").val('');
            $("#_amount").val('');
            $("#_amount_").val('');
            $("#_description").val('');
            tbody.append(newRow);
            renumberRows();
            calculateTotal();
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

        $("#tableItem").on("click", ".delete-row", function() {
            $(this).closest("tr").remove();

            if ($(this).hasClass('fixed-row')) {
                return;
            }

            $(this).remove();
            renumberRows();
            calculateTotal();
        });

        function calculateAmount() {
            const qty = parseFloat($("#_qty").val()) || 0;
            const price = parseFloat($("#_price").val()) || 0;

            const amount = qty * price;

            $("#_amount").val(amount);
            $("#_amount_").val(numbro(amount).format({
                thousandSeparated: true,
                mantissa: 0
            }));
        }

        function calculateTotal() {
            let total = 0;

            $('input[name="amount[]"]').each(function() {
                total += parseFloat($(this).val()) || 0;
            });

            let tax = 0;
            let grandTotal = 0;

            if (total > 0) {
                tax = tax_ / 100 * total;
                grandTotal = total + tax;
            }

            $("#total").val(total || '');
            $("#total_").val(total ? numbro(total).format({
                thousandSeparated: true,
                mantissa: 0
            }) : '');

            $("#tax").val(tax || '');
            $("#tax_").val(tax ? numbro(tax).format({
                thousandSeparated: true,
                mantissa: 0
            }) : '');

            $("#grand_total").val(grandTotal || '');
            $("#grand_total_").val(grandTotal ? numbro(grandTotal).format({
                thousandSeparated: true,
                mantissa: 0
            }) : '');
        }
    })();
</script>
