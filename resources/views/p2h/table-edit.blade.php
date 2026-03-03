@php
    $no = 1;
@endphp

@foreach ($p2hitem as $group => $items)
    <tr class="table-secondary">
        <th colspan="5" class="align-middle">{{ $group }}</th>
    </tr>
    @foreach ($items as $idx => $item)
        @php
            $p2h_detail = $p2h
                ->p2h_detail()
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
                $defect_listed = '';
                $action_taken = '';
            @endphp
            @if ($p2h_detail)
                @php
                    $check = $p2h_detail->check;
                    $defect_listed = $p2h_detail->defect_listed;
                    $action_taken = $p2h_detail->action_taken;
                    $p2h_id = $p2h_detail->id;
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
                <input type="text" class="form-control" name="defect_listed[]" id="defect-{{ $rowId }}"
                    value="{{ $defect_listed }}">
            </td>

            <td class="p-1 align-middle">
                <input type="text" class="form-control" name="action_taken[]" id="action-{{ $rowId }}"
                    value="{{ $action_taken }}">
            </td>
        </tr>
    @endforeach
@endforeach
