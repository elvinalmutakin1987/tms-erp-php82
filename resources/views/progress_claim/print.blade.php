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

    $layout = 'potrait';
    // if ($proforma_invoice->contract->service->type === 'Unit Rental') {
    //     $layout = 'potrait';
    // } elseif ($proforma_invoice->contract->service->type === 'LCT') {
    //     $layout = 'landscape';
    // } elseif ($proforma_invoice->contract->service->type === 'Explosive Material Transport') {
    //     $layout = 'landscape';
    // } elseif ($proforma_invoice->contract->service->type === 'Pallet') {
    //     $layout = 'landscape';
    // }
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
                                <tr>
                                    <td style="width: 30%">Contract No.</td>
                                    <td style="width: 5%; text-align: center">:</td>
                                    <td><b>{{ $contract->contract_no ?? '' }}</b></td>
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

                <table style="border: 1px double #000; border-collapse: collapse; border-spacing: 1px; width: 100%;">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 5px; vertical-align:middle">No.</th>
                            <th scope="col" style="vertical-align:middle">Item</th>
                            <th scope="col" style="width: 30px; vertical-align:middle">Unit</th>
                            <th scope="col" style="vertical-align:middle">Rate</th>
                            <th scope="col" style="vertical-align:middle">Estimation Contract Value</th>
                            <th scope="col" style="vertical-align:middle">Total Previous Program Claims
                            </th>
                            <th scope="col" style="vertical-align:middle">Amount</th>
                            <th scope="col" style="vertical-align:middle">PTD Amount</th>
                            <th scope="col" style="vertical-align:middle">Remaining Contract</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
                            $proforma_invoice_old = Proforma_invoice::where('contract_id', $contract->id)->pluck('id');
                            $contract_fmf = Contract_fmf::where('contract_id', $contract->id)
                                ->where('year', $year)
                                ->first();
                            $fix_monthly_fee = $contract_fmf->value;
                            $fmf_qty = 1;
                            $fmf_qty_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                                ->where('contract_fmf_id', $contract_fmf->id)
                                ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                                ->count();
                            $fmf_amount_ptd = Proforma_invoice_detail::where('contract_id', $contract->id)
                                ->where('contract_fmf_id', $contract_fmf->id)
                                ->whereIn('proforma_invoice_id', $proforma_invoice_old)
                                ->sum('value');

                            $total_amount = 0;
                            $total_amount_ptd = 0;
                        @endphp

                        <tr>
                            <td style="text-align:center">
                                1
                            </td>
                            <td>
                                Fix Monthly Fee
                            </td>
                            <td style="text-align: center">
                                Month
                            </td>
                            <td style="text-align: right">
                                {{ Number::format($fix_monthly_fee) }}
                            </td>
                            <td style="text-align: right">
                                {{ Number::format($contract?->value) }}
                            </td>
                            <td style="text-align: right">
                                {{ Number::format($fmf_amount_ptd) }}
                            </td>
                            <td style="text-align: right">
                                {{ Number::format($fix_monthly_fee) }}
                            </td>
                            <td style="text-align: right">
                                {{ Number::format($fmf_amount_ptd + $fix_monthly_fee) }}
                            </td>
                            <td style="text-align: right">
                                {{ Number::format($contract?->value - ($fmf_amount_ptd + $fix_monthly_fee)) }}
                            </td>
                        </tr>

                        @php
                            $total_amount += $fix_monthly_fee * $fmf_qty;
                            $total_amount_ptd += $fmf_amount_ptd + $fix_monthly_fee * $fmf_qty;
                        @endphp
                        <tr>
                            <td colspan="6" style="text-align: right">
                                <b>TOTAL</b>
                            </td>
                            <td style="text-align: right">
                                <b> {{ Number::format($fix_monthly_fee) }}</b>
                            </td>
                            <td style="text-align: right">
                                <b>{{ Number::format($fmf_amount_ptd + $fix_monthly_fee) }}</b>
                            </td>
                            <td style="text-align: right">
                                <b>{{ Number::format($contract?->value - ($fmf_amount_ptd + $fix_monthly_fee)) }}</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
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

                <div style="height: 50px;"></div>

                <div style="min-height: 35px;">
                    ( {{ $proforma_invoice->user->name ?? '' }} )<br>
                    PT. Tunas Mitra Sejati
                </div>
            </td>

            @if ($approval_step)
                @foreach ($approval_step as $d)
                    <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
                        <div style="height: 30px;">
                            {{ $d->action }} By,
                        </div>

                        @php
                            $approval_status = Approval_status::where('approval_flow_id', $approval_flow->id)
                                ->where('approvable_id', $proforma_invoice->id)
                                ->where('step', $d->order)
                                ->first();
                        @endphp

                        <div style="height: 50px; text-align: center;">
                            @if ($d->user->sign_path)
                                <img src="{{ public_path('storage/' . $d->user->sign_path) }}" alt="Signature"
                                    style="
                                                max-width: 150px;
                                                max-height: 85px;
                                                width: auto;
                                                height: auto;
                                                margin: 0 auto;
                                                display: block;
                                                object-fit: contain;
                                            ">
                            @endif
                        </div>

                        <div style="min-height: 35px;">
                            {{ $d->user->name }}
                        </div>
                    </td>
                @endforeach
            @endif

            <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
                <div style="height: 30px;">
                    Knows By,
                </div>

                <div style="height: 50px;"></div>

                <div style="min-height: 35px;">
                    Supt. CMD
                </div>
            </td>

            <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
                <div style="height: 30px;">
                    Approved By,
                </div>

                <div style="height: 50px;"></div>

                <div style="min-height: 35px;">
                    CMD Mgr
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
