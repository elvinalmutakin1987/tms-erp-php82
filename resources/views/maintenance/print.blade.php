@php
    use Illuminate\Support\Number;
    use Spatie\Permission\Models\Permission;

    $user = Auth::user();
    $guardName = config('auth.defaults.guard', 'web');

    $maintenanceCostPermissionExists = Permission::query()
        ->where('name', 'maintenance.cost')
        ->where('guard_name', $guardName)
        ->exists();

    $canViewMaintenanceCost =
        $user->hasRole('superadmin') ||
        ($maintenanceCostPermissionExists && $user->hasPermissionTo('maintenance.cost'));

    $formatWorkDuration = function ($duration): string {
        if ($duration === null || $duration === '') {
            return '00:00';
        }

        $duration = trim((string) $duration);

        /*
         * Kalau work_duration tersimpan sebagai total detik.
         * Contoh: 108000 => 30:00
         */
        if (is_numeric($duration)) {
            $totalSeconds = (int) $duration;

            $hours = intdiv($totalSeconds, 3600);
            $minutes = intdiv($totalSeconds % 3600, 60);

            return sprintf('%02d:%02d', $hours, $minutes);
        }

        /*
         * Kalau work_duration tersimpan sebagai akumulasi waktu.
         * Contoh:
         * 05:30:00 => 05:30
         * 30:00:00 => 30:00
         * 125:45:00 => 125:45
         *
         * Tidak pakai Carbon, karena ini bukan jam/tanggal.
         */
        $parts = explode(':', $duration);

        $hours = (int) ($parts[0] ?? 0);
        $minutes = (int) ($parts[1] ?? 0);
        $seconds = (int) ($parts[2] ?? 0);

        if ($seconds >= 60) {
            $minutes += intdiv($seconds, 60);
        }

        if ($minutes >= 60) {
            $hours += intdiv($minutes, 60);
            $minutes = $minutes % 60;
        }

        return sprintf('%02d:%02d', $hours, $minutes);
    };
@endphp

<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: "DejaVu Sans", "DejaVu Sans Mono", "DejaVu", "Helvetica", "Arial", sans-serif;
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
        font-size: 9.2pt;
        font-weight: 700;
        line-height: 1.1;
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

    .col-no {
        width: 5%;
        text-align: center;
        vertical-align: middle;
    }

    .col-action {
        width: 17%;
        text-align: center;
        vertical-align: middle;
    }

    .col-item {
        width: 58%;
    }

    .col-cost {
        width: 20%;
        text-align: right;
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

<table class="table-p2h">
    <colgroup>
        <col class="col-no" style="width: 5%;">
        <col class="col-action" style="width: 17%;">
        <col class="col-item" style="{{ $canViewMaintenanceCost ? 'width: 58%;' : 'width: 78%;' }}">

        @if ($canViewMaintenanceCost)
            <col class="col-cost" style="width: 20%;">
        @endif
    </colgroup>

    <thead>
        <tr>
            <th colspan="{{ $canViewMaintenanceCost ? 4 : 3 }}" class="doc-header-wrapper">
                <table class="doc-header-table">
                    <tr>
                        <td class="logo-cell">
                            <img src="{{ public_path('assets/images/tms_logo.png') }}" alt="Logo"
                                style="max-width: 95px; height: auto; margin: 0 auto;">
                        </td>

                        <td class="title-cell">
                            <div class="doc-title">MAINTENANCE</div>
                            <div class="doc-subtitle">Vehicle Maintenance</div>
                        </td>

                        <td class="meta-cell">
                            <div class="docno-label">Document No.</div>
                            <div class="docno">{{ $maintenance->maintenance_no }}</div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3" class="doc-info-cell">
                            <table class="doc-info-table">
                                <tr>
                                    <td width="30%">
                                        <table class="info-inner">
                                            <tr>
                                                <td class="label">Unit</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $maintenance->unit->vehicle_no ?? '-' }}</td>
                                            </tr>

                                            <tr>
                                                <td class="label">Date</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $maintenance->date ?? '-' }}</td>
                                            </tr>

                                            <tr>
                                                <td class="label">Vendor</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $maintenance->client_vendor->name ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td width="30%">
                                        <table class="info-inner">
                                            <tr>
                                                <td class="label">Mechanic</td>
                                                <td class="sep">:</td>
                                                <td class="val">{{ $maintenance->mechanic ?? '-' }}</td>
                                            </tr>

                                            <tr>
                                                <td class="label">Hour Meter</td>
                                                <td class="sep">:</td>
                                                <td class="val">
                                                    {{ Number::format($maintenance->hour_meter ?? 0, precision: 0) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="label">KM/HM</td>
                                                <td class="sep">:</td>
                                                <td class="val">
                                                    {{ Number::format($maintenance->km_hm ?? 0, precision: 0) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td width="30%">
                                        <table class="info-inner">
                                            <tr>
                                                <td class="label">Start</td>
                                                <td class="sep">:</td>
                                                <td class="val">
                                                    {{ $maintenance->start ? \Carbon\Carbon::parse($maintenance->start)->format('H:i') : '-' }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="label">Finish</td>
                                                <td class="sep">:</td>
                                                <td class="val">
                                                    {{ $maintenance->finish ? \Carbon\Carbon::parse($maintenance->finish)->format('H:i') : '-' }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="label">Work Duration</td>
                                                <td class="sep">:</td>
                                                <td class="val">
                                                    {{ $formatWorkDuration($maintenance->work_duration ?? null) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3">
                                        Remarks : <br>
                                        {!! $maintenance->remarks !!}
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
            <th class="col-action">Action</th>
            <th class="col-item">Item</th>

            @if ($canViewMaintenanceCost)
                <th class="col-cost">Cost</th>
            @endif
        </tr>
    </thead>

    <tbody>
        @foreach ($maintenance_detail as $d)
            <tr>
                <td class="p-1 align-middle" style="text-align: center;">
                    {{ $loop->iteration }}
                </td>

                <td class="p-1 align-middle">
                    {{ $d->action }}
                </td>

                <td class="p-1 align-middle">
                    {{ $d->maintenance_item->name ?? '-' }}
                </td>

                @if ($canViewMaintenanceCost)
                    <td class="p-1 align-middle" style="text-align: right;">
                        {{ Number::format($d->cost ?? 0, precision: 0) }}
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>

    @if ($canViewMaintenanceCost)
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: right;">
                    Total
                </th>

                <th style="text-align: right;">
                    {{ Number::format($maintenance->cost_total ?? 0, precision: 0) }}
                </th>
            </tr>
        </tfoot>
    @endif
</table>

{{-- Page number footer (DomPDF) --}}
<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont("Helvetica", "normal");
        $size = 8;

        $text = "Halaman {PAGE_NUM} / {PAGE_COUNT}";

        $x = 430;
        $y = 820;

        $pdf->page_text($x, $y, $text, $font, $size, array(0, 0, 0));
    }
</script>
