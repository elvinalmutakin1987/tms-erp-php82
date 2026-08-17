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
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice Monitoring</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 12pt;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            color: #000;
            background-color: #fff;
        }

        .print-shell {
            padding: 8pt;
        }

        .report-title {
            text-align: center;
            margin: 0 0 9px;
            padding: 0;
        }

        .company-name {
            font-size: 17px;
            font-weight: bold;
            line-height: 1.15;
            margin-bottom: 3px;
        }

        .report-name {
            font-size: 15px;
            font-weight: bold;
            line-height: 1.15;
            margin-bottom: 3px;
        }

        .report-period {
            font-size: 12px;
            font-weight: bold;
            line-height: 1.15;
        }

        .report-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
            padding: 0;
        }

        .report-table thead {
            display: table-header-group;
        }

        .report-table th {
            border: 1px solid #000;
            background-color: #f2f2f2;
            color: #000;
            text-align: center;
            vertical-align: middle;
            font-size: 7.2px;
            font-weight: bold;
            line-height: 1.15;
            padding: 4px 2px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .report-table td {
            border: 1px solid #000;
            background-color: #fff;
            color: #000;
            text-align: center;
            vertical-align: middle;
            font-size: 7.5px;
            line-height: 1.2;
            padding: 4px 2px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .report-table tr {
            page-break-inside: avoid;
        }

        .no-cell {
            text-align: center;
        }

        .invoice-cell {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            font-size: 7.5px;
        }

        .proforma-cell {
            text-align: center;
            font-size: 7.5px;
        }

        .keterangan-cell {
            text-align: left !important;
            vertical-align: middle;
            padding-left: 4px !important;
            padding-right: 4px !important;
            font-size: 7.5px;
            line-height: 1.25 !important;
        }

        /* Column Width */
        .col-no {
            width: 2.5%;
        }

        .col-invoice {
            width: 7.5%;
        }

        .col-proforma {
            width: 7.5%;
        }

        .col-keterangan {
            width: 15%;
        }

        .col-date {
            width: 4.5%;
        }

        /* Status */
        .status-invoicing,
        .status-paid,
        .status-partial,
        .status-unpaid,
        .status-other {
            text-align: center;
            vertical-align: middle;
            font-size: 7.5px;
            font-weight: bold;
        }

        .status-invoicing,
        .status-other {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        .status-paid {
            background-color: #198754 !important;
            color: #fff !important;
        }

        .status-partial {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .status-unpaid {
            background-color: #dc3545 !important;
            color: #fff !important;
        }
    </style>
</head>

<body>
    <div class="print-shell">
        <div class="report-title">
            <div class="company-name">PT. TUNAS MITRA SEJATI</div>
            <div class="report-name">Invoice Monitoring</div>
            <div class="report-period">
                {{ Carbon::create($year, $month, 1)->format('F Y') }}
            </div>
        </div>

        <table class="report-table">
            <colgroup>
                <col class="col-no">
                <col class="col-invoice">
                <col class="col-proforma">
                <col class="col-keterangan">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
            </colgroup>

            <thead>
                <tr>
                    <th>No.</th>
                    <th>Invoice</th>
                    <th>Proforma<br>Invoice</th>
                    <th>Keterangan</th>
                    <th>Cut Off<br>Date</th>
                    <th>Konsolidasi<br>Data<br>TMS &amp; CMD</th>
                    <th>Kirim Progress<br>Klaim Approval</th>
                    <th>Data Diterima<br>Dari Ops</th>
                    <th>Proforma Inv<br>Approved</th>
                    <th>Minta<br>CIC</th>
                    <th>Pembuatan<br>CIC</th>
                    <th>Terima<br>CIC</th>
                    <th>Tanggal<br>Inv</th>
                    <th>Pembuatan<br>Inv</th>
                    <th>Kirim CIC<br>Ke KPC</th>
                    <th>Informasi CIC<br>Bisa Diambil</th>
                    <th>CIC Diambil<br>TMS</th>
                    <th>Invoice Terima<br>KPC</th>
                    <th>Status<br>Bayar</th>
                </tr>
            </thead>

            <tbody>
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
                            $paymentClass = 'status-invoicing';
                        } else {
                            $paymentStatus = $paymentInvoice->payment_status ?? 'Invoicing';

                            $paymentClass = match ($paymentStatus) {
                                'Paid' => 'status-paid',
                                'Partialy Paid' => 'status-partial',
                                'Unpaid' => 'status-unpaid',
                                default => 'status-other',
                            };
                        }
                    @endphp

                    @foreach ($proformas as $index => $pi)
                        <tr>
                            <td class="no-cell">
                                {{ $no++ }}
                            </td>

                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}" class="invoice-cell">
                                    {{ $groupInvoice->invoice_no }}
                                </td>
                            @endif

                            <td class="proforma-cell">
                                {{ $pi->proforma_no }}
                            </td>

                            <td class="keterangan-cell">
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

                            <td>{{ $formatDate($pi->cut_off_date) }}</td>
                            <td>{{ $formatDate($pi->consolidation_date) }}</td>
                            <td>{{ $formatDate($pi->progress_claim_date) }}</td>
                            <td>{{ $formatDate($pi->ops_received_date) }}</td>
                            <td>{{ $formatDate($pi->prof_inv_app_date) }}</td>
                            <td>{{ $formatDate($pi->cic_request_date) }}</td>
                            <td>{{ $formatDate($pi->cic_created_date) }}</td>
                            <td>{{ $formatDate($pi->cic_received_date) }}</td>
                            <td>{{ $formatDate($pi->inv_date) }}</td>
                            <td>{{ $formatDate($pi->inv_create_date) }}</td>
                            <td>{{ $formatDate($pi->cic_send_date) }}</td>
                            <td>{{ $formatDate($pi->cic_ready_to_pick_date) }}</td>
                            <td>{{ $formatDate($pi->cic_pick_up_date) }}</td>
                            <td>{{ $formatDate($pi->inv_send_date) }}</td>

                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}" class="{{ $paymentClass }}">
                                    {{ $paymentStatus }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="19" style="text-align:center;padding:10px;">
                            Tidak ada data invoice pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
