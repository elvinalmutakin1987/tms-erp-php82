@php
    use Carbon\Carbon;
    use Illuminate\Support\Number;
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    use App\Models\Approval_flow;
    use App\Models\Approval_status;
    use App\Models\Approval_process;
    use App\Models\Approval_step;
    use App\Models\Proforma_invoice;
    use App\Models\Proforma_invoice_detail;
    use App\Models\Contract;
    use App\Models\Contract_rate;
    use App\Models\Contract_fmf;
    use App\Models\Unit_target;
    use App\Models\Maintenance;
    use App\Models\Daily_report;
    use App\Models\Daily_report_detail;
    use App\Models\Unit;

    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
    $endDate = $startDate->copy()->endOfMonth();

    $qrText =
        'PT. Tunas Mitra Sejati' .
        "\n" .
        "\n" .
        'Breakdown Summary ' .
        "\n" .
        'Unit : ' .
        $unit->vehicle_no .
        "\n" .
        'Periode : ' .
        $startDate->format('d F') .
        ' - ' .
        $endDate->format('d F Y');

    $qrImage = QrCode::format('png')->size(150)->margin(1)->generate($qrText);

    $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);

    $layout = 'potrait';
@endphp

<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: "Times New Roman", Times, serif;
        margin: 0;
        padding: 0;
        font-size: 12px;
        color: #000;
    }

    .table-p2h {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin: 0;
        padding: 0;
        table-layout: fixed;
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
        font-size: 16pt;
        padding-top: 0;
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

    .doc-title {
        font-size: 15pt;
        font-weight: 700;
        letter-spacing: .6px;
        line-height: 1.1;
    }

    .doc-subtitle {
        margin-top: 2px;
        font-size: 8.5pt;
        letter-spacing: .05px;
    }

    .avoid-break {
        page-break-inside: avoid;
    }

    img {
        display: block;
    }

    .custom-table-no-body-border tbody td {
        border-top: 0 !important;
        border-bottom: 0 !important;
    }

    .custom-table-no-body-border tbody tr:last-child td {
        border-bottom: 1px solid #000 !important;
    }

    /* Hilangkan garis kanan kolom 1, 2, dan 3 di body, kecuali baris pertama */
    .custom-table-no-body-border tbody td:nth-child(1),
    .custom-table-no-body-border tbody td:nth-child(2),
    .custom-table-no-body-border tbody td:nth-child(3),
    {
    border-right: 0 !important;
    }

    .custom-table-no-body-border tbody td:nth-child(4),
    {
    border-left: 0 !important;
    }

    /* Hilangkan garis kanan header kolom pertama dan kedua */
    .custom-table-no-body-border thead th:nth-child(1),
    .custom-table-no-body-border thead th:nth-child(2) {
        border-right: 0 !important;
    }

    @media print {
        @page {
            size: A4 {{ $layout }} !important;
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
                <div class="doc-title" style="padding-top: 15px">
                    BREAKDOWN SUMMARY
                </div>
            </th>
        </tr>
    </thead>
</table>

@php
    $currentMonth = Carbon::create($year, $month, 1)->startOfMonth();
    $daysInMonth = $currentMonth->daysInMonth;

    // Bagi tanggal setiap 10 hari
    $dateGroups = collect(range(1, $daysInMonth))->chunk(10);

    /*
    |--------------------------------------------------------------------------
    | Hitung jam Maintenance per tanggal
    |--------------------------------------------------------------------------
    */

    $hoursByDate = [];

    // Awal bulan
    $monthStart = $currentMonth->copy()->startOfDay();

    // Akhir bulan exclusive
    // Contoh Agustus 2026:
    // 2026-09-01 00:00:00
    $monthEnd = $currentMonth->copy()->addMonth()->startOfDay();

    /*
     * Ambil semua maintenance yang bersinggungan
     * dengan bulan yang sedang ditampilkan.
     *
     * Contoh:
     * start  = 31 Juli
     * finish = 2 Agustus
     *
     * tetap dihitung untuk tanggal 1-2 Agustus.
     */
    $maintenances = Maintenance::where('unit_id', $unit_id)
        ->whereNotNull('start')
        ->whereNotNull('finish')
        ->where('start', '<', $monthEnd)
        ->where('finish', '>', $monthStart)
        ->get();

    foreach ($maintenances as $maintenance) {
        $maintenanceStart = Carbon::parse($maintenance->start);
        $maintenanceFinish = Carbon::parse($maintenance->finish);

        // Abaikan data tidak valid
        if ($maintenanceFinish->lte($maintenanceStart)) {
            continue;
        }

        /*
         * Potong interval hanya pada bulan
         * yang sedang ditampilkan.
         */
        $rangeStart = $maintenanceStart->gt($monthStart) ? $maintenanceStart->copy() : $monthStart->copy();

        $rangeFinish = $maintenanceFinish->lt($monthEnd) ? $maintenanceFinish->copy() : $monthEnd->copy();

        /*
         * Mulai dari tanggal pertama maintenance
         */
        $cursor = $rangeStart->copy()->startOfDay();

        while ($cursor->lt($rangeFinish)) {
            // Awal tanggal
            $dayStart = $cursor->copy();

            // Awal tanggal berikutnya
            $dayEnd = $dayStart->copy()->addDay();

            /*
             * Start untuk tanggal ini
             */
            $partStart = $rangeStart->gt($dayStart) ? $rangeStart->copy() : $dayStart->copy();

            /*
             * Finish untuk tanggal ini
             */
            $partFinish = $rangeFinish->lt($dayEnd) ? $rangeFinish->copy() : $dayEnd->copy();

            if ($partStart->lt($partFinish)) {
                /*
                 * Hitung durasi dalam detik
                 */
                $seconds = $partFinish->getTimestamp() - $partStart->getTimestamp();

                /*
                 * Convert ke jam decimal
                 *
                 * contoh:
                 * 2 jam 30 menit = 2.5
                 */
                $hours = $seconds / 3600;

                /*
                 * Key tanggal
                 */
                $dateKey = $dayStart->format('Y-m-d');

                /*
                 * Kalau dalam tanggal yang sama
                 * terdapat beberapa maintenance,
                 * jumlahkan terlebih dahulu.
                 */
                $hoursByDate[$dateKey] = ($hoursByDate[$dateKey] ?? 0) + $hours;
            }

            // Lanjut ke tanggal berikutnya
            $cursor = $dayEnd;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Grand Total
    |--------------------------------------------------------------------------
    */

    $grandTotal = 0;
@endphp


<style>
    .operation-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 11px;
    }

    .operation-table th,
    .operation-table td {
        border: 1px solid #000;
        text-align: center;
        padding: 2px 3px;
        line-height: 1.15;
    }

    .operation-table .operation-title {
        font-weight: 600;
        height: 18px;
        padding: 2px;
    }

    .operation-table .date-cell {
        width: 7.5%;
        height: 18px;
        font-weight: 600;
        padding: 2px;
    }

    .operation-table .hour-cell {
        height: 18px;
        padding: 2px;
    }

    .operation-table .breakdown-column {
        width: 85px;
        font-weight: 600;
        padding: 3px;
        line-height: 1.1;
    }

    .operation-table .empty-cell {
        background-color: #e9ecef;
    }

    .operation-table .total-label {
        text-align: right;
        font-weight: 700;
        padding: 3px 6px;
    }

    .operation-table .total-value {
        font-weight: 700;
        padding: 2px;
    }
</style>

<table class="doc-header-vendor" style="padding-bottom: 10px">
    <tr>
        <td style="width: 50%">
            <table class="doc-header-detail">
                <tr>
                    <td style="width: 8%">Kode Unit</td>
                    <td style="width: 5%; text-align: center">:</td>
                    <td>{{ $unit->vehicle_no ?? '' }}</td>
                </tr>
                <tr>
                    <td style="width: 8%">No. Polisi</td>
                    <td style="width: 5%; text-align: center">:</td>
                    <td>{{ $unit->registration_no ?? '' }}</td>
                </tr>
                <tr>
                    <td style="width: 8%">Type</td>
                    <td style="width: 5%; text-align: center">:</td>
                    <td>{{ $unit->unit_brand->name ?? '' }} {{ $unit->unit_model->desc ?? '' }}</td>
                </tr>
                <tr>
                    <td style="width: 8%">Periode</td>
                    <td style="width: 5%; text-align: center">:</td>
                    <td><b>{{ $startDate->format('d F') }} - {{ $endDate->format('d F Y') }}</b></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="operation-table">
    @foreach ($dateGroups as $group)
        @php
            /*
             * Total untuk setiap kelompok 10 tanggal
             */
            $groupTotal = 0;
        @endphp


        {{-- Header --}}
        <tr>

            <td colspan="10" class="operation-title">
                Tanggal Operasi
            </td>

            <td rowspan="2" class="breakdown-column">
                Kalkulasi<br>
                Breakdown<br>
                Jam
            </td>

        </tr>


        {{-- Nomor tanggal --}}
        <tr>

            @for ($column = 0; $column < 10; $column++)
                @php
                    $day = $group->values()->get($column);
                @endphp


                @if ($day)
                    <td class="date-cell">
                        {{ $day }}
                    </td>
                @else
                    <td class="date-cell empty-cell">
                        &nbsp;
                    </td>
                @endif
            @endfor

        </tr>


        {{-- Jam Breakdown per tanggal --}}
        <tr>

            @for ($column = 0; $column < 10; $column++)
                @php
                    $day = $group->values()->get($column);
                @endphp


                @if ($day)
                    @php
                        /*
                         * Buat tanggal YYYY-MM-DD
                         */
                        $dateKey = Carbon::create($year, $month, $day)->format('Y-m-d');

                        /*
                         * Ambil total jam exact
                         * pada tanggal tersebut.
                         */
                        $hours = $hoursByDate[$dateKey] ?? 0;

                        /*
                         * Pembulatan seperti Excel:
                         *
                         * =ROUND(hours, 0)
                         *
                         * Contoh:
                         * 2.49 => 2
                         * 2.50 => 3
                         * 2.51 => 3
                         */
                        $hoursRounded = (int) round($hours, 0, PHP_ROUND_HALF_UP);

                        /*
                         * Kalkulasi breakdown per 10 hari
                         * menggunakan angka yang sudah
                         * dibulatkan per tanggal.
                         */
                        $groupTotal += $hoursRounded;
                    @endphp


                    <td class="hour-cell">

                        @if ($hoursRounded > 0)
                            {{ $hoursRounded }}
                        @endif

                    </td>
                @else
                    <td class="hour-cell empty-cell">
                        &nbsp;
                    </td>
                @endif
            @endfor


            {{-- Total per 10 tanggal --}}
            <td class="total-value">
                {{ $groupTotal }}
            </td>

        </tr>


        @php
            /*
             * Tambahkan total group
             * ke grand total
             */
            $grandTotal += $groupTotal;
        @endphp
    @endforeach


    {{-- Grand Total --}}
    <tr>

        <td colspan="10" class="total-label">
            TOTAL
        </td>

        <td class="total-value">
            {{ $grandTotal }}
        </td>

    </tr>

</table>

<table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="avoid-break">
    <tr>
        <td style="border: none;" colspan="4">
            <div>
                Sangatta,
                {{ \Carbon\Carbon::parse(date('Y-m-d'))->locale('id')->translatedFormat('d F Y') }}
            </div>
        </td>
    </tr>

    <tr>
        <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
            <div style="height: 30px;">
                Dibuat Oleh,
            </div>

            <div style="height: 50px;"></div>

            <div style="min-height: 35px;">
                PT. Tunas Mitra Sejati
            </div>
        </td>
        <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
            <div style="height: 30px;">
                Diperiksa Oleh,
            </div>

            <div style="height: 50px;"></div>

            <div style="min-height: 35px;">
                User
            </div>
        </td>
        <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
            <div style="height: 30px;">
                Disetujui Oleh,
            </div>

            <div style="height: 50px;"></div>

            <div style="min-height: 35px;">
                Mews
            </div>
        </td>
    </tr>
</table>

@php
    $main = Maintenance::where('unit_id', $unit_id)
        ->whereNotNull('start')
        ->whereNotNull('finish')
        ->whereYear('start', $year)
        ->whereMonth('start', $month)
        ->get();
@endphp
<b>Notes :</b>
<table style="width: 100%" class="mb-4">
    @foreach ($main as $d)
        <tr>
            <td width="12px">{{ $loop->iteration }} .</td>
            <td width="100px">{{ Carbon::parse($d->start)->format('d F Y') }} ,</td>
            <td>{!! $d->remarks !!}</td>
        </tr>
    @endforeach
</table>
