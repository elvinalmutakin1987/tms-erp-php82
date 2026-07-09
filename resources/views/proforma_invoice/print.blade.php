@php
    use Carbon\Carbon;
    use Illuminate\Support\Number;
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    use App\Models\Approval_flow;
    use App\Models\Approval_status;
    use App\Models\Approval_process;
    use App\Models\Approval_step;
    use App\Models\Maintenance;

    $qrDate = $proforma_invoice->date ? Carbon::parse($proforma_invoice->date)->format('d-m-Y') : '-';

    $qrText =
        'PT. Tunas Mitra Sejati' .
        "\n" .
        "\n" .
        'Nomor Proforma Invoice : ' .
        $proforma_invoice->proforma_no .
        "\n" .
        'Tanggal : ' .
        $qrDate .
        "\n" .
        'Client : ' .
        optional($proforma_invoice->client_vendor)->name .
        "\n" .
        'Total : ' .
        Number::format($proforma_invoice->total ?? 0, 0) .
        "\n" .
        'Telah disetujui secara digital.';

    $qrImage = QrCode::format('png')->size(150)->margin(1)->generate($qrText);

    $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);
@endphp

<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: "Times New Roman", Times, serif;
        margin: 0;
        padding: 0;
        font-size: 12pt;
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
        font-size: 12pt;
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
                    PROFORMA INVOICE
                </div>
            </th>
        </tr>
    </thead>
</table>

<table class="table-p2h" style="border: 1px double #000; border-collapse: separate; border-spacing: 1px; width: 100%;">
    <tbody>
        <tr>
            <td style="padding: 8px;">
                <table class="doc-header-vendor" style="padding-bottom: 10px">
                    <tr>
                        <td style="width: 50%" class="doc-header-vendor-td">
                            Client
                        </td>
                        <td style="width: 50%" class="doc-header-vendor-td"></td>
                    </tr>
                    <tr>
                        <td style="width: 50%">
                            <table class="doc-header-detail">
                                <tr>
                                    <td style="width: 30%">Name</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $proforma_invoice->client_vendor->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Address</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $proforma_invoice->client_vendor->address ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Phone</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $proforma_invoice->client_vendor->phone ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Email</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>{{ $proforma_invoice->client_vendor->email ?? '' }}</td>
                                </tr>
                            </table>
                        </td>

                        <td style="width: 50%">
                            <table class="doc-header-detail">
                                <tr>
                                    <td style="width: 30%">PI. No</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td><b>{{ $proforma_invoice->proforma_no }}</b></td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Date</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($proforma_invoice->date)->locale('id')->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Reff</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">Currency</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td>IDR</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                @php
                    $unit_id = $proforma_invoice->unit_id;
                    $price = $proforma_invoice->unit_target->price;
                    $target = $proforma_invoice->unit_target->target;
                @endphp
                @if ($contract->service->type == 'Unit Rental')
                    @php
                        $excelRound = function ($value, int $precision = 2) {
                            return round((float) $value, $precision, PHP_ROUND_HALF_UP);
                        };
                        $year = (int) $year;
                        $month = (int) $month;
                        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                        $endDate = $startDate->copy()->endOfMonth();
                        $hariKerja = $startDate->daysInMonth;
                        $totalJamKerja = $hariKerja * 24;
                        $totalBreakdownSeconds = Maintenance::whereYear('date', $year)
                            ->whereMonth('date', $month)
                            ->where('unit_id', $unit_id)
                            ->where('status', '!=', 'Draft')
                            ->selectRaw('COALESCE(SUM(TIME_TO_SEC(work_duration)), 0) as total_seconds')
                            ->value('total_seconds');
                        $total_breakdown = $excelRound($totalBreakdownSeconds / 3600, 2);
                        $price = $excelRound($price ?? 0, 2);
                        $target = $excelRound($target ?? 0, 2);
                        if ($totalJamKerja > 0) {
                            $pa = 100 - ($total_breakdown / $totalJamKerja) * 100;
                        } else {
                            $pa = 0;
                        }
                        $pa = max(0, min(100, $pa));
                        $pa = $excelRound($pa, 2);
                        $penalty = $pa >= $target ? 0 : $excelRound(((100 - $pa) / 100) * $price, 2);
                        $total_payment = $price - $penalty;
                        $total_payment = max(0, min($price, $total_payment));
                        $total_payment = $excelRound($total_payment, 2);
                    @endphp

                    <table class="custom-table-no-body-border"
                        style="width: 100%; border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 30%">Order Number</th>
                                <th scope="col" style="width: 50%">Description</th>
                                <th scope="col" style="width: 20%" colspan="2">Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td class="p-1">
                                    <b>{{ $contract->contract_no }}</b>
                                </td>

                                <td class="p-1">
                                    {{ $unit_target->unit->description }}
                                    (<b>{{ $unit_target->unit->vehicle_no }}</b>)
                                </td>

                                <td class="p-1" width="10px"></td>
                                <td class="p-1"></td>
                            </tr>

                            <tr>
                                <td class="p-1">
                                    (Rate IDR {{ Number::format($price, precision: 0) }})
                                </td>

                                <td class="p-1">
                                    Periode
                                    <b>
                                        {{ $startDate->locale('id')->translatedFormat('d F Y') }}
                                        -
                                        {{ $endDate->locale('id')->translatedFormat('d F Y') }}
                                    </b>
                                </td>

                                <td class="p-1" width="10px"></td>
                                <td class="p-1"></td>
                            </tr>

                            <tr>
                                <td class="p-1"></td>

                                <td class="p-1">
                                    1. Target PA &nbsp;&nbsp; {{ Number::format($target, precision: 2) }}%
                                </td>

                                <td class="p-1" width="10px"></td>
                                <td class="p-1"></td>
                            </tr>

                            <tr>
                                <td class="p-1"></td>

                                <td class="p-1">
                                    2. Actual Hari Kerja {{ $hariKerja }} <br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ({{ $hariKerja }} hari @
                                    Rp.
                                    {{ Number::format($price, precision: 0) }})
                                </td>

                                <td class="p-1" width="10px">IDR</td>
                                <td class="p-1" style="text-align: right">
                                    {{ Number::format($price, precision: 0) }}
                                </td>
                            </tr>

                            <tr>
                                <td class="p-1"></td>

                                <td class="p-1">
                                    3. Aktual PA <br>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Jam Tersedia
                                    {{ $hariKerja }} x 24 Jam =
                                    {{ Number::format($totalJamKerja, precision: 0) }} Jam
                                    <br>

                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Breakdown =
                                    <b>{{ Number::format($total_breakdown, precision: 2) }}</b> Jam
                                    <br>

                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; PA =
                                    {{ Number::format($pa, precision: 2) }}%
                                </td>

                                <td class="p-1" width="10px"></td>
                                <td class="p-1"></td>
                            </tr>

                            <tr>
                                <td class="p-1"></td>

                                <td class="p-1">
                                    4. Penalty / Potongan Breakdown (PA)
                                </td>

                                <td class="p-1" width="10px">IDR</td>

                                <td class="p-1" style="text-align: right">
                                    {{ Number::format($penalty, precision: 0) }}
                                </td>
                            </tr>

                            <tr>
                                <td class="p-1"></td>

                                <td class="p-1">
                                    5. Total Payment
                                </td>

                                <td class="p-1" width="10px">IDR</td>

                                <td class="p-1" style="text-align: right">
                                    {{ Number::format($total_payment, precision: 0) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @elseif($contract->service->type == 'LCT')

                @elseif($contract->service->type == 'Fuel Truck Rental')

                @elseif($contract->service->type == 'Explosive Material Rental')
                @endif
            </td>
        </tr>
    </tbody>
</table>

@if (!in_array($proforma_invoice->status, ['Draft', 'Open', 'Approval', 'Cancel', 'Received']))
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

                <div style="height: 95px;"></div>

                <div style="min-height: 35px;">
                    ( {{ $proforma_invoice->user->name ?? '' }} )<br>
                    PT. Tunas Mitra Sejati
                </div>
            </td>
            <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
                <div style="height: 30px;">
                    Diketahui Oleh,
                </div>

                <div style="height: 95px;"></div>

                <div style="min-height: 35px;">
                    User
                </div>
            </td>
            <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
                <div style="height: 30px;">

                </div>

                <div style="height: 95px;"></div>

                <div style="min-height: 35px;">
                    Team Dewatering
                </div>
            </td>
            <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
                <div style="height: 30px;">
                    Disetujui Oleh,
                </div>

                <div style="height: 95px;"></div>

                <div style="min-height: 35px;">
                    Custodian
                </div>
            </td>
        </tr>
    </table>
@endif

<script type="text/php">
    if (isset($pdf)) {
        $font = $fontMetrics->getFont("Helvetica", "normal");
        $size = 8;

        $text = "Halaman {PAGE_NUM} / {PAGE_COUNT}";

        $x = 430;
        $y = 820;

        $pdf->page_text($x, $y, $text, $font, $size, array(0, 0, 0));

        $qrBase64 = {!! json_encode($qrBase64) !!};

        $width = $pdf->get_width();
        $height = $pdf->get_height();

        $qrSize = 55;
        $qrX = $width - 120;
        $qrY = $height - 125;

        $pdf->image(
            $qrBase64,
            $qrX,
            $qrY,
            $qrSize,
            $qrSize
        );
    }
</script>
