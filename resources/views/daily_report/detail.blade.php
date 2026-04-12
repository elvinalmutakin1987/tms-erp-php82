@php
    use Illuminate\Support\Number;
    use Carbon\Carbon;
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="30%">Number <br>
                    <b>{{ $daily_report->report_no }}</b>
                </td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td width="30%">Unit <br>
                    <b>{{ $daily_report->unit->vehicle_no }}</b>
                </td>
                <td width="30%">Date <br>
                    <b>{{ $daily_report->date }}</b>
                </td>
                <td width="30%">Shift <br>
                    <b> {{ $daily_report->shift }}</b>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="row mb-2">
    <div class="col">
        @php
            $no = 1;
        @endphp
        @if ($daily_report->type == 'LCT')
            <table class="table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" colspan="6">Trip 1</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-secondary">
                        <th style="width: 50%" colspan="3" class="align-middle">Departure</th>
                        <th style="width: 50%" colspan="3" class="align-middle">Arrival</th>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            Location
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_1_location->name }}
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">
                            Location
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_1_arr_location->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            Loading At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_1_loading_at ? Carbon::parse($daily_report->trip_1_loading_at)->format('H:i') : '' }}
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">
                            Arrived At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_1_arrived_at ? Carbon::parse($daily_report->trip_1_arrived_at)->format('H:i') : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            Complete Loading At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_1_complete_loading_at ? Carbon::parse($daily_report->trip_1_complete_loading_at)->format('H:i') : '' }}
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">
                            Berthing At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            <b>
                                {{ $daily_report->trip_1_berthing_at ? Carbon::parse($daily_report->trip_1_berthing_at)->format('H:i') : '' }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            Departed At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            <b>{{ $daily_report->trip_1_departed_at ? Carbon::parse($daily_report->trip_1_departed_at)->format('H:i') : '' }}</b>
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">

                        </td>
                        <td class="p-1 align-middle" style="width: 5px">

                        </td>
                        <td class="p-1 align-middle">

                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            <b>Duration</b>
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            <b>:</b>
                        </td>
                        <td class="p-1 align-middle">
                            <b>{{ $daily_report->duration_trip_1 ? Carbon::parse($daily_report->duration_trip_1)->format('H:i') : '' }}</b>
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">

                        </td>
                        <td class="p-1 align-middle" style="width: 5px">

                        </td>
                        <td class="p-1 align-middle">

                        </td>
                    </tr>
                </tbody>
                <thead class="table-dark">
                    <tr>
                        <th scope="col" colspan="6">Trip 2</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-secondary">
                        <th style="width: 50%" colspan="3" class="align-middle">Departure</th>
                        <th style="width: 50%" colspan="3" class="align-middle">Arrival</th>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            Location
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_2_location->name }}
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">
                            Location
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_2_arr_location->name }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            Loading At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_2_loading_at ? Carbon::parse($daily_report->trip_2_loading_at)->format('H:i') : '' }}
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">
                            Arrived At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_2_arrived_at ? Carbon::parse($daily_report->trip_2_arrived_at)->format('H:i') : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            Complete Loading At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->trip_2_complete_loading_at ? Carbon::parse($daily_report->trip_2_complete_loading_at)->format('H:i') : '' }}
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">
                            Berthing At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            <b>{{ $daily_report->trip_2_berthing_at ? Carbon::parse($daily_report->trip_2_berthing_at)->format('H:i') : '' }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            Departed At
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            <b>{{ $daily_report->trip_2_departed_at ? Carbon::parse($daily_report->trip_2_departed_at)->format('H:i') : '' }}</b>
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">

                        </td>
                        <td class="p-1 align-middle" style="width: 5px">

                        </td>
                        <td class="p-1 align-middle">

                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            <b>Duration</b>
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            <b>:</b>
                        </td>
                        <td class="p-1 align-middle">
                            <b>{{ $daily_report->duration_trip_2 ? Carbon::parse($daily_report->duration_trip_2)->format('H:i') : '' }}</b>
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">

                        </td>
                        <td class="p-1 align-middle" style="width: 5px">

                        </td>
                        <td class="p-1 align-middle">

                        </td>
                    </tr>
                    <tr style='background-color: #FAF6F5'>
                        <td class="p-1 align-middle" style="width: 15%">
                            <b>Duration Total</b>
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            <b>:</b>
                        </td>
                        <td class="p-1 align-middle">
                            <b>{{ addTime($daily_report->duration_trip_1, $daily_report->duration_trip_2) }}</b>
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">

                        </td>
                        <td class="p-1 align-middle" style="width: 5px">

                        </td>
                        <td class="p-1 align-middle">

                        </td>
                    </tr>
                </tbody>
                <thead class="table-dark">
                    <tr>
                        <th scope="col" colspan="6">Refule</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            From
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->refule_type }}
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">

                        </td>
                        <td class="p-1 align-middle" style="width: 5px">

                        </td>

                        <td class="p-1 align-middle">

                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            KM
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->refule_km ? Number::format($daily_report->refule_km, precision: 0) : '' }}
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">

                        </td>
                        <td class="p-1 align-middle" style="width: 5px">

                        </td>

                        <td class="p-1 align-middle">

                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle" style="width: 15%">
                            From
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>

                        <td class="p-1 align-middle">
                            {{ $daily_report->refule_liter ? Number::format($daily_report->refule_liter, precision: 0) : '' }}
                        </td>

                        <td class="p-1 align-middle" style="width: 15%">

                        </td>
                        <td class="p-1 align-middle" style="width: 5px">

                        </td>

                        <td class="p-1 align-middle">

                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 5px">#</th>
                        <th scope="col">Unit</th>
                        <th scope="col">Item</th>
                        <th scope="col" style="width: 12%">Uom 1</th>
                        <th scope="col" style="width: 12%">Value 1</th>
                        <th scope="col" style="width: 12%">Uom 2</th>
                        <th scope="col" style="width: 12%">Value 2</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daily_report_detail as $d)
                        <tr>
                            <td class="p-1 align-middle">
                                {{ $loop->iteration }}
                            </td>
                            <td class="p-1 align-middle">
                                {{ $d->unit->vehicle_no }}
                            </td>
                            <td class="p-1 align-middle">
                                {{ $d->item }}
                            </td>
                            <td class="p-1 align-middle">
                                {{ $d->uom_1 }}
                            </td>
                            <td class="p-1 align-middle">
                                {{ $d->value_1 ? Number::format($d->value_1, precision: 2) : '' }}
                            </td>
                            <td class="p-1 align-middle">
                                {{ $d->uom_2 }}
                            </td>
                            <td class="p-1 align-middle">
                                {{ $d->value_2 ? Number::format($d->value_2, precision: 2) : '' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table class="table mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 5px">#</th>
                        <th scope="col" style="width: 15%">Item</th>
                        <th scope="col" colspan="2">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-secondary">
                        <th colspan="4" class="align-middle">KM</th>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle">
                            1
                        </td>
                        <td class="p-1 align-middle">
                            Start
                        </td>
                        <td class="p-1 align-middle" style="width: 5px">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->km_start ? Number::format($daily_report->km_start, precision: 0) : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle">
                            2
                        </td>
                        <td class="p-1 align-middle">
                            Finish
                        </td>
                        <td class="p-1 align-middle">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->km_finish ? Number::format($daily_report->km_finish, precision: 0) : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle">
                            3
                        </td>
                        <td class="p-1 align-middle">
                            Total
                        </td>
                        <td class="p-1 align-middle">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->km_total ? Number::format($daily_report->km_total, precision: 0) : '' }}
                        </td>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="4" class="align-middle">Person</th>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle">
                            4
                        </td>
                        <td class="p-1 align-middle">
                            Operator
                        </td>
                        <td class="p-1 align-middle">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->operator }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle">
                            5
                        </td>
                        <td class="p-1 align-middle">
                            Helper
                        </td>
                        <td class="p-1 align-middle">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->helper }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle">
                            6
                        </td>
                        <td class="p-1 align-middle">
                            Load
                        </td>
                        <td class="p-1 align-middle">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->load ? Number::format($daily_report->load, precision: 0) : '' }}
                        </td>
                    </tr>
                    <tr class="table-secondary">
                        <th colspan="4" class="align-middle">Refule</th>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle">
                            7
                        </td>
                        <td class="p-1 align-middle">
                            From
                        </td>
                        <td class="p-1 align-middle">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->refule_liter || $daily_report->refule_km ? $daily_report->operator : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle">
                            8
                        </td>
                        <td class="p-1 align-middle">
                            Liter
                        </td>
                        <td class="p-1 align-middle">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->refule_liter ? Number::format($daily_report->refule_liter, precision: 0) : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="p-1 align-middle">
                            9
                        </td>
                        <td class="p-1 align-middle">
                            KM
                        </td>
                        <td class="p-1 align-middle">
                            :
                        </td>
                        <td class="p-1 align-middle">
                            {{ $daily_report->refule_km ? Number::format($daily_report->refule_km, precision: 0) : '' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>
