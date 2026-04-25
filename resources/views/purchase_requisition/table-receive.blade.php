@php
    use Illuminate\Support\Number;
    use Carbon\Carbon;
@endphp
<form enctype="multipart/form-data">
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
                        <th scope="col" style="width: 10%;">Received At</th>
                        <th scope="col" style="width: 10%;">Received By</th>
                        <th scope="col" style="width: 25%;">Note</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase_requisition_detail as $d)
                        <tr>
                            <td class="p-1 align-middle">{{ $loop->iteration }}</td>
                            <td class="p-1 align-middle">
                                <input type="text" class="form-control" readonly
                                    value=" {{ $d->maintenance_item->name }}">
                            </td>
                            <td class="p-1 align-middle">
                                <input type="text" class="form-control" readonly value=" {{ $d->mro_item->name }}">
                            </td>
                            <td class="p-1 align-middle">
                                <input type="text" class="form-control" id="received_at{{ $d->id }}"
                                    name="received_at[]" readonly value="{{ $d->received_at }}">
                            </td>
                            <td class="p-1 align-middle">
                                <input type="text" class="form-control" id="received_by{{ $d->id }}"
                                    name="received_by[]" readonly value="{{ $d->received_by }}">
                            </td>
                            <td class="p-1 align-middle">
                                <input type="text" class="form-control" id="received_note{{ $d->id }}"
                                    name="received_note[]" readonly value="{{ $d->received_note }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</form>
