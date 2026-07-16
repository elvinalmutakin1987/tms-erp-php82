@php
    use App\Models\Maintenance;
    use App\Models\Contract_rate;
    use App\Models\Contract_fmf;
    use App\Models\Unit_target;
    use App\Models\Daily_report;
    use App\Models\Daily_report_detail;
    use App\Models\Proforma_invoice;
    use App\Models\Proforma_invoice_detail;
    use App\Models\Unit;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();
@endphp

<h6 class="mb-2" style="display: inline-block;">
    <table style="width:100%">
        <tr>
            <td>Client</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;{{ optional($contract->client_vendor)->name }}
            </td>
        </tr>
        <tr>
            <td>Contract Type</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;{{ optional($contract->service)->type }}
            </td>
        </tr>
        <tr>
            <td>Progress Claim</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;{{ Carbon::parse($startDate)->format('F Y') }}
            </td>
        </tr>
        <tr>
            <td>Starting Date</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;{{ Carbon::parse($startDate)->format('d F Y') }}
            </td>
        </tr>
        <tr>
            <td>Closing Date</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;{{ Carbon::parse($endDate)->format('d F Y') }}
            </td>
        </tr>
    </table>
</h6>

<table class="table mb-0" id="tableItem">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:3%">#</th>
            <th scope="col">Item</th>
            <th scope="col" style="width:15%">Unit</th>
            <th scope="col" style="width:15%" class="text-end">Rate</th>
            <th scope="col" style="width:10%" class="text-end">Qty</th>
            <th scope="col" style="width:15%" class="text-end">Amount</th>
            <th scope="col" style="width:2%">Action</th>
        </tr>
    </thead>
    <tbody id="tbody">
        <tr class="fixed-row">
            <td class="p-1 align-middle">

            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control" id="_item" name="_item">
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-pallet" id="_unit_target" name="_unit_target">
                    @foreach ($unit_target as $d)
                        <option value="{{ $d->unit_id }}">{{ $d->unit->vehicle_no }}</option>
                    @endforeach
                </select>
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-pallet text-end" id="_contract_rate" name="_contract_rate">
                    @foreach ($contract_rate as $d)
                        <option value="{{ $d->rate }}">{{ Number::format($d->rate, precision: 0) }}</option>
                    @endforeach
                </select>
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_qty" name="_qty">
                <input type="text" class="form-control" id="_qty_" name="_qty_" style="text-align: right;">
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
        @foreach ($proforma_invoice->proforma_invoice_detail as $d)
            @php
                $unit = Unit::find($d->unit_id);
            @endphp
            <tr>
                <td class="p-1 align-middle row-number">
                    {{ $loop->iteration }}
                </td>
                <td class="p-1 align-middle">
                    <input type="text" class="form-control" id="service_item" name="service_item[]" readonly
                        value="{{ $d->service_item }}">
                </td>
                <td class="p-1 align-middle">
                    <input type="hidden" class="form-control" id="unit_id" name="unit_id[]" readonly
                        value="{{ $d->unit_id }}">
                    <input type="text" class="form-control" id="vehicle_no" name="vehicle_no[]" readonly
                        value="{{ $unit->vehicle_no }}">
                </td>
                <td class="p-1 align-middle">
                    <input type="hidden" class="form-control" id="rate" name="rate[]" readonly
                        value="{{ $d->rate }}">
                    <input type="text" class="form-control" id="__rate" name="__rate[]" readonly
                        value="{{ Number::format($d->rate, precision: 0) }}" style="text-align: right;">
                </td>
                <td class="p-1 align-middle">
                    <input type="hidden" class="form-control" id="qty" name="qty[]" readonly
                        value="{{ $d->qty }}">
                    <input type="text" class="form-control" id="__qty" name="__qty[]" readonly
                        value="{{ Number::format($d->qty, precision: 2) }}" style="text-align: right;">
                </td>
                <td class="p-1 align-middle">
                    <input type="hidden" class="form-control amount" name="amount[]" readonly
                        value="{{ $d->amount }}">
                    <input type="text" class="form-control" name="__amount[]" readonly
                        value="{{ Number::format($d->amount, precision: 0) }}" style="text-align: right;">
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
    </tbody>
    <tfoot>
        <tr>
            <td scope="col" colspan="5" class="text-end p-1 align-middle"><b>Total</b>
            </td>
            <td scope="col" class="p-1 align-middle">
                <input type="hidden" id="total" name="total" readonly
                    value="{{ $proforma_invoice->total }}">
                <input type="text" class="form-control" id="total_" name="total_" readonly
                    style="text-align: right;" value="{{ Number::format($proforma_invoice->total, precision: 0) }}">
            </td>
            <td scope="col" class="p-1 align-middle"></td>
        </tr>
    </tfoot>
</table>

<script>
    (() => {
        const modalEl = document.querySelector('#formModal');
        const modalBody = document.querySelector('#formModal .modal-body');

        const $qty = $('#_qty_');

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
            calculateAmount();
        });

        $('#addItemButton').on('click', function() {
            var tbody = $("#tableItem > tbody");
            var _item = $("#_item").val();
            var _unit_id = $("#_unit_target").val();
            var _vehicle_no = $("#_unit_target option:selected").text();
            var _qty = $("#_qty").val();
            var _qty_ = $("#_qty_").val();
            var _rate = $("#_contract_rate").val();
            var _rate_ = $("#_contract_rate option:selected").text();
            var _amount = $("#_amount").val();
            var _amount_ = $("#_amount_").val();
            var newRow = `
                <tr>
                    <td class="p-1 align-middle row-number">
                        #
                    </td>
                    <td class="p-1 align-middle">
                       <input type="text" class="form-control" id="service_item" name="service_item[]" readonly value="${_item}">
                    </td>
                     <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="unit_id" name="unit_id[]" readonly value="${_unit_id}">
                       <input type="text" class="form-control" id="vehicle_no" name="vehicle_no[]" readonly value="${_vehicle_no}">
                    </td>
                     <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="rate" name="rate[]" readonly value="${_rate}">
                       <input type="text" class="form-control" id="__rate" name="__rate[]" readonly value="${_rate_}" style="text-align: right;">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control" id="qty" name="qty[]" readonly value="${_qty}">
                       <input type="text" class="form-control" id="__qty" name="__qty[]" readonly value="${_qty_}" style="text-align: right;">
                    </td>
                    <td class="p-1 align-middle">
                       <input type="hidden" class="form-control amount" name="amount[]" readonly value="${_amount}">
                        <input type="text" class="form-control" name="__amount[]" readonly value="${_amount_}" style="text-align: right;">
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
            $("#_item").val('');
            $("#_qty").val('');
            $("#_qty_").val('');
            $("#_amount").val('');
            $("#_amount_").val('');
            $("#_unit_target").val('').trigger('change');
            tbody.append(newRow);
            renumberRows();
            calculateTotal();
        });

        function renumberRows() {
            let no = 1;

            $('#tableItem > tbody > tr').each(function() {
                if ($(this).hasClass('fixed-row')) {
                    $(this).find('.row-number').text('');
                    return;
                }
                $(this).find('.row-number').text(no);
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
            const rate = parseFloat($("#_contract_rate").val()) || 0;

            const amount = qty * rate;

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

            $("#total").val(total || 0);
            $("#total_").val(total ? numbro(total).format({
                thousandSeparated: true,
                mantissa: 0
            }) : 0);
        }
    })();
</script>
