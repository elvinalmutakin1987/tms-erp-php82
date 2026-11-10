@php
    use App\Models\Contract_rate;
    use App\Models\Unit_target;
    use App\Models\Daily_report;
    use App\Models\Daily_report_detail;
    use App\Models\Proforma_invoice;
    use App\Models\Proforma_invoice_detail;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    // ==========================================
    // 1. PERIOD CONFIGURATION
    // ==========================================

    $startDate = Carbon::create((int) $year, (int) $month, 1)->startOfMonth();

    $endDate = $startDate->copy()->endOfMonth();

    // ==========================================
    // 2. CONTRACT RATE
    // ==========================================

    $contractRates = Contract_rate::query()->where('contract_id', $contract->id)->get();

    // ==========================================
    // 3. UNIT TARGET
    // ==========================================

    $unitTargetIds = Unit_target::query()->where('contract_id', $contract->id)->pluck('unit_id');

    // ==========================================
    // 4. PREVIOUS PROFORMA INVOICE
    // ==========================================

    $startPeriod = $startDate->copy()->startOfYear()->format('Y-m');

    $endPeriod = $startDate->copy()->subMonth()->format('Y-m');

    $oldInvoiceIds = collect();

    // Validasi periode agar tidak terbalik pada bulan Januari
    if ($endPeriod >= $startPeriod) {
        $oldInvoiceIds = Proforma_invoice::query()
            ->where('contract_id', $contract->id)
            ->whereBetween('periode', [$startPeriod, $endPeriod])
            ->pluck('id');
    }

    // ==========================================
    // 5. DAILY REPORT
    // ==========================================

    $dailyReportIds = Daily_report::query()
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
        ->where('service_type', 'LCT')
        ->pluck('id');

    // ==========================================
    // 6. DAILY REPORT DETAIL
    // AGGREGATION AN, PUPUK, ANSOL
    // ==========================================

    $dailyTotals = collect();

    if ($dailyReportIds->isNotEmpty() && $unitTargetIds->isNotEmpty()) {
        $dailyTotals = Daily_report_detail::query()
            ->whereIn('daily_report_id', $dailyReportIds)
            ->whereIn('unit_id', $unitTargetIds)
            ->whereIn('item', ['AN', 'Pupuk', 'ANSOL'])
            ->select('item')
            ->selectRaw('COALESCE(SUM(value_1), 0) AS total_value_1')
            ->selectRaw('COALESCE(SUM(value_2), 0) AS total_value_2')
            ->groupBy('item')
            ->get()
            ->keyBy('item');
    }

    // ==========================================
    // 7. PROFORMA INVOICE DETAIL
    // PTD AGGREGATION
    // ==========================================

    $ptdTotals = collect();

    if ($oldInvoiceIds->isNotEmpty()) {
        $ptdTotals = Proforma_invoice_detail::query()
            ->where('contract_id', $contract->id)
            ->whereIn('proforma_invoice_id', $oldInvoiceIds)
            ->whereNull('deleted_at')
            ->select('contract_rate_id')
            ->selectRaw('COALESCE(SUM(qty), 0) AS total_qty')
            ->selectRaw('COALESCE(SUM(amount), 0) AS total_amount')
            ->groupBy('contract_rate_id')
            ->get()
            ->keyBy('contract_rate_id');
    }

    // ==========================================
    // 8. CALCULATE DATA
    // ==========================================

    $rows = [];

    $total_amount = 0;
    $total_amount_ptd = 0;

    foreach ($contractRates as $contractRate) {
        // Pastikan rate tidak NULL
        $rate = (float) ($contractRate->rate ?? 0);

        $qty = 0;

        // --------------------------------------
        // CALCULATE QTY
        // --------------------------------------

        if ($contractRate->type == 'AN') {
            $totalAN = (float) data_get($dailyTotals->get('AN'), 'total_value_2', 0);

            $totalPupuk = (float) data_get($dailyTotals->get('Pupuk'), 'total_value_2', 0);

            $qty = $totalAN + $totalPupuk;
        } elseif ($contractRate->type == 'ANSOL') {
            $totalANSOL = (float) data_get($dailyTotals->get('ANSOL'), 'total_value_1', 0);

            $qty = $totalANSOL;
        }

        // --------------------------------------
        // CALCULATE AMOUNT
        // --------------------------------------

        $amount = $rate * $qty;

        // --------------------------------------
        // CALCULATE PTD
        // --------------------------------------

        $previous = $ptdTotals->get($contractRate->id);

        $previousQty = (float) data_get($previous, 'total_qty', 0);

        $previousAmount = (float) data_get($previous, 'total_amount', 0);

        // PTD = Previous + Current

        $qty_ptd = $previousQty + $qty;

        $amount_ptd = $previousAmount + $amount;

        // --------------------------------------
        // STORE RESULT
        // --------------------------------------

        $rows[] = [
            'contract_rate' => $contractRate,
            'rate' => $rate,
            'qty' => $qty,
            'amount' => $amount,
            'qty_ptd' => $qty_ptd,
            'amount_ptd' => $amount_ptd,
        ];

        // --------------------------------------
        // GRAND TOTAL
        // --------------------------------------

        $total_amount += $amount;

        $total_amount_ptd += $amount_ptd;
    }
@endphp


{{-- ==========================================
     CONTRACT INFORMATION
========================================== --}}

<div class="mb-2">

    <table style="width:100%">

        <tr>
            <td>Client</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;
                {{ optional($contract->client_vendor)->name }}
            </td>
        </tr>

        <tr>
            <td>Contract Type</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;
                {{ optional($contract->service)->type }}
            </td>
        </tr>

        <tr>
            <td>Progress Claim</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;
                {{ $startDate->format('F Y') }}
            </td>
        </tr>

        <tr>
            <td>Starting Date</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;
                {{ $startDate->format('d F Y') }}
            </td>
        </tr>

        <tr>
            <td>Closing Date</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;
                {{ $endDate->format('d F Y') }}
            </td>
        </tr>

    </table>

</div>


{{-- ==========================================
     PROFORMA INVOICE TABLE
========================================== --}}

<table class="table tableItem">

    <thead class="table-dark">

        <tr>
            <th scope="col" style="width: 5px">No.</th>

            <th scope="col">Item</th>

            <th scope="col" style="width: 8%">
                Unit
            </th>

            <th scope="col" style="width: 15%" class="text-end">
                Rate (IDR)
            </th>

            <th scope="col" style="width: 8%" class="text-end">
                Qty
            </th>

            <th scope="col" style="width: 15%" class="text-end">
                Amount (IDR)
            </th>

            <th scope="col" style="width: 8%" class="text-end">
                Qty (PTD)
            </th>

            <th scope="col" style="width: 15%" class="text-end">
                PTD Amount (IDR)
            </th>

        </tr>

    </thead>


    <tbody>

        {{-- DETAIL --}}

        @foreach ($rows as $row)
            <tr>

                <td>
                    {{ $loop->iteration }}
                </td>

                <td>
                    {{ $row['contract_rate']->service_item }}
                </td>

                <td>
                    {{ $row['contract_rate']->unit }}
                </td>

                <td class="text-end">
                    {{ Number::format((float) ($row['rate'] ?? 0), precision: 0) }}
                </td>

                <td class="text-end">
                    {{ Number::format((float) ($row['qty'] ?? 0), precision: 2) }}
                </td>

                <td class="text-end">
                    {{ Number::format((float) ($row['amount'] ?? 0), precision: 0) }}
                </td>

                <td class="text-end">
                    {{ Number::format((float) ($row['qty_ptd'] ?? 0), precision: 2) }}
                </td>

                <td class="text-end">
                    {{ Number::format((float) ($row['amount_ptd'] ?? 0), precision: 0) }}
                </td>

            </tr>
        @endforeach


        {{-- TOTAL --}}

        <tr>

            <td colspan="5" class="text-end">
                <b>TOTAL</b>
            </td>

            <td class="text-end">
                <b>
                    {{ Number::format((float) $total_amount, precision: 0) }}
                </b>
            </td>

            <td></td>

            <td class="text-end">
                <b>
                    {{ Number::format((float) $total_amount_ptd, precision: 0) }}
                </b>
            </td>

        </tr>

    </tbody>

</table>
