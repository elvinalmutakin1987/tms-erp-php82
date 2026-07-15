@php
    use App\Models\Maintenance;
    use App\Models\Contract_rate;
    use App\Models\Contract_fmf;
    use App\Models\Contract_unit;
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
            $proforma_invoice_old = Proforma_invoice::where('contract_id', $contract->id)->pluck('id');
            $daily_report = Daily_report::whereBetween('date', [
                Carbon::parse($startDate)->format('Y-m-d'),
                Carbon::parse($endDate)->format('Y-m-d'),
            ])
                ->where('service_type', 'LCT')
                ->get();
            $total_an = Daily_report_detail::where('daily_report_id', $daily_report->id)
                ->where('item', 'AN')
                ->sum('value_1');
            $total_pupuk = Daily_report_detail::where('daily_report_id', $daily_report->id)
                ->where('item', 'Pupuk')
                ->sum('value_1');

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
