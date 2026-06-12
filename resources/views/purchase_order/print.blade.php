@php
    use Carbon\Carbon;
    use Illuminate\Support\Number;
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    use App\Models\Approval_flow;
    use App\Models\Approval_status;
    use App\Models\Approval_process;
    use App\Models\Approval_step;

    $maxRowsPerPage = 15;
    $maxRowsWithSummary = 15;

    $details = collect($purchase_order_detail)->values();

    $detailChunks = $details->isNotEmpty() ? $details->chunk($maxRowsPerPage) : collect([collect()]);

    $detailCount = $details->count();

    $lastChunkCount = $detailCount % $maxRowsPerPage;

    $lastChunkCount = $lastChunkCount === 0 && $detailCount > 0 ? $maxRowsPerPage : $lastChunkCount;

    $summaryMustMoveToNextPage = $lastChunkCount > $maxRowsWithSummary;

    $approvalStepCount = $approval_step ? count($approval_step) : 0;

    $qrDate = $purchase_order->date ? Carbon::parse($purchase_order->date)->format('d-m-Y') : '-';

    $qrText =
        'PT. Tunas Mitra Sejati' .
        "\n" .
        "\n" .
        'Nomor PO : ' .
        $purchase_order->order_no .
        "\n" .
        'Tanggal : ' .
        $qrDate .
        "\n" .
        'Vendor : ' .
        optional($purchase_order->client_vendor)->name .
        "\n" .
        'Total : ' .
        Number::format($purchase_order->grand_total ?? 0, 0) .
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

@foreach ($detailChunks as $chunkIndex => $detailChunk)
    @php
        $isLastChunk = $loop->last;
    @endphp

    @if ($chunkIndex > 0)
        <div style="page-break-before: always;"></div>
    @endif

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
                        PURCHASE ORDER
                    </div>
                </th>
            </tr>
        </thead>
    </table>

    <table class="table-p2h"
        style="border: 1px double #000; border-collapse: separate; border-spacing: 1px; width: 100%;">
        <tbody>
            <tr>
                <td style="padding: 8px;">
                    <table class="doc-header-vendor" style="padding-bottom: 10px">
                        <tr>
                            <td style="width: 50%" class="doc-header-vendor-td">
                                Vendor
                            </td>
                            <td style="width: 50%" class="doc-header-vendor-td"></td>
                        </tr>

                        <tr>
                            <td style="width: 50%">
                                <table class="doc-header-detail">
                                    <tr>
                                        <td style="width: 30%">Name</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>{{ $purchase_order->client_vendor->name ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 30%">Address</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>{{ $purchase_order->client_vendor->address ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 30%">Phone</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>{{ $purchase_order->client_vendor->phone ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 30%">Email</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>{{ $purchase_order->client_vendor->email ?? '' }}</td>
                                    </tr>
                                </table>
                            </td>

                            <td style="width: 50%">
                                <table class="doc-header-detail">
                                    <tr>
                                        <td style="width: 30%">PO. No</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td><b>{{ $purchase_order->order_no }}</b></td>
                                    </tr>

                                    <tr>
                                        <td style="width: 30%">Date</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($purchase_order->date)->locale('id')->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="width: 30%">Reff</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>
                                            {{ $purchase_order->purchase_requisition->unit->vehicle_no ? 'EST-' . $purchase_order->purchase_requisition->unit->vehicle_no : '' }}
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
                                        <td>{{ $purchase_order->purchase_requisition->unit->vehicle_no ?? '' }}</td>
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
                        <colgroup>
                            <col style="width: 5%;">
                            <col style="width: 10%;">
                            <col style="width: 10%;">
                            <col style="width: 30%;">
                            <col style="width: 15%;">
                            <col style="width: 15%;">
                            <col style="width: 15%;">
                        </colgroup>

                        <thead>
                            <tr>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 8%">
                                    Item
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 8%">
                                    Qty
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 10%">
                                    Unit
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 30%">
                                    Description
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 15%">
                                    Price IDR
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 15%">
                                    Discount
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 15%">
                                    Total IDR
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($detailChunk as $d)
                                <tr>
                                    <td style="text-align: center; border: 1px solid #000;">
                                        {{ $chunkIndex * $maxRowsPerPage + $loop->iteration }}
                                    </td>

                                    <td style="text-align: center; border: 1px solid #000;">
                                        {{ $d->qty ? Number::format($d->qty, precision: 0) : '' }}
                                    </td>

                                    <td style="text-align: center; border: 1px solid #000;">
                                        {{ $d->uom }}
                                    </td>

                                    <td style="border: 1px solid #000;">
                                        @if ($purchase_order->type == 'General')
                                            {{ $d->desc_vendor ?? $d->description }}
                                        @else
                                            {{ $d->desc_vendor ?? optional($d->mro_item)->name }}
                                        @endif
                                    </td>

                                    <td style="text-align: right; border: 1px solid #000;">
                                        {{ $d->price ? Number::format($d->price, precision: 0) : '' }}
                                    </td>

                                    <td style="text-align: right; border: 1px solid #000;">
                                        {{ $d->discount_item ? Number::format($d->discount_item, precision: 0) : '' }}
                                    </td>

                                    <td style="text-align: right; border: 1px solid #000;">
                                        {{ $d->amount ? Number::format($d->amount, precision: 0) : '' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        @if ($isLastChunk && !$summaryMustMoveToNextPage)
                            <tfoot>
                                @if (($purchase_order->client_vendor->taxable ?? '') == 'PKP')
                                    <tr>
                                        <td colspan="5" rowspan="3"
                                            style="text-align: left; border: 1px solid #000;">
                                            Notes: <br>
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
                                @else
                                    <tr>
                                        <td colspan="5" style="text-align: left; border: 1px solid #000;">
                                            Notes: <br>
                                            {!! nl2br(e($purchase_order->notes)) !!}
                                        </td>

                                        <td style="text-align: left; border: 1px solid #000;">
                                            <b>Grand Total</b>
                                        </td>

                                        <td style="text-align: right; border: 1px solid #000;">
                                            {{ $purchase_order->grand_total ? Number::format($purchase_order->grand_total, precision: 0) : '' }}
                                        </td>
                                    </tr>
                                @endif
                            </tfoot>
                        @endif
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endforeach

@if ($summaryMustMoveToNextPage)
    <div style="page-break-before: always;"></div>

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
                        PURCHASE ORDER
                    </div>
                </th>
            </tr>
        </thead>
    </table>

    <table class="table-p2h"
        style="border: 1px double #000; border-collapse: separate; border-spacing: 1px; width: 100%;">
        <tbody>
            <tr>
                <td style="padding: 8px;">
                    <table class="doc-header-vendor" style="padding-bottom: 10px">
                        <tr>
                            <td style="width: 50%" class="doc-header-vendor-td">
                                Vendor
                            </td>
                            <td style="width: 50%" class="doc-header-vendor-td"></td>
                        </tr>

                        <tr>
                            <td style="width: 50%">
                                <table class="doc-header-detail">
                                    <tr>
                                        <td style="width: 30%">Name</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>{{ $purchase_order->client_vendor->name ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 30%">Address</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>{{ $purchase_order->client_vendor->address ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 30%">Phone</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>{{ $purchase_order->client_vendor->phone ?? '' }}</td>
                                    </tr>

                                    <tr>
                                        <td style="width: 30%">Email</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>{{ $purchase_order->client_vendor->email ?? '' }}</td>
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
                                        <td>
                                            {{ \Carbon\Carbon::parse($purchase_order->date)->locale('id')->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td style="width: 30%">Reff</td>
                                        <td style="width: 5%; text-align: center">:</td>
                                        <td>
                                            {{ $purchase_order->purchase_requisition->unit->vehicle_no ? 'EST-' . $purchase_order->purchase_requisition->unit->vehicle_no : '' }}
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
                                        <td>{{ $purchase_order->purchase_requisition->unit->vehicle_no ?? '' }}</td>
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
                        <colgroup>
                            <col style="width: 5%;">
                            <col style="width: 10%;">
                            <col style="width: 10%;">
                            <col style="width: 30%;">
                            <col style="width: 15%;">
                            <col style="width: 15%;">
                            <col style="width: 15%;">
                        </colgroup>

                        <thead>
                            <tr>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 8%">
                                    Item
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 8%">
                                    Qty
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 10%">
                                    Unit
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 30%">
                                    Description
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 15%">
                                    Price IDR
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 15%">
                                    Discount
                                </th>
                                <th
                                    style="text-align: center; border: 1px solid #000; background-color: #d9ecff; width: 15%">
                                    Total IDR
                                </th>
                            </tr>
                        </thead>

                        <tbody></tbody>

                        <tfoot>
                            @if (($purchase_order->client_vendor->taxable ?? '') == 'PKP')
                                <tr>
                                    <td colspan="5" rowspan="3"
                                        style="text-align: left; border: 1px solid #000;">
                                        Notes: <br>
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
                            @else
                                <tr>
                                    <td colspan="5" style="text-align: left; border: 1px solid #000;">
                                        Notes: <br>
                                        {!! nl2br(e($purchase_order->notes)) !!}
                                    </td>

                                    <td style="text-align: left; border: 1px solid #000;">
                                        <b>Grand Total</b>
                                    </td>

                                    <td style="text-align: right; border: 1px solid #000;">
                                        {{ $purchase_order->grand_total ? Number::format($purchase_order->grand_total, precision: 0) : '' }}
                                    </td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
@endif

@if (!in_array($purchase_order->status, ['Draft', 'Open', 'Approval', 'Cancel', 'Received']))
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;" class="avoid-break">
        <tr>
            <td style="border: none;" colspan="{{ $approvalStepCount + 2 }}">
                <div>
                    Sangatta,
                    {{ \Carbon\Carbon::parse(date('Y-m-d'))->locale('id')->translatedFormat('d F Y') }}
                </div>
            </td>
        </tr>

        <tr>
            <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
                <div style="height: 30px;">
                    Approved By,
                </div>

                <div style="height: 95px;"></div>

                <div style="min-height: 35px;">
                    {{ $purchase_order->client_vendor->name ?? '' }}
                </div>
            </td>

            <td style="border: none; text-align: center; padding: 10px; vertical-align: top;">
                <div style="height: 30px;">
                    Prepared By,
                </div>

                <div style="height: 95px; text-align: center;">
                    @if ($purchase_order->user->sign_path)
                        <img src="{{ public_path('storage/' . $purchase_order->user->sign_path) }}" alt="Signature"
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
                    {{ $purchase_order->user->username }}
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
                                ->where('approvable_id', $purchase_order->id)
                                ->where('step', $d->order)
                                ->first();
                        @endphp

                        <div style="height: 95px; text-align: center;">
                            {{-- @if ($approval_status)
                                @if ($approval_status->status == 'Open')
                                    <div style="height: 95px; line-height: 95px;">
                                        <b>Approval</b>
                                    </div>
                                @elseif ($approval_status->status == 'Rejected')
                                    <div style="height: 95px; line-height: 95px;">
                                        <b>Rejected</b>
                                    </div>
                                @else
                                    @if ($d->user->sign_path)
                                        <img src="{{ public_path('storage/' . $d->user->sign_path) }}"
                                            alt="Signature"
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
                                @endif
                            @endif --}}
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
