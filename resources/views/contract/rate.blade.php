<table class="table mb-0" id="tableItem">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:3%">#</th>
            <th scope="col">Item No.</th>
            <th scope="col">Description</th>
            <th scope="col">Rate</th>
        </tr>
    </thead>
    <tbody>
        @if ($service_item->count() > 0)
            @foreach ($service_item as $d)
                <tr>
                    <td class="p-1 align-middle">{{ $loop->iteration }}</td>
                    <td class="p-1 align-middle">{{ $d->item_no }}</td>
                    <td class="p-1 align-middle">{{ $d->item_des }}</td>
                    <td class="p-1 align-middle">
                        <input type="hidden" class="form-control" id="service_item_id{{ $d->id }}"
                            name="service_item_id[]" value="{{ $d->id }}">
                        <input type="hidden" class="form-control" id="rate{{ $d->id }}" name="rate[]">
                        <input type="text" class="form-control" id="_rate{{ $d->id }}" name="_rate[]">
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="p-1 align-middle" colspan="4">No data showed.</td>
            </tr>
        @endif
    </tbody>
</table>

<script>
    (() => {
        @foreach ($service_item as $d)
            const $rate{{ $d->id }} = $('#_rate{{ $d->id }}');
        @endforeach

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

        @foreach ($service_item as $d)
            $rate{{ $d->id }}.on('keydown', function(e) {
                textKeyDown(e);
            });

            $rate{{ $d->id }}.on('input', function(e) {
                textInput("rate{{ $d->id }}", e);
            });
        @endforeach
    })();
</script>
