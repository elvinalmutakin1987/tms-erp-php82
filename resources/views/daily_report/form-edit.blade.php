@php
    use Illuminate\Support\Number;
@endphp
<table class="table mb-0" id="tableItem">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:3%">#</th>
            <th scope="col">Item</th>
            <th scope="col" class="text-center">Value</th>
        </tr>
    </thead>
    <tbody id="tbody">
        <tr class="table-secondary">
            <th colspan="3" class="align-middle">KM</th>
        </tr>
        <tr>
            <td class="p-1 align-middle">1</td>
            <td class="p-1 align-middle">
                Start
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="km_start" name="km_start"
                    value="{{ $daily_report->km_start }}">
                <input type="text" class="form-control" id="_km_start" name="_km_start"
                    value="{{ $daily_report->km_start ? Number::format($daily_report->km_start, precision: 0) : '' }}">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">2</td>
            <td class="p-1 align-middle">
                Finish
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="km_finish" name="km_finish"
                    value="{{ $daily_report->km_finish }}">
                <input type="text" class="form-control" id="_km_finish" name="_km_finish"
                    value="{{ $daily_report->km_finish ? Number::format($daily_report->km_finish, precision: 0) : '' }}">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">3</td>
            <td class="p-1 align-middle">
                Total
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="km_total" name="km_total"
                    value="{{ $daily_report->km_total }}">
                <input type="text" class="form-control" id="_km_total" name="_km_total"
                    value="{{ $daily_report->km_total ? Number::format($daily_report->km_total, precision: 0) : '' }}"
                    readonly>
            </td>
        </tr>
        <tr class="table-secondary">
            <th colspan="3" class="align-middle">Person</th>
        </tr>
        <tr>
            <td class="p-1 align-middle">4</td>
            <td class="p-1 align-middle">
                Operator
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control" id="operator" name="operator"
                    value="{{ $daily_report->operator }}">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">5</td>
            <td class="p-1 align-middle">
                Helper
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control" id="helper" name="helper"
                    value="{{ $daily_report->helper }}">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">6</td>
            <td class="p-1 align-middle">
                Load
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="load" name="load"
                    value="{{ $daily_report->load }}">
                <input type="text" class="form-control" id="_load" name="_load"
                    value="{{ $daily_report->load ? Number::format($daily_report->load, precision: 0) : '' }}">
            </td>
        </tr>
        <tr class="table-secondary">
            <th colspan="3" class="align-middle">Refule</th>
        </tr>
        <tr>
            <td class="p-1 align-middle">6</td>
            <td class="p-1 align-middle">
                From
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="refule_type" name="refule_type">
                    <option value="KPC" {{ $daily_report->refule_type == 'KPC' ? 'selected' : '' }}>KPC</option>
                    <option value="POM Bensin" {{ $daily_report->refule_type == 'POM Bensin' ? 'selected' : '' }}>POM
                        Bensin
                    </option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">7</td>
            <td class="p-1 align-middle">
                Liter
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="refule_liter" name="refule_liter"
                    value="{{ $daily_report->refule_liter }}">
                <input type="text" class="form-control" id="_refule_liter" name="_refule_liter"
                    value="{{ $daily_report->refule_liter ? Number::format($daily_report->refule_liter, precision: 0) : '' }}">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">8</td>
            <td class="p-1 align-middle">
                KM
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="refule_km" name="refule_km"
                    value="{{ $daily_report->refule_km }}">
                <input type="text" class="form-control" id="_refule_km" name="_refule_km"
                    value="{{ $daily_report->refule_km ? Number::format($daily_report, precision: 0) : '' }}">
            </td>
        </tr>
    </tbody>
</table>

<script>
    $('#refule_type').each(function() {
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

    (() => {
        $('.select-select').each(function() {
            const $el = $(this);
            $el.select2({
                    theme: "bootstrap-5",
                    dropdownParent: $('#formModal'),
                    width: $el.data('width') ? $el.data('width') : ($el.hasClass('w-100') ? '100%' :
                        'style'),
                    selectOnClose: false,
                    minimumResultsForSearch: 0,
                })
                .on('select2:open', function() {
                    setTimeout(function() {
                        const $search = $(
                            '.select2-container--open .select2-search__field');
                        $search.trigger('focus');
                        $('.select2-container--open').css('z-index', 1056);
                    }, 0);
                });
        });

        const $km_start = $('#_km_start');
        const $km_finish = $('#_km_finish');
        const $km_total = $('#km_total');
        const $refule_liter = $('#_refule_liter');
        const $refule_km = $('#_refule_km');
        const $load = $('#_load');

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

        function updateKmTotal() {
            const startVal = $('#km_start').val();
            const finishVal = $('#km_finish').val();

            if (startVal === '' || finishVal === '') {
                $('#km_total').val('');
                $('#_km_total').val('');
                return;
            }

            const start = parseFloat(startVal) || 0;
            const finish = parseFloat(finishVal) || 0;
            const total = finish - start;

            // simpan nilai mentah ke hidden
            $('#km_total').val(total);

            // tampilkan nilai formatted ke textbox readonly
            $('#_km_total').val(
                numbro(total).format({
                    thousandSeparated: true,
                    mantissa: 0
                })
            );
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
            updateKmTotal();
        }

        $km_start.off('keydown').on('keydown', function(e) {
            textKeyDown(e);
        });

        $km_start.off('input').on('input', function(e) {
            textInput("km_start", e);
        });

        $km_finish.off('keydown').on('keydown', function(e) {
            textKeyDown(e);
        });

        $km_finish.off('input').on('input', function(e) {
            textInput("km_finish", e);
        });

        $refule_liter.off('keydown').on('keydown', function(e) {
            textKeyDown(e);
        });

        $refule_liter.off('input').on('input', function(e) {
            textInput("refule_liter", e);
        });

        $refule_km.off('keydown').on('keydown', function(e) {
            textKeyDown(e);
        });

        $refule_km.off('input').on('input', function(e) {
            textInput("refule_km", e);
        });

        $load.off('keydown').on('keydown', function(e) {
            textKeyDown(e);
        });

        $load.off('input').on('input', function(e) {
            textInput("load", e);
        });
    })();
</script>
