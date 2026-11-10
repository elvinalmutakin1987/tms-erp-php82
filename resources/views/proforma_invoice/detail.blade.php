@php
    use Carbon\Carbon;
    use Illuminate\Support\Number;
    use App\Models\Approval_flow;
    use App\Models\Approval_status;
    use App\Models\Approval_process;
    use App\Models\Approval_step;
    use App\Models\Proforma_invoice;
    use App\Models\Proforma_invoice_detail;
    use App\Models\Contract;
    use App\Models\Contract_rate;
    use App\Models\Contract_fmf;
    use App\Models\Unit_target;
    use App\Models\Unit;
    use App\Models\Maintenance;
    use App\Models\Daily_report;
    use App\Models\Daily_report_detail;

    /* ============================================================
       DEBUG HELPER — deteksi baris error
       Set $__debug = false di production
       ============================================================ */
    $__debug = true;

    $__report = function ($e, $line = null, $label = '') use ($__debug) {
        if (!$__debug) {
            return;
        }
        $lineInfo = $line ? "Blade line ~{$line}" : "Compiled line {$e->getLine()}";
        echo '<div style="background:#fee;color:#900;padding:8px;border:1px solid #900;' .
            'margin:4px 0;font-family:monospace;font-size:12px;text-align:left">' .
            '<b>ERROR</b>' .
            ($label ? " [{$label}]" : '') .
            ': ' .
            e($e->getMessage()) .
            '<br><b>Location:</b> ' .
            $lineInfo .
            '<br><b>Compiled line:</b> ' .
            $e->getLine() .
            '</div>';
    };
@endphp

@if (optional($contract->service)->type == 'Unit Rental')
    @php
        $__block_unit_line = __LINE__ + 1;
        $price = 0;
        $target = 0;

        try {
            $unit_id = $proforma_invoice->unit_id;
            $price = $proforma_invoice->unit_target?->price ?? 0;
            $target = $proforma_invoice->unit_target?->target ?? 0;
        } catch (\Throwable $e) {
            $__report($e, $__block_unit_line, 'Unit Rental - Init');
        }
    @endphp
    <h6 class="mb-2" style="display: inline-block;">
        <table style="width:100%">
            <tr>
                <td>Client</td>
                <td style="width:5px"> &nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;&nbsp;{{ optional($contract->client_vendor)->name }}
                </td>
            </tr>
            <tr>
                <td>Contract Type</td>
                <td style="width:5px"> &nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;&nbsp;{{ optional($contract->service)->type }}
                </td>
            </tr>
            <tr>
                <td>Status</td>
                <td style="width:5px"> &nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;
                    @if ($proforma_invoice->status == 'Done')
                        <span class="badge bg-success" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approved' || $proforma_invoice->status == 'Received')
                        <span class="badge bg-warning" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @else
                        <span class="badge bg-secondary" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </h6>
    @php
        $__block_rental_calc = __LINE__ + 1;
        $hariKerja = 0;
        $totalJamKerja = 0;
        $total_breakdown = 0;
        $pa = 0;
        $penalty = 0;
        $total_payment = 0;

        try {
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
        } catch (\Throwable $e) {
            $__report($e, $__block_rental_calc, 'Unit Rental - Calculation');
        }
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
                    {{ $proforma_invoice->unit_target->unit?->description ?? '-' }}
                    (<b>{{ $proforma_invoice->unit_target->unit?->vehicle_no ?? '-' }}</b>)
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
@elseif(optional($contract->service)->type == 'LCT')
    @php
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
    @endphp
    <h6 class="mb-2" style="display: inline-block;">
        <table style="width:100%">
            <tr>
                <td>Client</td>
                <td style="width:5px">&nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;&nbsp;{{ optional($contract->client_vendor)->name }}
                </td>
            </tr>
            <tr>
                <td>Contract Type</td>
                <td style="width:5px">&nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;&nbsp;{{ optional($contract->service)->type }}
                </td>
            </tr>
            <tr>
                <td>Starting Date</td>
                <td style="width:5px">&nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;&nbsp;{{ Carbon::parse($startDate)->format('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Closing Date</td>
                <td style="width:5px">&nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;&nbsp;{{ Carbon::parse($endDate)->format('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Status</td>
                <td style="width:5px"> &nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;
                    @if ($proforma_invoice->status == 'Done')
                        <span class="badge bg-success" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approved' || $proforma_invoice->status == 'Received')
                        <span class="badge bg-warning" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @else
                        <span class="badge bg-secondary" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </h6>

    <table class="table tableItem">
        <thead class="table-dark">
            <tr>
                <th scope="col" style="width: 5px">No.</th>
                <th scope="col">Item</th>
                <th scope="col" style="width: 8%">Unit</th>
                <th scope="col" style="width: 15%" class="text-end">Rate (IDR)</th>
                <th scope="col" style="width: 8%" class="text-end">Qty</th>
                <th scope="col" style="width: 15%" class="text-end">Amount (IDR)</th>
                <th scope="col" style="width: 8%" class="text-end">Qty (PTD)</th>
                <th scope="col" style="width: 15%" class="text-end">PTD Amount (IDR)</th>
            </tr>
        </thead>

        <tbody>
            @php
                $__block_lct_fmf = __LINE__ + 1;
                $contract_rate = collect();
                $proforma_invoice_old = collect();
                $contract_fmf = null;
                $fix_monthly_fee = 0;
                $fmf_qty = 1;
                $fmf_qty_ptd = 0;
                $fmf_amount_ptd = 0;
                $total_amount = 0;
                $total_amount_ptd = 0;
                $has_fmf = false;
                $row_number = 1;
                $trip = 0;

                try {
                    $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
                    $proforma_invoice_old = Proforma_invoice::where('contract_id', $contract->id)->pluck('id');
                    $contract_fmf = Contract_fmf::where('contract_id', $contract->id)->where('year', $year)->first();

                    if ($contract_fmf) {
                        $has_fmf = true;
                        $fix_monthly_fee = $contract_fmf->value;

                        $fmf_qty_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                            ->where('contract_fmf_id', $contract_fmf->id)
                            ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                            ->count();

                        $fmf_amount_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                            ->where('contract_fmf_id', $contract_fmf->id)
                            ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                            ->sum('value');
                    }
                } catch (\Throwable $e) {
                    $__report($e, $__block_lct_fmf, 'LCT - FMF Block');
                }
            @endphp

            @if ($has_fmf)
                <tr>
                    <td>{{ $row_number++ }}</td>
                    <td>Fix Monthly Fee</td>
                    <td>Month</td>
                    <td class="text-end">
                        {{ Number::format($fix_monthly_fee) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($fmf_qty, precision: 2) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($fix_monthly_fee * $fmf_qty) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($fmf_qty_ptd + $fmf_qty, precision: 2) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($fmf_amount_ptd + $fix_monthly_fee * $fmf_qty, precision: 0) }}
                    </td>
                </tr>
                @php
                    $total_amount += $fix_monthly_fee * $fmf_qty;
                    $total_amount_ptd += $fmf_amount_ptd + $fix_monthly_fee * $fmf_qty;
                @endphp
            @endif

            @php
                $__block_lct_trip = __LINE__ + 1;
                try {
                    $unit_lct_id = Unit::where('type', 'LCT')->pluck('id');
                    $daily_report = Daily_report::whereBetween('date', [
                        Carbon::parse($startDate)->format('Y-m-d'),
                        Carbon::parse($endDate)->format('Y-m-d'),
                    ])
                        ->whereIn('unit_id', $unit_lct_id)
                        ->get();
                    $trip = $daily_report->count();
                } catch (\Throwable $e) {
                    $__report($e, $__block_lct_trip, 'LCT - Trip Calculation');
                }
            @endphp

            @foreach ($contract_rate as $contractrate)
                @php
                    $__block_lct_rate = __LINE__ + 1;
                    $qty_ptd = 0;
                    $amount_ptd = 0;
                    try {
                        $qty_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                            ->where('contract_rate_id', $contractrate->id)
                            ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                            ->sum('qty');

                        $amount_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                            ->where('contract_rate_id', $contractrate->id)
                            ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                            ->sum('amount');
                    } catch (\Throwable $e) {
                        $__report($e, $__block_lct_rate, "LCT - Rate Loop id={$contractrate->id}");
                    }
                @endphp

                <tr>
                    <td>{{ $row_number++ }}</td>
                    <td>{{ $contractrate->service_item }}</td>
                    <td>Trip</td>
                    <td class="text-end">
                        {{ Number::format($contractrate?->rate ?? 0, precision: 0) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($trip, precision: 2) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format(($contractrate?->rate ?? 0) * $trip, precision: 0) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($qty_ptd + $trip, precision: 2) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($amount_ptd + ($contractrate?->rate ?? 0) * $trip, precision: 0) }}
                    </td>
                </tr>

                @php
                    $total_amount += ($contractrate?->rate ?? 0) * $trip;
                    $total_amount_ptd += $amount_ptd + ($contractrate?->rate ?? 0) * $trip;
                @endphp
            @endforeach
            <tr>
                <td colspan="5" class="text-end">
                    <b>TOTAL</b>
                </td>
                <td class="text-end">
                    <b>{{ Number::format($total_amount, precision: 0) }}</b>
                </td>
                <td></td>
                <td class="text-end">
                    <b>{{ Number::format($total_amount_ptd, precision: 0) }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@elseif(optional($contract->service)->type == 'Explosive Material Transport')
    @php
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
    @endphp
    <h6 class="mb-2" style="display: inline-block;">
        <table style="width:100%">
            <tr>
                <td>Client</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ optional($contract->client_vendor)->name }}</td>
            </tr>
            <tr>
                <td>Contract Type</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ optional($contract->service)->type }}</td>
            </tr>
            <tr>
                <td>Progress Claim</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ Carbon::parse($startDate)->format('F Y') }}</td>
            </tr>
            <tr>
                <td>Starting Date</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ Carbon::parse($startDate)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Closing Date</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ Carbon::parse($endDate)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td style="width:5px"> &nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;
                    @if ($proforma_invoice->status == 'Done')
                        <span class="badge bg-success" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approved' || $proforma_invoice->status == 'Received')
                        <span class="badge bg-warning" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @else
                        <span class="badge bg-secondary"
                            style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </h6>
    <table class="table tableItem">
        <thead class="table-dark">
            <tr>
                <th scope="col" style="width: 5px">No.</th>
                <th scope="col">Item</th>
                <th scope="col" style="width: 8%">Unit</th>
                <th scope="col" style="width: 15%" class="text-end">Rate (IDR)</th>
                <th scope="col" style="width: 8%" class="text-end">Qty</th>
                <th scope="col" style="width: 15%" class="text-end">Amount (IDR)</th>
                <th scope="col" style="width: 8%" class="text-end">Qty (PTD)</th>
                <th scope="col" style="width: 15%" class="text-end">PTD Amount (IDR)</th>
            </tr>
        </thead>

        <tbody>
            @php
                $__block_emt_init = __LINE__ + 1;
                $contract_rate = collect();
                $unit_target = collect();
                $daily_report = collect();
                $proforma_invoice_old = collect();
                $total_amount = 0;
                $total_amount_ptd = 0;

                try {
                    $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
                    $unit_target = Unit_target::where('contract_id', $contract->id)->pluck('unit_id');

                    $inputPeriod = Carbon::createFromFormat('Y-m', sprintf('%04d-%02d', $year, $month));
                    $startPeriod = $inputPeriod->copy()->startOfYear()->format('Y-m');
                    $endPeriod = $inputPeriod->copy()->subMonth()->format('Y-m');

                    $proforma_invoice_old = Proforma_invoice::query()
                        ->where('contract_id', $contract->id)
                        ->whereBetween('periode', [$startPeriod, $endPeriod])
                        ->pluck('id');

                    $daily_report = Daily_report::whereBetween('date', [
                        Carbon::parse($startDate)->format('Y-m-d'),
                        Carbon::parse($endDate)->format('Y-m-d'),
                    ])
                        ->where('service_type', 'LCT')
                        ->pluck('id');
                } catch (\Throwable $e) {
                    $__report($e, $__block_emt_init, 'EMT - Init Block');
                }
            @endphp

            @foreach ($contract_rate as $contractrate)
                @php
                    $__block_emt_loop = __LINE__ + 1;
                    $qty = 0;
                    $amount = 0;
                    $qty_ptd = 0;
                    $amount_ptd = 0;

                    try {
                        if ($contractrate->type == 'AN') {
                            $total_an = Daily_report_detail::whereIn('daily_report_id', $daily_report)
                                ->whereIn('unit_id', $unit_target)
                                ->where('item', 'AN')
                                ->sum('value_2');
                            $total_pupuk = Daily_report_detail::whereIn('daily_report_id', $daily_report)
                                ->whereIn('unit_id', $unit_target)
                                ->where('item', 'Pupuk')
                                ->sum('value_2');
                            $qty = $total_an + $total_pupuk;
                        } elseif ($contractrate->type == 'ANSOL') {
                            $total_ansol = Daily_report_detail::whereIn('daily_report_id', $daily_report)
                                ->whereIn('unit_id', $unit_target)
                                ->where('item', 'ANSOL')
                                ->sum('value_1');
                            $qty = $total_ansol;
                        }

                        $amount = ($contractrate?->rate ?? 0) * $qty;

                        $qty_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                            ->where('contract_rate_id', $contractrate->id)
                            ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                            ->where('deleted_at', null)
                            ->sum('qty');

                        $amount_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                            ->where('contract_rate_id', $contractrate->id)
                            ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                            ->where('deleted_at', null)
                            ->sum('amount');

                        $qty_ptd = $qty_ptd + $qty;
                        $amount_ptd = $amount_ptd + $amount;
                    } catch (\Throwable $e) {
                        $__report($e, $__block_emt_loop, "EMT - Rate Loop id={$contractrate->id}");
                    }
                @endphp

                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $contractrate->service_item }}</td>
                    <td>{{ $contractrate->unit }}</td>
                    <td class="text-end">
                        {{ Number::format($contractrate?->rate ?? 0, precision: 0) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($qty, precision: 2) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($amount, precision: 0) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($qty_ptd, precision: 2) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($amount_ptd, precision: 0) }}
                    </td>
                </tr>
                @php
                    $total_amount += $amount;
                    $total_amount_ptd += $amount_ptd;
                @endphp
            @endforeach

            <tr>
                <td colspan="5" class="text-end">
                    <b>TOTAL</b>
                </td>
                <td class="text-end">
                    <b>{{ Number::format($total_amount, precision: 0) }}</b>
                </td>
                <td></td>
                <td class="text-end">
                    <b>{{ Number::format($total_amount_ptd, precision: 0) }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@elseif(optional($contract->service)->type == 'Pallet')
    @php
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
    @endphp
    <h6 class="mb-2" style="display: inline-block;">
        <table style="width:100%">
            <tr>
                <td>Client</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ optional($contract->client_vendor)->name }}</td>
            </tr>
            <tr>
                <td>Contract Type</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ optional($contract->service)->type }}</td>
            </tr>
            <tr>
                <td>Progress Claim</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ Carbon::parse($startDate)->format('F Y') }}</td>
            </tr>
            <tr>
                <td>Starting Date</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ Carbon::parse($startDate)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Closing Date</td>
                <td style="width:5px">:</td>
                <td>&nbsp;&nbsp;&nbsp;{{ Carbon::parse($endDate)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td style="width:5px"> &nbsp;&nbsp;&nbsp;:</td>
                <td>
                    &nbsp;&nbsp;
                    @if ($proforma_invoice->status == 'Done')
                        <span class="badge bg-success" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approved' || $proforma_invoice->status == 'Received')
                        <span class="badge bg-warning" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @else
                        <span class="badge bg-secondary"
                            style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </h6>

    <table class="table tableItem">
        <thead class="table-dark">
            <tr>
                <th scope="col" style="width: 5px">No.</th>
                <th scope="col">Item</th>
                <th scope="col" style="width: 10%">Unit</th>
                <th scope="col" style="width: 15%" class="text-end">Rate</th>
                <th scope="col" style="width: 10%" class="text-end">Qty</th>
                <th scope="col" style="width: 15%" class="text-end">Amount</th>
            </tr>
        </thead>

        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($proforma_invoice->proforma_invoice_detail as $d)
                @php
                    $__block_pallet_loop = __LINE__ + 1;
                    $unit = null;
                    try {
                        $unit = Unit::find($d->unit_id);
                    } catch (\Throwable $e) {
                        $__report($e, $__block_pallet_loop, "Pallet - Loop id={$d->id}");
                    }
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d?->service_item ?? '-' }}</td>
                    <td>{{ $unit?->vehicle_no ?? '-' }}</td>
                    <td class="text-end">
                        {{ Number::format($d?->rate ?? 0, precision: 0) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($d?->qty ?? 0, precision: 2) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($d?->amount ?? 0, precision: 0) }}
                    </td>
                </tr>
                @php
                    $total += $d?->amount ?? 0;
                @endphp
            @endforeach

            <tr>
                <td colspan="5" class="text-end">
                    <b>TOTAL</b>
                </td>
                <td class="text-end">
                    <b>{{ Number::format($total, precision: 0) }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endif

@empty(!$approval_process)
    <table style="border-collapse:separate; border-spacing:0;" class="mb-4">
        <tr>
            <td style="vertical-align: top" colspan="3">
                <b>
                    <h6 style="border-bottom: 1px solid #000; display: inline-block;">
                        Approval Progress
                    </h6>
                </b>
            </td>
        </tr>
        @foreach ($approval_process as $d)
            <tr>
                <td style="vertical-align: top">
                    <b>{{ optional($d->approval_step)->order }}. {{ optional($d->user)->name ?? '-' }}</b> &nbsp;
                </td>
                <td>
                    @if ($d->action == 'Approved')
                        <span class="badge bg-success" style="font-size: 13px">{{ $d->action }}</span>
                    @elseif($d->action == 'Rejected')
                        <span class="badge bg-danger" style="font-size: 13px">{{ $d->action }}</span>
                    @elseif($d->action == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $d->action }}</span>
                    @else
                        <span class="badge bg-secondary" style="font-size: 13px">{{ $d->action }}</span>
                    @endif
                </td>
                <td>
                    @if ($d->action == 'Approved' && $d->updated_at)
                        {{ Carbon::parse($d->updated_at)->translatedFormat('d F Y H:i') }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endempty

<table style="border-collapse:separate; border-spacing:0;">
    <tr>
        <td style="vertical-align: top" colspan="3">
            <b>
                <h6 style="border-bottom: 1px solid #000; display: inline-block;">
                    Document Progress
                </h6>
            </b>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top">Cut Off Date</td>
        <td> &nbsp;:&nbsp;</td>
        <td>
            {{ $proforma_invoice->cut_off_date ? Carbon::parse($proforma_invoice->cut_off_date)->translatedFormat('d F Y') : '-' }}
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top">Konsolidasi Data TMS & CMD</td>
        <td> &nbsp;:&nbsp;</td>
        <td>
            {{ $proforma_invoice->consolidation_date ? Carbon::parse($proforma_invoice->consolidation_date)->translatedFormat('d F Y') : '-' }}
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top">Kirim Progress Claim Approval</td>
        <td> &nbsp;:&nbsp;</td>
        <td>
            {{ $proforma_invoice->progress_claim_date ? Carbon::parse($proforma_invoice->progress_claim_date)->translatedFormat('d F Y') : '-' }}
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top">Data Terima Dari OPS</td>
        <td> &nbsp;:&nbsp;</td>
        <td>
            {{ $proforma_invoice->ops_received_date ? Carbon::parse($proforma_invoice->ops_received_date)->translatedFormat('d F Y') : '-' }}
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top">Proforma Invoice Approved</td>
        <td> &nbsp;:&nbsp;</td>
        <td>
            {{ $proforma_invoice->prof_inv_app_date ? Carbon::parse($proforma_invoice->prof_inv_app_date)->translatedFormat('d F Y') : '-' }}
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top">CIC Request Date</td>
        <td> &nbsp;:&nbsp;</td>
        <td>
            {{ $proforma_invoice->cic_request_date ? Carbon::parse($proforma_invoice->cic_request_date)->translatedFormat('d F Y') : '-' }}
        </td>
    </tr>
</table>
