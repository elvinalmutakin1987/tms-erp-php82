@php
    use Illuminate\Support\Number;
@endphp
<form enctype="multipart/form-data">
    <div class="row mb-2">
        <div class="col">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0 12px;">
                <tr>
                    <td width="30%">
                        Number<br>
                        <b>{{ $maintenance->maintenance_no }}</b>
                    </td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td width="30%">
                        Unit<br>
                        <b>{{ $maintenance->unit->vehicle_no }}</b>
                    </td>
                    <td width="30%">
                        Date<br>
                        <b>{{ $maintenance->date }}</b>
                    </td>
                    <td width="30%">
                        Vendor<br>
                        <b>{{ $maintenance->client_vendor->name }}</b>
                    </td>
                </tr>
                <tr>
                    <td width="30%">
                        Mechanic<br>
                        <b>{{ $maintenance->mechanic }}</b>
                    </td>
                    <td width="30%">
                        Hour Meter<br>
                        <b>{{ Number::format($maintenance->hour_mter ?? 0, precision: 0) }}</b>
                    </td>
                    <td width="30%">
                        KM/HM<br>
                        <b>{{ Number::format($maintenance->km_hm ?? 0, precision: 0) }}</b>
                    </td>
                </tr>
                <tr>
                    <td width="30%">
                        Start<br>
                        <b>{{ \Carbon\Carbon::parse($maintenance->start)->format('H:i') }}</b>
                    </td>
                    <td width="30%">
                        Finish<br>
                        <b>{{ \Carbon\Carbon::parse($maintenance->finish)->format('H:i') }}</b>
                    </td>
                    <td width="30%">
                        Work Duration<br>
                        <b>{{ \Carbon\Carbon::parse($maintenance->work_duration)->format('H:i') }}</b>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <table class="table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width:3%">#</th>
                        <th scope="col" style="width:20%">Action</th>
                        <th scope="col">Item</th>
                        <th scope="col" style="width:20%">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp

                    @foreach ($maintenance_detail as $d)
                        <tr>
                            <td class="p-1 align-middle">{{ $no++ }}</td>

                            <td class="p-1 align-middle">
                                {{ $d->action }}
                                <input type="hidden" class="form-control" name="maintenance_item_id[]"
                                    value="{{ $d->maintenance_item_id }}">
                            </td>

                            <td class="p-1 align-middle">
                                {{ $d->maintenance_item->name }}
                            </td>

                            <td class="p-1 align-middle">
                                <input type="hidden" class="cost" name="cost[]" value="{{ $d->cost ?? '' }}">
                                <input type="text" class="form-control cost-text text-end" name="_cost[]"
                                    value="{{ $d->cost ? Number::format($d->cost) : '' }}" autocomplete="off"
                                    inputmode="numeric">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="p-1 align-middle text-end" colspan="3">Total</td>
                        <td class="p-1 align-middle">
                            <input type="text" class="form-control text-end" id="_cost_total" name="_cost_total"
                                readonly
                                value="{{ $maintenance->cost_total ? Number::format($maintenance->cost_total) : 0 }}">
                            <input type="hidden" class="form-control" name="cost_total" id="cost_total"
                                value="{{ $maintenance->cost_total ?? '0' }}">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</form>

<script>
    (function() {
        // separator ribuan untuk semua cost dan total
        var COST_THOUSANDS_SEPARATOR = ',';

        $(document).ready(function() {
            $('.cost-text').each(function() {
                syncRowCost($(this));
            });

            calculateTotal();
        });

        $(document).on('keydown', '.cost-text', function(e) {
            textKeyDown_(e);
        });

        $(document).on('input', '.cost-text', function() {
            syncRowCost($(this));
            calculateTotal();
        });

        function textKeyDown_(e) {
            const allowedKeys = [
                'Backspace', 'Delete', 'Tab', 'Escape', 'Enter',
                'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown',
                'Home', 'End'
            ];

            if (
                allowedKeys.includes(e.key) ||
                (e.ctrlKey && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase()))
            ) {
                return;
            }

            if (!/^[0-9]$/.test(e.key)) {
                e.preventDefault();
            }
        }

        function syncRowCost($input) {
            let raw = $input.val() || '';

            // ambil angka saja
            let digits = raw.replace(/\D/g, '');

            // buang nol di depan, tapi tetap sisakan 1 nol jika nilainya nol
            digits = digits.replace(/^0+(?=\d)/, '');

            const numericValue = digits === '' ? 0 : parseInt(digits, 10);

            // update hidden cost pada row yang sama
            $input.closest('tr').find('.cost').val(numericValue);

            // update tampilan input
            $input.val(digits === '' ? '' : formatThousands(digits));
        }

        function calculateTotal() {
            let total = 0;

            $('.cost').each(function() {
                total += parseInt($(this).val() || 0, 10);
            });

            $('#cost_total').val(total);
            $('#_cost_total').val(formatThousands(String(total)));
        }

        function formatThousands(value) {
            let digits = String(value || '').replace(/\D/g, '');

            if (digits === '') return '0';

            return digits.replace(/\B(?=(\d{3})+(?!\d))/g, COST_THOUSANDS_SEPARATOR);
        }

    })();
</script>
