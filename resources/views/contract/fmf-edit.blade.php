@php
    use Illuminate\Support\Number;
@endphp

@foreach ($contract_fmf as $d)
    <tr>
        <td class="p-1 align-middle row-number">
            {{ $loop->iteration }}
        </td>
        <td class="p-1 align-middle">
            <input type="text" class="form-control" id="year" name="year[]" readonly value="{{ $d->year }}">
        </td>
        <td class="p-1 align-middle">
            <input type="hidden" class="form-control" id="value_fmf" name="value_fmf[]" readonly
                value="{{ $d->value }}">
            <input type="text" class="form-control" id="_value_fmf" name="_value_fmf[]" readonly
                value="{{ $d->value ? Number::format($d->value, precision: 0) : '' }}">
        </td>
        <td class="text-center p-1 align-middle">
            <div class="row row-cols-auto g-3">
                <div class="col">
                    <button type="button" class="btn btn-lg btn-danger bx bx-trash mr-1 delete-row  "
                        id="removeFmfButton"></button>
                </div>
            </div>
        </td>
    </tr>
@endforeach
