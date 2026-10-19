@php
    use App\Models\Contract;
    use App\Models\Contract_rate;
    use App\Models\Contract_fmf;
    use App\Models\Unit_rate;
    use App\Models\Unit_target;
    use App\Models\Daily_report;
    use App\Models\Daily_report_detail;
    use App\Models\Plan;
    use App\Models\Unit;
    use App\Models\Plan_detail;
    use App\Models\Purchase_order;
    use App\Models\Purchase_order_detail;
    use App\Models\Proforma_invoice;
    use App\Models\Proforma_invoice_detail;
    use App\Models\Invoice_proforma_invoice;
    use App\Models\Invoice;
    use App\Models\Invoice_detail;
    use App\Models\Approval_flow;
    use App\Models\Approval_process;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    $now = Carbon::now();
    $dateStart = Carbon::now()->startOfYear()->toDateString();
    $dateEnd = Carbon::now()->endOfMonth()->toDateString();
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();
    $months = range(1, $now->month);

    $year = date('Y');
    $month = [
        ['month' => '01', 'month_name' => 'Jan'],
        ['month' => '02', 'month_name' => 'Feb'],
        ['month' => '03', 'month_name' => 'Mar'],
        ['month' => '04', 'month_name' => 'Apr'],
        ['month' => '05', 'month_name' => 'May'],
        ['month' => '06', 'month_name' => 'Jun'],
        ['month' => '07', 'month_name' => 'Jul'],
        ['month' => '08', 'month_name' => 'Aug'],
        ['month' => '09', 'month_name' => 'Sep'],
        ['month' => '10', 'month_name' => 'Oct'],
        ['month' => '11', 'month_name' => 'Nov'],
        ['month' => '12', 'month_name' => 'Des'],
    ];
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

<div class="page-wrapper">
    <div class="page-content container-xxl">
        @php
            $periode = Carbon::now()->format('Y-m');

            $proformaInvoices = Proforma_invoice::where('periode', $periode)
                ->whereIn('status', ['Approved', 'CIC Approval', 'Invoicing', 'Done'])
                ->get();

            $proforma_invoice_id = $proformaInvoices = Proforma_invoice::where('periode', $periode)
                ->whereIn('status', ['Approved', 'CIC Approval', 'Invoicing', 'Done'])
                ->pluck('id');

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

            // $total_invoice = Invoice::whereIn('proforma_invoice_id', $proforma_invoice_id)->count();
            $total_submit_cic = Proforma_invoice::whereIn('id', $proforma_invoice_id)
                ->where(function ($query) {
                    $query->whereNotNull('cic_request_date');
                })
                ->count();

            $total_request_invoice = Invoice::whereIn('proforma_invoice_id', $proforma_invoice_id)
                ->whereIn('status', ['Approval', 'Approved', 'Reject'])
                ->count();

            $total_signature_invoice = Invoice::whereIn('proforma_invoice_id', $proforma_invoice_id)
                ->where('status', 'Approved')
                ->count();

            $total_decline_invoice = Invoice::whereIn('proforma_invoice_id', $proforma_invoice_id)
                ->where('status', 'Reject')
                ->count();

        @endphp

        <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4">
            <div class="col">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary fw-bold">CIC Request</p>
                                <h1 class="my-1">{{ $total_submit_cic }}</h1>
                                <p class="mb-0 font-13 text-success">&nbsp;
                                </p>
                            </div>
                            <div class="widgets-icons bg-light-warning text-warning ms-auto">
                                <i class='bx bxs-file-export'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary fw-bold">Invoice Request</p>
                                <h1 class="my-1">{{ $total_request_invoice }}</h1>
                                <p class="mb-0 font-13 text-success">&nbsp;</p>
                            </div>
                            <div class="widgets-icons bg-light-primary text-primary ms-auto">
                                <i class='bx bxs-edit-alt'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary fw-bold">Signature Invoice</p>
                                <h1 class="my-1">{{ $total_signature_invoice }}</h1>
                                @php
                                    $sign_invoice_persen =
                                        $total_request_invoice > 0
                                            ? ($total_signature_invoice / $total_request_invoice) * 100
                                            : 0;
                                @endphp
                                <p class="mb-0 font-13 text-success">{{ Number::format($sign_invoice_persen) }}%
                                </p>
                            </div>
                            <div class="widgets-icons bg-light-success text-success ms-auto">
                                <i class='bx bxs-check-circle'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary fw-bold">Decline Invoice</p>
                                <h1 class="my-1">{{ $total_decline_invoice }}</h1>
                                @php
                                    $dec_invoice_persen =
                                        $total_request_invoice > 0
                                            ? ($total_decline_invoice / $total_request_invoice) * 100
                                            : 0;
                                @endphp
                                <p class="mb-0 font-13 text-danger">{{ Number::format($dec_invoice_persen) }}%
                                </p>
                            </div>
                            <div class="widgets-icons bg-light-danger text-danger ms-auto">
                                <i class='bx bxs-x-circle'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="mb-0">Invoice Monitoring - {{ date('M Y') }}</h5>
                    </div>
                    <div class="font-22 ms-auto"><i class='bx bx-dots-horizontal-rounded'></i>
                    </div>
                </div>
                <hr />
                <div class="table-responsive">
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

                        <thead class="table-light">
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
                                        There is no invoice data for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
                min-width: 1800px;
                table-layout: fixed !important;
                margin-bottom: 0;
                border-collapse: separate !important;
                border-spacing: 0;
                position: relative;
                z-index: 0;
                background-color: #ffffff;
            }

            /* Column Width */
            .calendar-table col.col-po {
                width: 160px;
            }

            .calendar-table col.col-vendor {
                width: 320px;
            }

            .calendar-table col.col-normal {
                width: 125px;
            }

            .calendar-table col.col-remarks {
                width: 320px;
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

            .calendar-table tbody td.text-cell {
                text-align: left;
                padding: 6px 10px;
                line-height: 1.4;
                white-space: normal;
                word-break: normal;
                overflow-wrap: break-word;
            }

            /* Freeze No. PO */
            .calendar-table thead th.sticky-po {
                position: sticky;
                top: 0;
                left: 0;
                z-index: 8;
                background-color: #f8f9fa;
                border-right: 2px solid #adb5bd;
                box-shadow: 4px 0 6px rgba(0, 0, 0, 0.08), 0 2px 3px rgba(0, 0, 0, 0.05);
            }

            .calendar-table tbody td.sticky-po {
                position: sticky;
                left: 0;
                z-index: 3;
                background-color: #ffffff;
                border-right: 2px solid #adb5bd;
                box-shadow: 4px 0 6px rgba(0, 0, 0, 0.08);
                font-weight: 600;
            }

            .calendar-table tbody tr:hover td {
                background-color: #f8f9fa;
            }

            .calendar-table tbody tr:hover td.sticky-po {
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
                    min-width: 1800px;
                }

                .calendar-table thead th {
                    font-size: 11px;
                    padding: 6px 4px;
                }

                .calendar-table tbody td {
                    font-size: 11px;
                    padding: 5px 4px;
                }

                .calendar-table tbody td.text-cell {
                    padding: 5px 8px;
                }

                .calendar-table .badge {
                    font-size: 11px !important;
                }
            }
        </style>

        @php
            $purchase_order = Purchase_order::query();
            $purchase_order->whereYear('date', date('Y'));
            $purchase_order->whereMonth('date', date('m'));
            $purchase_order = $purchase_order
                ->whereIn('status', ['Approved', 'Approval', 'Received', 'Done'])
                ->orderBy('date', 'desc')
                ->get();
        @endphp
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="mb-0">PO Monitoring - {{ date('M Y') }}</h5>
                    </div>
                    <div class="font-22 ms-auto"><i class='bx bx-dots-horizontal-rounded'></i>
                    </div>
                </div>
                <hr />
                <div class="table-responsive">
                    <table class="table calendar-table mb-0">
                        <colgroup>
                            <col class="col-po">
                            <col class="col-vendor">
                            <col class="col-normal">
                            <col class="col-normal">
                            <col class="col-normal">
                            <col class="col-normal">
                            <col class="col-normal">
                            <col class="col-normal">
                            <col class="col-normal">
                            <col class="col-remarks">
                            <col class="col-normal">
                        </colgroup>

                        <thead>
                            <tr>
                                <th class="sticky-po">No. PO</th>
                                <th>Vendor</th>
                                <th>Unit</th>
                                <th>Divisi</th>
                                <th>Tanggal Pembuatan<br>PO</th>
                                <th>Pengajuan<br>Approval</th>
                                <th>Tanggal<br>Approved</th>
                                <th>Approval<br>Aging</th>
                                <th>Keterangan</th>
                                <th>Remarks</th>
                                <th>Status Bayar</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($purchase_order as $po)
                                @php
                                    $app_proc_awal = Approval_process::with('approval_step')
                                        ->join(
                                            'approval_steps',
                                            'approval_processes.approval_step_id',
                                            '=',
                                            'approval_steps.id',
                                        )
                                        ->whereIn('approval_processes.approval_flow_id', $approval_flow_id)
                                        ->where('approval_processes.approvable_id', $po->id)
                                        ->orderBy('approval_steps.order', 'asc')
                                        ->select('approval_processes.*')
                                        ->first();

                                    $tgl_pengajuan = $app_proc_awal?->created_at ?? $po->created_at;

                                    $app_proc_akhir = Approval_process::with('approval_step')
                                        ->join(
                                            'approval_steps',
                                            'approval_processes.approval_step_id',
                                            '=',
                                            'approval_steps.id',
                                        )
                                        ->whereIn('approval_processes.approval_flow_id', $approval_flow_id)
                                        ->where('approval_processes.approvable_id', $po->id)
                                        ->orderBy('approval_steps.order', 'desc')
                                        ->select('approval_processes.*')
                                        ->first();

                                    $tgl_approve = $app_proc_akhir?->updated_at ?? $po->created_at;

                                    $tanggal_awal = Carbon::parse($tgl_pengajuan)->startOfDay();
                                    $tanggal_akhir = Carbon::parse($tgl_approve)->startOfDay();
                                    $selisih_hari = (int) round($tanggal_awal->diffInDays($tanggal_akhir));
                                @endphp

                                <tr>
                                    <td class="sticky-po">{{ $po->order_no }}</td>

                                    <td class="text-cell">
                                        {{ $po->client_vendor->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $po->unit->vehicle_no ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $po->department ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $po->date ? Carbon::parse($po->date)->format('d M Y') : '-' }}
                                    </td>

                                    <td>
                                        {{ $app_proc_awal?->created_at
                                            ? Carbon::parse($app_proc_awal->created_at)->format('d M Y')
                                            : Carbon::parse($po->created_at)->format('d M Y') }}
                                    </td>

                                    <td>
                                        {{ $app_proc_akhir?->action === 'Approved' && $app_proc_akhir?->updated_at
                                            ? Carbon::parse($app_proc_akhir->updated_at)->format('d M Y')
                                            : Carbon::parse($po->created_at)->format('d M Y') }}
                                    </td>

                                    <td>
                                        {!! match (true) {
                                            $selisih_hari < 3 => '<span class="badge bg-success">' . $selisih_hari . ' Hari</span>',
                                            $selisih_hari == 3 => '<span class="badge bg-warning text-dark">' . $selisih_hari . ' Hari</span>',
                                            $selisih_hari > 3 => '<span class="badge bg-danger">' . $selisih_hari . ' Hari</span>',
                                        } !!}
                                    </td>

                                    <td>
                                        {!! match ($po->urgency) {
                                            'P5' => '<span class="badge bg-success">LP</span>',
                                            'P4' => '<span class="badge bg-info">MP</span>',
                                            'P3' => '<span class="badge bg-primary">HP</span>',
                                            'P2' => '<span class="badge bg-warning text-dark">U</span>',
                                            'P1' => '<span class="badge bg-danger">TU</span>',
                                            default => '<span class="badge bg-secondary">-</span>',
                                        } !!}
                                    </td>

                                    <td class="text-cell">
                                        {{ $po->remarks ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $po->payment_status ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" style="text-align:center; padding:20px;">
                                        There is no order data for this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
