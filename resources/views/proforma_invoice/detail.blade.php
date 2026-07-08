@php
    use App\Models\Maintenance;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    $unit_id = $proforma_invoice->unit_id;
    $price = $proforma_invoice->unit_target->price;
    $target = $proforma_invoice->unit_target->target;
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
    </table>
</h6>

@if ($contract->service->type == 'Unit Rental')
    @php
        $excelRound = function ($value, int $precision = 2) {
            return round((float) $value, $precision, PHP_ROUND_HALF_UP);
        };
        $year = (int) $year;
        $month = (int) $month;
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $hariKerja = $startDate->daysInMonth;
        $totalJamKerja = $hariKerja * 24;
        $totalBreakdownSeconds = Maintenance::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('unit_id', $unit_id)
            ->where('status', '!=', 'Draft')
            ->selectRaw('COALESCE(SUM(TIME_TO_SEC(work_duration)), 0) as total_seconds')
            ->value('total_seconds');
        $total_breakdown = $excelRound($totalBreakdownSeconds / 3600, 2);
        $price = $excelRound($price ?? 0, 2);
        $target = $excelRound($target ?? 0, 2);
        if ($totalJamKerja > 0) {
            $pa = 100 - ($total_breakdown / $totalJamKerja) * 100;
        } else {
            $pa = 0;
        }
        $pa = max(0, min(100, $pa));
        $pa = $excelRound($pa, 2);
        $penalty = $pa >= $target ? 0 : $excelRound(((100 - $pa) / 100) * $price, 2);
        $total_payment = $price - $penalty;
        $total_payment = max(0, min($price, $total_payment));
        $total_payment = $excelRound($total_payment, 2);
    @endphp

    <table class="table table-borderless tableItem">
        <thead class="table-dark">
            <tr>
                <th scope="col" style="width: 30%">Order Number</th>
                <th scope="col" style="width: 50%">Description</th>
                <th scope="col" style="width: 20%" colspan="2">Amount</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="p-1">
                    <b>{{ $contract->contract_no }}</b>
                </td>

                <td class="p-1">
                    {{ $unit_target->unit->description }}
                    (<b>{{ $unit_target->unit->vehicle_no }}</b>)
                </td>

                <td class="p-1" width="10px"></td>
                <td class="p-1"></td>
            </tr>

            <tr>
                <td class="p-1">
                    (Rate IDR {{ Number::format($price, precision: 0) }})
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
                    1. Target PA &nbsp;&nbsp; {{ Number::format($target, precision: 2) }}%
                </td>

                <td class="p-1" width="10px"></td>
                <td class="p-1"></td>
            </tr>

            <tr>
                <td class="p-1"></td>

                <td class="p-1">
                    2. Actual Hari Kerja {{ $hariKerja }} <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ({{ $hariKerja }} hari @ Rp.
                    {{ Number::format($price, precision: 0) }})
                </td>

                <td class="p-1" width="10px">IDR</td>
                <td class="p-1" style="text-align: right">
                    {{ Number::format($price, precision: 0) }}
                </td>
            </tr>

            <tr>
                <td class="p-1"></td>

                <td class="p-1">
                    3. Aktual PA <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Jam Tersedia {{ $hariKerja }} x 24 Jam =
                    {{ Number::format($totalJamKerja, precision: 0) }} Jam
                    <br>

                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Breakdown =
                    <b>{{ Number::format($total_breakdown, precision: 2) }}</b> Jam
                    <br>

                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; PA =
                    {{ Number::format($pa, precision: 2) }}%
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
                    {{ Number::format($penalty, precision: 0) }}
                </td>
            </tr>

            <tr>
                <td class="p-1"></td>

                <td class="p-1">
                    5. Total Payment
                </td>

                <td class="p-1" width="10px">IDR</td>

                <td class="p-1" style="text-align: right">
                    {{ Number::format($total_payment, precision: 0) }}
                </td>
            </tr>
        </tbody>
    </table>
@elseif($contract->service->type == 'LCT')

@elseif($contract->service->type == 'Fuel Truck Rental')

@elseif($contract->service->type == 'Explosive Material Rental')
@endif
