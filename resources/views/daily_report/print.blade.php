@php
    use Carbon\Carbon;
    use Illuminate\Support\Number;

    $formatTime = function ($time) {
        if ($time === null || $time === '') {
            return '';
        }

        try {
            return Carbon::parse($time)->format('H:i');
        } catch (\Throwable $e) {
            return '';
        }
    };

    $formatNumber = function ($value, $precision = 0) {
        if ($value === null || $value === '') {
            return '';
        }

        return Number::format($value, precision: $precision);
    };

    $durationToMinutes = function ($time) {
        if ($time === null || $time === '') {
            return 0;
        }

        $parts = explode(':', (string) $time);

        $hour = isset($parts[0]) ? (int) $parts[0] : 0;
        $minute = isset($parts[1]) ? (int) $parts[1] : 0;

        return ($hour * 60) + $minute;
    };

    $durationTotal = function ($time1, $time2) use ($durationToMinutes) {
        if (($time1 === null || $time1 === '') && ($time2 === null || $time2 === '')) {
            return '';
        }

        $totalMinutes = $durationToMinutes($time1) + $durationToMinutes($time2);

        return sprintf(
            '%02d:%02d',
            intdiv($totalMinutes, 60),
            $totalMinutes % 60,
        );
    };

    $logoPath = public_path('assets/images/tms_logo.png');
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

    .table-p2h {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
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

    .checklist-head th {
        background: #111;
        color: #fff;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .3px;
        border-top: 0 !important;
    }

    .group-row td {
        background: #e9ecef;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .2px;
        padding: 7px 8px;
    }

    .item-row td {
        background: #fff;
    }

    .item-row.zebra td {
        background: #fafafa;
    }

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

    .checklist-head th.col-broken {
        font-size: 8pt;
        line-height: 1.05;
        padding-left: 3px;
        padding-right: 3px;
        white-space: normal;
        word-break: break-word;
    }

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

    .status.ok::before {
        content: "✔";
        vertical-align: middle;
    }

    .status.ng::before {
        content: "✗";
        vertical-align: middle;
    }

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

    .avoid-break {
        page-break-inside: avoid;
    }

    img {
        display: block;
    }

    @media print {
        @page {
            size: A4 !important;
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
                            @if (file_exists($logoPath))
                                <img src="{{ $logoPath }}" alt="Logo" style="max-width:95px;height:auto;margin:0 auto;">
                            @else
                                <div style="font-weight:700;font-size:12pt;">TMS</div>
                            @endif
                        </td>

                        <td class="title-cell">
                            <div class="doc-title">DAILY REPORT</div>
                            <div class="doc-subtitle">Vehicle / LCT</div>
                        </td>

                        <td class="meta-cell">
                            <div class="docno-label">Document No.</div>
                            <div class="docno">{{ $daily_report->report_no ?? '-' }}</div>
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
                                                <td class="val">{{ data_get($daily_report, 'unit.vehicle_no', '-') }}</td>
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
                <td class="p-1 align-middle">{{ data_get($daily_report, 'trip_1_location.name', '') }}</td>
                <td class="p-1 align-middle" style="width: 20%">Location</td>
                <td class="p-1 align-middle">{{ data_get($daily_report, 'trip_1_arr_location.name', '') }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Loading At</td>
                <td class="p-1 align-middle">{{ $formatTime($daily_report->trip_1_loading_at ?? null) }}</td>
                <td class="p-1 align-middle" style="width: 20%">Arrived At</td>
                <td class="p-1 align-middle">{{ $formatTime($daily_report->trip_1_arrived_at ?? null) }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Complete Loading At</td>
                <td class="p-1 align-middle">{{ $formatTime($daily_report->trip_1_complete_loading_at ?? null) }}</td>
                <td class="p-1 align-middle" style="width: 20%">Berthing At</td>
                <td class="p-1 align-middle">
                    <b>{{ $formatTime($daily_report->trip_1_berthing_at ?? null) }}</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Departed At</td>
                <td class="p-1 align-middle">
                    <b>{{ $formatTime($daily_report->trip_1_departed_at ?? null) }}</b>
                </td>
                <td class="p-1 align-middle" style="width: 20%" colspan="2"></td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%"><b>Duration</b></td>
                <td class="p-1 align-middle" colspan="3">
                    <b>{{ $formatTime($daily_report->duration_trip_1 ?? null) }}</b>
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
                <td class="p-1 align-middle">{{ data_get($daily_report, 'trip_2_location.name', '') }}</td>
                <td class="p-1 align-middle" style="width: 20%">Location</td>
                <td class="p-1 align-middle">{{ data_get($daily_report, 'trip_2_arr_location.name', '') }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Loading At</td>
                <td class="p-1 align-middle">{{ $formatTime($daily_report->trip_2_loading_at ?? null) }}</td>
                <td class="p-1 align-middle" style="width: 20%">Arrived At</td>
                <td class="p-1 align-middle">{{ $formatTime($daily_report->trip_2_arrived_at ?? null) }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Complete Loading At</td>
                <td class="p-1 align-middle">{{ $formatTime($daily_report->trip_2_complete_loading_at ?? null) }}</td>
                <td class="p-1 align-middle" style="width: 20%">Berthing At</td>
                <td class="p-1 align-middle">
                    <b>{{ $formatTime($daily_report->trip_2_berthing_at ?? null) }}</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Departed At</td>
                <td class="p-1 align-middle">
                    <b>{{ $formatTime($daily_report->trip_2_departed_at ?? null) }}</b>
                </td>
                <td class="p-1 align-middle" style="width: 20%" colspan="2"></td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%"><b>Duration</b></td>
                <td class="p-1 align-middle" colspan="3">
                    <b>{{ $formatTime($daily_report->duration_trip_2 ?? null) }}</b>
                </td>
            </tr>
            <tr style="background-color: #FAF6F5">
                <td class="p-1 align-middle" style="width: 20%"><b>Duration Total</b></td>
                <td class="p-1 align-middle" colspan="3">
                    <b>{{ $durationTotal($daily_report->duration_trip_1 ?? null, $daily_report->duration_trip_2 ?? null) }}</b>
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
                    {!! $daily_report->remarks ?? '' !!}
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
                <td class="p-1 align-middle" colspan="3">{{ $daily_report->refule_type ?? '' }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">Liter</td>
                <td class="p-1 align-middle" colspan="3">
                    {{ $formatNumber($daily_report->refule_liter ?? null, 0) }}
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" style="width: 20%">KM</td>
                <td class="p-1 align-middle" colspan="3">
                    {{ $formatNumber($daily_report->refule_km ?? null, 0) }}
                </td>
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
            @forelse ($daily_report_detail as $d)
                <tr>
                    <td class="p-1 align-middle">
                        {{ $loop->iteration }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ data_get($d, 'unit.vehicle_no', '') }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $d->item ?? '' }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $d->uom_1 ?? '' }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $formatNumber($d->value_1 ?? null, 2) }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $d->uom_2 ?? '' }}
                    </td>
                    <td class="p-1 align-middle">
                        {{ $formatNumber($d->value_2 ?? null, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="p-1 align-middle" colspan="7" style="text-align:center;">
                        No detail data
                    </td>
                </tr>
            @endforelse
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
                    {{ $formatNumber($daily_report->km_start ?? null, 0) }}
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">2</td>
                <td class="p-1 align-middle">Finish</td>
                <td class="p-1 align-middle">
                    {{ $formatNumber($daily_report->km_finish ?? null, 0) }}
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">3</td>
                <td class="p-1 align-middle">Total</td>
                <td class="p-1 align-middle">
                    {{ $formatNumber($daily_report->km_total ?? null, 0) }}
                </td>
            </tr>

            <tr style="background-color: #D9D2D0">
                <td class="p-1 align-middle" colspan="3">
                    <b>Person</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">4</td>
                <td class="p-1 align-middle">Operator</td>
                <td class="p-1 align-middle">{{ $daily_report->operator ?? '' }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle">5</td>
                <td class="p-1 align-middle">Helper</td>
                <td class="p-1 align-middle">{{ $daily_report->helper ?? '' }}</td>
            </tr>
            <tr>
                <td class="p-1 align-middle">6</td>
                <td class="p-1 align-middle">Total</td>
                <td class="p-1 align-middle">
                    {{ $formatNumber($daily_report->load ?? null, 0) }}
                </td>
            </tr>

            <tr style="background-color: #D9D2D0">
                <td class="p-1 align-middle" colspan="3">
                    <b>Refule</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">7</td>
                <td class="p-1 align-middle">From</td>
                <td class="p-1 align-middle">
                    {{ ($daily_report->refule_liter || $daily_report->refule_km) ? ($daily_report->refule_type ?? '') : '' }}
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">8</td>
                <td class="p-1 align-middle">Liter</td>
                <td class="p-1 align-middle">
                    {{ $formatNumber($daily_report->refule_liter ?? null, 0) }}
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle">9</td>
                <td class="p-1 align-middle">KM</td>
                <td class="p-1 align-middle">
                    {{ $formatNumber($daily_report->refule_km ?? null, 0) }}
                </td>
            </tr>

            <tr style="background-color: #D9D2D0">
                <td class="p-1 align-middle" colspan="3">
                    <b>Remarks</b>
                </td>
            </tr>
            <tr>
                <td class="p-1 align-middle" colspan="3">
                    {!! $daily_report->remarks ?? '' !!}
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

        $text = "Halaman {PAGE_NUM} / {PAGE_COUNT}";

        $x = 430;
        $y = 820;

        $pdf->page_text($x, $y, $text, $font, $size, array(0,0,0));
    }
</script>