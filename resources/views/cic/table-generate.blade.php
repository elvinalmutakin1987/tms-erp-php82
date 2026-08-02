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
    use App\Models\Invoice;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    $periode = Carbon::parse("$year-$month")->format('Y-m');
@endphp

<h6 class="mb-2" style="display: inline-block;">
    <table style="width:100%">
        <tr>
            <td>Periode</td>
            <td style="width:5px">:</td>
            <td>
                &nbsp;&nbsp;&nbsp;{{ Carbon::parse($year . '-' . $month . '-01')->format('F Y') }}
            </td>
        </tr>
    </table>
</h6>

<table class="table table-bordered tableItem align-middle">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width: 46%">Contract Number</th>
            <th scope="col" style="width: 18%">Proforma Invoice</th>
            <th scope="col" style="width: 18%">CIC Number</th>
            <th scope="col" style="width: 18%">Invoice Number</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($contract as $cont)
            @php
                $proforma_invoice = Proforma_invoice::where('contract_id', $cont->id)
                    ->where('periode', $periode)
                    ->get();

                $invoices = Invoice::whereIn('id', $proforma_invoice->pluck('invoice_id')->filter())
                    ->get()
                    ->keyBy('id');

                $jumlahBaris = max($proforma_invoice->count(), 1);
            @endphp

            @if ($proforma_invoice->isNotEmpty())
                @foreach ($proforma_invoice as $index => $pi)
                    @php
                        $invoice = $pi->invoice_id ? $invoices->get($pi->invoice_id) : null;
                    @endphp

                    <tr>
                        @if ($index === 0)
                            <td rowspan="{{ $jumlahBaris }}" class="align-top">
                                {{ $cont->contract_no }}
                                -
                                {{ $cont->service->name ?? '-' }}
                            </td>
                        @endif

                        <td>
                            {{ $pi->proforma_no }}
                        </td>

                        <td>
                            @if ($pi->cic_number)
                                {{-- {{ $pi->cic_number }} --}}
                                <a href="{{ route('cic.export_file', $pi->id) }}" target="_blank">
                                    {{ $pi->cic_number }}
                                </a>
                            @else
                                <span class="text-danger opacity-75">
                                    Not Available.
                                </span>
                            @endif
                        </td>

                        <td>
                            @if ($invoice)
                                {{ $invoice->invoice_no }}
                            @else
                                <span class="text-danger opacity-75">
                                    Not Available.
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="align-top">
                        {{ $cont->contract_no }}
                        -
                        {{ $cont->service->name ?? '-' }}
                    </td>

                    <td>
                        <span class="text-danger opacity-75">
                            Not Available.
                        </span>
                    </td>

                    <td>
                        <span class="text-danger opacity-75">
                            Not Available.
                        </span>
                    </td>

                    <td>
                        <span class="text-danger opacity-75">
                            Not Available.
                        </span>
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
