@php
    use App\Models\Contract;
    use App\Models\Contract_rate;
    use App\Models\Contract_fmf;
    use App\Models\Unit_rate;
    use App\Models\Unit_target;
    use App\Models\Daily_report;
    use App\Models\Daily_report_detail;
    use App\Models\Plan;
    use App\Models\Unit;
    use App\Models\Plan_detail;
    use Illuminate\Support\Number;
    use Carbon\Carbon;

    $item = ['AN', 'ANSOL', 'Pupuk', 'Access.', 'LCT', 'Pallet'];
    $unit_truck = ['Light Truck', 'Crane Truck', 'Prime Truck', 'Fuel Truck', 'Water Truck', 'Flat Deck Crane Truck'];

    $now = Carbon::now();
    $dateStart = Carbon::now()->startOfYear()->toDateString();
    $dateEnd = Carbon::now()->endOfMonth()->toDateString();
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();
    $months = range(1, $now->month);

    $unit = Unit::all();
    $contract = Contract::where('status', 'Active')->get();
    $contract_id = Contract::where('status', 'Active')->pluck('id');
    $unit_pa_id = Unit_target::whereIn('contract_id', $contract_id)->pluck('unit_id');

    $year = date('Y');
    $month = [
        ['month' => '01', 'month_name' => 'Jan'],
        ['month' => '02', 'month_name' => 'Feb'],
        ['month' => '03', 'month_name' => 'Mar'],
        ['month' => '04', 'month_name' => 'Apr'],
        ['month' => '05', 'month_name' => 'May'],
        ['month' => '06', 'month_name' => 'Jun'],
        ['month' => '07', 'month_name' => 'Jul'],
        ['month' => '08', 'month_name' => 'Aug'],
        ['month' => '09', 'month_name' => 'Sep'],
        ['month' => '10', 'month_name' => 'Oct'],
        ['month' => '11', 'month_name' => 'Nov'],
        ['month' => '12', 'month_name' => 'Des'],
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

    .row-equal>[class*="col-"] {
        display: flex;
    }

    .card-equal {
        width: 100%;
        height: 340px;
        display: flex;
        flex-direction: column;
    }

    .card-equal .card-header {
        flex-shrink: 0;
    }

    .card-equal .card-body {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
</style>

<div class="page-wrapper">
    <div class="page-content container-xxl">

        <div class="row row-equal">
            <div class="col-6">
                <div class="card rounded-4 card-equal">
                    <div class="card-header py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-3 mb-md-0">Plan Vs Actual - {{ date('M Y') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sum-plan-act" class="flex-grow-1" style="min-height: 0;"></div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card rounded-4 card-equal">
                    <div class="card-header py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-3 mb-md-0">MTD Vs YTD - {{ date('M Y') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="sum-mtd-ytd" class="flex-grow-1" style="min-height: 0;"></div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $today = now()->toDateString();
        @endphp

        <div class="row row-equal mt-3">
            <div class="col-6">
                <div class="card rounded-4 card-equal">
                    <div class="card-header py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-3 mb-md-0">Waste Transfer Summary</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="waste-sum" class="flex-grow-1" style="min-height: 0;"></div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card rounded-4 card-equal">
                    <div class="card-header py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-3 mb-md-0">Tanker Summary</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="tanker-sum" class="flex-grow-1" style="min-height: 0;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row row-equal mt-3">
            <div class="col-6">
                <div class="card rounded-4 card-equal">
                    <div class="card-header py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="mb-3 mb-md-0">PA Rental Summary</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="pa-sum" class="flex-grow-1" style="min-height: 0;"></div>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card rounded-4 card-equal">
                    <div class="card-body">
                        <div class="d-flex align-items-center flex-shrink-0">
                            <div class="col">
                                <h5 class="mb-0 font-weight-bold">
                                    Unit Operation
                                    <span class="badge bg-primary ms-2" id="total-unit">
                                        {{ $unit->count() }}
                                    </span>
                                </h5>
                            </div>
                            <div class="col">
                                <input type="date" class="form-control" id="date_unit_operation"
                                    name="date_unit_operation" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        @php
                            $date = date('Y-m-d');
                            $unit = Unit::whereIn('type', $unit_truck)
                                ->withCount([
                                    'daily_report as trip_count' => function ($q) use ($date) {
                                        $q->whereDate('date', $date);
                                    },
                                ])
                                ->orderByDesc('trip_count')
                                ->get();
                        @endphp

                        <div class="product-list table-responsive mt-4 flex-grow-1"
                            style="overflow-y: auto; min-height: 0;">
                            <table class="table mb-0" id="table-unit">
                                <tbody id="table-unit-body">
                                    @foreach ($unit as $d)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>{{ $d->vehicle_no }}</div>
                                                </div>
                                            </td>
                                            <td class="px-0 text-right">
                                                <span
                                                    class="badge rounded-pill fs-8 px-3 py-2 {{ $d->trip_count > 0 ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $d->trip_count }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Summary Plan vs Act --}}
@php
    $plan_an_id = Plan::where('type', 'AN')->where('year', date('Y'))->pluck('id');
    $plan_an = Plan_detail::whereIn('plan_id', $plan_an_id)->whereIn('month', $months)->sum('value');

    $plan_ansol_id = Plan::where('type', 'ANSOL')->where('year', date('Y'))->pluck('id');
    $plan_ansol = Plan_detail::whereIn('plan_id', $plan_ansol_id)->whereIn('month', $months)->sum('value');

    $plan_pupuk_id = Plan::where('type', 'Pupuk')->where('year', date('Y'))->pluck('id');
    $plan_pupuk = Plan_detail::whereIn('plan_id', $plan_pupuk_id)->whereIn('month', $months)->sum('value');

    $plan_acc_id = Plan::where('type', 'Access.')->where('year', date('Y'))->pluck('id');
    $plan_acc = Plan_detail::whereIn('plan_id', $plan_acc_id)->whereIn('month', $months)->sum('value');

    $plan_lct_id = Plan::where('type', 'LCT')->where('year', date('Y'))->pluck('id');
    $plan_lct = Plan_detail::whereIn('plan_id', $plan_lct_id)->whereIn('month', $months)->sum('value');

    $plan_pallet_id = Plan::where('type', 'Pallet')->where('year', date('Y'))->pluck('id');
    $plan_pallet = Plan_detail::whereIn('plan_id', $plan_pallet_id)->whereIn('month', $months)->sum('value');

    $daily_report = Daily_report::whereIn(DB::raw('MONTH(date)'), $months)->whereYear('date', $now->year)->get();
    $daily_report_id = Daily_report::whereIn(DB::raw('MONTH(date)'), $months)
        ->whereYear('date', $now->year)
        ->pluck('id');

    $act_an = Daily_report_detail::whereIn('daily_report_id', $daily_report_id)->where('item', 'AN')->sum('value_1');
    $act_ansol = Daily_report_detail::whereIn('daily_report_id', $daily_report_id)
        ->where('item', 'ANSOL')
        ->sum('value_1');
    $act_pupuk = Daily_report_detail::whereIn('daily_report_id', $daily_report_id)
        ->where('item', 'Pupuk')
        ->sum('value_1');
    $act_acc = Daily_report_detail::whereIn('daily_report_id', $daily_report_id)
        ->where('item', 'Access.')
        ->sum('value_1');
    $act_lct = Daily_report::whereIn('id', $daily_report_id)->where('type', 'LCT')->count();
    $act_pallet = Daily_report_detail::whereIn('daily_report_id', $daily_report_id)
        ->where('item', 'Pallet')
        ->sum('value_1');
@endphp

{{-- Summary Mtd vs Ytd --}}
@php
    $mtd_an = $act_an;
    $mtd_ansol = $act_ansol;
    $mtd_pupuk = $act_pupuk;
    $mtd_acc = $act_acc;
    $mtd_lct = $act_lct;
    $mtd_pallet = $act_pallet;

    $ytd_an = Plan_detail::whereIn('plan_id', $plan_an_id)->sum('value') ?? 0;
    $ytd_ansol = Plan_detail::whereIn('plan_id', $plan_ansol_id)->sum('value') ?? 0;
    $ytd_pupuk = Plan_detail::whereIn('plan_id', $plan_pupuk_id)->sum('value') ?? 0;
    $ytd_acc = Plan_detail::whereIn('plan_id', $plan_acc_id)->sum('value') ?? 0;
    $ytd_lct = Plan_detail::whereIn('plan_id', $plan_lct_id)->sum('value') ?? 0;
    $ytd_pallet = Plan_detail::whereIn('plan_id', $plan_pallet_id)->sum('value') ?? 0;
@endphp

{{-- Summary Waste --}}
@php
    $wasteMonthly = Daily_report::selectRaw('MONTH(date) as bulan, COUNT(*) as total')
        ->whereYear('date', now()->year)
        ->where('service_type', 'Waste Transfer')
        ->groupBy('bulan')
        ->pluck('total', 'bulan');

    $currentMonth = now()->month;

    $months = [
        1 => 'jan',
        2 => 'feb',
        3 => 'mar',
        4 => 'apr',
        5 => 'may',
        6 => 'jun',
        7 => 'jul',
        8 => 'aug',
        9 => 'sep',
        10 => 'oct',
        11 => 'nov',
        12 => 'dec',
    ];

    foreach ($months as $num => $name) {
        $value = $wasteMonthly->get($num, 0);
        ${"waste_{$name}"} = $num <= $currentMonth ? $value : 0;
    }
@endphp

{{-- Tanker Summary --}}
@php
    $tankerMonthly = Daily_report::selectRaw('MONTH(date) as bulan, COUNT(*) as total')
        ->whereYear('date', now()->year)
        ->whereHas('unit', function ($q) use ($unit_truck) {
            $q->whereIn('type', $unit_truck);
        })
        ->groupBy('bulan')
        ->pluck('total', 'bulan');

    $currentMonth = now()->month;

    $months = [
        1 => 'jan',
        2 => 'feb',
        3 => 'mar',
        4 => 'apr',
        5 => 'may',
        6 => 'jun',
        7 => 'jul',
        8 => 'aug',
        9 => 'sep',
        10 => 'oct',
        11 => 'nov',
        12 => 'dec',
    ];

    foreach ($months as $num => $name) {
        $value = $tankerMonthly->get($num, 0);
        ${"tanker_{$name}"} = $num <= $currentMonth ? $value : 0;
    }
@endphp

{{-- PA Rental --}}
@php
    $paMonthly = Daily_report::selectRaw('MONTH(date) as bulan, COUNT(*) as total')
        ->whereYear('date', now()->year)
        ->whereHas('daily_report_detail', function ($q) use ($unit_pa_id) {
            $q->whereIn('unit_id', $unit_pa_id);
        })
        ->groupBy('bulan')
        ->pluck('total', 'bulan');

    $currentMonth = now()->month;

    $months = [
        1 => 'jan',
        2 => 'feb',
        3 => 'mar',
        4 => 'apr',
        5 => 'may',
        6 => 'jun',
        7 => 'jul',
        8 => 'aug',
        9 => 'sep',
        10 => 'oct',
        11 => 'nov',
        12 => 'dec',
    ];

    foreach ($months as $num => $name) {
        $value = $paMonthly->get($num, 0);
        ${"pa_{$name}"} = $num <= $currentMonth ? $value : 0;
    }
@endphp

@push('script-chart')
    <script>
        $(function() {
            "use strict";

            function renderDualChart(selector, nameA, dataA, nameB, dataB, categories, colors) {
                new ApexCharts(document.querySelector(selector), {
                    series: [{
                            name: nameA,
                            data: dataA
                        },
                        {
                            name: nameB,
                            data: dataB
                        }
                    ],
                    chart: {
                        foreColor: "#9ba7b2",
                        height: "100%",
                        type: "bar",
                        toolbar: {
                            show: false
                        },
                        sparkline: {
                            enabled: false
                        },
                        zoom: {
                            enabled: false
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            fontSize: "11px",
                            fontWeight: "700",
                            colors: ["#1e3a8a"],
                        },
                        formatter: function(val) {
                            return Number(val).toLocaleString('id-ID', {
                                maximumFractionDigits: 0
                            });
                        },
                        offsetY: -20,
                        dropShadow: {
                            enabled: false
                        },
                    },
                    stroke: {
                        width: 4,
                        curve: "smooth",
                        colors: ["transparent"]
                    },
                    fill: {
                        type: "gradient",
                        gradient: {
                            shade: "dark",
                            gradientToColors: colors,
                            shadeIntensity: 1,
                            type: "vertical",
                            stops: [0, 200],
                        },
                    },
                    colors: colors,
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            borderRadius: 0,
                            borderRadiusApplication: "around",
                            borderRadiusWhenStacked: "last",
                            columnWidth: "60%",
                            dataLabels: {
                                position: "top"
                            },
                        },
                    },
                    grid: {
                        show: true,
                        borderColor: "rgba(0, 0, 0, 0.15)",
                        strokeDashArray: 4,
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: true
                        },
                        x: {
                            show: true
                        },
                        y: {
                            title: {
                                formatter: function() {
                                    return "";
                                }
                            }
                        },
                        marker: {
                            show: false
                        },
                    },
                    xaxis: {
                        categories: categories
                    },
                }).render();
            }

            function renderMonthlyChart(selector, seriesName, data, color, labelColor) {
                var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];

                var points = data.map(function(val, i) {
                    return {
                        x: months[i],
                        y: val,
                        marker: {
                            size: 0
                        },
                        label: {
                            text: Number(val).toLocaleString('id-ID', {
                                maximumFractionDigits: 0
                            }),
                            borderWidth: 0,
                            offsetY: val > 0 ? -5 : 0,
                            style: {
                                background: "transparent",
                                color: labelColor,
                                fontSize: "11px",
                                fontWeight: "700",
                                padding: {
                                    left: 0,
                                    right: 0,
                                    top: 0,
                                    bottom: 0
                                },
                            },
                        },
                    };
                });

                new ApexCharts(document.querySelector(selector), {
                    series: [{
                        name: seriesName,
                        data: data
                    }],
                    chart: {
                        foreColor: "#9ba7b2",
                        type: "bar",
                        height: "100%",
                        toolbar: {
                            show: false
                        },
                        sparkline: {
                            enabled: false
                        },
                        zoom: {
                            enabled: false
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    annotations: {
                        points: points
                    },
                    stroke: {
                        width: 4,
                        curve: "smooth",
                        colors: ["transparent"]
                    },
                    colors: [color],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            borderRadius: 0,
                            borderRadiusApplication: "around",
                            borderRadiusWhenStacked: "last",
                            columnWidth: "55%",
                        },
                    },
                    yaxis: {
                        min: 0,
                        max: function(max) {
                            return Math.ceil(max * 1);
                        },
                        forceNiceScale: true,
                        labels: {
                            formatter: function(val) {
                                return Number(val).toFixed(1);
                            }
                        },
                    },
                    grid: {
                        show: true,
                        borderColor: "rgba(0, 0, 0, 0.15)",
                        strokeDashArray: 4,
                    },
                    tooltip: {
                        theme: "dark",
                        fixed: {
                            enabled: true
                        },
                        x: {
                            show: true
                        },
                        y: {
                            title: {
                                formatter: function() {
                                    return "";
                                }
                            }
                        },
                        marker: {
                            show: false
                        },
                    },
                    xaxis: {
                        categories: months
                    },
                }).render();
            }

            renderDualChart(
                "#sum-plan-act", "Plan",
                [{{ $plan_an }}, {{ $plan_ansol }}, {{ $plan_pupuk }}, {{ $plan_acc }},
                    {{ $plan_lct }}, {{ $plan_pallet }}
                ],
                "Actual",
                [{{ $act_an }}, {{ $act_ansol }}, {{ $act_pupuk }}, {{ $act_acc }},
                    {{ $act_lct }}, {{ $act_pallet }}
                ],
                ["AN", "ANSOL", "Pupuk", "Access.", "LCT", "Pallet"],
                ["#0d6efd", "#198754"]
            );

            renderDualChart(
                "#sum-mtd-ytd", "Mtd",
                [{{ $mtd_an }}, {{ $mtd_ansol }}, {{ $mtd_pupuk }}, {{ $mtd_acc }},
                    {{ $mtd_lct }}, {{ $mtd_pallet }}
                ],
                "Ytd",
                [{{ $ytd_an }}, {{ $ytd_ansol }}, {{ $ytd_pupuk }}, {{ $ytd_acc }},
                    {{ $ytd_lct }}, {{ $ytd_pallet }}
                ],
                ["AN", "ANSOL", "Pupuk", "Access.", "LCT", "Pallet"],
                ["#198754", "#dc3545"]
            );

            renderMonthlyChart(
                "#waste-sum", "Waste Transfer",
                [{{ $waste_jan }}, {{ $waste_feb }}, {{ $waste_mar }}, {{ $waste_apr }},
                    {{ $waste_may }}, {{ $waste_jun }}, {{ $waste_jul }}, {{ $waste_aug }},
                    {{ $waste_sep }}, {{ $waste_oct }}, {{ $waste_nov }}, {{ $waste_dec }}
                ],
                "#dc3545", "#7f1d1d"
            );

            renderMonthlyChart(
                "#tanker-sum", "Tanker",
                [{{ $tanker_jan }}, {{ $tanker_feb }}, {{ $tanker_mar }}, {{ $tanker_apr }},
                    {{ $tanker_may }}, {{ $tanker_jun }}, {{ $tanker_jul }}, {{ $tanker_aug }},
                    {{ $tanker_sep }}, {{ $tanker_oct }}, {{ $tanker_nov }}, {{ $tanker_dec }}
                ],
                "#ffc107", "#7a5c00"
            );

            renderMonthlyChart(
                "#pa-sum", "PA",
                [{{ $pa_jan }}, {{ $pa_feb }}, {{ $pa_mar }}, {{ $pa_apr }},
                    {{ $pa_may }}, {{ $pa_jun }}, {{ $pa_jul }}, {{ $pa_aug }},
                    {{ $pa_sep }}, {{ $pa_oct }}, {{ $pa_nov }}, {{ $pa_dec }}
                ],
                "#198754", "#0a3622"
            );

            new PerfectScrollbar('.customers-list');
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('date_unit_operation');
            const tbody = document.getElementById('table-unit-body');
            const totalEl = document.getElementById('total-unit');
            if (!dateInput || !tbody) return;

            const setTbody = html => tbody.innerHTML = html;

            dateInput.addEventListener('change', function() {
                const date = this.value;
                if (!date) return;

                setTbody(`<tr><td colspan="2" class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="ms-2">Loading...</span>
                </td></tr>`);

                fetch(`{{ route('dashboard.unit.operation') }}?date=${encodeURIComponent(date)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (totalEl) {
                            totalEl.textContent = new Intl.NumberFormat().format(res.total);
                        }

                        if (!res.data.length) {
                            return setTbody(`<tr><td colspan="2" class="text-center text-secondary py-3">
                                Tidak ada data
                            </td></tr>`);
                        }

                        setTbody(res.data.map(d => `
                            <tr>
                                <td><div class="d-flex align-items-center"><div>${d.vehicle_no}</div></div></td>
                                <td class="px-0 text-right">
                                    <span class="badge rounded-pill fs-8 px-3 py-2 ${d.trip_count > 0 ? 'bg-success' : 'bg-danger'}">
                                        ${d.trip_count}
                                    </span>
                                </td>
                            </tr>`).join(''));
                    })
                    .catch(err => {
                        console.error(err);
                        setTbody(
                            `<tr><td colspan="2" class="text-center text-danger py-3">${err}</td></tr>`
                        );
                    });
            });
        });
    </script>
@endpush
