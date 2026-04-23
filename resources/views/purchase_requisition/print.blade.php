@php
    use Carbon\Carbon;
    use Illuminate\Support\Number;
    use App\Models\Approval_flow;
    use App\Models\Approval_status;
    use App\Models\Approval_process;
    use App\Models\Approval_step;

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

    .col-maintenance-item {
        width: 30%;
    }

    .col-mro-item {
        width: 35%;
    }

    .col-uom {
        width: 15%;
    }

    .col-qty {
        width: 15%;
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

    .watermark {
        position: fixed;
        top: 35%;
        left: 15%;
        transform: rotate(-30deg);
        font-size: 72px;
        color: rgba(150, 150, 150, 0.2);
        opacity: 0.08;
        z-index: -1;
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
        <col class="col-maintenance-item" style="width:30%;">
        <col class="col-mro-item" style="width:35%;">
        <col class="col-uom" style="width:15%;">
        <col class="col-qty" style="width:15%;">
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
                            <div class="doc-title">Purchase Requisition</div>
                            <div class="doc-subtitle">Equipment Dept.</div>
                        </td>

                        <td class="meta-cell">
                            <div class="docno-label">Document No.</div>
                            <div class="docno">{{ $purchase_requisition->requisition_no }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="doc-info-cell">
                            <table class="doc-info-table">
                                <tr>
                                    <td width="30%">
                                        <table class="info-inner">
                                            <tr>
                                                <td class="label" style="width: 15%">Unit</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $purchase_requisition->unit->vehicle_no ?? '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">Date</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $purchase_requisition->date ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Maintenance No.</td>
                                                <td class="sep">:</td>
                                                <td class="val">
                                                    {{ $purchase_requisition->maintenance->maintenance_no ?? '-' }}</td>
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
            <th class="col-maintenance-item">Maintenance Item</th>
            <th class="col-mro-item">MRO Item</th>
            <th class="col-uom">Uom</th>
            <th class="col-qty">Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($purchase_requisition_detail as $d)
            <tr>
                <td class="p-1 align-middle">
                    {{ $loop->iteration }}
                </td>
                <td class="p-1 align-middle">
                    {{ $d->maintenance_item->name }}
                </td>
                <td class="p-1 align-middle">
                    {{ $d->mro_item->name }}
                </td>
                <td class="p-1 align-middle">
                    {{ $d->uom }}
                </td>
                <td class="p-1 align-middle" style="text-align: right">
                    {{ $d->qty ? Number::format($d->qty, precision: 0) : '' }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <thead>
        <tr>
            <td colspan="5" class="p-1">
                Notes : <br>
                @if ($purchase_requisition->notes != '')
                    {!! $purchase_requisition->notes !!}
                @endif
                <br>
            </td>
        </tr>
    </thead>
    <thead>
        <tr>
            <td colspan="5" class="p-1">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <tr>
                        <td style="border: none; text-align: center">
                            Created By
                            <br>
                        </td>
                        @if ($approval_step)
                            @foreach ($approval_step as $d)
                                <td style="border: none; text-align: center">
                                    {{ $d->action }} By
                                    <br>
                                </td>
                            @endforeach
                        @endif
                    </tr>
                    <tr>
                        <td style="border: none; text-align: center">
                            @if ($purchase_requisition->user->sign_path)
                                <img src="{{ public_path('storage/' . $purchase_requisition->user->sign_path) }}"
                                    alt="Logo" style="max-width:100px;height:auto;margin:0 auto;">
                            @endif
                            <br>
                            {{ $purchase_requisition->user->name }}
                        </td>
                        @if ($approval_step)
                            @foreach ($approval_step as $d)
                                <td style="border: none; text-align: center">
                                    @php
                                        $approval_status = $approval_status
                                            ->where('approval_flow_id', $approval_flow->id)
                                            ->where('approvable_id', $purchase_requisition->id)
                                            ->where('step', $d->order)
                                            ->first();
                                    @endphp
                                    @if ($approval_status->status == 'Open')
                                        <b>Approval Process</b>
                                    @elseif($approval_status->status == 'Rejected')
                                        <b>Rejected</b>
                                    @else
                                        @if ($d->user->sign_path)
                                            <img src="{{ public_path('storage/' . $d->user->sign_path) }}"
                                                alt="Logo" style="max-width:100px;height:auto;margin:0 auto;">
                                        @endif
                                    @endif
                                    <br>
                                    {{ $d->user->name }}
                                </td>
                            @endforeach
                        @endif
                    </tr>
                </table>
            </td>
        </tr>
    </thead>
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
