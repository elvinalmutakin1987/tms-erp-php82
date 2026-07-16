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
@endphp
@if ($contract->service->type == 'Unit Rental')
    @php
        $unit_id = $proforma_invoice->unit_id;
        $price = $proforma_invoice->unit_target->price;
        $target = $proforma_invoice->unit_target->target;
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
                    @if ($proforma_invoice->status == 'Draft')
                        <span class="badge bg-secondary" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Approved' || $proforma_invoice->status == 'Received')
                        <span class="badge bg-warning" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @elseif($proforma_invoice->status == 'Done')
                        <span class="badge bg-success" style="font-size: 13px">{{ $proforma_invoice->status }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </h6>
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
            {{-- Ini untuk Fix Monthly Fee --}}
            @php
                $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
                $proforma_invoice_old = Proforma_invoice::where('contract_id', $contract->id)->pluck('id');
                $contract_fmf = Contract_fmf::where('contract_id', $contract->id)->where('year', $year)->first();
                $fix_monthly_fee = $contract_fmf->value;
                $fmf_qty = 1;
                $fmf_qty_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                    ->where('contract_fmf_id', $contract_fmf->id)
                    ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                    ->count();
                $fmf_amount_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                    ->where('contract_fmf_id', $contract_fmf->id)
                    ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                    ->sum('value');

                $total_amount = 0;
                $total_amount_ptd = 0;

            @endphp

            <tr>
                <td>
                    1
                </td>
                <td>
                    Fix Monthly Fee
                </td>
                <td>
                    Month
                </td>
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
                $unit_lct_id = Unit::where('type', 'LCT')->pluck('id');
                $daily_report = Daily_report::whereBetween('date', [
                    Carbon::parse($startDate)->format('Y-m-d'),
                    Carbon::parse($endDate)->format('Y-m-d'),
                ])
                    ->whereIn('unit_id', $unit_lct_id)
                    ->get();
                $trip = $daily_report->count();
            @endphp

            @foreach ($contract_rate as $contractrate)
                @php
                    $qty_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                        ->where('contract_rate_id', $contractrate->id)
                        ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                        ->sum('qty');
                    $amount_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                        ->where('contract_rate_id', $contractrate->id)
                        ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                        ->sum('amount');
                @endphp
                <tr>
                    <td>
                        {{ $loop->iteration + 1 }}
                    </td>
                    <td>
                        {{ $contractrate->service_item }}
                    </td>
                    <td>
                        Trip
                    </td>
                    <td class="text-end">
                        {{ Number::format($contractrate->rate, precision: 0) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($trip, precision: 2) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($contractrate->rate * $trip, precision: 0) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($qty_ptd + $trip, precision: 2) }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($amount_ptd + $contractrate->rate * $trip, precision: 0) }}
                    </td>
                </tr>

                @php
                    $total_amount += $contractrate->rate * $trip;
                    $total_amount_ptd += $amount_ptd + $contractrate->rate * $trip;
                @endphp
            @endforeach
            {{-- end --}}
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
@elseif($contract->service->type == 'Explosive Material Transport')
    @php
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
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
            <tr>
                <td>Progress Claim</td>
                <td style="width:5px">:</td>
                <td>
                    &nbsp;&nbsp;&nbsp;{{ Carbon::parse($startDate)->format('F Y') }}
                </td>
            </tr>
            <tr>
                <td>Starting Date</td>
                <td style="width:5px">:</td>
                <td>
                    &nbsp;&nbsp;&nbsp;{{ Carbon::parse($startDate)->format('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Closing Date</td>
                <td style="width:5px">:</td>
                <td>
                    &nbsp;&nbsp;&nbsp;{{ Carbon::parse($endDate)->format('d F Y') }}
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
                $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
                $unit_target = Unit_target::where('contract_id', $contract->id)->pluck('unit_id');
                // $proforma_invoice_old = Proforma_invoice::where('contract_id', $contract->id)->pluck('id');
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
                $total_amount = 0;
                $total_amount_ptd = 0;
            @endphp

            @foreach ($contract_rate as $contractrate)
                @php
                    $qty = 0;
                    $amount = 0;
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
                    $amount = $contractrate->rate * $qty;
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
                @endphp

                <tr>
                    <td>
                        {{ $loop->iteration }}
                    </td>
                    <td>
                        {{ $contractrate->service_item }}
                    </td>
                    <td>
                        {{ $contractrate->unit }}
                    </td>
                    <td class="text-end">
                        {{ Number::format($contractrate->rate, precision: 0) }}
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
            {{-- end --}}
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
@endif


@empty(!$approval_process)
    <table style="border-collapse:separate; border-spacing:0;">
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
                    <b>{{ $d->approval_step->order }}. {{ $d->user->name }}</b> &nbsp;
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
                    @if ($d->action == 'Approved')
                        {{ Carbon::parse($d->updated_at)->translatedFormat('d F Y H:i') }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endempty
