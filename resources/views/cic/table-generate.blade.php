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
                $proformaInvoices = Proforma_invoice::where('contract_id', $cont->id)
                    ->where('periode', $periode)
                    ->get();

                $invoices = Invoice::whereIn('id', $proformaInvoices->pluck('invoice_id')->filter())
                    ->get()
                    ->keyBy('id');

                /*
                 * Menambahkan data invoice ke setiap proforma,
                 * kemudian mengurutkan agar invoice_no yang sama berdekatan.
                 *
                 * Proforma tanpa invoice dibuat unik supaya tidak digabung
                 * menjadi satu cell "Not Available".
                 */
                $rows = $proformaInvoices
                    ->map(function ($pi, $index) use ($invoices) {
                        $invoice = $pi->invoice_id ? $invoices->get($pi->invoice_id) : null;

                        return [
                            'pi' => $pi,
                            'invoice' => $invoice,
                            'invoice_key' => $invoice ? 'invoice-' . $invoice->invoice_no : 'empty-' . $index,
                        ];
                    })
                    ->sortBy(function ($row) {
                        return $row['invoice'] ? '0-' . $row['invoice']->invoice_no : '1-' . $row['pi']->proforma_no;
                    })
                    ->values();

                /*
                 * Menghitung jumlah rowspan untuk setiap invoice.
                 */
                $invoiceRowspans = $rows->groupBy('invoice_key')->map(fn($group) => $group->count());

                $renderedInvoices = [];

                $jumlahBaris = max($rows->count(), 1);
            @endphp

            @if ($rows->isNotEmpty())
                @foreach ($rows as $index => $row)
                    @php
                        $pi = $row['pi'];
                        $invoice = $row['invoice'];
                        $invoiceKey = $row['invoice_key'];
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
                                <a href="{{ route('cic.export_file', $pi->id) }}" target="_blank">
                                    {{ $pi->cic_number }}
                                </a>
                            @else
                                <span class="text-danger opacity-75">
                                    Not Available.
                                </span>
                            @endif
                        </td>

                        @if (!in_array($invoiceKey, $renderedInvoices, true))
                            @php
                                $renderedInvoices[] = $invoiceKey;
                            @endphp

                            <td rowspan="{{ $invoiceRowspans->get($invoiceKey, 1) }}" class="align-top">
                                @if ($invoice)
                                    {{ $invoice->invoice_no }}
                                @else
                                    <span class="text-danger opacity-75">
                                        Not Available.
                                    </span>
                                @endif
                            </td>
                        @endif
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
