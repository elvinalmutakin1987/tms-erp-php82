@php
    use App\Models\Invoice;
    use App\Models\Proforma_invoice;
    use App\Models\Invoice_proforma_invoice;
    use Carbon\Carbon;

    /*
    |--------------------------------------------------------------------------
    | Periode
    |--------------------------------------------------------------------------
    */

    $periode = Carbon::create($year, $month, 1)->format('Y-m');

    /*
    |--------------------------------------------------------------------------
    | Ambil Proforma Invoice
    |--------------------------------------------------------------------------
    */

    $proformaInvoices = Proforma_invoice::where('periode', $periode)
        ->whereIn('status', ['Approved', 'CIC Approval', 'Invoicing', 'Done'])
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Group berdasarkan Invoice
    |--------------------------------------------------------------------------
    */

    $invoiceGroups = [];

    foreach ($proformaInvoices as $pi) {
        /*
        |--------------------------------------------------------------------------
        | Cari relasi Proforma -> Invoice
        |--------------------------------------------------------------------------
        */

        $invoiceProforma = Invoice_proforma_invoice::where('proforma_invoice_id', $pi->id)->first();

        /*
        |--------------------------------------------------------------------------
        | Skip jika relasi tidak tersedia
        |--------------------------------------------------------------------------
        */

        if (!$invoiceProforma) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Cari Invoice
        |--------------------------------------------------------------------------
        */

        $groupInvoice = Invoice::find($invoiceProforma->invoice_id);

        /*
        |--------------------------------------------------------------------------
        | Skip jika Invoice tidak ditemukan
        |--------------------------------------------------------------------------
        */

        if (!$groupInvoice) {
            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Buat group
        |--------------------------------------------------------------------------
        */

        if (!isset($invoiceGroups[$groupInvoice->id])) {
            $invoiceGroups[$groupInvoice->id] = [
                'invoice' => $groupInvoice,
                'proformas' => [],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Masukkan PI ke group
        |--------------------------------------------------------------------------
        */

        $invoiceGroups[$groupInvoice->id]['proformas'][] = $pi;
    }

    /*
    |--------------------------------------------------------------------------
    | Nomor
    |--------------------------------------------------------------------------
    */

    $no = 1;

@endphp


<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <title>Invoice Monitoring</title>


    <style>
        /*
        |--------------------------------------------------------------------------
        | PAGE
        |--------------------------------------------------------------------------
        |
        | Margin halaman dibuat kecil: 12pt ± 4,2mm.
        |
        | Selain itu print-shell memiliki padding 8pt ± 2,8mm.
        |
        | Total ruang visual dari tepi sekitar 7mm.
        |
        */

        @page {
            size: A4 landscape;
            margin: 12pt;
        }


        /*
        |--------------------------------------------------------------------------
        | RESET
        |--------------------------------------------------------------------------
        */

        html,
        body {
            margin: 0;
            padding: 0;

            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;

            color: #000000;

            background-color: #ffffff;
        }


        /*
        |--------------------------------------------------------------------------
        | PRINT SHELL
        |--------------------------------------------------------------------------
        |
        | Ini yang memastikan tabel TIDAK menempel ke pinggir.
        |
        */

        .print-shell {
            padding-top: 8pt;
            padding-right: 8pt;
            padding-bottom: 8pt;
            padding-left: 8pt;
        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        .report-title {
            text-align: center;

            margin: 0 0 9px 0;
            padding: 0;
        }


        .company-name {
            font-size: 17px;
            font-weight: bold;

            line-height: 1.15;

            margin: 0 0 3px 0;
        }


        .report-name {
            font-size: 15px;
            font-weight: bold;

            line-height: 1.15;

            margin: 0 0 3px 0;
        }


        .report-period {
            font-size: 12px;
            font-weight: bold;

            line-height: 1.15;

            margin: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        .report-table {
            width: 100%;

            table-layout: fixed;

            border-collapse: separate;
            border-spacing: 0;

            margin: 0;
            padding: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | REPEAT HEADER
        |--------------------------------------------------------------------------
        */

        .report-table thead {
            display: table-header-group;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER CELL
        |--------------------------------------------------------------------------
        */

        .report-table th {
            border: 1px solid #000000;

            background-color: #f2f2f2;

            color: #000000;

            text-align: center;
            vertical-align: middle;

            font-size: 7.2px;
            font-weight: bold;

            line-height: 1.15;

            padding: 4px 2px;

            word-wrap: break-word;
            overflow-wrap: break-word;
        }


        /*
        |--------------------------------------------------------------------------
        | BODY CELL
        |--------------------------------------------------------------------------
        */

        .report-table td {
            border: 1px solid #000000;

            background-color: #ffffff;

            color: #000000;

            text-align: center;
            vertical-align: middle;

            font-size: 7.5px;

            line-height: 1.2;

            padding: 4px 2px;

            word-wrap: break-word;
            overflow-wrap: break-word;
        }


        /*
        |--------------------------------------------------------------------------
        | Jangan pecah row
        |--------------------------------------------------------------------------
        */

        .report-table tr {
            page-break-inside: avoid;
        }


        /*
        |--------------------------------------------------------------------------
        | NO
        |--------------------------------------------------------------------------
        */

        .no-cell {
            text-align: center;
        }


        /*
        |--------------------------------------------------------------------------
        | INVOICE
        |--------------------------------------------------------------------------
        */

        .invoice-cell {
            text-align: center;

            vertical-align: middle;

            font-weight: bold;

            font-size: 7.5px;
        }


        /*
        |--------------------------------------------------------------------------
        | PROFORMA
        |--------------------------------------------------------------------------
        */

        .proforma-cell {
            text-align: center;

            font-size: 7.5px;
        }


        /*
        |--------------------------------------------------------------------------
        | KETERANGAN
        |--------------------------------------------------------------------------
        */

        .keterangan-cell {
            text-align: left !important;

            vertical-align: middle;

            padding-left: 4px !important;
            padding-right: 4px !important;

            font-size: 7.5px;

            line-height: 1.25 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | COLUMN WIDTH
        |--------------------------------------------------------------------------
        |
        | Total = 100%
        |
        | No          2.5
        | Invoice     7.5
        | Proforma    7.5
        | Keterangan 15
        | 15 x 4.5   67.5
        |
        */

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


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        .status-invoicing {
            background-color: #0d6efd !important;

            color: #ffffff !important;

            text-align: center;

            vertical-align: middle;

            font-size: 7.5px;

            font-weight: bold;
        }


        .status-paid {
            background-color: #198754 !important;

            color: #ffffff !important;

            text-align: center;

            vertical-align: middle;

            font-size: 7.5px;

            font-weight: bold;
        }


        .status-partial {
            background-color: #ffc107 !important;

            color: #000000 !important;

            text-align: center;

            vertical-align: middle;

            font-size: 7.5px;

            font-weight: bold;
        }


        .status-unpaid {
            background-color: #dc3545 !important;

            color: #ffffff !important;

            text-align: center;

            vertical-align: middle;

            font-size: 7.5px;

            font-weight: bold;
        }


        .status-other {
            background-color: #0d6efd !important;

            color: #ffffff !important;

            text-align: center;

            vertical-align: middle;

            font-size: 7.5px;

            font-weight: bold;
        }
    </style>

</head>


<body>


    <div class="print-shell">


        {{-- ================================================================== --}}
        {{-- JUDUL --}}
        {{-- ================================================================== --}}

        <div class="report-title">


            <div class="company-name">

                PT. TUNAS MITRA SEJATI

            </div>


            <div class="report-name">

                Invoice Monitoring

            </div>


            <div class="report-period">

                {{ Carbon::create($year, $month, 1)->format('F Y') }}

            </div>


        </div>



        {{-- ================================================================== --}}
        {{-- TABLE --}}
        {{-- ================================================================== --}}

        <table class="report-table">


            {{-- ============================================================== --}}
            {{-- COLUMN WIDTH --}}
            {{-- ============================================================== --}}

            <colgroup>

                {{-- 1 --}}
                <col class="col-no">

                {{-- 2 --}}
                <col class="col-invoice">

                {{-- 3 --}}
                <col class="col-proforma">

                {{-- 4 --}}
                <col class="col-keterangan">

                {{-- 5 --}}
                <col class="col-date">

                {{-- 6 --}}
                <col class="col-date">

                {{-- 7 --}}
                <col class="col-date">

                {{-- 8 --}}
                <col class="col-date">

                {{-- 9 --}}
                <col class="col-date">

                {{-- 10 --}}
                <col class="col-date">

                {{-- 11 --}}
                <col class="col-date">

                {{-- 12 --}}
                <col class="col-date">

                {{-- 13 --}}
                <col class="col-date">

                {{-- 14 --}}
                <col class="col-date">

                {{-- 15 --}}
                <col class="col-date">

                {{-- 16 --}}
                <col class="col-date">

                {{-- 17 --}}
                <col class="col-date">

                {{-- 18 --}}
                <col class="col-date">

                {{-- 19 --}}
                <col class="col-date">

            </colgroup>



            {{-- ============================================================== --}}
            {{-- HEADER --}}
            {{-- ============================================================== --}}

            <thead>


                <tr>


                    <th>
                        No.
                    </th>


                    <th>
                        Invoice
                    </th>


                    <th>
                        Proforma<br>
                        Invoice
                    </th>


                    <th>
                        Keterangan
                    </th>


                    <th>
                        Cut Off<br>
                        Date
                    </th>


                    <th>
                        Konsolidasi<br>
                        Data<br>
                        TMS &amp; CMD
                    </th>


                    <th>
                        Kirim Progress<br>
                        Klaim Approval
                    </th>


                    <th>
                        Data Diterima<br>
                        Dari Ops
                    </th>


                    <th>
                        Proforma Inv<br>
                        Approved
                    </th>


                    <th>
                        Minta<br>
                        CIC
                    </th>


                    <th>
                        Pembuatan<br>
                        CIC
                    </th>


                    <th>
                        Terima<br>
                        CIC
                    </th>


                    <th>
                        Tanggal<br>
                        Inv
                    </th>


                    <th>
                        Pembuatan<br>
                        Inv
                    </th>


                    <th>
                        Kirim CIC<br>
                        Ke KPC
                    </th>


                    <th>
                        Informasi CIC<br>
                        Bisa Diambil
                    </th>


                    <th>
                        CIC Diambil<br>
                        TMS
                    </th>


                    <th>
                        Invoice Terima<br>
                        KPC
                    </th>


                    <th>
                        Status<br>
                        Bayar
                    </th>


                </tr>


            </thead>



            {{-- ============================================================== --}}
            {{-- BODY --}}
            {{-- ============================================================== --}}

            <tbody>


                @forelse ($invoiceGroups as $group)


                    @php

                        /*
                |--------------------------------------------------------------------------
                | Invoice
                |--------------------------------------------------------------------------
                */

                        $groupInvoice = $group['invoice'];

                        /*
                |--------------------------------------------------------------------------
                | Semua Proforma
                |--------------------------------------------------------------------------
                */

                        $proformas = $group['proformas'];

                        /*
                |--------------------------------------------------------------------------
                | Rowspan
                |--------------------------------------------------------------------------
                */

                        $rowspan = count($proformas);

                        /*
                |--------------------------------------------------------------------------
                | Status pembayaran
                |--------------------------------------------------------------------------
                */

                        $paymentInvoice = Invoice::where('id', $groupInvoice->id)
                            ->whereIn('status', ['Approved', 'Approval', 'Received', 'Done'])
                            ->first();

                    @endphp



                    @foreach ($proformas as $index => $pi)
                        <tr>


                            {{-- ================================================== --}}
                            {{-- NO --}}
                            {{-- ================================================== --}}

                            <td class="no-cell">

                                {{ $no++ }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- INVOICE --}}
                            {{-- ================================================== --}}

                            @if ($index === 0)
                                <td rowspan="{{ $rowspan }}" class="invoice-cell">

                                    {{ $groupInvoice->invoice_no }}

                                </td>
                            @endif



                            {{-- ================================================== --}}
                            {{-- PROFORMA --}}
                            {{-- ================================================== --}}

                            <td class="proforma-cell">

                                {{ $pi->proforma_no }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- KETERANGAN --}}
                            {{-- ================================================== --}}

                            <td class="keterangan-cell">


                                @if ($pi->contract)
                                    {{ strip_tags($pi->contract->notes ?? '') }}


                                    @if (
                                        $pi->contract->service &&
                                            ($pi->contract->service->type == 'Unit Rental' || $pi->contract->service->type == 'Fuel Truck Rental'))
                                        @if ($pi->unit)
                                            -
                                            {{ $pi->unit->vehicle_no }}
                                        @endif
                                    @endif
                                @else
                                    -
                                @endif


                            </td>



                            {{-- ================================================== --}}
                            {{-- CUT OFF DATE --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->cut_off_date ? Carbon::parse($pi->cut_off_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- KONSOLIDASI --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->consolidation_date ? Carbon::parse($pi->consolidation_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- PROGRESS CLAIM --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->progress_claim_date ? Carbon::parse($pi->progress_claim_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- OPS RECEIVED --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->ops_received_date ? Carbon::parse($pi->ops_received_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- PROFORMA APPROVED --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->prof_inv_app_date ? Carbon::parse($pi->prof_inv_app_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- CIC REQUEST --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->cic_request_date ? Carbon::parse($pi->cic_request_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- CIC CREATED --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->cic_created_date ? Carbon::parse($pi->cic_created_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- CIC RECEIVED --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->cic_received_date ? Carbon::parse($pi->cic_received_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- INVOICE DATE --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->inv_date ? Carbon::parse($pi->inv_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- INVOICE CREATE --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->inv_create_date ? Carbon::parse($pi->inv_create_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- CIC SEND --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->cic_send_date ? Carbon::parse($pi->cic_send_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- CIC READY --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->cic_ready_to_pick_date ? Carbon::parse($pi->cic_ready_to_pick_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- CIC PICK UP --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->cic_pick_up_date ? Carbon::parse($pi->cic_pick_up_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- INVOICE SEND --}}
                            {{-- ================================================== --}}

                            <td>

                                {{ $pi->inv_send_date ? Carbon::parse($pi->inv_send_date)->format('d M Y') : '-' }}

                            </td>



                            {{-- ================================================== --}}
                            {{-- STATUS BAYAR --}}
                            {{-- ================================================== --}}

                            @if ($index === 0)
                                @if (!$paymentInvoice)
                                    <td rowspan="{{ $rowspan }}" class="status-invoicing">

                                        Invoicing

                                    </td>
                                @elseif ($paymentInvoice->payment_status == 'Paid')
                                    <td rowspan="{{ $rowspan }}" class="status-paid">

                                        Paid

                                    </td>
                                @elseif ($paymentInvoice->payment_status == 'Partialy Paid')
                                    <td rowspan="{{ $rowspan }}" class="status-partial">

                                        Partialy Paid

                                    </td>
                                @elseif ($paymentInvoice->payment_status == 'Unpaid')
                                    <td rowspan="{{ $rowspan }}" class="status-unpaid">

                                        Unpaid

                                    </td>
                                @else
                                    <td rowspan="{{ $rowspan }}" class="status-other">

                                        {{ $paymentInvoice->payment_status ?? 'Invoicing' }}

                                    </td>
                                @endif
                            @endif


                        </tr>
                    @endforeach



                @empty


                    <tr>


                        <td colspan="19"
                            style="
                        text-align: center;
                        padding: 10px;
                    ">

                            Tidak ada data invoice pada periode ini.

                        </td>


                    </tr>


                @endforelse


            </tbody>


        </table>


    </div>


</body>

</html>
