@php
    use Illuminate\Support\Number;

    $total_item = $p2h->p2h_detail()->count();
    $total_broken = $p2h->p2h_detail()->where('check', 1)->count();
    $total_good = $p2h->p2h_detail()->where('check', 0)->count();
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;"">
            <tr>
                <td width="30%">Number <br>
                    <b>{{ $p2h->p2h_no }}</b>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td width="30%">Unit <br>
                    <b>{{ $p2h->unit->vehicle_no }}</b>
                </td>
                <td width="30%">Driver <br>
                    <b>{{ $p2h->driver }}</b>
                </td>
                <td width="30%">Shift <br>
                    <b> {{ $p2h->shift }}</b>
                </td>
            </tr>
            <tr>
                <td width="30%">Date <br>
                    <b>{{ $p2h->date }}</b>
                </td>
                <td width="30%">KM Start <br>
                    <b>{{ Number::format($p2h->km_start, precision: 2) }}</b>
                </td>
                <td width="30%">KM Finish <br>
                    <b> {{ Number::format($p2h->km_finish, precision: 2) }}</b>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    Summary <br>
                    <table>
                        <tr>
                            <td><span class="badge bg-success" style="font-size: 12px"> Good &#10003;</span>
                            </td>
                            <td style="text-align: right"><b>{{ $total_good }}</b></td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger" style="font-size: 12px"> Broken &#10007;</span></td>
                            <td style="text-align: right"><b>{{ $total_broken }}</b></td>
                        </tr>
                    </table>
                </td>
                <td colspan="2" style="vertical-align: top">
                    <table>
                        <tr>
                            <td>
                                Condition <br>
                                @php
                                    $span = '';
                                    $percentage = ($total_good / $total_item) * 100;

                                    if ($percentage >= 80):
                                        $span = 'badge bg-success'; // 0-19.99%
                                    elseif ($percentage >= 60):
                                        $span = 'badge bg-primary';
                                    elseif ($percentage >= 40):
                                        $span = 'badge bg-info';
                                    elseif ($percentage >= 20):
                                        $span = 'badge bg-warning';
                                    else:
                                        $span = 'badge bg-danger';
                                    endif;
                                @endphp
                                <b>
                                    <span class="{{ $span }}" style="font-size: 12px">
                                        {{ Number::format(($total_good / $total_item) * 100, precision: 0) }}%
                                    </span>
                                </b>
                            </td>
                        </tr>
                    </table>
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
                    <th scope="col" class="text-center" style="width:60px;">Broken</th>
                    <th scope="col">Defect Listed</th>
                    <th scope="col">Action Taken</th>
                </tr>
            </thead>
            <tbody>
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

                            @php $check @endphp
                            @if ($p2h_detail)
                                @php
                                    $check = $p2h_detail->check;
                                    $defect_listed = $p2h_detail->defect_listed;
                                    $action_taken = $p2h_detail->action_taken;
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
                                {!! $defect_listed !!}
                            </td>

                            <td class="p-1 align-middle">
                                {!! $action_taken !!}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
