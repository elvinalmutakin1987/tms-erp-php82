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

    $startPeriod = $startDate->copy()->startOfYear()->format('Y-m');

    $endPeriod = $startDate->copy()->subMonth()->format('Y-m');

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
    // PTD = Previous To Date
    // ==========================================

    $oldInvoiceIds = collect();

    // Hindari periode terbalik ketika bulan Januari
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
    // 6. DAILY REPORT DETAIL AGGREGATION
    // Query hanya satu kali
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
    // 7. PREVIOUS PROFORMA DETAIL AGGREGATION
    // Query hanya satu kali
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
    // 8. CALCULATE CONTRACT RATE
    // ==========================================

    $rows = [];

    $total_amount = 0;
    $total_amount_ptd = 0;

    foreach ($contractRates as $contractRate) {
        // Pastikan rate selalu numerik
        $rate = (float) ($contractRate->rate ?? 0);

        $qty = 0;

        // --------------------------------------
        // Calculate Quantity
        // --------------------------------------

        if ($contractRate->type == 'AN') {
            $totalAN = (float) ($dailyTotals->get('AN')->total_value_2 ?? 0);

            $totalPupuk = (float) ($dailyTotals->get('Pupuk')->total_value_2 ?? 0);

            $qty = $totalAN + $totalPupuk;
        } elseif ($contractRate->type == 'ANSOL') {
            $totalANSOL = (float) ($dailyTotals->get('ANSOL')->total_value_1 ?? 0);

            $qty = $totalANSOL;
        }

        // --------------------------------------
        // Calculate Current Amount
        // --------------------------------------

        $amount = $rate * $qty;

        // --------------------------------------
        // Calculate PTD
        // --------------------------------------

        $previous = $ptdTotals->get($contractRate->id);

        $previousQty = (float) ($previous->total_qty ?? 0);

        $previousAmount = (float) ($previous->total_amount ?? 0);

        // PTD = Previous Period + Current Period

        $qty_ptd = $previousQty + $qty;

        $amount_ptd = $previousAmount + $amount;

        // --------------------------------------
        // Store Result
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
        // Calculate Grand Total
        // --------------------------------------

        $total_amount += $amount;

        $total_amount_ptd += $amount_ptd;
    }
@endphp


{{-- ==========================================
     CONTRACT INFORMATION
========================================== --}}

<div class="mb-2">

    <table class="table table-borderless table-sm w-auto mb-0">

        <tr>
            <td>Client</td>
            <td style="width: 5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;
                {{ optional($contract->client_vendor)->name }}
            </td>
        </tr>

        <tr>
            <td>Contract Type</td>
            <td style="width: 5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;
                {{ optional($contract->service)->type }}
            </td>
        </tr>

        <tr>
            <td>Progress Claim</td>
            <td style="width: 5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;
                {{ $startDate->format('F Y') }}
            </td>
        </tr>

        <tr>
            <td>Starting Date</td>
            <td style="width: 5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;
                {{ $startDate->format('d F Y') }}
            </td>
        </tr>

        <tr>
            <td>Closing Date</td>
            <td style="width: 5px">:</td>
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

        {{-- CONTRACT RATE DETAILS --}}

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
                    {{ Number::format($row['rate'], precision: 0) }}
                </td>

                <td class="text-end">
                    {{ Number::format($row['qty'], precision: 2) }}
                </td>

                <td class="text-end">
                    {{ Number::format($row['amount'], precision: 0) }}
                </td>

                <td class="text-end">
                    {{ Number::format($row['qty_ptd'], precision: 2) }}
                </td>

                <td class="text-end">
                    {{ Number::format($row['amount_ptd'], precision: 0) }}
                </td>

            </tr>
        @endforeach


        {{-- GRAND TOTAL --}}

        <tr>

            <td colspan="5" class="text-end">
                <b>TOTAL</b>
            </td>

            <td class="text-end">
                <b>
                    {{ Number::format($total_amount, precision: 0) }}
                </b>
            </td>

            <td></td>

            <td class="text-end">
                <b>
                    {{ Number::format($total_amount_ptd, precision: 0) }}
                </b>
            </td>

        </tr>

    </tbody>

</table>
