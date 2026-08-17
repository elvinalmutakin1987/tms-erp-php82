@php
    use App\Models\Maintenance;
    use App\Models\Invoice;
    use App\Models\Invoice_detail;
    use App\Models\Proforma_invoice;
    use App\Models\Invoice_proforma_invoice;
    use App\Models\Proforma_invoice_detail;
    use Illuminate\Support\Number;
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
@endphp

<style>
    .calendar-container {
        width: 100%;
        max-width: 100%;
        position: relative;
        isolation: isolate;
        z-index: 0;
    }

    .calendar-table-wrapper {
        width: 100%;
        max-width: 100%;
        max-height: 600px;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #dee2e6;
        position: relative;
        isolation: isolate;
        z-index: 0;
        background-color: #ffffff;
    }

    .calendar-table {
        width: 100%;
        min-width: 2575px;
        table-layout: fixed !important;
        margin-bottom: 0;
        border-collapse: separate !important;
        border-spacing: 0;
        position: relative;
        z-index: 0;
        background-color: #ffffff;
    }

    /* Column Width */
    .calendar-table col.col-invoice {
        width: 160px;
    }

    .calendar-table col.col-proforma {
        width: 160px;
    }

    .calendar-table col.col-keterangan {
        width: 440px;
    }

    .calendar-table col.col-date {
        width: 125px;
    }

    /* Header */
    .calendar-table thead th {
        position: sticky;
        top: 0;
        z-index: 5;
        text-align: center;
        vertical-align: middle;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.25;
        padding: 7px 5px;
        white-space: normal;
        background-color: #f8f9fa;
        color: #212529;
        border-top: 0;
        border-left: 0;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        box-sizing: border-box;
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.05);
    }

    /* Body */
    .calendar-table tbody td {
        height: 45px;
        padding: 6px 5px;
        vertical-align: middle;
        text-align: center;
        position: relative;
        z-index: 0;
        font-size: 12px;
        line-height: 1.3;
        background-color: #ffffff;
        border-top: 0;
        border-left: 0;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        white-space: normal;
        word-break: break-word;
        box-sizing: border-box;
    }

    .calendar-table tbody td.keterangan-cell {
        text-align: left;
        padding: 6px 10px;
        line-height: 1.4;
        white-space: normal;
        word-break: normal;
        overflow-wrap: break-word;
    }

    /* Invoice Rowspan */
    .calendar-table tbody td.invoice-cell {
        vertical-align: top !important;
        padding: 0 !important;
        text-align: center;
        font-size: 12px;
        white-space: normal;
        word-break: break-word;
        background-color: #ffffff;
    }

    .invoice-cell-content {
        width: 100%;
        height: 45px;
        padding: 6px 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.3;
        text-align: center;
        white-space: normal;
        word-break: break-word;
    }

    /* Freeze Invoice */
    .calendar-table thead th.sticky-invoice {
        position: sticky;
        top: 0;
        left: 0;
        z-index: 8;
        background-color: #f8f9fa;
    }

    .calendar-table tbody td.sticky-invoice {
        position: sticky;
        left: 0;
        z-index: 3;
        background-color: #ffffff;
    }

    /* Freeze Proforma */
    .calendar-table thead th.sticky-proforma {
        position: sticky;
        top: 0;
        left: 160px;
        z-index: 8;
        background-color: #f8f9fa;
        border-right: 2px solid #adb5bd;
        box-shadow: 4px 0 6px rgba(0, 0, 0, 0.08), 0 2px 3px rgba(0, 0, 0, 0.05);
    }

    .calendar-table tbody td.sticky-proforma {
        position: sticky;
        left: 160px;
        z-index: 3;
        background-color: #ffffff;
        border-right: 2px solid #adb5bd;
        box-shadow: 4px 0 6px rgba(0, 0, 0, 0.08);
    }

    .calendar-table tbody tr:hover td {
        background-color: #f8f9fa;
    }

    .calendar-table tbody tr:hover td.sticky-invoice,
    .calendar-table tbody tr:hover td.sticky-proforma {
        background-color: #f8f9fa;
    }

    .calendar-table .badge {
        font-size: 12px !important;
        white-space: nowrap;
    }

    /* Scrollbar */
    .calendar-table-wrapper::-webkit-scrollbar {
        width: 9px;
        height: 9px;
    }

    .calendar-table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .calendar-table-wrapper::-webkit-scrollbar-thumb {
        background: #adb5bd;
        border-radius: 10px;
    }

    .calendar-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #6c757d;
    }

    @media (max-width: 768px) {
        .calendar-table-wrapper {
            max-height: 500px;
        }

        .calendar-table {
            min-width: 2575px;
        }

        .calendar-table thead th {
            font-size: 11px;
            padding: 6px 4px;
        }

        .calendar-table tbody td {
            font-size: 11px;
            padding: 5px 4px;
        }

        .calendar-table tbody td.keterangan-cell {
            padding: 5px 8px;
        }

        .calendar-table tbody td.invoice-cell {
            padding: 0 !important;
        }

        .invoice-cell-content {
            height: 45px;
            padding: 5px 4px;
            font-size: 11px;
        }

        .calendar-table .badge {
            font-size: 11px !important;
        }
    }
</style>

<div class="calendar-container mb-4">
    <div class="text-center mb-3">
        <h4 class="fw-bold mb-0">
            {{ Carbon::create($year, $month, 1)->format('F Y') }}
        </h4>
    </div>

    <div class="calendar-table-wrapper">
        <table class="table calendar-table mb-0">
            <colgroup>
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
                    <th class="sticky-invoice">Invoice</th>
                    <th class="sticky-proforma">Proforma Invoice</th>
                    <th>Keterangan</th>
                    <th>Cut Off<br>Date</th>
                    <th>Konsolidasi Data<br>TMS & CMD</th>
                    <th>Kirim Progress<br>Klaim Approval</th>
                    <th>Data Diterima<br>Dari Ops</th>
                    <th>Proforma Inv<br>Approved</th>
                    <th>Minta CIC</th>
                    <th>Pembuatan<br>CIC</th>
                    <th>Terima CIC</th>
                    <th>Tgl Inv</th>
                    <th>Pembuatan<br>Inv</th>
                    <th>Kirim CIC<br>Ke KPC</th>
                    <th>Informasi CIC<br>Bisa Diambil</th>
                    <th>CIC Diambil<br>TMS</th>
                    <th>Inv Terima<br>KPC</th>
                    <th>Status Bayar</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($invoiceGroups as $group)
                    @php
                        $groupInvoice = $group['invoice'];
                        $proformas = $group['proformas'];
                        $rowspan = count($proformas);
                    @endphp

                    @foreach ($proformas as $index => $pi)
                        <tr>
                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}" class="invoice-cell sticky-invoice">
                                    <div class="invoice-cell-content">
                                        {{ $groupInvoice->invoice_no }}
                                    </div>
                                </td>
                            @endif

                            <td class="sticky-proforma">
                                {{ $pi->proforma_no }}
                            </td>

                            <td class="keterangan-cell">
                                {!! $pi->contract->notes !!}

                                @if ($pi->contract->service->type == 'Unit Rental' || $pi->contract->service->type == 'Fuel Truck Rental')
                                    - {!! $pi->unit->vehicle_no !!}
                                @endif
                            </td>

                            <td>
                                {{ $pi->cut_off_date ? Carbon::parse($pi->cut_off_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->consolidation_date ? Carbon::parse($pi->consolidation_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->progress_claim_date ? Carbon::parse($pi->progress_claim_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->ops_received_date ? Carbon::parse($pi->ops_received_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->prof_inv_app_date ? Carbon::parse($pi->prof_inv_app_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->cic_request_date ? Carbon::parse($pi->cic_request_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->cic_created_date ? Carbon::parse($pi->cic_created_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->cic_received_date ? Carbon::parse($pi->cic_received_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->inv_date ? Carbon::parse($pi->inv_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->inv_create_date ? Carbon::parse($pi->inv_create_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->cic_send_date ? Carbon::parse($pi->cic_send_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->cic_ready_to_pick_date ? Carbon::parse($pi->cic_ready_to_pick_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->cic_pick_up_date ? Carbon::parse($pi->cic_pick_up_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                {{ $pi->inv_send_date ? Carbon::parse($pi->inv_send_date)->format('d M Y') : '-' }}
                            </td>

                            <td>
                                @php
                                    $paymentInvoice = Invoice::where('id', $pi->invoice_id)
                                        ->whereIn('status', ['Approved', 'Approval', 'Received', 'Done'])
                                        ->first();
                                @endphp

                                @if (!$paymentInvoice)
                                    <span class="badge bg-primary">Invoicing</span>
                                @elseif ($paymentInvoice->payment_status == 'Paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif ($paymentInvoice->payment_status == 'Partialy Paid')
                                    <span class="badge bg-warning text-dark">Partialy Paid</span>
                                @elseif ($paymentInvoice->payment_status == 'Unpaid')
                                    <span class="badge bg-danger">Unpaid</span>
                                @else
                                    <span class="badge bg-primary">
                                        {{ $paymentInvoice->payment_status ?? 'Invoicing' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="18" class="text-center text-muted py-4">
                            Tidak ada data invoice pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-md-flex d-grid align-items-center gap-1">
    <a href="{{ route('report.export', [
        't' => $t,
        'year' => $year,
        'month' => $month,
    ]) }}"
        target="_blank" type="button" class="btn btn-success">
        Export To Excel
    </a>

    <a href="{{ route('report.export_image', [
        't' => $t,
        'year' => $year,
        'month' => $month,
    ]) }}"
        target="_blank" type="button" class="btn btn-warning">
        Export To PNG
    </a>

    <a href="{{ route('report.print', [
        't' => $t,
        'year' => $year,
        'month' => $month,
    ]) }}"
        target="_blank" type="button" class="btn btn-primary">
        Print
    </a>
</div>
