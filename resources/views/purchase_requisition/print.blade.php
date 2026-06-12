@php
    use Carbon\Carbon;
    use Illuminate\Support\Number;
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    use App\Models\Approval_flow;
    use App\Models\Approval_status;
    use App\Models\Approval_process;
    use App\Models\Approval_step;

    $maxRowsPerPage = 10;

    $details = collect($purchase_requisition_detail)->values();

    $detailChunks = $details->isNotEmpty() ? $details->chunk($maxRowsPerPage) : collect([collect()]);

    $approvalStepCount = $approval_step ? count($approval_step) : 0;

    $qrDate = $purchase_requisition->date ? Carbon::parse($purchase_requisition->date)->format('d-m-Y') : '-';

    $qrText =
        'PT. Tunas Mitra Sejati' .
        "\n" .
        "\n" .
        'Nomor PR : ' .
        $purchase_requisition->requisition_no .
        "\n" .
        'Tanggal : ' .
        $qrDate .
        "\n" .
        'Department : ' .
        ($purchase_requisition->department ?? '-') .
        "\n" .
        'Total : ' .
        Number::format($purchase_requisition->grand_total ?? 0, 0) .
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
        font-size: 10pt;
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

    .col-description {
        width: 39%;
    }

    .col-uom {
        width: 10%;
    }

    .col-qty {
        width: 10%;
    }

    .col-price {
        width: 12%;
    }

    .col-discount {
        width: 12%;
    }

    .col-amount {
        width: 12%;
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
    @if ($chunkIndex > 0)
        <div style="page-break-before: always;"></div>
    @endif

    <table class="table-p2h">
        <colgroup>
            <col class="col-no" style="width:5%;">
            <col class="col-description" style="width:39%;">
            <col class="col-uom" style="width:10%;">
            <col class="col-qty" style="width:10%;">
            <col class="col-price" style="width:12%;">
            <col class="col-discount" style="width:12%;">
            <col class="col-amount" style="width:12%;">
        </colgroup>

        <thead>
            <tr>
                <th colspan="7" class="doc-header-wrapper">
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
                                                    <td class="label" style="width: 15%">Department</td>
                                                    <td class="sep">:</td>
                                                    <td class="val">
                                                        {{ $purchase_requisition->department ?? '-' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="label" style="width: 15%">Unit</td>
                                                    <td class="sep">:</td>
                                                    <td class="val">
                                                        {{ $purchase_requisition->unit->vehicle_no ?? '-' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="label">Date</td>
                                                    <td class="sep">:</td>
                                                    <td class="val">
                                                        {{ $purchase_requisition->date ?? '-' }}
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td class="label">Job</td>
                                                    <td class="sep">:</td>
                                                    <td class="val">
                                                        {{ $purchase_requisition->job ?? '-' }}
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
                <th class="col-no" style="width:5%;">#</th>
                <th class="col-description" style="width:39%;">Description</th>
                <th class="col-uom" style="width:10%;">Uom</th>
                <th class="col-qty" style="width:10%;">Qty</th>
                <th class="col-price" style="width:12%;">Price</th>
                <th class="col-discount" style="width:12%;">Discount</th>
                <th class="col-amount" style="width:12%;">Amount</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($detailChunk as $d)
                <tr>
                    <td class="p-1 align-middle" style="text-align: center;">
                        {{ $chunkIndex * $maxRowsPerPage + $loop->iteration }}
                    </td>

                    <td class="p-1 align-middle">
                        {{ $d->description ? $d->description : $d->mro_item->name }}
                    </td>

                    <td class="p-1 align-middle" style="text-align: center;">
                        {{ $d->uom }}
                    </td>

                    <td class="p-1 align-middle" style="text-align: center;">
                        {{ $d->qty ? Number::format($d->qty, precision: 0) : '' }}
                    </td>

                    <td class="p-1 align-middle" style="text-align: right;">
                        {{ $d->price ? Number::format($d->price, precision: 0) : '' }}
                    </td>

                    <td class="p-1 align-middle" style="text-align: right;">
                        {{ $d->discount_item ? Number::format($d->discount_item, precision: 0) : '' }}
                    </td>

                    <td class="p-1 align-middle" style="text-align: right;">
                        {{ $d->amount ? Number::format($d->amount, precision: 0) : '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>

        @if ($loop->last)
            <tfoot>
                <tr>
                    <td class="p-1 align-middle" style="text-align: right;" colspan="6">
                        <b>Total</b>
                    </td>
                    <td class="p-1 align-middle" style="text-align: right;">
                        {{ $purchase_requisition->total ? Number::format($purchase_requisition->total, precision: 0) : '' }}
                    </td>
                </tr>

                <tr>
                    <td class="p-1 align-middle" style="text-align: right;" colspan="6">
                        <b>Discount</b>
                    </td>
                    <td class="p-1 align-middle" style="text-align: right;">
                        {{ $purchase_requisition->discount ? Number::format($purchase_requisition->discount, precision: 0) : '' }}
                    </td>
                </tr>

                @if ($purchase_requisition->tax != 0)
                    <tr>
                        <td class="p-1 align-middle" style="text-align: right;" colspan="6">
                            <b>Tax</b>
                        </td>
                        <td class="p-1 align-middle" style="text-align: right;">
                            {{ $purchase_requisition->tax ? Number::format($purchase_requisition->tax, precision: 0) : '' }}
                        </td>
                    </tr>
                @endif

                <tr>
                    <td class="p-1 align-middle" style="text-align: right;" colspan="6">
                        <b>Grand Total</b>
                    </td>
                    <td class="p-1 align-middle" style="text-align: right;">
                        {{ $purchase_requisition->grand_total ? Number::format($purchase_requisition->grand_total, precision: 0) : '' }}
                    </td>
                </tr>

                <tr>
                    <td colspan="7" class="p-1">
                        Notes : <br>
                        @if ($purchase_requisition->notes != '')
                            {!! $purchase_requisition->notes !!}
                        @endif
                        <br>
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    @if ($loop->last && !in_array($purchase_requisition->status, ['Draft', 'Open', 'Approval', 'Cancel', 'Received']))
        <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px;" class="avoid-break">
            <tr>
                <td colspan="{{ $approvalStepCount + 1 }}" class="p-1" style="border: none;">
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0;">
                        <tr>
                            <td style="border: none; text-align: center; vertical-align: top; padding: 10px;">
                                <div style="height: 30px;">
                                    Created By,
                                </div>
                            </td>

                            @if ($approval_step)
                                @foreach ($approval_step as $d)
                                    <td style="border: none; text-align: center; vertical-align: top; padding: 10px;">
                                        <div style="height: 30px;">
                                            {{ $d->action }} By,
                                        </div>
                                    </td>
                                @endforeach
                            @endif
                        </tr>

                        <tr>
                            <td style="border: none; text-align: center; vertical-align: top; padding: 10px;">
                                <div style="height: 95px; text-align: center;">
                                    @if ($purchase_requisition->user->sign_path)
                                        <img src="{{ public_path('storage/' . $purchase_requisition->user->sign_path) }}"
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
                                </div>

                                <div style="min-height: 35px; text-align: center;">
                                    {{ $purchase_requisition->user->name }}
                                </div>
                            </td>

                            @if ($approval_step)
                                @foreach ($approval_step as $d)
                                    <td style="border: none; text-align: center; vertical-align: top; padding: 10px;">
                                        @php
                                            $approval_status = Approval_status::where(
                                                'approval_flow_id',
                                                $approval_flow->id,
                                            )
                                                ->where('approvable_id', $purchase_requisition->id)
                                                ->where('step', $d->order)
                                                ->first();
                                        @endphp
                                        <div style="height: 95px; text-align: center;">
                                            {{-- @if ($approval_status)
                                                @if ($approval_status->status == 'Open')
                                                    <div style="height: 95px; line-height: 95px;">
                                                        <b>Approval Process</b>
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
                                        </div>

                                        <div style="min-height: 35px; text-align: center;">
                                            {{ $d->user->name }}
                                        </div>
                                    </td>
                                @endforeach
                            @endif
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endif
@endforeach

{{-- Page number footer and QR Code footer for DomPDF --}}
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
