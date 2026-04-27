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
                    <th scope="col" style="width: 10%;">Qty</th>
                    <th scope="col" style="width: 15%;">Received At</th>
                    <th scope="col" style="width: 15%;">Received By</th>
                    <th scope="col" style="width: 25%;">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase_requisition_detail as $d)
                    <tr>
                        <td class="p-1 align-middle">{{ $loop->iteration }}</td>
                        <td class="p-1 align-middle">
                            <input type="hidden" id="purchase_requisition_detail_id"
                                name="purchase_requisition_detail_id[]" value="{{ $d->id }}">
                            <input type="text" class="form-control" readonly value=" {{ $d->description }}">
                        </td>
                        <td class="p-1 align-middle">
                            <input type="text" class="form-control" readonly
                                value=" {{ $d->qty ? Number::format($d->qty, precision: 0) : '' }}">
                        </td>
                        <td class="p-1 align-middle">
                            <input type="text" class="form-control datepicker" id="received_at{{ $d->id }}"
                                name="received_at[]" value="{{ $d->received_at }}">
                        </td>
                        <td class="p-1 align-middle">
                            <input type="text" class="form-control" id="received_by{{ $d->id }}"
                                name="received_by[]" value="{{ $d->received_by }}">
                        </td>
                        <td class="p-1 align-middle">
                            <input type="text" class="form-control" id="received_note{{ $d->id }}"
                                name="received_note[]" value="{{ $d->received_note }}">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".datepicker").flatpickr();
    });
</script>
