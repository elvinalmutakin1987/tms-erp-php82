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

    $qrDate = $invoice->date ? Carbon::parse($invoice->date)->format('d-m-Y') : '-';

    $qrText =
        'Signature document Invoice ' .
        "\n" .
        $invoice->invoice_no .
        "\n" .
        'From ERP PT. Tunas Mitra Sejati, ' .
        "\n" .
        'in the name of the signature Protasius Siboro' .
        "\n" .
        'Date Time Signature ' .
        Carbon::parse($invoice->checked_at)->format('Y-m-d H:i:s');

    $qrImage = QrCode::format('png')->size(150)->margin(1)->generate($qrText);

    $qrBase64 = 'data:image/png;base64,' . base64_encode($qrImage);

    $layout = 'potrait';

    $month_indonesia = function ($value) {
        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        return $bulan[(int) $value] ?? '-';
    };
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

    .doc-header-document-td {
        border: none !important;
        vertical-align: middle;
        border-spacing: 0;
        padding-top: 0;
    }

    .doc-header-document-vertical-align-td {
        border: none !important;
        vertical-align: top !important;
        padding-top: 0;
        border-spacing: 0;
        font-size: 26pt;
        font-weight: 700;
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
    </thead>
</table>
<table class="table-p2h" style="border: none; border-collapse: collapse; border-spacing: 0; width: 100%;">
    <tbody>
        <tr style="border: none">
            <td style="padding: 8px; border: none">

                @isset($invoice->contract_id)
                    <table class="doc-header-vendor" style="padding-bottom: 10px; font-size: 12pt">
                        <tr>
                            <td style="width: 50%" class="doc-header-document-td">
                                <table class="doc-header-detail">
                                    <tr>
                                        <td style="width: 30%">Date</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($invoice->date)->locale('id')->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%">Invoice Ref.</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td><b>{{ $invoice->invoice_no }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%">Currency</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>IDR</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%; text-align: center;" class="doc-header-document-vertical-align-td">
                                INVOICE
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%">
                                <table class="doc-header-detail">
                                    <tr>
                                        <td style="width: 30%">Contract No.</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td><b>{{ $invoice->contract->contract_no }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%">CIC No.</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>
                                            @php
                                                $filteredInvoices = $proforma_invoice
                                                    ->filter(fn($pi) => filled($pi->cic_number))
                                                    ->values();
                                            @endphp

                                            @foreach ($filteredInvoices as $pi)
                                                {{ $pi->cic_number }}{!! $loop->last ? '' : '<br>' !!}
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%">
                                <table class="doc-header-detail">
                                    <tr>
                                        <td style="width: 10%">To</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td><b>{{ $invoice->client_vendor->name ?? '' }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 10%"></td>
                                        <td style="width: 5%; text-align: center"></td>
                                        <td>{{ $invoice->client_vendor->address ?? '' }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                @else
                    <table class="doc-header-vendor" style="padding-bottom: 10px; font-size: 12pt">
                        <tr>
                            <td style="width: 50%" class="doc-header-document-td">
                                <table class="doc-header-detail">
                                    <tr>
                                        <td style="width: 30%">Date</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($invoice->date)->locale('id')->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%">Invoice Ref.</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td><b>{{ $invoice->invoice_no }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%">Currency</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>IDR</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%; text-align: center;" class="doc-header-document-vertical-align-td">
                                INVOICE
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%; border-right: none !important;">
                                <table class="doc-header-detail">
                                    <tr>
                                        <td style="width: 10%">To</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td><b>{{ $invoice->client_vendor->name ?? '' }}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 10%"></td>
                                        <td style="width: 5%; text-align: center"></td>
                                        <td>{{ $invoice->client_vendor->address ?? '' }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%; border-left: none !important;">
                            </td>
                        </tr>
                    </table>
                @endisset

                <style>
                    /* =========================
                    TABEL UTAMA
                    ========================== */
                    .invoice-detail {
                        width: 100%;
                        border-collapse: collapse;
                        table-layout: fixed;
                        font-size: 11pt;

                        /* Bingkai luar tabel */
                        border: 1px solid #000;
                    }

                    .invoice-detail,
                    .invoice-detail th,
                    .invoice-detail td {
                        box-sizing: border-box;
                    }

                    /* =========================
                    HEADER
                    ========================== */
                    .invoice-detail thead th {
                        padding: 7px;
                        text-align: center;
                        vertical-align: middle;
                        background-color: #d9ecff;
                        font-weight: bold;

                        border-top: none;
                        border-bottom: 1px solid #000;
                    }

                    /* Garis pemisah header 80:20 */
                    .invoice-detail thead th:first-child {
                        width: 80%;
                        border-left: none;
                        border-right: 1px solid #000;
                    }

                    .invoice-detail thead th:last-child {
                        width: 20%;
                        border-left: none;
                        border-right: none;
                    }

                    /* =========================
                    KOLOM UTAMA 80:20
                    ========================== */
                    .description-side {
                        width: 80%;
                        border-right: 1px solid #000 !important;
                    }

                    .amount-side {
                        width: 20%;
                    }

                    /* =========================
                    JUDUL DAN PERIODE
                    ========================== */
                    .invoice-intro>td {
                        vertical-align: top;
                        border-top: none;
                        border-bottom: none;
                    }

                    .invoice-intro>.description-side {
                        padding: 8px 10px 14px 10px;
                        line-height: 1.2;
                    }

                    .invoice-intro>.amount-side {
                        padding: 8px 5px 14px 5px;
                    }

                    /* =========================
                    BARIS DETAIL
                    ========================== */
                    .invoice-item>td {
                        padding: 0;
                        vertical-align: top;

                        /* Hilangkan garis atas dan bawah */
                        border-top: none !important;
                        border-bottom: none !important;
                    }

                    /* Garis pemisah vertikal tetap ada */
                    .invoice-item>.description-side {
                        border-left: none !important;
                        border-right: 1px solid #000 !important;
                    }

                    .invoice-item>.amount-side {
                        border-left: none !important;
                        border-right: none !important;
                    }

                    /* Jarak bawah pada item terakhir */
                    .invoice-item.last-item>td {
                        padding-bottom: 8px;
                        border-bottom: none !important;
                    }

                    /* =========================
                    TABEL DALAM DESCRIPTION
                    ========================== */
                    .description-detail {
                        width: 100%;
                        border: none !important;
                        border-collapse: collapse;
                        table-layout: fixed;
                    }

                    .description-detail tr,
                    .description-detail td {
                        border: none !important;
                    }

                    .description-detail td {
                        padding-top: 3px;
                        padding-bottom: 3px;
                        vertical-align: top;
                        line-height: 1.2;
                    }

                    .item-description {
                        width: 62.5%;
                        padding-left: 25px !important;
                        padding-right: 5px !important;
                        text-align: left;
                        white-space: nowrap;
                    }

                    .item-quantity {
                        width: 12.5%;
                        padding-left: 3px !important;
                        padding-right: 3px !important;
                        text-align: center;
                        white-space: nowrap;
                    }

                    .item-rate {
                        width: 25%;
                        padding-left: 3px !important;
                        padding-right: 8px !important;
                        text-align: right;
                        white-space: nowrap;
                    }

                    /* =========================
                    TABEL DALAM AMOUNT
                    ========================== */
                    .amount-detail {
                        width: 100%;
                        border: none !important;
                        border-collapse: collapse;
                        table-layout: fixed;
                    }

                    .amount-detail tr,
                    .amount-detail td {
                        border: none !important;
                    }

                    .amount-detail td {
                        padding-top: 3px;
                        padding-bottom: 3px;
                        vertical-align: top;
                        line-height: 1.2;
                    }

                    .item-currency {
                        width: 20%;
                        padding-left: 7px !important;
                        padding-right: 0 !important;
                        text-align: left;
                        white-space: nowrap;
                    }

                    .item-total {
                        width: 80%;
                        padding-left: 2px !important;
                        padding-right: 8px !important;
                        text-align: right;
                        white-space: nowrap;
                    }
                </style>

                @isset($invoice->contract_id)
                    <table class="invoice-detail">

                        {{-- Tabel utama hanya memiliki dua kolom: 80% dan 20% --}}
                        <colgroup>
                            <col style="width: 80%;">
                            <col style="width: 20%;">
                        </colgroup>

                        <thead>
                            <tr>
                                <th width="80%">
                                    DESCRIPTION
                                </th>

                                <th width="20%">
                                    AMOUNT
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- Judul pekerjaan dan periode --}}
                            <tr class="invoice-intro">
                                <td width="80%" class="description-side">
                                    <b>
                                        {{ strtoupper($contract->notes ?? '') }}
                                    </b>

                                    <br>

                                    <b>
                                        PERIODE :
                                        {{ strtoupper($month_indonesia($month)) . ' ' . $year }}
                                    </b>
                                </td>

                                <td width="20%" class="amount-side">
                                    &nbsp;
                                </td>
                            </tr>

                            {{-- Detail Unit Rental --}}
                            @if (($contract->service->type ?? null) === 'Unit Rental' || ($contract->service->type ?? null) === 'Fuel Truck Rental')
                                @foreach ($proforma_invoice as $pi)
                                    <tr @class(['invoice-item', 'last-item' => $loop->last])>

                                        {{-- DESCRIPTION UTAMA 80% --}}
                                        <td width="80%" class="description-side">
                                            <table class="description-detail">
                                                <colgroup>
                                                    <col style="width: 62.5%;">
                                                    <col style="width: 12.5%;">
                                                    <col style="width: 25%;">
                                                </colgroup>

                                                <tbody>
                                                    <tr>
                                                        <td width="62.5%" class="item-description">
                                                            FIX MONTHLY FEE
                                                            ({{ $pi->unit->vehicle_no ?? '-' }})
                                                        </td>

                                                        <td width="12.5%" class="item-quantity">
                                                            {{ Number::format(1, 2) }}
                                                            &nbsp;&nbsp;X
                                                        </td>

                                                        <td width="25%" class="item-rate">
                                                            {{ Number::format($pi->total ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>

                                        {{-- AMOUNT UTAMA 20% --}}
                                        <td width="20%" class="amount-side">
                                            <table class="amount-detail">
                                                <colgroup>
                                                    <col style="width: 20%;">
                                                    <col style="width: 80%;">
                                                </colgroup>

                                                <tbody>
                                                    <tr>
                                                        <td width="20%" class="item-currency">
                                                            Rp.
                                                        </td>

                                                        <td width="80%" class="item-total">
                                                            {{ Number::format($pi->total ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            @elseif(($contract->service->type ?? null) === 'LCT')
                                @foreach ($contract_rate as $rate)
                                    @php
                                        $proforma_invoice_id = $proforma_invoice->pluck('id');
                                        $qty = Proforma_invoice_detail::whereIn(
                                            'proforma_invoice_id',
                                            $proforma_invoice_id,
                                        )
                                            ->where('contract_rate_id', $rate->id)
                                            ->sum('qty');
                                        $rate = Proforma_invoice_detail::whereIn(
                                            'proforma_invoice_id',
                                            $proforma_invoice_id,
                                        )
                                            ->where('contract_rate_id', $rate->id)
                                            ->sum('rate');
                                        $amount = Proforma_invoice_detail::whereIn(
                                            'proforma_invoice_id',
                                            $proforma_invoice_id,
                                        )
                                            ->where('contract_rate_id', $rate->id)
                                            ->sum('amount');
                                    @endphp
                                    <tr @class(['invoice-item', 'last-item' => $loop->last])>

                                        {{-- DESCRIPTION UTAMA 80% --}}
                                        <td width="80%" class="description-side">
                                            <table class="description-detail">
                                                <colgroup>
                                                    <col style="width: 62.5%;">
                                                    <col style="width: 12.5%;">
                                                    <col style="width: 25%;">
                                                </colgroup>

                                                <tbody>
                                                    <tr>
                                                        <td width="62.5%" class="item-description">
                                                            ({{ $rate->service_item ?? '-' }})
                                                        </td>

                                                        <td width="12.5%" class="item-quantity">
                                                            {{ Number::format($qty, 2) }}
                                                            &nbsp;&nbsp;X
                                                        </td>

                                                        <td width="25%" class="item-rate">
                                                            {{ Number::format($rate ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>

                                        {{-- AMOUNT UTAMA 20% --}}
                                        <td width="20%" class="amount-side">
                                            <table class="amount-detail">
                                                <colgroup>
                                                    <col style="width: 20%;">
                                                    <col style="width: 80%;">
                                                </colgroup>

                                                <tbody>
                                                    <tr>
                                                        <td width="20%" class="item-currency">
                                                            Rp.
                                                        </td>

                                                        <td width="80%" class="item-total">
                                                            {{ Number::format($amount ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            @elseif(($contract->service->type ?? null) === 'Explosive Material Transport')
                                @foreach ($contract_rate as $contractrate)
                                    @php
                                        $qty = Proforma_invoice_detail::whereIn(
                                            'proforma_invoice_id',
                                            $proforma_invoice_id,
                                        )
                                            ->where('contract_rate_id', $contractrate->id)
                                            ->sum('qty');
                                        $rate = Proforma_invoice_detail::whereIn(
                                            'proforma_invoice_id',
                                            $proforma_invoice_id,
                                        )
                                            ->where('contract_rate_id', $contractrate->id)
                                            ->sum('rate');
                                        $amount = 0;
                                        $amount = Proforma_invoice_detail::whereIn(
                                            'proforma_invoice_id',
                                            $proforma_invoice_id,
                                        )
                                            ->where('contract_rate_id', $contractrate->id)
                                            ->sum('amount');
                                    @endphp
                                    <tr @class(['invoice-item', 'last-item' => $loop->last])>

                                        {{-- DESCRIPTION UTAMA 80% --}}
                                        <td width="80%" class="description-side">
                                            <table class="description-detail">
                                                <colgroup>
                                                    <col style="width: 62.5%;">
                                                    <col style="width: 12.5%;">
                                                    <col style="width: 25%;">
                                                </colgroup>

                                                <tbody>
                                                    <tr>
                                                        <td width="62.5%" class="item-description">
                                                            {{ $contractrate->service_item ?? '-' }}
                                                        </td>

                                                        <td width="12.5%" class="item-quantity">
                                                            {{ Number::format($qty, 2) }}
                                                            &nbsp;&nbsp;X
                                                        </td>

                                                        <td width="25%" class="item-rate">
                                                            {{ Number::format($rate ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>

                                        {{-- AMOUNT UTAMA 20% --}}
                                        <td width="20%" class="amount-side">
                                            <table class="amount-detail">
                                                <colgroup>
                                                    <col style="width: 20%;">
                                                    <col style="width: 80%;">
                                                </colgroup>

                                                <tbody>
                                                    <tr>
                                                        <td width="20%" class="item-currency">
                                                            Rp.
                                                        </td>

                                                        <td width="80%" class="item-total">
                                                            {{ Number::format($amount ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            @elseif(($contract->service->type ?? null) === 'Pallet')
                                @php
                                    $proforma_invoice_detail = Proforma_invoice_detail::whereIn(
                                        'proforma_invoice_id',
                                        $proforma_invoice_id,
                                    )->get();
                                @endphp
                                @foreach ($proforma_invoice_detail as $pid)
                                    <tr @class(['invoice-item', 'last-item' => $loop->last])>

                                        {{-- DESCRIPTION UTAMA 80% --}}
                                        <td width="80%" class="description-side">
                                            <table class="description-detail">
                                                <colgroup>
                                                    <col style="width: 62.5%;">
                                                    <col style="width: 12.5%;">
                                                    <col style="width: 25%;">
                                                </colgroup>

                                                <tbody>
                                                    <tr>
                                                        <td width="62.5%" class="item-description">
                                                            {{ $pid?->service_item ?? '-' }}
                                                        </td>

                                                        <td width="12.5%" class="item-quantity">
                                                            {{ Number::format($pid?->qty ?? 0, 2) }}
                                                            &nbsp;&nbsp;X
                                                        </td>

                                                        <td width="25%" class="item-rate">
                                                            {{ Number::format($pid->rate ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>

                                        {{-- AMOUNT UTAMA 20% --}}
                                        <td width="20%" class="amount-side">
                                            <table class="amount-detail">
                                                <colgroup>
                                                    <col style="width: 20%;">
                                                    <col style="width: 80%;">
                                                </colgroup>

                                                <tbody>
                                                    <tr>
                                                        <td width="20%" class="item-currency">
                                                            Rp.
                                                        </td>

                                                        <td width="80%" class="item-total">
                                                            {{ Number::format($pid?->amount ?? 0, 2) }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                            <tr @class('invoice-item')>

                                {{-- DESCRIPTION UTAMA 80% --}}
                                <td width="80%" class="description-side">
                                    <table class="description-detail">
                                        <colgroup>
                                            <col style="width: 62.5%;">
                                            <col style="width: 12.5%;">
                                            <col style="width: 25%;">
                                        </colgroup>

                                        <tbody>
                                            <tr>
                                                <td width="25%" class="item-rate" colspan="3">
                                                    SUB TOTAL
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="25%" class="item-rate" colspan="3">
                                                    PPN : 12% X {{ Number::format($invoice?->dpp ?? 0, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="25%" class="item-rate" colspan="3">
                                                    <b>TOTAL DUE</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>

                                {{-- AMOUNT UTAMA 20% --}}
                                <td width="20%" class="amount-side">
                                    <table class="amount-detail">
                                        <colgroup>
                                            <col style="width: 20%;">
                                            <col style="width: 80%;">
                                        </colgroup>

                                        <tbody>
                                            <tr>
                                                <td width="20%" class="item-currency">
                                                    Rp.
                                                </td>

                                                <td width="80%" class="item-total">
                                                    {{ Number::format($invoice?->total ?? 0, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="20%" class="item-currency">
                                                    Rp.
                                                </td>

                                                <td width="80%" class="item-total">
                                                    {{ Number::format($invoice?->ppn ?? 0, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="20%" class="item-currency">
                                                    <b>Rp.</b>
                                                </td>

                                                <td width="80%" class="item-total">
                                                    <b>{{ Number::format($invoice?->grand_total ?? 0, 2) }}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr @class('invoice-item')>

                                {{-- DESCRIPTION UTAMA 80% --}}
                                <td width="80%" class="description-side">
                                    <table class="description-detail" style="padding-top: 30px">
                                        <colgroup>
                                            <col style="width: 100%">
                                        </colgroup>

                                        <tbody>
                                            <tr>
                                                <td width="100%" style="font-size: 12pt">
                                                    Terbilang : IDR <br>
                                                    {{ ucfirst(\Riskihajar\Terbilang\Facades\Terbilang::make((int) round($invoice->grand_total), ' rupiah')) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>

                                {{-- AMOUNT UTAMA 20% --}}
                                <td width="20%" class="amount-side">
                                    <table class="amount-detail">
                                        <colgroup>
                                            <col style="width: 20%;">
                                            <col style="width: 80%;">
                                        </colgroup>

                                        <tbody>

                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr @class('invoice-item') style="padding-top: 60px">

                                {{-- DESCRIPTION UTAMA 80% --}}
                                <td width="80%" class="description-side">
                                    <table class="description-detail" style="padding-top: 20px;">
                                        <colgroup>
                                            <col style="width: 100%">
                                        </colgroup>
                                        <tbody>
                                            @if (!in_array($invoice->status, ['Draft', 'Open', 'Approval', 'Cancel', 'Received']))
                                                <tr>
                                                    <td width="100%" style="font-size: 12pt">
                                                        Jakarta,
                                                        {{ Carbon::parse($invoice->date)->format('d') . ' ' . $month_indonesia(Carbon::parse($invoice->date)->format('m')) . ' ' . Carbon::parse($invoice->date)->format('Y') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>

                                                        <img src="{{ $qrBase64 }}" width="100" height="100"
                                                            alt="QR Code">

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        ( Protasius Siboro )
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </td>

                                {{-- AMOUNT UTAMA 20% --}}
                                <td width="20%" class="amount-side">
                                    <table class="amount-detail">
                                        <colgroup>
                                            <col style="width: 20%;">
                                            <col style="width: 80%;">
                                        </colgroup>

                                        <tbody>

                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <table class="invoice-detail">
                        {{-- Tabel utama hanya memiliki dua kolom: 80% dan 20% --}}
                        <colgroup>
                            <col style="width: 80%;">
                            <col style="width: 20%;">
                        </colgroup>

                        <thead>
                            <tr>
                                <th width="80%">
                                    DESCRIPTION
                                </th>

                                <th width="20%">
                                    AMOUNT
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            {{-- Judul pekerjaan dan periode --}}
                            @foreach ($invoice->invoice_detail as $detail)
                                <tr @class(['invoice-item', 'last-item' => $loop->last])>

                                    {{-- DESCRIPTION UTAMA 80% --}}
                                    <td width="80%" class="description-side">
                                        <table class="description-detail">
                                            <colgroup>
                                                <col style="width: 62.5%;">
                                                <col style="width: 12.5%;">
                                                <col style="width: 25%;">
                                            </colgroup>

                                            <tbody>
                                                <tr>
                                                    <td width="62.5%" class="item-description">
                                                        {{ $detail->service_item ?? '-' }}
                                                    </td>

                                                    <td width="12.5%" class="item-quantity">
                                                        {{ Number::format($detail->qty, 2) }}
                                                        &nbsp;&nbsp;X
                                                    </td>

                                                    <td width="25%" class="item-rate">
                                                        {{ Number::format($detail?->price ?? (0 - $detail?->discount_item ?? 0), 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>

                                    {{-- AMOUNT UTAMA 20% --}}
                                    <td width="20%" class="amount-side">
                                        <table class="amount-detail">
                                            <colgroup>
                                                <col style="width: 20%;">
                                                <col style="width: 80%;">
                                            </colgroup>

                                            <tbody>
                                                <tr>
                                                    <td width="20%" class="item-currency">
                                                        Rp.
                                                    </td>

                                                    <td width="80%" class="item-total">
                                                        {{ Number::format($detail->amount ?? 0, 2) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach

                            <tr @class('invoice-item')>

                                {{-- DESCRIPTION UTAMA 80% --}}
                                <td width="80%" class="description-side">
                                    <table class="description-detail">
                                        <colgroup>
                                            <col style="width: 62.5%;">
                                            <col style="width: 12.5%;">
                                            <col style="width: 25%;">
                                        </colgroup>

                                        <tbody>
                                            <tr>
                                                <td width="25%" class="item-rate" colspan="3">
                                                    SUB TOTAL
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="25%" class="item-rate" colspan="3">
                                                    PPN : {{ $system_setting['tax'] }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="25%" class="item-rate" colspan="3">
                                                    Discount
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="25%" class="item-rate" colspan="3">
                                                    <b>TOTAL DUE</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>

                                {{-- AMOUNT UTAMA 20% --}}
                                <td width="20%" class="amount-side">
                                    <table class="amount-detail">
                                        <colgroup>
                                            <col style="width: 20%;">
                                            <col style="width: 80%;">
                                        </colgroup>

                                        <tbody>
                                            <tr>
                                                <td width="20%" class="item-currency">
                                                    Rp.
                                                </td>

                                                <td width="80%" class="item-total">
                                                    {{ Number::format($invoice?->total ?? 0, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="20%" class="item-currency">
                                                    Rp.
                                                </td>

                                                <td width="80%" class="item-total">
                                                    {{ Number::format($invoice?->tax ?? 0, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="20%" class="item-currency">
                                                    Rp.
                                                </td>

                                                <td width="80%" class="item-total">
                                                    {{ Number::format($invoice?->discount ?? 0, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="20%" class="item-currency">
                                                    <b>Rp.</b>
                                                </td>

                                                <td width="80%" class="item-total">
                                                    <b>{{ Number::format($invoice?->grand_total ?? 0, 2) }}</b>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr @class('invoice-item')>

                                {{-- DESCRIPTION UTAMA 80% --}}
                                <td width="80%" class="description-side">
                                    <table class="description-detail" style="padding-top: 30px">
                                        <colgroup>
                                            <col style="width: 100%">
                                        </colgroup>

                                        <tbody>
                                            <tr>
                                                <td width="100%" style="font-size: 12pt">
                                                    Terbilang : IDR <br>
                                                    {{ ucfirst(\Riskihajar\Terbilang\Facades\Terbilang::make((int) round($invoice?->grand_total ?? 0), ' rupiah')) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>

                                {{-- AMOUNT UTAMA 20% --}}
                                <td width="20%" class="amount-side">
                                    <table class="amount-detail">
                                        <colgroup>
                                            <col style="width: 20%;">
                                            <col style="width: 80%;">
                                        </colgroup>

                                        <tbody>

                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr @class('invoice-item') style="padding-top: 60px">

                                {{-- DESCRIPTION UTAMA 80% --}}
                                <td width="80%" class="description-side">
                                    <table class="description-detail" style="padding-top: 20px;">
                                        <colgroup>
                                            <col style="width: 100%">
                                        </colgroup>
                                        <tbody>
                                            @if (!in_array($invoice->status, ['Draft', 'Open', 'Approval', 'Cancel', 'Received']))
                                                <tr>
                                                    <td width="100%" style="font-size: 12pt">
                                                        Jakarta,
                                                        {{ Carbon::parse($invoice->date)->format('d') . ' ' . $month_indonesia(Carbon::parse($invoice->date)->format('m')) . ' ' . Carbon::parse($invoice->date)->format('Y') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>

                                                        <img src="{{ $qrBase64 }}" width="100" height="100"
                                                            alt="QR Code">

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        ( Protasius Siboro )
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </td>

                                {{-- AMOUNT UTAMA 20% --}}
                                <td width="20%" class="amount-side">
                                    <table class="amount-detail">
                                        <colgroup>
                                            <col style="width: 20%;">
                                            <col style="width: 80%;">
                                        </colgroup>

                                        <tbody>

                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endisset
            </td>
        </tr>
    </tbody>
</table>

<table class="table-p2h" style="border: none; border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
        <tr>
            <th class="doc-header-wrapper">
                <table class="doc-header-table">
                    <tr>
                        <td style="font-size: 10pt; font-weight: normal">
                            Pembayaran dengan cek/giro dianggap sah setelah duangkan atau setelah clearing oleh bank
                            <br>
                            Payment by cheque/draft etc. is not considered valid before it is cashed or cleared by our
                            bank
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 10pt;">
                            Banks : <br>
                            {{ $system_setting['bank_invoice'] }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12pt;">
                            AC/IDR &nbsp; &nbsp; : {{ $system_setting['account_invoice'] }}
                        </td>
                    </tr>
                </table>
            </th>
        </tr>
    </thead>
</table>

<style>
    @page {
        /* Sisakan ruang supaya isi halaman tidak menimpa footer */
        margin-bottom: 65px;
    }

    .table-footer {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 10mm;
        /* 10% dari 297 mm */
        width: 100%;
        border: none;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .table-footer td,
    .table-footer th {
        border: none;
        padding: 0;
    }

    .doc-footer-wrapper {
        font-size: 10pt;
        font-weight: normal;
        text-align: left;
        vertical-align: bottom;
    }

    .doc-footer-table {
        width: 100%;
        border: none;
        border-collapse: collapse;
        border-spacing: 0;
    }

    .doc-footer-table td {
        border: none;
        padding: 0;
        font-size: 10pt;
        font-weight: normal;
        line-height: 1.2;
        text-align: left;
    }
</style>

<table class="table-footer">
    <tbody>
        <tr>
            <td class="doc-footer-wrapper">
                <table class="doc-footer-table">
                    <tbody>
                        <tr>
                            <td>
                                Dokumen ini dihasilkan secara elektronik. Tidak diperlukan lagi tanda-tangan basah <br>
                                This document is computer generated. No wet signature needed.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>

@if (!in_array($invoice->status, ['Draft', 'Open', 'Approval', 'Cancel', 'Received']))
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
