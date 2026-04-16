@php
    use Carbon\Carbon;
    use Illuminate\Support\Number;
@endphp

<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: "DejaVu Sans", "DejaVu Sans Mono", "DejaVu",
            "Helvetica", "Arial", sans-serif;
        margin: 0;
        padding: 0;
        font-size: 9.5pt;
        color: #000;
    }

    /* ===== Main table (single table to repeat header) ===== */
    .table-p2h {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        /* table-layout: fixed; */
        margin: 0;
        padding: 0;
    }

    .table-p2h th,
    .table-p2h td {
        border: 1px solid #000;
        padding: 6px 7px;
        vertical-align: top;
        line-height: 1.25;
        word-wrap: break-word;
    }


    /* ===== Document header wrapper row (nested header table) ===== */
    .table-p2h .doc-header-wrapper {
        padding: 0 !important;
        border: 0 !important;
        line-height: 0;
    }

    .doc-header-table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: fixed;
        border: 1px solid #000;
    }

    .doc-header-table td {
        border: 1px solid #000;
        padding: 8px 10px;
        vertical-align: middle;
        line-height: 1.2;
    }

    .logo-cell {
        width: 18%;
        text-align: center;
    }

    .title-cell {
        text-align: center;
    }

    .meta-cell {
        width: 22%;
        text-align: center;
    }

    .doc-title {
        font-size: 15pt;
        font-weight: 700;
        letter-spacing: .6px;
        line-height: 1.1;
    }

    .doc-subtitle {
        margin-top: 2px;
        font-size: 8.5pt;
        letter-spacing: .2px;
    }

    .docno-label {
        font-size: 8pt;
        letter-spacing: .2px;
        margin-bottom: 2px;
    }

    .docno {
        font-size: 10pt;
        font-weight: 700;
        line-height: 1.1;
    }

    .page-placeholder {
        display: inline-block;
        min-width: 70px;
        letter-spacing: .3px;
    }

    /* ===== Info block under header ===== */
    .doc-info-cell {
        padding: 0 !important;
        line-height: 0;
    }

    .doc-info-table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: fixed;
    }

    .doc-info-table td {
        border: 1px solid #000;
        padding: 7px 10px;
        vertical-align: top;
        line-height: 1.2;
    }

    /* remove outer borders to avoid double border with parent cell */
    .doc-info-table tr:first-child td {
        border-top: 0 !important;
    }

    .doc-info-table tr:last-child td {
        border-bottom: 0 !important;
    }

    .doc-info-table td:first-child {
        border-left: 0 !important;
    }

    .doc-info-table td:last-child {
        border-right: 0 !important;
    }

    .info-inner {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: fixed;
    }

    .info-inner td {
        border: 0 !important;
        padding: 2px 0;
        vertical-align: top;
        line-height: 1.25;
    }

    .info-inner td.label {
        width: 45%;
        font-weight: 700;
    }

    .info-inner td.sep {
        width: 5%;
        text-align: center;
    }

    .info-inner td.val {
        width: 50%;
    }

    /* ===== Checklist header ===== */
    .checklist-head th {
        background: #111;
        color: #fff;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
        border-top: 0 !important;
        /* avoid double border with doc header bottom */
    }

    /* ===== Group row ===== */
    .group-row td {
        background: #e9ecef;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .2px;
        padding: 7px 8px;
    }

    /* ===== Item rows ===== */
    .item-row td {
        background: #fff;
    }

    /* subtle zebra for readability */
    .item-row.zebra td {
        background: #fafafa;
    }

    /* ===== Column widths (kept stable for DomPDF) ===== */
    .col-no {
        width: 5%;
        text-align: center;
        vertical-align: middle;

    }

    .col-item {
        width: 37%;
    }

    .col-action {
        width: 10%;
        text-align: center;
        vertical-align: middle;
    }

    .col-cost {
        width: 20%;
    }

    /* Make "BROKEN" fit on narrow col */
    .checklist-head th.col-broken {
        font-size: 8pt;
        line-height: 1.05;
        padding-left: 3px;
        padding-right: 3px;
        white-space: normal;
        word-break: break-word;
    }

    /* ===== Status badge (print-friendly) ===== */

    .status {
        display: inline-block;
        min-width: 18px;
        height: 18px;
        line-height: 18px;
        text-align: center;
        border: 1px solid #000;
        border-radius: 3px;
        font-weight: 700;
        font-size: 11pt;
        font-family: "DejaVu Sans", "DejaVu", "Symbola", "Arial Unicode MS", sans-serif;
    }

    .status.ok {
        background-color: #d1e7dd;
        color: #000;
    }

    .status.ng {
        background-color: #f8d7da;
        color: #000;
    }

    /* Force Unicode rendering */
    .status.ok::before {
        content: "✔";
        vertical-align: middle;
    }

    .status.ng::before {
        content: "✗";
        vertical-align: middle;
    }


    /* Notes HTML inside cells */
    .cell-notes p {
        margin: 0 0 4px 0;
    }

    .cell-notes ul,
    .cell-notes ol {
        margin: 0;
        padding-left: 16px;
    }

    .cell-notes li {
        margin: 0 0 2px 0;
    }

    /* Avoid awkward splitting */
    .avoid-break {
        page-break-inside: avoid;
    }

    img {
        display: block;
    }

    @media print {
        @page {
            size: A4 !important;
            /* leave a bit more bottom space for footer page number */
            margin: 14px 14px 20px 14px !important;
        }

        thead {
            display: table-header-group !important;
        }

        tfoot {
            display: table-footer-group !important;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* try to preserve background colors */
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

@php
    $no = 1;
@endphp

<table class="table-p2h">
    <colgroup>
        <col class="col-no" style="width:5px;">
        <col class="col-item-detail" style="width:25%;">
        <col class="col-item" style="width:20%;">
        <col class="col-uom-1" style="width:10%;">
        <col class="col-value-1" style="width:10%;">
        <col class="col-uom-2" style="width:10%;">
        <col class="col-value-2" style="width:10%;">
    </colgroup>

    <thead>
        <tr>
            <th colspan="7" class="doc-header-wrapper">
                <table class="doc-header-table">
                    <tr>
                        <td class="logo-cell">
                            <img src="{{ public_path('assets/images/tms_logo.png') }}" alt="Logo"
                                style="max-width:95px;height:auto;margin:0 auto;">
                        </td>

                        <td class="title-cell">
                            <div class="doc-title">DAILY REPORT</div>
                            <div class="doc-subtitle">Vehicle / LCT</div>
                        </td>

                        <td class="meta-cell">
                            <div class="docno-label">Document No.</div>
                            <div class="docno">{{ $daily_report->report_no }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="doc-info-cell">
                            <table class="doc-info-table">
                                <tr>
                                    <td width="30%">
                                        <table class="info-inner">
                                            <tr>
                                                <td class="label" style="width: 10%">Unit</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $daily_report->unit->vehicle_no ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Date</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $daily_report->date ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Shift</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $daily_report->shift ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </th>
        </tr>
    </thead>
</table>

@if ($daily_report->type == 'LCT')
    <table class="table-p2h">
        <thead>
            <tr class="checklist-head">
                <th colspan="4">Trip 1</th>
            </tr>
        </thead>

        <tbody>
            <tr style="background-color: #D9D2D0">
                <td class="p-1 align-middle" colspan="2" style="width: 50%;">
                    <b>Departure</b>
                </td>
                <td class="p-1 align-middle" colspan="2" style="width: 50%">
                    <b>Arrival</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Location</td>
                <td class="p-1 align-middle">{{ $daily_report->trip_1_location->name }}</td>
                <td class="p-1 align-middle" style="width: 20%">Location</td>
                <td class="p-1 align-middle">{{ $daily_report->trip_1_arr_location->name }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Loading At</td>
                <td class="p-1 align-middle">{{ Carbon::parse($daily_report->trip_1_loading_at)->format('H:i') }}</td>
                <td class="p-1 align-middle" style="width: 20%">Arrived At</td>
                <td class="p-1 align-middle">{{ Carbon::parse($daily_report->trip_1_arrived_at)->format('H:i') }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Complete Loading At</td>
                <td class="p-1 align-middle">
                    {{ Carbon::parse($daily_report->trip_1_complete_loading_at)->format('H:i') }}</td>
                <td class="p-1 align-middle" style="width: 20%">Berthing At</td>
                <td class="p-1 align-middle">
                    <b>{{ Carbon::parse($daily_report->trip_1_berthing_at)->format('H:i') }}</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Departed At</td>
                <td class="p-1 align-middle">
                    <b>{{ Carbon::parse($daily_report->trip_1_departed_at)->format('H:i') }}</b>
                </td>
                <td class="p-1 align-middle" style="width: 20%" colspan="2"></td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%"><b>Duration</b></td>
                <td class="p-1 align-middle" colspan="3">
                    <b>{{ Carbon::parse($daily_report->duration_trip_1)->format('H:i') }}</b>
                </td>
            </tr>
        </tbody>

        <thead>
            <tr class="checklist-head">
                <th colspan="4">Trip 2</th>
            </tr>
        </thead>

        <tbody>
            <tr style="background-color: #D9D2D0">
                <td class="p-1 align-middle" colspan="2" style="width: 50%;">
                    <b>Departure</b>
                </td>
                <td class="p-1 align-middle" colspan="2" style="width: 50%">
                    <b>Arrival</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Location</td>
                <td class="p-1 align-middle">{{ $daily_report->trip_2_location->name }}</td>
                <td class="p-1 align-middle" style="width: 20%">Location</td>
                <td class="p-1 align-middle">{{ $daily_report->trip_2_arr_location->name }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Loading At</td>
                <td class="p-1 align-middle">{{ Carbon::parse($daily_report->trip_2_loading_at)->format('H:i') }}</td>
                <td class="p-1 align-middle" style="width: 20%">Arrived At</td>
                <td class="p-1 align-middle">{{ Carbon::parse($daily_report->trip_2_arrived_at)->format('H:i') }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Complete Loading At</td>
                <td class="p-1 align-middle">
                    {{ Carbon::parse($daily_report->trip_2_complete_loading_at)->format('H:i') }}</td>
                <td class="p-1 align-middle" style="width: 20%">Berthing At</td>
                <td class="p-1 align-middle">
                    <b>{{ Carbon::parse($daily_report->trip_2_berthing_at)->format('H:i') }}</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Departed At</td>
                <td class="p-1 align-middle">
                    <b>{{ Carbon::parse($daily_report->trip_2_departed_at)->format('H:i') }}</b>
                </td>
                <td class="p-1 align-middle" style="width: 20%" colspan="2"></td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%"><b>Duration </b></td>
                <td class="p-1 align-middle" colspan="3">
                    <b>{{ Carbon::parse($daily_report->duration_trip_2)->format('H:i') }}</b>
                </td>
            </tr>
            <tr style='background-color: #FAF6F5'>
                <td class="p-1 align-middle" style="width: 20%"><b>Duration Total</b></td>
                <td class="p-1 align-middle" colspan="3">
                    <b>{{ addTime($daily_report->duration_trip_1, $daily_report->duration_trip_2) }}</b>
                </td>
            </tr>
        </tbody>

        <thead>
            <tr class="checklist-head">
                <th colspan="4">Remarks</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="p-1 align-middle" colspan="4">
                    {!! $daily_report->remarks !!}
                </td>
            </tr>
        </tbody>

        <thead>
            <tr class="checklist-head">
                <th colspan="4">Refule</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">From</td>
                <td class="p-1 align-middle" colspan="3">{{ $daily_report->refule_type }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Liter</td>
                <td class="p-1 align-middle" colspan="3">
                    {{ $daily_report->refule_liter ? Number::format($daily_report->refule_liter, precision: 0) : '' }}
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">KM</td>
                <td class="p-1 align-middle" colspan="3">
                    {{ $daily_report->refule_km ? Number::format($daily_report->refule_km, precision: 0) : '' }}</td>
            </tr>
        </tbody>
    </table>

    <table class="table-p2h">
        <thead>
            <tr class="checklist-head">
                <th style="width: 5px">#</th>
                <th>Unit</th>
                <th>Item</th>
                <th style="width: 12%">Uom 1</th>
                <th style="width: 12%">Value 1</th>
                <th style="width: 12%">Uom 2</th>
                <th style="width: 12%">Value 2</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($daily_report_detail as $d)
                <tr>
                    <td class="p-1 align-middle">
                        {{ $loop->iteration }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $d->unit->vehicle_no }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $d->item }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $d->uom_1 }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $d->value_1 ? Number::format($d->value_1, precision: 2) : '' }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $d->uom_2 }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $d->value_2 ? Number::format($d->value_2, precision: 2) : '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <table class="table-p2h">
        <thead>
            <tr class="checklist-head">
                <th style="width: 5%">#</th>
                <th style="width: 25%">Item</th>
                <th>Value</th>
            </tr>
        </thead>

        <tbody>
            <tr style="background-color: #D9D2D0">
                <td class="p-1 align-middle" colspan="3">
                    <b>KM</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">1</td>
                <td class="p-1 align-middle">Start</td>
                <td class="p-1 align-middle">
                    {{ $daily_report->km_start ? Number::format($daily_report->km_start, precision: 0) : '' }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle">2</td>
                <td class="p-1 align-middle">Finish</td>
                <td class="p-1 align-middle">
                    {{ $daily_report->km_finish ? Number::format($daily_report->km_finish, precision: 0) : '' }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle">3</td>
                <td class="p-1 align-middle">Total</td>
                <td class="p-1 align-middle">
                    {{ $daily_report->km_total ? Number::format($daily_report->km_total, precision: 0) : '' }}</td>
            </tr>

            <tr style="background-color: #D9D2D0">
                <td class="p-1 align-middle" colspan="3">
                    <b>Person</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">4</td>
                <td class="p-1 align-middle">Operator</td>
                <td class="p-1 align-middle">{{ $daily_report->operator }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle">5</td>
                <td class="p-1 align-middle">Helper</td>
                <td class="p-1 align-middle">{{ $daily_report->helper }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle">6</td>
                <td class="p-1 align-middle">Total</td>
                <td class="p-1 align-middle">
                    {{ $daily_report->load ? Number::format($daily_report->load, precision: 0) : '' }}</td>
            </tr>

            <tr style="background-color: #D9D2D0">
                <td class="p-1 align-middle" colspan="3">
                    <b>Refule</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">6</td>
                <td class="p-1 align-middle">From</td>
                <td class="p-1 align-middle">
                    {{ $daily_report->refule_liter || $daily_report->refule_km ? $daily_report->refule_type : '' }}
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">7</td>
                <td class="p-1 align-middle">Liter</td>
                <td class="p-1 align-middle">
                    {{ $daily_report->refule_liter ? Number::format($daily_report->refule_liter, precision: 0) : '' }}
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">8</td>
                <td class="p-1 align-middle">KM</td>
                <td class="p-1 align-middle">
                    {{ $daily_report->refule_km ? Number::format($daily_report->refule_km, precision: 0) : '' }}</td>
            </tr>
            <tr style="background-color: #D9D2D0">
                <td class="p-1 align-middle" colspan="3">
                    <b>Remarks</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" colspan="3">
                    {!! $daily_report->remarks !!}
                </td>
            </tr>
        </tbody>
    </table>
@endif

{{-- Page number footer (DomPDF) --}}
<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont("Helvetica", "normal");
        $size = 8;

        // Footer right
        $text = "Halaman {PAGE_NUM} / {PAGE_COUNT}";

        // A4 portrait ~ 595x842 pt. Adjust slightly if needed.
        $x = 430;   // move left/right
        $y = 820;   // move up/down

        $pdf->page_text($x, $y, $text, $font, $size, array(0,0,0));
    }
</script>
