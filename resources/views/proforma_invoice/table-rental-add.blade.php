@php
    use App\Models\Maintenance;
    use Illuminate\Support\Number;
    use Carbon\Carbon;
@endphp

<h6 class="mb-2" style="display: inline-block;">
    <b>Client : {{ optional($contract->client_vendor)->name }}</b>
</h6>

@foreach ($data['unit_target'] as $unittarget)
    @php
        $year = (int) $data['year'];
        $month = (int) $data['month'];

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Jumlah hari dalam bulan tersebut
        $hariKerja = $startDate->daysInMonth;

        // Total breakdown dalam jam
        $total_breakdown = Maintenance::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('unit_id', $unittarget->unit_id)
            ->where('status', '!=', 'Draft')
            ->selectRaw('COALESCE(ROUND(SUM(TIME_TO_SEC(work_duration)) / 3600, 2), 0) as total_duration_decimal')
            ->value('total_duration_decimal');

        $total_breakdown = (float) $total_breakdown;

        $price = (float) ($unittarget->price ?? 0);
        $target = (float) ($unittarget->target ?? 0);

        // Total jam tersedia dalam bulan tersebut
        $totalJamKerja = $hariKerja * 24;

        // Perhitungan PA
        $pa = $totalJamKerja > 0 ? 100 - ($total_breakdown / $totalJamKerja) * 100 : 0;

        // Supaya PA tidak minus dan tidak lebih dari 100
        $pa = max(0, min(100, $pa));

        // Total payment
        // Jika PA >= target, bayar full price.
        // Jika PA < target, bayar proporsional terhadap target.
        if ($target > 0) {
            $total_payment = $pa >= $target ? $price : ($pa / $target) * $price;
        } else {
            $total_payment = 0;
        }

        // Supaya payment tidak melebihi price
        $total_payment = min($price, $total_payment);

        // Penalty
        $penalty = $price - $total_payment;
    @endphp

    <table class="table table-borderless tableItem">
        <thead class="table-dark">
            <tr>
                <th scope="col" style="width: 30%">Order Number</th>
                <th scope="col" style="width: 30%">Description</th>
                <th scope="col" style="width: 30%" colspan="2">Amount</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="p-1">
                    <b>{{ $contract->contract_no }}</b>
                </td>

                <td class="p-1">
                    {{ optional($unittarget->unit)->description }}
                    (<b>{{ optional($unittarget->unit)->vehicle_no }}</b>)
                </td>

                <td class="p-1" width="10px"></td>
                <td class="p-1"></td>
            </tr>

            <tr>
                <td class="p-1">
                    (Rate IDR {{ Number::format($price, precision: 0, locale: 'id') }})
                </td>

                <td class="p-1">
                    Periode
                    <b>
                        {{ $startDate->locale('id')->translatedFormat('d F Y') }}
                        -
                        {{ $endDate->locale('id')->translatedFormat('d F Y') }}
                    </b>
                </td>

                <td class="p-1" width="10px"></td>
                <td class="p-1"></td>
            </tr>

            <tr>
                <td class="p-1"></td>

                <td class="p-1">
                    1. Target PA &nbsp;&nbsp; {{ Number::format($target, precision: 2, locale: 'id') }}%
                </td>

                <td class="p-1" width="10px"></td>
                <td class="p-1"></td>
            </tr>

            <tr>
                <td class="p-1"></td>

                <td class="p-1">
                    2. Actual Hari Kerja {{ $hariKerja }} <br>
                    &nbsp;&nbsp;&nbsp;&nbsp; ({{ $hariKerja }} hari @ Rp.
                    {{ Number::format($price, precision: 0, locale: 'id') }})
                </td>

                <td class="p-1" width="10px">IDR</td>
                <td class="p-1" style="text-align: right">
                    {{ Number::format($price, precision: 0, locale: 'id') }}
                </td>
            </tr>

            <tr>
                <td class="p-1"></td>

                <td class="p-1">
                    3. Aktual PA <br>
                    &nbsp;&nbsp;&nbsp;&nbsp; Jam Tersedia {{ $hariKerja }} x 24 Jam =
                    {{ Number::format($totalJamKerja, precision: 0, locale: 'id') }} Jam
                    <br>

                    &nbsp;&nbsp;&nbsp;&nbsp; Breakdown =
                    <b>{{ Number::format($total_breakdown, precision: 2, locale: 'id') }}</b> Jam
                    <br>

                    &nbsp;&nbsp;&nbsp;&nbsp; PA =
                    {{ Number::format($pa, precision: 2, locale: 'id') }}%
                </td>

                <td class="p-1" width="10px"></td>
                <td class="p-1"></td>
            </tr>

            <tr>
                <td class="p-1"></td>

                <td class="p-1">
                    4. Penalty / Potongan Breakdown (PA)
                </td>

                <td class="p-1" width="10px">IDR</td>

                <td class="p-1" style="text-align: right">
                    {{ Number::format($penalty, precision: 0, locale: 'id') }}
                </td>
            </tr>

            <tr>
                <td class="p-1"></td>

                <td class="p-1">
                    5. Total Payment
                </td>

                <td class="p-1" width="10px">IDR</td>

                <td class="p-1" style="text-align: right">
                    {{ Number::format($total_payment, precision: 0, locale: 'id') }}
                </td>
            </tr>
        </tbody>
    </table>
@endforeach
