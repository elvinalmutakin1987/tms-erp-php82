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
            <th colspan="3" class="align-middle">Departure</th>
        </tr>
        <tr>
            <td class="p-1 align-middle">1</td>
            <td class="p-1 align-middle">
                Location
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="location_id" name="location_id">
                </select>
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">2</td>
            <td class="p-1 align-middle">
                Loading At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="loading_at" name="loading_at">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">3</td>
            <td class="p-1 align-middle">
                Complete Loading At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="complete_loading_at"
                    name="complete_loading_at">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">4</td>
            <td class="p-1 align-middle">
                Depart At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="depart_at" name="depart_at">
            </td>
        </tr>
        <tr class="table-secondary">
            <th colspan="3" class="align-middle">Arrival</th>
        </tr>
        <tr>
            <td class="p-1 align-middle">5</td>
            <td class="p-1 align-middle">
                Location
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="arrival_location_id" name="arrival_location_id">
                </select>
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">6</td>
            <td class="p-1 align-middle">
                Arrived At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="arrived_at" name="arrived_at">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">7</td>
            <td class="p-1 align-middle">
                Berthing At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="berthing_at" name="berthing_at">
            </td>
        </tr>
    </tbody>
</table>
<table class="table mb-0" id="tableUnit">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width: 2%">#</th>
            <th scope="col" style="width: 20%">Unit</th>
            <th scope="col" style="width: 25%">Item</th>
            <th scope="col" style="width: 11%">Uom 1</th>
            <th scope="col" style="width: 11%">Value 1</th>
            <th scope="col" style="width: 11%">Uom 2</th>
            <th scope="col" style="width: 11%">Value 2</th>
            <th scope="col" style="width: 2%">Action</th>
        </tr>
    </thead>
    <tbody id="tbody">
        <tr class="fixed-row">
            <td class="p-1 align-middle" style="width: 2%">
            </td>
            <td class="p-1 align-middle" style="width: 20%">
                <select class="form-select select-select" id="_unit_id" name="_unit_id">

                </select>
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_item" name="_item">

                </select>
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_uom_1" name="_uom_1">
                    <option value=""></option>
                </select>
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_uom_1" name="_uom_1">
                <input type="text" class="form-control" id="_uom_1_" name="_uom_1_">
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_uom_2" name="_uom_2">
                    <option value=""></option>
                </select>
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_uom_2" name="_uom_2">
                <input type="text" class="form-control" id="_uom_2_" name="_uom_2_">
            </td>
            <td class="p-1 align-middle" style="width:2%">
                <div class="row row-cols-auto g-3">
                    <div class="col">
                        <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1"
                            id="addUnitButton"></button>
                    </div>
                </div>
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
        $.ajax({
            url: '{{ route('dailyreport.get_project_location') }}',
            type: 'GET',
            success: function(response) {
                $('#arrival_location_id').empty();
                $('#location_id').empty();
                $.each(response.data, function(index, location) {
                    $('#arrival_location_id').append('<option value="' + location.id +
                        '">' +
                        location.name +
                        '</option>');

                    $('#location_id').append('<option value="' + location.id +
                        '">' +
                        location.name +
                        '</option>');
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
            url: '{{ route('dailyreport.get_unit_all') }}',
            type: 'GET',
            success: function(response) {
                $('#_unit_id').empty();
                $.each(response.data, function(index, unit) {
                    $('#_unit_id').append('<option value="' + unit.id +
                        '">' +
                        unit.vehicle_no +
                        '</option>');
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

        $(".timepicker").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 1
        });

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
