@php
    use App\Models\Maintenance;
    use App\Models\Contract_rate;
    use App\Models\Contract_fmf;
    use App\Models\Unit_target;
    use App\Models\Daily_report;
    use App\Models\Daily_report_detail;
    use App\Models\Proforma_invoice;
    use App\Models\Proforma_invoice_detail;
    use App\Models\Unit;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

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
