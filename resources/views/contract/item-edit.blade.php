@php
    use Illuminate\Support\Number;
@endphp

@foreach ($contract_rate as $d)
    <tr>
        <td class="p-1 align-middle row-number">
            {{ $loop->iteration }}
        </td>
        <td class="p-1 align-middle">
            <input type="text" class="form-control" id="item_no" name="item_no[]" readonly value="{{ $d->item_no }}">
        </td>
        <td class="p-1 align-middle">
            <input type="text" class="form-control" id="service_item" name="service_item[]" readonly
                value="{{ $d->service_item }}">
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control" id="rate" name="rate[]" readonly
                value="{{ $d->rate }}">
            <input type="text" class="form-control" id="_rate" name="_rate[]" readonly
                value="{{ $d->rate ? Number::format($d->rate, precision: 0) : '' }}">
        </td>
        <td class="text-center p-1 align-middle">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row  "
                        id="removeItemButton"></button>
                </div>
            </div>
        </td>
    </tr>
@endforeach
