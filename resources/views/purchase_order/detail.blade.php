@php
    use Illuminate\Support\Number;
    use Carbon\Carbon;
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="30%">Number <br>
                    <b>{{ $purchase_order->order_no }}</b>
                </td>
                <td>Status <br>
                    @if ($purchase_order->status == 'Draft')
                        <span class="badge bg-secondary" style="font-size: 13px">{{ $purchase_order->status }}</span>
                    @elseif($purchase_order->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $purchase_order->status }}</span>
                    @elseif($purchase_order->status == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $purchase_order->status }}</span>
                    @elseif($purchase_order->status == 'Approved' || $purchase_order->status == 'Received')
                        <span class="badge bg-warning" style="font-size: 13px">{{ $purchase_order->status }}</span>
                    @elseif($purchase_order->status == 'Done')
                        <span class="badge bg-success" style="font-size: 13px">{{ $purchase_order->status }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td width="30%">Date <br>
                    <b>{{ $purchase_order->date }}</b>
                </td>
                <td width="30%">Requisition No. <br>
                    <b> {{ $purchase_order->purchase_requisition->requisition_no }}</b>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        Notes : <br>
        {!! nl2br(e($purchase_order->notes)) !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        @if ($purchase_order->type == 'General')
            <table class="table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 5%">#</th>
                        <th scope="col">Description</th>
                        <th scope="col" style="width: 10%">Uom</th>
                        <th scope="col" style="width: 10%; text-align: right">Qty</th>
                        <th scope="col" style="width: 13%; text-align: right">Price</th>
                        <th scope="col" style="width: 13%; text-align: right">Discount</th>
                        <th scope="col" style="width: 13%; text-align: right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase_order_detail as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->description }}</td>
                            <td>{{ $d->uom }}</td>
                            <td style="text-align: right">{{ $d->qty ? Number::format($d->qty, precision: 0) : 0 }}
                            </td>
                            <td style="text-align: right">
                                {{ $d->price ? Number::format($d->price, precision: 0) : 0 }}
                            </td>
                            <td style="text-align: right">
                                {{ $d->discount_item ? Number::format($d->discount_item, precision: 0) : 0 }}</td>
                            <td style="text-align: right">
                                {{ $d->amount ? Number::format($d->amount, precision: 0) : 0 }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="text-align:right" colspan="6"><b>Total</b></td>
                        <td style="text-align:right">
                            {{ $purchase_order->total ? Number::format($purchase_order->total, precision: 0) : 0 }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right" colspan="6"><b>Discount</b></td>
                        <td style="text-align:right">
                            {{ $purchase_order->discount ? Number::format($purchase_order->discount, precision: 0) : 0 }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right" colspan="6"><b>Tax</b></td>
                        <td style="text-align:right">
                            {{ $purchase_order->tax ? Number::format($purchase_order->tax, precision: 0) : 0 }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right" colspan="6"><b>Grand Total</b></td>
                        <td style="text-align:right">
                            {{ $purchase_order->grand_total ? Number::format($purchase_order->grand_total, precision: 0) : 0 }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        @else
            <table class="table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 5%">#</th>
                        <th scope="col">Maintenance Item</th>
                        <th scope="col">MRO Item</th>
                        <th scope="col" style="width: 10%">Uom</th>
                        <th scope="col" style="width: 10%; text-align: right">Qty</th>
                        <th scope="col" style="width: 13%; text-align: right">Price</th>
                        <th scope="col" style="width: 13%; text-align: right">Discount</th>
                        <th scope="col" style="width: 13%; text-align: right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase_order_detail as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $d->maintenance_item->name }}</td>
                            <td>{{ $d->mro_item->name }}</td>
                            <td>{{ $d->uom }}</td>
                            <td style="text-align: right">{{ $d->qty ? Number::format($d->qty, precision: 0) : 0 }}
                            </td>
                            <td style="text-align: right">
                                {{ $d->price ? Number::format($d->price, precision: 0) : 0 }}
                            </td>
                            <td style="text-align: right">
                                {{ $d->discount_item ? Number::format($d->discount_item, precision: 0) : 0 }}</td>
                            <td style="text-align: right">
                                {{ $d->amount ? Number::format($d->amount, precision: 0) : 0 }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td style="text-align:right" colspan="7"><b>Total</b></td>
                        <td style="text-align:right">
                            {{ $purchase_order->total ? Number::format($purchase_order->total, precision: 0) : 0 }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right" colspan="7"><b>Discount</b></td>
                        <td style="text-align:right">
                            {{ $purchase_order->discount ? Number::format($purchase_order->discount, precision: 0) : 0 }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right" colspan="7"><b>Tax</b></td>
                        <td style="text-align:right">
                            {{ $purchase_order->tax ? Number::format($purchase_order->tax, precision: 0) : 0 }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align:right" colspan="7"><b>Grand Total</b></td>
                        <td style="text-align:right">
                            {{ $purchase_order->grand_total ? Number::format($purchase_order->grand_total, precision: 0) : 0 }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        @php
            $html = '<table style="width: 100%">';
            if ($request_quotation->count() > 0) {
                foreach ($request_quotation->get() as $key => $value) {
                    $html .= '<tr>';
                    $html .= '<td>';
                    $html .=
                        '<a href="' .
                        route('purchaseorder.export_file', $value->id) .
                        '" target="_blank">' .
                        $value->real_name .
                        '</a>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
            } else {
                $html .= '<tr><td class="text-center">No quotation file</td></tr>';
            }
            $html .= '</table>';

            echo $html;
        @endphp
    </div>
</div>
