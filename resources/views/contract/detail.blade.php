@php
    use Illuminate\Support\Number;
@endphp
<div class="row mb-2">
    <div class="col">
        <table style="width: 100%;border-collapse:separate; border-spacing:0 12px;">
            <tr>
                <td width="50%">Number :<br>
                    <b>{{ $contract->contract_no }}</b>
                </td>
                <td width="50%">Service :<br>
                    <b>{{ $contract->service->name }}</b>
                </td>
            </tr>
            <tr>
                <td width="50%">Client :<br>
                    <b>{{ $contract->client_vendor->name }}</b>
                </td>
                <td width="50%">Client :<br>
                    <b>{{ $contract->value ? Number::format($contract->value, precision: 0) : 0 }}</b>
                </td>
            </tr>
            <tr>
                <td width="50%">Start Date :<br>
                    <b>{{ $contract->start_date }}</b>
                </td>
                <td width="50%">End Date :<br>
                    <b>{{ $contract->end_date }}</b>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-12">
        <b>Service Rate</b>
    </div>
    <div class="col-lg-12">
        <table class="table mb-0">
            <thead class="table-dark">
                <tr>
                    <th scope="col" style="width: 5%">#</th>
                    {{-- <th scope="col" style="width: 10%">Item No</th> --}}
                    <th scope="col">Description</th>
                    <th scope="col" style="width: 10%">Unit</th>
                    <th scope="col" style="width: 15%" class="text-end">Rate</th>
                    <th scope="col" style="width: 40%">Notes</th>
                </tr>
            </thead>
            <tbody>
                @if ($contract_rate->count() > 0)
                    @foreach ($contract_rate as $d)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            {{-- <td>{{ $d->item_no }}</td> --}}
                            <td>{{ $d->service_item }}</td>
                            <td>{{ $d->unit }}</td>
                            <td class="text-end">{{ $d->rate ? Number::format($d->rate, precision: 0) : '' }}</td>
                            <td>{{ $d->notes }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No data showed</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="row mb-4">
    <div class="col-6">
        <div class="row">
            <div class="col-lg-12">
                <b>Fix Monthly Fee</b>
            </div>
            <div class="col-lg-12">
                <table class="table mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="width: 5%">#</th>
                            <th scope="col" style="width: 45%">Year</th>
                            <th scope="col" style="width: 45%" class="text-end">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($contract_fmf->count() > 0)
                            @foreach ($contract_fmf as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->year }}</td>
                                    <td class="text-end">{{ $d->value ? Number::format($d->value, precision: 0) : '' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3">No data showed</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="row">
            <div class="col-lg-12">
                <b>Unit Rate</b>
            </div>
            <div class="col-lg-12">
                <table class="table mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="width: 5%">#</th>
                            <th scope="col" style="width: 25%">Unit</th>
                            <th scope="col" style="width: 30%" class="text-center">Target</th>
                            <th scope="col" style="width: 30%" class="text-end">Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($unit_target->count() > 0)
                            @foreach ($unit_target as $d)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $d->unit->vehicle_no }}</td>
                                    <td class="text-center">
                                        {{ $d->target ? Number::format($d->target, precision: 0) : '' }} %</td>
                                    <td class="text-end">{{ $d->price ? Number::format($d->price, precision: 0) : '' }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">No data showed</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
