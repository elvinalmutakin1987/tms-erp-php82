@php
    use App\Models\Invoice;
    use App\Models\Proforma_invoice;
    use App\Models\Invoice_proforma_invoice;
    use Carbon\Carbon;

    $periode = Carbon::create($year, $month, 1)->format('Y-m');

    $proformaInvoices = Proforma_invoice::where('periode', $periode)
        ->whereIn('status', ['Approved', 'CIC Approval', 'Invoicing', 'Done'])
        ->get();

    $invoiceGroups = [];

    foreach ($proformaInvoices as $pi) {
        $invoiceProforma = Invoice_proforma_invoice::where('proforma_invoice_id', $pi->id)->first();

        if (!$invoiceProforma) {
            continue;
        }

        $groupInvoice = Invoice::find($invoiceProforma->invoice_id);

        if (!$groupInvoice) {
            continue;
        }

        if (!isset($invoiceGroups[$groupInvoice->id])) {
            $invoiceGroups[$groupInvoice->id] = [
                'invoice' => $groupInvoice,
                'proformas' => [],
            ];
        }

        $invoiceGroups[$groupInvoice->id]['proformas'][] = $pi;
    }

    $no = 1;

    $formatDate = function ($value) {
        return $value ? Carbon::parse($value)->format('d M Y') : '-';
    };

    $titleStyle = 'text-align:center;vertical-align:middle;';
    $headerStyle =
        'text-align:center;vertical-align:middle;border:1px solid #000000;font-weight:bold;background-color:#f2f2f2;';
    $cellStyle = 'text-align:center;vertical-align:middle;border:1px solid #000000;';
    $textCellStyle = 'text-align:left;vertical-align:middle;border:1px solid #000000;';
@endphp

<table>
    {{-- Judul --}}
    <tr>
        <td colspan="19" style="{{ $titleStyle }}font-size:18px;">
            <b>PT. TUNAS MITRA SEJATI</b>
        </td>
    </tr>
    <tr>
        <td colspan="19" style="{{ $titleStyle }}font-size:18px;">
            <b>Invoice Monitoring</b>
        </td>
    </tr>
    <tr>
        <td colspan="19" style="{{ $titleStyle }}font-size:14px;">
            <b>{{ Carbon::create($year, $month, 1)->format('F Y') }}</b>
        </td>
    </tr>
    <tr>
        <td colspan="19"></td>
    </tr>

    {{-- Header --}}
    <tr>
        <td style="{{ $headerStyle }}width:40px;"><b>No.</b></td>
        <td style="{{ $headerStyle }}width:160px;"><b>Invoice</b></td>
        <td style="{{ $headerStyle }}width:160px;"><b>Proforma Invoice</b></td>
        <td style="{{ $headerStyle }}width:400px;"><b>Keterangan</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Cut Off Date</b></td>
        <td style="{{ $headerStyle }}width:125px;">
            <b>Konsolidasi Data<br>TMS &amp; CMD</b>
        </td>
        <td style="{{ $headerStyle }}width:125px;">
            <b>Kirim Progress<br>Klaim Approval</b>
        </td>
        <td style="{{ $headerStyle }}width:125px;">
            <b>Data Diterima<br>Dari Ops</b>
        </td>
        <td style="{{ $headerStyle }}width:125px;">
            <b>Proforma Invoice<br>Approved</b>
        </td>
        <td style="{{ $headerStyle }}width:125px;"><b>Minta CIC</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Pembuatan<br>CIC</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Terima CIC</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Tanggal Inv</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Pembuatan<br>Inv</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Kirim CIC<br>Ke KPC</b></td>
        <td style="{{ $headerStyle }}width:125px;">
            <b>Informasi CIC<br>Bisa Diambil</b>
        </td>
        <td style="{{ $headerStyle }}width:125px;"><b>CIC Diambil<br>TMS</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Invoice Terima<br>KPC</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Status Bayar</b></td>
    </tr>

    {{-- Data --}}
    @forelse ($invoiceGroups as $group)
        @php
            $groupInvoice = $group['invoice'];
            $proformas = $group['proformas'];
            $rowspan = count($proformas);

            $paymentInvoice = Invoice::where('id', $groupInvoice->id)
                ->whereIn('status', ['Approved', 'Approval', 'Received', 'Done'])
                ->first();

            if (!$paymentInvoice) {
                $paymentStatus = 'Invoicing';
                $paymentBg = '#0d6efd';
                $paymentColor = '#ffffff';
            } else {
                $paymentStatus = $paymentInvoice->payment_status ?? 'Invoicing';

                [$paymentBg, $paymentColor] = match ($paymentStatus) {
                    'Paid' => ['#198754', '#ffffff'],
                    'Partialy Paid' => ['#ffc107', '#000000'],
                    'Unpaid' => ['#dc3545', '#ffffff'],
                    default => ['#0d6efd', '#ffffff'],
                };
            }

            $paymentStyle =
                $cellStyle .
                'font-weight:bold;' .
                'background-color:' .
                $paymentBg .
                ';' .
                'color:' .
                $paymentColor .
                ';';
        @endphp

        @foreach ($proformas as $index => $pi)
            <tr>
                <td style="{{ $cellStyle }}">
                    {{ $no++ }}
                </td>

                @if ($index === 0)
                    <td rowspan="{{ $rowspan }}" style="{{ $cellStyle }}font-weight:bold;">
                        {{ $groupInvoice->invoice_no }}
                    </td>
                @endif

                <td style="{{ $cellStyle }}">
                    {{ $pi->proforma_no }}
                </td>

                <td style="{{ $textCellStyle }}">
                    @if ($pi->contract)
                        {{ strip_tags($pi->contract->notes ?? '') }}

                        @if (
                            $pi->contract->service &&
                                ($pi->contract->service->type == 'Unit Rental' || $pi->contract->service->type == 'Fuel Truck Rental'))
                            @if ($pi->unit)
                                - {{ $pi->unit->vehicle_no }}
                            @endif
                        @endif
                    @else
                        -
                    @endif
                </td>

                <td style="{{ $cellStyle }}">{{ $formatDate($pi->cut_off_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->consolidation_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->progress_claim_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->ops_received_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->prof_inv_app_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->cic_request_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->cic_created_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->cic_received_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->inv_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->inv_create_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->cic_send_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->cic_ready_to_pick_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->cic_pick_up_date) }}</td>
                <td style="{{ $cellStyle }}">{{ $formatDate($pi->inv_send_date) }}</td>

                @if ($index === 0)
                    <td rowspan="{{ $rowspan }}" style="{{ $paymentStyle }}">
                        {{ $paymentStatus }}
                    </td>
                @endif
            </tr>
        @endforeach
    @empty
        <tr>
            <td colspan="19" style="{{ $cellStyle }}">
                Tidak ada data invoice pada periode ini.
            </td>
        </tr>
    @endforelse
</table>
