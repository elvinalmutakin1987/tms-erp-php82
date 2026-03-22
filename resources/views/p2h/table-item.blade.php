@php
    $no = 1;
@endphp

@foreach ($item as $group => $items)
    <tr class="table-secondary">
        <th colspan="5" class="align-middle">{{ $group }}</th>
    </tr>
    @foreach ($items as $idx => $item)
        @php
            $rowId = \Illuminate\Support\Str::slug($group) . '-' . $idx;
        @endphp

        <tr>
            <td class="p-1 align-middle">{{ $no++ }}</td>

            <td class="p-1 align-middle">
                {{ $item }}

                <input type="hidden" name="inspection_group[]" value="{{ $group }}">
                <input type="hidden" name="inspection_item[]" value="{{ $item }}">
            </td>

            <td class="p-1 align-middle text-center" style="width:60px;">
                <div class="d-flex justify-content-center align-items-center h-100 form-check-danger">
                    <input type="hidden" name="check[{{ $item }}][{{ $group }}]" value="0">

                    <input class="form-check-input m-0" type="checkbox"
                        name="check[{{ $item }}][{{ $group }}]" value="1"
                        id="check-{{ $rowId }}">
                </div>
            </td>

            <td class="p-1 align-middle">
                <input type="text" class="form-control" name="defect_listed[]" id="defect-{{ $rowId }}">
            </td>

            <td class="p-1 align-middle">
                <input type="text" class="form-control" name="action_taken[]" id="action-{{ $rowId }}">
            </td>
        </tr>
    @endforeach
@endforeach
