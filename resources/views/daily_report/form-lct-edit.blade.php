@php
    use Illuminate\Support\Number;
@endphp
<table class="table mb-0" id="tableItem">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:3%">#</th>
            <th scope="col" colspan="4">Trip 1</th>
        </tr>
    </thead>
    <tbody id="tbody">
        <tr class="table-secondary">
            <th colspan="3" class="align-middle">Departure</th>
            <th colspan="2" class="align-middle">Arrival</th>
        </tr>
        <tr>
            <td class="p-1 align-middle">1</td>
            <td class="p-1 align-middle">
                Location
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="trip_1_location_id" name="trip_1_location_id">
                    @foreach ($location as $d)
                        <option value="{{ $d->id }}"
                            {{ $d->id == $daily_report->trip_1_location_id ? 'selected' : '' }}>{{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="p-1 align-middle">
                Location
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="trip_1_arr_location_id" name="trip_1_arr_location_id">
                    @foreach ($location as $d)
                        <option value="{{ $d->id }}"
                            {{ $d->id == $daily_report->trip_1_arr_location_id ? 'selected' : '' }}>{{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">2</td>
            <td class="p-1 align-middle">
                Loading At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_1_loading_at" name="trip_1_loading_at"
                    value="{{ $daily_report?->trip_1_loading_at ?? '' }}">
            </td>
            <td class="p-1 align-middle">
                Arrived At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_1_arrived_at" name="trip_1_arrived_at"
                    value="{{ $daily_report?->trip_1_arrived_at ?? '' }}">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">3</td>
            <td class="p-1 align-middle">
                Complete Loading At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_1_complete_loading_at"
                    name="trip_1_complete_loading_at" value="{{ $daily_report?->trip_1_complete_loading_at ?? '' }}">
            </td>
            <td class="p-1 align-middle">
                Berthing At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_1_berthing_at" name="trip_1_berthing_at"
                    value="{{ $daily_report?->trip_1_berthing_at ?? '' }}">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">4</td>
            <td class="p-1 align-middle">
                Departed At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_1_departed_at" name="trip_1_departed_at"
                    value="{{ $daily_report?->trip_1_departed_at ?? '' }}">
            </td>
            <td class="p-1 align-middle">

            </td>
            <td class="p-1 align-middle">

            </td>
        </tr>
    </tbody>
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:3%">#</th>
            <th scope="col" colspan="4">Trip 2</th>
        </tr>
    </thead>
    <tbody id="tbody2">
        <tr class="table-secondary">
            <th colspan="3" class="align-middle">Departure</th>
            <th colspan="2" class="align-middle">Arrival</th>
        </tr>
        <tr>
            <td class="p-1 align-middle">5</td>
            <td class="p-1 align-middle">
                Location
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="trip_2_location_id" name="trip_2_location_id">
                    @foreach ($location as $d)
                        <option value="{{ $d->id }}"
                            {{ $d->id == $daily_report->trip_2_location_id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="p-1 align-middle">
                Location
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="trip_2_arr_location_id" name="trip_2_arr_location_id">
                    @foreach ($location as $d)
                        <option value="{{ $d->id }}"
                            {{ $d->id == $daily_report->trip_2_arr_location_id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">6</td>
            <td class="p-1 align-middle">
                Loading At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_2_loading_at" name="trip_2_loading_at"
                    value="{{ $daily_report?->trip_2_loading_at ?? '' }}">
            </td>
            <td class="p-1 align-middle">
                Arrived At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_2_arrived_at"
                    name="trip_2_arrived_at" value="{{ $daily_report?->trip_2_arrived_at ?? '' }}">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">7</td>
            <td class="p-1 align-middle">
                Complete Loading At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_2_complete_loading_at"
                    name="trip_2_complete_loading_at" value="{{ $daily_report?->trip_2_complete_loading_at ?? '' }}">
            </td>
            <td class="p-1 align-middle">
                Berthing At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_2_berthing_at"
                    name="trip_2_berthing_at" value="{{ $daily_report?->trip_2_berthing_at ?? '' }}">
            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">8</td>
            <td class="p-1 align-middle">
                Departed At
            </td>
            <td class="p-1 align-middle">
                <input type="text" class="form-control timepicker" id="trip_2_departed_at"
                    name="trip_2_departed_at" value="{{ $daily_report?->trip_2_departed_at ?? '' }}">
            </td>
            <td class="p-1 align-middle">

            </td>
            <td class="p-1 align-middle">

            </td>
        </tr>
    </tbody>
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:3%">#</th>
            <th scope="col" colspan="4">Remarks</th>
        </tr>
    </thead>
    <tbody id="tbody4">
        <tr>
            <td class="p-1 align-middle">9</td>
            <td class="p-1 align-middle">
                Remarks
            </td>
            <td class="p-1 align-middle" colspan="3">
                <textarea class="form-control" id="remarks" name="remarks" rows="5" required>{!! $daily_report?->remarks ?? '' !!}</textarea>
            </td>
        </tr>
    </tbody>
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width:3%">#</th>
            <th scope="col" colspan="4">Refule</th>
        </tr>
    </thead>
    <tbody id="tbody3">
        <tr>
            <td class="p-1 align-middle">10</td>
            <td class="p-1 align-middle">
                From
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="refule_type" name="refule_type">
                    <option value="KPC" value="{{ $daily_report->refule_type == 'KPC' ? 'selected' : '' }}">KPC
                    </option>
                    <option value="POM Bensin"
                        value="{{ $daily_report->refule_type == 'POM Bensin' ? 'selected' : '' }}">POM Bensin</option>
                </select>
            </td>
            <td class="p-1 align-middle">

            </td>
            <td class="p-1 align-middle">

            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">11</td>
            <td class="p-1 align-middle">
                Liter
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="refule_liter" name="refule_liter"
                    value="{{ $daily_report?->refule_liter ?? 0 }}">
                <input type="text" class="form-control" id="_refule_liter" name="_refule_liter"
                    value="{{ $daily_report?->refule_liter ? Number::format($daily_report?->refule_liter ?? 0, precision: 0) : '' }}">
            </td>
            <td class="p-1 align-middle">

            </td>
            <td class="p-1 align-middle">

            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">12</td>
            <td class="p-1 align-middle">
                KM
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="refule_km" name="refule_km"
                    value="{{ $daily_report?->refule_km ?? 0 }}">
                <input type="text" class="form-control" id="_refule_km" name="_refule_km"
                    value="{{ $daily_report?->refule_km ? Number::format($daily_report?->refule_km ?? 0, precision: 0) : '' }}">
            </td>
            <td class="p-1 align-middle">

            </td>
            <td class="p-1 align-middle">

            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">13</td>
            <td class="p-1 align-middle">
                Main Mechine
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="refule_mechine" name="refule_mechine"
                    value="{{ $daily_report?->refule_mechine ?? 0 }}">
                <input type="text" class="form-control" id="_refule_mechine" name="_refule_mechine"
                    value="{{ $daily_report?->refule_mechine ? Number::format($daily_report?->refule_mechine ?? 0, precision: 0) : '' }}">
            </td>
            <td class="p-1 align-middle">

            </td>
            <td class="p-1 align-middle">

            </td>
        </tr>
        <tr>
            <td class="p-1 align-middle">14</td>
            <td class="p-1 align-middle">
                Generator
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="refule_genset" name="refule_genset"
                    value="{{ $daily_report?->refule_genset ?? 0 }}">
                <input type="text" class="form-control" id="_refule_genset" name="_refule_genset"
                    value="{{ $daily_report?->refule_genset ? Number::format($daily_report?->refule_genset ?? 0, precision: 0) : '' }}">
            </td>
            <td class="p-1 align-middle">

            </td>
            <td class="p-1 align-middle">

            </td>
        </tr>
    </tbody>
</table>
<table class="table mb-0" id="tableUnit">
    <thead class="table-dark">
        <tr>
            <th scope="col" style="width: 2%">#</th>
            <th scope="col" style="width: 20%">Unit</th>
            <th scope="col" style="width: 25%">Item</th>
            <th scope="col" style="width: 11%">Uom 1</th>
            <th scope="col" style="width: 11%">Value 1</th>
            <th scope="col" style="width: 11%">Uom 2</th>
            <th scope="col" style="width: 11%">Value 2</th>
            <th scope="col" style="width: 2%">Action</th>
        </tr>
    </thead>
    <tbody id="tbody">
        <tr class="fixed-row">
            <td class="p-1 align-middle" style="width: 2%">
            </td>
            <td class="p-1 align-middle" style="width: 20%">
                <select class="form-select select-select" id="_unit_id" name="_unit_id">
                </select>
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_item" name="_item">
                    <option value="ANSOL">ANSOL</option>
                    <option value="AN">AN</option>
                    <option value="Pupuk">Pupuk</option>
                    <option value="Access.">Access.</option>
                    <option value="Used Fuel">Used Fuel</option>
                </select>
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_uom_1" name="_uom_1">
                    <option value="Isotank">Isotank</option>
                    <option value="Bag">Bag</option>
                    <option value="Sack">Sack</option>
                    <option value="Cont.">Cont.</option>
                    <option value="IBC">IBC</option>
                    <option value="Unit">Unit</option>
                </select>
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_value_1" name="_value_1">
                <input type="text" class="form-control" id="_value_1_" name="_value_1_">
            </td>
            <td class="p-1 align-middle">
                <select class="form-select select-select" id="_uom_2" name="_uom_2">
                    <option value="" selected>-</option>
                    <option value="Ton">Ton</option>
                </select>
            </td>
            <td class="p-1 align-middle">
                <input type="hidden" class="form-control" id="_value_2" name="_value_2">
                <input type="text" class="form-control" id="_value_2_" name="_value_2_">
            </td>
            <td class="p-1 align-middle" style="width:2%">
                <div class="row row-cols-auto g-3">
                    <div class="col">
                        <button type="button" class="btn btn-lg btn-primary bx bx-plus mr-1"
                            id="addUnitButton"></button>
                    </div>
                </div>
            </td>
        </tr>

        @foreach ($daily_report_detail as $d)
            <tr>
                <td class="p-1 align-middle row-number">
                    {{ $loop->iteration + 8 }}
                </td>
                <td class="p-1 align-middle">
                    <input type="hidden" class="form-control" id="detail_unit_id" name="detail_unit_id[]" readonly
                        value="{{ $d->unit_id }}">
                    <input type="text" class="form-control" id="unit_name" name="unit_name[]" readonly
                        value="{{ $d->unit->vehicle_no }}">
                </td>
                <td class="p-1 align-middle">
                    <input type="text" class="form-control" id="item" name="item[]" readonly
                        value="{{ $d->item }}">
                </td>
                <td class="p-1 align-middle">
                    <input type="text" class="form-control" id="uom_1" name="uom_1[]" readonly
                        value="{{ $d->uom_1 }}">
                </td>
                <td class="p-1 align-middle">
                    <input type="hidden" class="form-control" id="value_1" name="value_1[]" readonly
                        value="{{ $d->value_1 }}">
                    <input type="text" class="form-control" id="value_1__" name="value_1__[]" readonly
                        value="{{ $d->value_1 ? Number::format($d->value_1, precision: 0) : '' }}">
                </td>
                <td class="p-1 align-middle">
                    <input type="text" class="form-control" id="uom_2" name="uom_2[]" readonly
                        value="{{ $d->uom_2 }}">
                </td>
                <td class="p-1 align-middle">
                    <input type="hidden" class="form-control" id="value_2" name="value_2[]" readonly
                        value="{{ $d->value_2 }}">
                    <input type="text" class="form-control" id="value_2__" name="value_2__[]" readonly
                        value="{{ $d->value_2 ? Number::format($d->value_2, precision: 0) : '' }}">
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
    </tbody>
</table>
