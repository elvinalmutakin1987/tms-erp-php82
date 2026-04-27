@php
    use Illuminate\Support\Number;
    use Carbon\Carbon;
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="30%">Number <br>
                    <b>{{ $purchase_requisition->requisition_no }}</b>
                </td>
                <td colspan="2">Status <br>
                    @if ($purchase_requisition->status == 'Draft')
                        <span class="badge bg-secondary"
                            style="font-size: 13px">{{ $purchase_requisition->status }}</span>
                    @elseif($purchase_requisition->status == 'Approval')
                        <span class="badge bg-info" style="font-size: 13px">{{ $purchase_requisition->status }}</span>
                    @elseif($purchase_requisition->status == 'Open')
                        <span class="badge bg-primary" style="font-size: 13px">{{ $purchase_requisition->status }}</span>
                    @elseif(
                        $purchase_requisition->status == 'Done' ||
                            $purchase_requisition->status == 'Approved' ||
                            $purchase_received->status == 'Received')
                        <span class="badge bg-success"
                            style="font-size: 13px">{{ $purchase_requisition->status }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td width="30%">Unit <br>
                    <b>{{ $purchase_requisition->unit->vehicle_no }}</b>
                </td>
                <td width="30%">Date <br>
                    <b>{{ $purchase_requisition->date }}</b>
                </td>
                <td width="30%">Maintenance No. <br>
                    <b> {{ $purchase_requisition->maintenance->maintenance_no }}</b>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        Notes : <br>
        {!! $purchase_requisition->notes !!}
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        <table class="table mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col" style="width: 5%">#</th>
                    <th scope="col">Maintenance Item</th>
                    <th scope="col">MRO Item</th>
                    <th scope="col" style="width: 10%">Uom</th>
                    <th scope="col" style="width: 12%; text-align: right">Qty</th>
                    <th scope="col" style="width: 15%; text-align: right">Price</th>
                    <th scope="col" style="width: 15%; text-align: right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase_requisition_detail as $d)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $d->maintenance_item->name }}</td>
                        <td>{{ $d->mro_item->name }}</td>
                        <td>{{ $d->uom }}</td>
                        <td style="text-align: right">{{ $d->qty ? Number::format($d->qty, precision: 0) : '' }}</td>
                        <td style="text-align: right">{{ $d->price ? Number::format($d->price, precision: 0) : '' }}
                        </td>
                        <td style="text-align: right">{{ $d->amount ? Number::format($d->amount, precision: 0) : '' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align:right" colspan="6"><b>Total</b></td>
                    <td style="text-align:right">
                        {{ $purchase_requisition->total ? Number::format($purchase_requisition->total, precision: 0) : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right" colspan="6"><b>Tax</b></td>
                    <td style="text-align:right">
                        {{ $purchase_requisition->tax ? Number::format($purchase_requisition->tax, precision: 0) : '' }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:right" colspan="6"><b>Grand Total</b></td>
                    <td style="text-align:right">
                        {{ $purchase_requisition->grand_total ? Number::format($purchase_requisition->grand_total, precision: 0) : '' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
