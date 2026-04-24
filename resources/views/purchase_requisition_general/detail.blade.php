@php
    use Illuminate\Support\Number;
    use Carbon\Carbon;
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="30%">Number <br>
                    <b>{{ $purchase_requisition->requsition_no }}</b>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td width="30%">Department <br>
                    <b>{{ $purchase_requisition->department }}</b>
                </td>
                <td width="30%">Date <br>
                    <b>{{ $purchase_requisition->date }}</b>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        <table class="table mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col" style="width: 5%">#</th>
                    <th scope="col">Description</th>
                    <th scope="col" style="width: 15%">Uom</th>
                    <th scope="col" style="width: 15%; text-align: right">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase_requisition_detail as $d)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $d->description }}</td>
                        <td>{{ $d->uom }}</td>
                        <td style="text-align: right">{{ $d->qty ? Number::format($d->qty, precision: 0) : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        Notes : <br>
        {!! $purchase_requisition->notes !!}
    </div>
</div>
