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
        font-family: "Times New Roman", Times, serif;
        margin: 0;
        padding: 0;
        /* font-size: 9.5pt; */
        font-size: 12pt;
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
        border: none !important;
    }

    .doc-header-table td {
        border: 0px solid #000;
        padding: 8px 10px;
        vertical-align: middle;
        line-height: 1.2;
    }

    .doc-header-vendor {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: fixed;
        border: none !important;
    }

    .doc-header-vendor-td {
        border: none !important;
        vertical-align: middle;
        border-spacing: 0;
        font-weight: 700;
        font-size: 12pt;
        padding-top: 0
    }

    .doc-header-detail {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: fixed;
        border: none !important;
    }

    .doc-header-detail td {
        border: none !important;
        padding: 0px;
        line-height: 1.2;
    }


    .logo-cell {
        width: 20%;
        text-align: center;
    }

    .title-cell {
        text-align: left;
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

    .doc-time {
        font-size: 11pt;
        font-weight: 30;
        letter-spacing: .6px;
        line-height: 1.1;
    }

    .doc-subtitle {
        margin-top: 2px;
        font-size: 8.5pt;
        letter-spacing: .05px;
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

    .col-price {
        width: 15%;
    }

    .col-amount {
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
    <thead>
        <tr>
            <th class="doc-header-wrapper">
                <table class="doc-header-table">
                    <tr>
                        <td class="logo-cell">
                            <img src="{{ public_path('assets/images/tms_logo.png') }}" alt="Logo"
                                style="max-width:120px;height:auto;margin:0 auto;">
                        </td>

                        <td class="title-cell">
                            <div class="doc-title">PT. TUNAS MITRA SEJATI</div>
                            <div class="doc-subtitle">Perum GPL Munthe Hatari A4-05</div>
                            <div class="doc-subtitle">Sangatta - Kutai Timur</div>
                            <div class="doc-subtitle">Telp. (0549)-2129100 Cp. 082370205584</div>
                        </td>
                    </tr>
                </table>
            </th>
        </tr>
        <tr>
            <th class="doc-header-wrapper">
                <div class="doc-title" style="padding-top: 15px">PURCHASE ORDER</div>
            </th>
        </tr>
    </thead>
</table>
<table class="table-p2h" style="border: 1px double #000; border-collapse: separate; border-spacing: 1; width: 100%;">
    <tbody>
        <tr>
            <td style="padding: 8px;">
                <table class="doc-header-vendor" style="padding-bottom: 10px">
                    <tr>
                        <td style="width: 50%" class="doc-header-vendor-td"> Vendor</td>
                        <td style="width: 50%" class="doc-header-vendor-td"></td>
                    </tr>
                    <tr>
                        <td style="width: 50%">
                            <table class="doc-header-detail">
                                <tr>
                                    <td style="width: 30%">Name</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $purchase_order->client_vendor->name }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Address</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $purchase_order->client_vendor->address }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Phone</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $purchase_order->client_vendor->phone }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Email</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $purchase_order->client_vendor->email }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 50%">
                            <table class="doc-header-detail">
                                <tr>
                                    <td style="width: 30%">PO. No</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $purchase_order->order_no }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Date</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ \Carbon\Carbon::parse($purchase_order->date)->locale('id')->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Reff</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $purchase_order->purchase_requisition->unit->vehicle_no ? 'EST-' . $purchase_order->purchase_requisition->unit->vehicle_no : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Currency</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>IDR</td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Fleet No.</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $purchase_order->purchase_requisition->unit->fleet_no ?? '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Job</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $purchase_order->job ?? '' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <table class="table-p2h">
                    <thead>
                        <tr>
                            <th
                                style="width:5%; text-align: center; border: 1px solid #000; background-color: #d9ecff;">
                                Item
                            </th>
                            <th
                                style="width: 10%; text-align: center; border: 1px solid #000; background-color: #d9ecff;">
                                Qty
                            </th>
                            <th
                                style="width: 10%; text-align: center; border: 1px solid #000; background-color: #d9ecff;">
                                Unit
                            </th>
                            <th
                                style="width: 30%; text-align: center; border: 1px solid #000; background-color: #d9ecff;">
                                Description
                            </th>
                            <th
                                style="width: 15%; text-align: center; border: 1px solid #000; background-color: #d9ecff;">
                                Price IDR
                            </th>
                            <th
                                style="width: 15%; text-align: center; border: 1px solid #000; background-color: #d9ecff;">
                                Discount (%)
                            </th>
                            <th
                                style="width: 15%; text-align: center; border: 1px solid #000; background-color: #d9ecff;">
                                Total IDR
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase_order_detail as $d)
                            <tr>
                                <td style="width: 5%; text-align: center; border: 1px solid #000;">
                                    {{ $loop->iteration }}</td>
                                <td style="width: 10%; text-align: center; border: 1px solid #000;">
                                    {{ $d->qty ? Number::format($d->qty, precision: 0) : '' }}</td>
                                <td style="width: 10%; text-align: center; border: 1px solid #000;">
                                    {{ $d->uom }}</td>
                                <td style="width: 30%; border: 1px solid #000;">{{ $d->description }}
                                </td>
                                <td style="width: 15%; text-align: right; border: 1px solid #000;">
                                    {{ $d->price ? Number::format($d->price, precision: 0) : '' }}</td>
                                <td style="width: 15%; text-align: right; border: 1px solid #000;">
                                    {{ $d->discount_item ? Number::format($d->discount_item, precision: 0) : '' }}
                                </td>
                                <td style="width: 15%; text-align: right; border: 1px solid #000;">
                                    {{ $d->amount ? Number::format($d->amount, precision: 0) : '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" rowspan='3' style="text-align: left; border: 1px solid #000;">
                                {!! nl2br(e($purchase_order->notes)) !!}
                            </td>
                            <td style="text-align: left; border: 1px solid #000;">
                                <b>Sub Total</b>
                            </td>
                            <td style="text-align: right; border: 1px solid #000;">
                                {{ $purchase_order->total ? Number::format($purchase_order->total, precision: 0) : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left; border: 1px solid #000;">
                                <b>VAT ({{ $system_setting['tax'] ?? 10 }}%)</b>
                            </td>
                            <td style="text-align: right; border: 1px solid #000;">
                                {{ $purchase_order->tax ? Number::format($purchase_order->tax, precision: 0) : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left; border: 1px solid #000;">
                                <b>Grand Total</b>
                            </td>
                            <td style="text-align: right; border: 1px solid #000;">
                                {{ $purchase_order->grand_total ? Number::format($purchase_order->grand_total, precision: 0) : '' }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<table>
    <tfoot>
        <tr>
            <td style="border: none;">
                <div>Sangatta,
                    {{ \Carbon\Carbon::parse(date('Y-m-d'))->locale('id')->translatedFormat('d F Y') }}
                </div>
            </td>
        </tr>
        <tr>
            <td style="border: none; text-align: center">
                Approved By,
                <br>
                <br>
                <br>
                <br>
                {{ $purchase_order->client_vendor->name }}
            </td>
            <td style="border: none;text-align: center">
                Prepared By,
                <br>
                <br>
                <br>
                <br>
                {{ $purchase_order->user->username }}
            </td>
            @if ($approval_step)
                @foreach ($approval_step as $d)
                    <td style="border: none; text-align: center">
                        {{ $d->action }} By
                        <br>
                        <br>
                        <br>
                        <br>
                        {{ $approval_step->user->username }}
                    </td>
                @endforeach
            @endif
        </tr>
    </tfoot>
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
