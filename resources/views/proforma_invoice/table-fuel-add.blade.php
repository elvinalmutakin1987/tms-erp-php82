@php
    use App\Models\Maintenance;
    use App\Models\Contract_rate;
    use App\Models\Contract_fmf;
    use App\Models\Contract_unit;
    use App\Models\Daily_report;
    use App\Models\Proforma_invoice;
    use App\Models\Proforma_invoice_detail;
    use App\Models\Unit;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

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
            'margin:4px 0;font-family:monospace;font-size:12px">' .
            '<b>ERROR</b>' .
            ($label ? " [{$label}]" : '') .
            ': ' .
            e($e->getMessage()) .
            '<br><b>Location:</b> ' .
            $lineInfo .
            '<br><b>File:</b> ' .
            e($e->getFile()) .
            '<br><b>Compiled line:</b> ' .
            $e->getLine() .
            '</div>';
    };
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
        {{-- ============================================================
             BLOK 1: Fix Monthly Fee — inisialisasi & query
             ============================================================ --}}
        @php
            $__block1_line = __LINE__ + 1;

            // Default aman (supaya view tidak fatal jika error di bawah)
            $contract_rate = collect();
            $proforma_invoice_old = collect();
            $contract_fmf = null;
            $fix_monthly_fee = 0;
            $fmf_qty = 1;
            $fmf_qty_ptd = 0;
            $fmf_amount_ptd = 0;
            $total_amount = 0;
            $total_amount_ptd = 0;

            // Flag untuk mengecek apakah FMF perlu ditampilkan
            $has_fmf = false;

            // Variabel penomoran baris dinamis
            $row_number = 1;

            try {
                $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
                $proforma_invoice_old = Proforma_invoice::where('contract_id', $contract->id)->pluck('id');

                $contract_fmf = Contract_fmf::where('contract_id', $contract->id)->where('year', $year)->first();

                // Cek jika FMF ada
                if ($contract_fmf) {
                    $has_fmf = true;
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

                    // Tambahkan ke total HANYA jika FMF ada
                    $total_amount += $fix_monthly_fee * $fmf_qty;
                    $total_amount_ptd += $fmf_amount_ptd + $fix_monthly_fee * $fmf_qty;
                }
            } catch (\Throwable $e) {
                $__report($e, $__block1_line, 'BLOK 1 - FMF');
            }
        @endphp

        {{-- Tampilkan Baris FMF HANYA JIKA datanya ada --}}
        @if ($has_fmf)
            <tr>
                <td>{{ $row_number++ }}</td>
                <td>Fix Monthly Fee</td>
                <td>Month</td>
                <td class="text-end">
                    {{ Number::format((float) $fix_monthly_fee) }}
                </td>
                <td class="text-end">
                    {{ Number::format((float) $fmf_qty, precision: 2) }}
                </td>
                <td class="text-end">
                    {{ Number::format((float) $fix_monthly_fee * (float) $fmf_qty) }}
                </td>
                <td class="text-end">
                    {{ Number::format((float) $fmf_qty_ptd + (float) $fmf_qty, precision: 2) }}
                </td>
                <td class="text-end">
                    {{ Number::format((float) $fmf_amount_ptd + (float) $fix_monthly_fee * (float) $fmf_qty, precision: 0) }}
                </td>
            </tr>
        @endif

        {{-- ============================================================
             BLOK 2: Hitung trip dari Daily_report
             ============================================================ --}}
        @php
            $__block2_line = __LINE__ + 1;
            $trip = 0;

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
                $__report($e, $__block2_line, 'BLOK 2 - Trip / Daily_report');
            }
        @endphp

        {{-- ============================================================
             BLOK 3: Loop Contract_rate
             ============================================================ --}}
        @foreach ($contract_rate as $contractrate)
            @php
                $__block3_line = __LINE__ + 1;
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
                    $__report($e, $__block3_line, "BLOK 3 - Contract_rate id={$contractrate->id}");
                }
            @endphp

            <tr>
                <td>
                    {{ $row_number++ }}
                </td>
                <td>
                    {{ $contractrate->service_item }}
                </td>
                <td>
                    Trip
                </td>
                <td class="text-end">
                    {{ Number::format((float) $contractrate->rate, precision: 0) }}
                </td>
                <td class="text-end">
                    {{ Number::format((float) $trip, precision: 2) }}
                </td>
                <td class="text-end">
                    {{ Number::format((float) $contractrate->rate * (float) $trip, precision: 0) }}
                </td>
                <td class="text-end">
                    {{ Number::format((float) $qty_ptd + (float) $trip, precision: 2) }}
                </td>
                <td class="text-end">
                    {{ Number::format((float) $amount_ptd + (float) $contractrate->rate * (float) $trip, precision: 0) }}
                </td>
            </tr>

            @php
                $__block3b_line = __LINE__ + 1;
                try {
                    $total_amount += $contractrate->rate * $trip;
                    $total_amount_ptd += $amount_ptd + $contractrate->rate * $trip;
                } catch (\Throwable $e) {
                    $__report($e, $__block3b_line, "BLOK 3b - Akumulasi total rate id={$contractrate->id}");
                }
            @endphp
        @endforeach

        {{-- ============================================================
             BARIS TOTAL
             ============================================================ --}}
        <tr>
            <td colspan="5" class="text-end">
                <b>TOTAL</b>
            </td>
            <td class="text-end">
                <b>{{ Number::format((float) $total_amount, precision: 0) }}</b>
            </td>
            <td></td>
            <td class="text-end">
                <b>{{ Number::format((float) $total_amount_ptd, precision: 0) }}</b>
            </td>
        </tr>
    </tbody>
</table>
