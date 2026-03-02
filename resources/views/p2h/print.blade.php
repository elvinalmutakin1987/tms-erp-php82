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
        font-size: 14pt;
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
        width: 28%;
        font-weight: 700;
    }

    .info-inner td.sep {
        width: 4%;
        text-align: center;
    }

    .info-inner td.val {
        width: 68%;
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

    .col-broken {
        width: 10%;
        text-align: center;
        vertical-align: middle;
    }

    .col-defect {
        width: 23%;
    }

    .col-action {
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

<table class="table-p2h">
    <colgroup>
        <col class="col-no" style="width:5%;">
        <col class="col-item" style="width:30%;">
        <col class="col-broken" style="width:15%;">
        <col class="col-defect" style="width:25%;">
        <col class="col-action" style="width:25%;">
    </colgroup>

    <thead>
        <tr>
            <th colspan="5" class="doc-header-wrapper">
                <table class="doc-header-table">
                    <tr>
                        <td class="logo-cell">
                            <img src="{{ public_path('assets/images/tms_logo.png') }}" alt="Logo"
                                style="max-width:95px;height:auto;margin:0 auto;">
                        </td>

                        <td class="title-cell">
                            <div class="doc-title">PRE START CHECK</div>
                            <div class="doc-subtitle">Vehicle Daily Inspection</div>
                        </td>

                        <td class="meta-cell">
                            <div class="docno-label">Document No.</div>
                            <div class="docno">{{ $p2h->p2h_no }}</div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" class="doc-info-cell">
                            <table class="doc-info-table">
                                <tr>
                                    <td width="50%">
                                        <table class="info-inner">
                                            <tr>
                                                <td class="label">Unit</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $p2h->unit->vehicle_no ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Driver</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $p2h->driver ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Shift</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $p2h->shift ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td width="50%">
                                        <table class="info-inner">
                                            <tr>
                                                <td class="label">Date</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $p2h->date ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">KM Start</td>
                                                <td class="sep">:</td>
                                                <td class="val">
                                                    {{ Number::format($p2h->km_start ?? 0, precision: 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">KM Finish</td>
                                                <td class="sep">:</td>
                                                <td class="val">
                                                    {{ Number::format($p2h->km_finish ?? 0, precision: 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">KM Total</td>
                                                <td class="sep">:</td>
                                                <td class="val">
                                                    {{ Number::format($p2h->km_finish - $p2h->km_start ?? 0, precision: 2) }}
                                                </td>
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

        <tr class="checklist-head">
            <th class="col-no">#</th>
            <th class="col-item">Item</th>
            <th class="col-broken">Broken</th>
            <th class="col-defect">Defect Listed</th>
            <th class="col-action">Action Taken</th>
        </tr>
    </thead>

    <tbody>
        @php
            $no = 1;
            $zebra = 0;
        @endphp

        @foreach ($p2hitem as $group => $items)
            <tr class="group-row avoid-break">
                <td colspan="5">{{ $group }}</td>
            </tr>

            @foreach ($items as $idx => $item)
                @php
                    $p2h_detail = $p2h
                        ->p2h_detail()
                        ->where([
                            'inspection_group' => $group,
                            'inspection_item' => $item,
                        ])
                        ->first();

                    $check = 0;
                    $defect_listed = '';
                    $action_taken = '';

                    if ($p2h_detail) {
                        $check = $p2h_detail->check;
                        $defect_listed = $p2h_detail->defect_listed;
                        $action_taken = $p2h_detail->action_taken;
                    }

                    // zebra only for item rows
                    $zebra = 1 - $zebra;
                    $rowClass = $zebra ? 'item-row zebra' : 'item-row';
                @endphp

                <tr class="{{ $rowClass }} avoid-break">
                    <td class="col-no">{{ $no++ }}</td>

                    <td class="col-item">
                        {{ $item }}
                        <input type="hidden" name="inspection_group[]" value="{{ $group }}">
                        <input type="hidden" name="inspection_item[]" value="{{ $item }}">
                    </td>

                    <td class="col-broken">
                        @if ($check == 0)
                            <span class="status ok">

                            </span>
                        @else
                            <span class="status ng">

                            </span>
                        @endif
                    </td>

                    <td class="col-defect cell-notes">{!! $defect_listed !!}</td>
                    <td class="col-action cell-notes">{!! $action_taken !!}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

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
