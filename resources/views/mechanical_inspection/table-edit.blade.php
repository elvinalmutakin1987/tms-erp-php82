@php
    $no = 1;
@endphp

@foreach ($inspection_item as $group => $items)
    <tr class="table-secondary">
        <th colspan="5" class="align-middle">{{ $group }}</th>
    </tr>
    @foreach ($items as $idx => $item)
        @php
            $inspection_detail = $mechanical_inspection
                ->mechanical_inspection_detail()
                ->where([
                    'inspection_group' => $group,
                    'inspection_item' => $item,
                ])
                ->first();
        @endphp
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

            @php
                $check = 0;
                $remarks = '';
                $inspected_by = '';
                $mechanical_inspection_detail_id = '';
            @endphp
            @if ($inspection_detail)
                @php
                    $check = $inspection_detail->check;
                    $remarks = $inspection_detail->remarks;
                    $inspected_by = $inspection_detail->insepcted_by;
                    $mechanical_inspection_detail_id = $inspection_detail->id;
                @endphp
            @endif

            <td class="p-1 align-middle text-center" style="width:60px;">
                <div class="d-flex justify-content-center align-items-center h-100 form-check-danger">
                    <input type="hidden" name="check[{{ $item }}][{{ $group }}]" value="0">

                    <input class="form-check-input m-0" type="checkbox"
                        name="check[{{ $item }}][{{ $group }}]" value="1"
                        id="check-{{ $rowId }}" {{ $check == 1 ? 'checked' : '' }}>
                </div>
            </td>

            <td class="p-1 align-middle">
                <input type="text" class="form-control" name="remarks[]" id="remarks-{{ $rowId }}"
                    value="{{ $remarks }}">
            </td>

            <td class="p-1 align-middle">
                <input type="text" class="form-control" name="inspected_by[]" id="inspected-{{ $rowId }}"
                    value="{{ $inspected_by }}">
            </td>
        </tr>
    @endforeach
@endforeach
