@php
    use Illuminate\Support\Number;
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="30%">Number <br>
                    <b>{{ $maintenance->maintenance_no }}</b>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td width="30%">Unit <br>
                    <b>{{ $maintenance->unit->vehicle_no }}</b>
                </td>
                <td width="30%">Date <br>
                    <b>{{ $maintenance->date }}</b>
                </td>
                <td width="30%">Vendor <br>
                    <b> {{ $maintenance->client_vendor->name }}</b>
                </td>
            </tr>
            <tr>
                <td width="30%">Mechanic <br>
                    <b>{{ $maintenance->mechanic }}</b>
                </td>
                <td width="30%">Hour Meter <br>
                    <b>{{ Number::format($maintenance->hour_meter ?? 0, precision: 0) }}</b>
                </td>
                <td width="30%">KM/HM <br>
                    <b> {{ Number::format($maintenance->km_hm ?? 0, precision: 0) }}</b>
                </td>
            </tr>
            <tr>
                <td width="30%">Start <br>
                    <b>{{ \Carbon\Carbon::parse($maintenance->start)->format('H:i') }}</b>
                </td>
                <td width="30%">Finish <br>
                    <b>{{ \Carbon\Carbon::parse($maintenance->finish)->format('H:i') }}</b>
                </td>
                <td width="30%">Work Duration <br>
                    <b> {{ \Carbon\Carbon::parse($maintenance->work_duration)->format('H:i') }}</b>
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
                    <th scope="col" style="width: 17%">Action</th>
                    <th scope="col">Item</th>
                    @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('maintenance.cost'))
                        <th scope="col" style="width: 20%">Cost</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp

                @foreach ($maintenance_detail as $d)
                    <tr>
                        <td class="p-1 align-middle">{{ $no++ }}</td>

                        <td class="p-1 align-middle">
                            {{ $d->action }}
                        </td>

                        <td class="p-1 align-middle">
                            {{ $d->maintenance_item->name }}
                        </td>
                        @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('maintenance.cost'))
                            <td class="p-1 align-middle" style="text-align: right">
                                {{ Number::format($d->cost ?? 0, precision: 0) }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            @if (Auth::user()->hasRole('superadmin') || Auth::user()->hasPermissionTo('maintenance.cost'))
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right">Total</th>
                        <th style="text-align: right">
                            {{ Number::format($maintenance->cost_total ?? 0, precision: 0) }}
                        </th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
