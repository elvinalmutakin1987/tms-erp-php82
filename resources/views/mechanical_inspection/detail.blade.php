@php
    use Illuminate\Support\Number;

    $total_item = $mechanical_inspection->mechanical_inspection_detail()->count();
    $total_broken = $mechanical_inspection->mechanical_inspection_detail()->where('check', 1)->count();
    $total_good = $mechanical_inspection->mechanical_inspection_detail()->where('check', 0)->count();
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="30%">Number :<br>
                    <b>{{ $mechanical_inspection->inspection_no }}</b>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td width="30%">Unit :<br>
                    <b>{{ $mechanical_inspection->unit->vehicle_no }}</b>
                </td>
                <td width="30%">Date :<br>
                    <b>{{ $mechanical_inspection->date }}</b>
                </td>
                <td width="30%">Inspector :<br>
                    <b> {{ $mechanical_inspection->inspector }}</b>
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
                    <th scope="col" style="width:3%">#</th>
                    <th scope="col">Item</th>
                    <th scope="col" class="text-center" style="width:60px;">Condition</th>
                    <th scope="col">Remarks</th>
                    <th scope="col">Inspected By</th>
                </tr>
            </thead>
            <tbody>
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
                            @endphp
                            @if ($inspection_detail)
                                @php
                                    $check = $inspection_detail->check;
                                    $remarks = $inspection_detail->defect_listed;
                                    $inspected_by = $inspection_detail->action_taken;
                                @endphp
                            @endif

                            <td class="p-1 align-middle text-center" style="width:60px;">
                                @if ($check == 0)
                                    <span class="badge bg-success">&#10003;</span>
                                @else
                                    <span class="badge bg-danger">&#10007;</span>
                                @endif
                            </td>

                            <td class="p-1 align-middle">
                                {!! $remarks !!}
                            </td>

                            <td class="p-1 align-middle">
                                {!! $inspected_by !!}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
