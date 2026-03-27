<table class="table mb-0" id="tableItem">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:3%">#</th>
            <th scope="col">From</th>
            <th scope="col">Loading</th>
            <th scope="col">Complete Loading</th>
            <th scope="col">Depart</th>
            <th scope="col">To</th>
            <th scope="col">Arrived</th>
            <th scope="col">Berthing</th>
        </tr>
    </thead>
    <tbody id="tbody">
        <tr class="table-secondary">
            <th colspan="8" class="align-middle">Trip</th>
        </tr>
        <tr>
            <td class="p-1 align-middle">1</td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_location_id" name="_location_id">
                </select>
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="_loading_at" name="_loading_at">
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="_complete_loading_at"
                    name="_complete_loading_at">
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="_depart_at" name="_depart_at">
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_depart_location_id" name="_depart_location_id">

                </select>
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="_arrived_at" name="_arrived_at">
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="_berthing_at" name="_berthing_at">
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
    })();
</script>
