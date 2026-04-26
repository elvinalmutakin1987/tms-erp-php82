@php
    use Illuminate\Support\Number;
@endphp

@foreach ($unit_target as $d)
    <tr>
        <td class="p-1 align-middle row-number">
            {{ $loop->iteration }}
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control" id="unit_id" name="unit_id[]" readonly value="{{ $d->unit_id }}">
            <input type="text" class="form-control" id="unit_name" name="unit_name[]" readonly
                value="{{ $d->unit->vehicle_no }}">
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control" id="target" name="target[]" readonly
                value="{{ $d->target }}">
            <input type="text" class="form-control" id="_target" name="_target[]" readonly
                value="{{ $d->target ? Number::format($d->target, precision: 0) : '' }}">
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control" id="price" name="price[]" readonly
                value="{{ $d->price }}">
            <input type="text" class="form-control" id="_price" name="_price[]" readonly
                value="{{ $d->price ? Number::format($d->price, precision: 0) : '' }}">
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
