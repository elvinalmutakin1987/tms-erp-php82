@php
    $year = date('Y');
    $month = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des'];
    $month = [
        [
            'month' => '01',
            'month_name' => 'Jan',
        ],
        [
            'month' => '02',
            'month_name' => 'Feb',
        ],
        [
            'month' => '03',
            'month_name' => 'Mar',
        ],
        [
            'month' => '04',
            'month_name' => 'Apr',
        ],
        [
            'month' => '05',
            'month_name' => 'May',
        ],
        [
            'month' => '06',
            'month_name' => 'Jun',
        ],
        [
            'month' => '07',
            'month_name' => 'Jul',
        ],
        [
            'month' => '08',
            'month_name' => 'Aug',
        ],
        [
            'month' => '09',
            'month_name' => 'Sep',
        ],
        [
            'month' => '10',
            'month_name' => 'Oct',
        ],
        [
            'month' => '11',
            'month_name' => 'Nov',
        ],
        [
            'month' => '12',
            'month_name' => 'Des',
        ],
    ];
@endphp

<style>
    .table-container {
        width: 100%;
        max-height: 500px;
        overflow-x: auto;
        overflow-y: auto;
    }

    .col-no {
        width: 50px;
        min-width: 50px;
        text-align: center;
    }

    .col-uraian {
        width: 200px;
        min-width: 200px;
    }

    .col-satuan {
        width: 120px;
        min-width: 120px;
        text-align: center;
    }

    .col-month {
        width: 80px;
        min-width: 80px;
        text-align: center;
    }

    .col-number {
        width: 130px;
        min-width: 130px;
        text-align: right;
    }

    .col-var {
        width: 80px;
        min-width: 80px;
        text-align: center;
    }

    .col-keterangan {
        width: 250px;
        min-width: 250px;
    }
</style>

<div class="page-wrapper">
    <div class="page-content container-xxl">
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card rounded-4">
                    <div class="card-body">
                        <h6 class="mb-3">
                            Summary Operational Periode {{ $year }}
                        </h6>
                        <div class="table-container">
                            <table id="table-data" class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="col-no">No</th>
                                        <th class="col-uraian">Uraian</th>
                                        <th class="col-satuan">Satuan</th>
                                        @foreach ($month as $item)
                                            <th class="col-month">
                                                {{ $item['month_name'] }}
                                            </th>
                                        @endforeach
                                        <th class="col-number">Aktual MTD</th>
                                        <th class="col-number">Plan MTD</th>
                                        <th class="col-var">VAR %</th>
                                        <th class="col-keterangan">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $data = [
                                            ['AN', 'TON'],
                                            ['ANSOL', 'TANKI'],
                                            ['Pupuk', 'TON'],
                                            ['Acc', 'TRIP'],
                                            ['LCT', 'TRIP'],
                                            ['Pallet', 'TRIP/Truck'],
                                            ['Heavy Equip', 'TRIP (LCT)'],
                                        ];
                                    @endphp
                                <tbody>
                                    <tr>
                                        <td class="col-no">1</td>
                                        <td class="col-uraian">AN</td>
                                        <td class="col-satuan">TON</td>

                                        @foreach ($month as $item)
                                            <td class="col-month">0</td>
                                        @endforeach

                                        <td class="col-number">0</td>
                                        <td class="col-number">0</td>
                                        <td class="col-var">0%</td>
                                        <td class="col-keterangan">-</td>
                                    </tr>

                                    <tr>
                                        <td class="col-no">2</td>
                                        <td class="col-uraian">ANSOL</td>
                                        <td class="col-satuan">TANKI</td>

                                        @foreach ($month as $item)
                                            <td class="col-month">0</td>
                                        @endforeach

                                        <td class="col-number">0</td>
                                        <td class="col-number">0</td>
                                        <td class="col-var">0%</td>
                                        <td class="col-keterangan">-</td>
                                    </tr>

                                    <tr>
                                        <td class="col-no">3</td>
                                        <td class="col-uraian">Pupuk</td>
                                        <td class="col-satuan">TON</td>

                                        @foreach ($month as $item)
                                            <td class="col-month">0</td>
                                        @endforeach

                                        <td class="col-number">0</td>
                                        <td class="col-number">0</td>
                                        <td class="col-var">0%</td>
                                        <td class="col-keterangan">-</td>
                                    </tr>

                                    <tr>
                                        <td class="col-no">4</td>
                                        <td class="col-uraian">Acc</td>
                                        <td class="col-satuan">TRIP</td>

                                        @foreach ($month as $item)
                                            <td class="col-month">0</td>
                                        @endforeach

                                        <td class="col-number">0</td>
                                        <td class="col-number">0</td>
                                        <td class="col-var">0%</td>
                                        <td class="col-keterangan">-</td>
                                    </tr>

                                    <tr>
                                        <td class="col-no">5</td>
                                        <td class="col-uraian">LCT</td>
                                        <td class="col-satuan">TRIP</td>

                                        @foreach ($month as $item)
                                            <td class="col-month">0</td>
                                        @endforeach

                                        <td class="col-number">0</td>
                                        <td class="col-number">0</td>
                                        <td class="col-var">0%</td>
                                        <td class="col-keterangan">-</td>
                                    </tr>

                                    <tr>
                                        <td class="col-no">6</td>
                                        <td class="col-uraian">Pallet</td>
                                        <td class="col-satuan">TRIP/Truck</td>

                                        @foreach ($month as $item)
                                            <td class="col-month">0</td>
                                        @endforeach

                                        <td class="col-number">0</td>
                                        <td class="col-number">0</td>
                                        <td class="col-var">0%</td>
                                        <td class="col-keterangan">-</td>
                                    </tr>

                                    <tr>
                                        <td class="col-no">7</td>
                                        <td class="col-uraian">Heavy Equip</td>
                                        <td class="col-satuan">TRIP (LCT)</td>

                                        @foreach ($month as $item)
                                            <td class="col-month">0</td>
                                        @endforeach

                                        <td class="col-number">0</td>
                                        <td class="col-number">0</td>
                                        <td class="col-var">0%</td>
                                        <td class="col-keterangan">-</td>
                                    </tr>
                                </tbody>

                                </tbody>

                            </table>

                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-12 d-flex">
                <div class="card rounded-4 mb-0 w-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="">
                                <h6 class="mb-0">Explosive MTD {{ $year }}</h6>
                            </div>
                            <div class="dropdown"> <a href="javascript:;"
                                    class="dropdown-toggle-nocaret more-options dropdown-toggle"
                                    data-bs-toggle="dropdown"> <i class='bx bx-dots-vertical-rounded'></i> </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                                    <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                                    <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div id="chart3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
