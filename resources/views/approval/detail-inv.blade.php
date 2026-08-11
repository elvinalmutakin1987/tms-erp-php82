@php
    use Illuminate\Support\Number;
    use Carbon\Carbon;
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
    use App\Models\Unit;
    use App\Models\Maintenance;
    use App\Models\Daily_report;
    use App\Models\Daily_report_detail;

    // $proforma_invoice_id = $invoice->proforma_invoice_id ?? null;

@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="30%" style="vertical-align: top">Number <br>
                    <b>{{ $invoice->invoice_no }}</b>
                </td>
                <td style="vertical-align: top">Status <br>
                    @if ($invoice->status == 'Draft')
                        <span class="badge bg-secondary" style="font-size: 13px">{{ $invoice->status }}</span>
                    @elseif($invoice->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $invoice->status }}</span>
                    @elseif($invoice->status == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $invoice->status }}</span>
                    @elseif($invoice->status == 'Approved' || $invoice->status == 'Received' || $invoice->status == 'Payment')
                        <span class="badge bg-warning" style="font-size: 13px">{{ $invoice->status }}</span>
                    @elseif($invoice->status == 'Done')
                        <span class="badge bg-success" style="font-size: 13px">{{ $invoice->status }}</span>
                    @endif
                </td>
                <td width="30%" style="vertical-align: top">Contract No. <br>
                    <b>
                        @if ($invoice->contract_id)
                            {{ $invoice->contract->contract_no }} -
                            {{ $invoice->client_vendor->name }}
                        @else
                            Direct Invoice
                        @endif
                    </b>
                </td>
            </tr>
            <tr>
                <td width="30%" style="vertical-align: top">Date <br>
                    <b>{{ $invoice->date }}</b>
                </td>
                <td width="30%" style="vertical-align: top">Client <br>
                    <b>{{ $invoice->client_vendor->name ?? '' }}</b>
                </td>
                <td></td>
            </tr>
            <td style="vertical-align: top">Payment Status <br>
                @if ($invoice->payment_status == 'Unpaid')
                    <span class="badge bg-warning" style="font-size: 13px">{{ $invoice->payment_status }}</span>
                @elseif($invoice->payment_status == 'Partially Paid')
                    <span class="badge bg-primary" style="font-size: 13px">{{ $invoice->payment_status }}</span>
                @elseif($invoice->payment_status == 'Paid')
                    <span class="badge bg-success" style="font-size: 13px">{{ $invoice->payment_status }}</span>
                @endif
            </td>
        </table>
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        Notes : <br>
        {!! nl2br(e($invoice->notes)) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        <table class="table mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col" style="width: 5%">#</th>
                    <th scope="col">Item</th>
                    <th scope="col" style="width: 10%; text-align: right">Qty</th>
                    <th scope="col" style="width: 13%; text-align: right">Price</th>
                    <th scope="col" style="width: 13%; text-align: right">Discount</th>
                    <th scope="col" style="width: 13%; text-align: right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @isset($invoice->contract_id)
                    @if (($contract->service->type ?? null) === 'Unit Rental' || ($contract->service->type ?? null) === 'Fuel Truck Rental')
                        @foreach ($proforma_invoice as $pi)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>FIX MONTHLY FEE
                                    ({{ $pi->unit->vehicle_no ?? '-' }})
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format(1, 2) }}
                                </td>
                                <td style="text-align: right">
                                    {{ $pi->price ? Number::format($pi->price, precision: 0) : 0 }}
                                </td>
                                <td style="text-align: right">
                                    0
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($pi->total ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @elseif(($contract->service->type ?? null) === 'LCT')
                        @foreach ($contract_rate as $rate)
                            @php
                                $proforma_invoice_id = $proforma_invoice->pluck('id');
                                $qty = Proforma_invoice_detail::whereIn('proforma_invoice_id', $proforma_invoice_id)
                                    ->where('contract_rate_id', $rate->id)
                                    ->sum('qty');
                                $rate = Proforma_invoice_detail::whereIn('proforma_invoice_id', $proforma_invoice_id)
                                    ->where('contract_rate_id', $rate->id)
                                    ->sum('rate');
                                $amount = Proforma_invoice_detail::whereIn('proforma_invoice_id', $proforma_invoice_id)
                                    ->where('contract_rate_id', $rate->id)
                                    ->sum('amount');
                            @endphp
                            <tr>
                                <td>
                                    {{ $loop->iteration }}</td>
                                <td>FIX MONTHLY FEE
                                    ({{ $pi->unit->vehicle_no ?? '-' }})
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($qty, 2) }}
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($rate ?? 0, 2) }}
                                </td>
                                <td style="text-align: right">
                                    0
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($amount ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @elseif(($contract->service->type ?? null) === 'Explosive Material Transport')
                        @foreach ($contract_rate as $contractrate)
                            @php
                                $qty = Proforma_invoice_detail::whereIn('proforma_invoice_id', $proforma_invoice_id)
                                    ->where('contract_rate_id', $contractrate->id)
                                    ->sum('qty');
                                $rate = Proforma_invoice_detail::whereIn('proforma_invoice_id', $proforma_invoice_id)
                                    ->where('contract_rate_id', $contractrate->id)
                                    ->sum('rate');
                                $amount = 0;
                                $amount = Proforma_invoice_detail::whereIn('proforma_invoice_id', $proforma_invoice_id)
                                    ->where('contract_rate_id', $contractrate->id)
                                    ->sum('amount');
                            @endphp
                            <tr>
                                <td>
                                    {{ $loop->iteration }}</td>
                                <td>FIX MONTHLY FEE
                                    {{ $contractrate->service_item ?? '-' }}
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($qty, 2) }}
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($rate ?? 0, 2) }}
                                </td>
                                <td style="text-align: right">
                                    0
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($amount ?? 0, 2) }}
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
                            <tr>
                                <td>
                                    {{ $loop->iteration }}</td>
                                <td> {{ $pid?->service_item ?? '-' }}
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($pid?->qty ?? 0, 2) }}
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($pid->rate ?? 0, 2) }}
                                </td>
                                <td style="text-align: right">
                                    0
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($pid?->amount ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @elseif(($contract->service->type ?? null) === 'Survey')
                        @php
                            $proforma_invoice_detail = Proforma_invoice_detail::whereIn(
                                'proforma_invoice_id',
                                $proforma_invoice_id,
                            )->get();
                        @endphp
                        @foreach ($proforma_invoice_detail as $pid)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}</td>
                                <td> {{ $pid?->service_item ?? '-' }}
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($pid?->qty ?? 0, 2) }}
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($pid->rate ?? 0, 2) }}
                                </td>
                                <td style="text-align: right">
                                    0
                                </td>
                                <td style="text-align: right">
                                    {{ Number::format($pid?->amount ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @else
                    @foreach ($invoice->invoice_detail as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->service_item }}</td>
                            <td style="text-align: right">{{ $d->qty ? Number::format($d->qty, precision: 0) : 0 }}
                            </td>
                            <td style="text-align: right">
                                {{ $d->price ? Number::format($d->price, precision: 0) : 2 }}
                            </td>
                            <td style="text-align: right">
                                {{ $d->discount_item ? Number::format($d->discount_item, precision: 2) : 0 }}</td>
                            <td style="text-align: right">
                                {{ $d->amount ? Number::format($d->amount, precision: 0) : 2 }}
                            </td>
                        </tr>
                    @endforeach
                @endisset
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align:right" colspan="5"><b>Total</b></td>
                    <td style="text-align:right">
                        {{ $invoice->total ? Number::format($invoice->total, precision: 2) : 0 }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right" colspan="5"><b>Discount</b></td>
                    <td style="text-align:right">
                        {{ $invoice->discount ? Number::format($invoice->discount, precision: 2) : 0 }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right" colspan="5"><b>Tax
                            ({{ (int) $invoice?->tax === 0 ? 'Non PKP' : 'PKP' }})</b></td>
                    <td style="text-align:right">
                        {{ $invoice->tax ? Number::format($invoice->tax, precision: 2) : 0 }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right" colspan="5"><b>Grand Total</b></td>
                    <td style="text-align:right">
                        {{ $invoice->grand_total ? Number::format($invoice->grand_total, precision: 2) : 0 }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="row mb-2">
    <div class="col">
        @php
            $html = '<table style="width: 100%">';
            if ($invoice->invoice_path !== null) {
                $html .= '<tr>';
                $html .= '<td>';
                $html .=
                    '<a href="' .
                    route('invoice.export_file', $invoice->id) .
                    '" target="_blank">' .
                    $invoice->real_name .
                    '</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else {
                $html .= '<tr><td class="text-center">No quotation file</td></tr>';
            }
            $html .= '</table>';
            echo $html;
        @endphp
    </div>
</div>

@empty(!$approval_process)
    <table style="border-collapse:separate; border-spacing:0;" class="mb-4">
        <tr>
            <td style="vertical-align: top" colspan="3">
                <b>
                    <h6 style="border-bottom: 1px solid #000; display: inline-block;">
                        Approval Progress
                    </h6>
                </b>
            </td>
        </tr>
        @foreach ($approval_process as $d)
            <tr>
                <td style="vertical-align: top">
                    <b>{{ $d->approval_step->order }}. {{ $d->user->name }}</b> &nbsp;
                </td>
                <td>
                    @if ($d->action == 'Approved')
                        <span class="badge bg-success" style="font-size: 13px">{{ $d->action }}</span>
                    @elseif($d->action == 'Rejected')
                        <span class="badge bg-danger" style="font-size: 13px">{{ $d->action }}</span>
                    @elseif($d->action == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $d->action }}</span>
                    @else
                        <span class="badge bg-secondary" style="font-size: 13px">{{ $d->action }}</span>
                    @endif
                </td>
                <td>
                    @if ($d->action == 'Approved')
                        {{ Carbon::parse($d->updated_at)->translatedFormat('d F Y H:i') }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endempty

@isset($invoice->contract_id)
    <table style="border-collapse:separate; border-spacing:0;">
        <tr>
            <td style="vertical-align: top" colspan="3">
                <b>
                    <h6 style="border-bottom: 1px solid #000; display: inline-block;">
                        Document Progress
                    </h6>
                </b>
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Cut Off Date
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->cut_off_date ? Carbon::parse($invoice->cut_off_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Konsolidasi Data TMS & CMD
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->consolidation_date ? Carbon::parse($invoice->consolidation_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Kirim Progress Claim Approval
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->progress_claim_date ? Carbon::parse($invoice->progress_claim_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Data Terima Dari OPS
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->ops_received_date ? Carbon::parse($invoice->ops_received_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Proforma Invoice Approved
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->prof_inv_app_date ? Carbon::parse($invoice->prof_inv_app_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Minta CIC
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->cic_request_date ? Carbon::parse($invoice->cic_request_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Pembuatan CIC
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->cic_created_date ? Carbon::parse($invoice->cic_created_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Tanggal Invoice
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->inv_date ? Carbon::parse($invoice->inv_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Pembuatan Invoice
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->inv_create_date ? Carbon::parse($invoice->inv_create_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Kirim CIC Ke KPC
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->cic_send_date ? Carbon::parse($invoice->cic_send_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Informasi CIC Bisa Diambil
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->cic_ready_to_pick_date ? Carbon::parse($invoice->cic_ready_to_pick_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                CIC Diambil TMS
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->cic_pick_up_date ? Carbon::parse($invoice->cic_pick_up_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td style="vertical-align: top">
                Invoice Terima KPC
            </td>
            <td> &nbsp;:&nbsp;</td>
            <td>
                {{ $invoice->inv_send_date ? Carbon::parse($invoice->inv_send_date)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
    </table>
@endisset
