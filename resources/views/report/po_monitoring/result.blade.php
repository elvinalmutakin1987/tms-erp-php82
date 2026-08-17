@php
    use App\Models\Purchase_order;
    use App\Models\Purchase_order_detail;
    use App\Models\Approval_flow;
    use App\Models\Approval_status;
    use App\Models\Approval_process;
    use App\Models\Approval_step;
    use Carbon\Carbon;
    use Illuminate\Support\Number;

    $approval_flow = Approval_flow::where('approvable_model', 'App\Models\Purchase_order')->get();

    $approval_flow_id = Approval_flow::where('approvable_model', 'App\Models\Purchase_order')->pluck('id');
@endphp


<style>
    /*
    |--------------------------------------------------------------------------
    | Calendar Container
    |--------------------------------------------------------------------------
    |
    | isolation: isolate membuat seluruh z-index tabel berada
    | dalam stacking context sendiri sehingga tidak menutupi navbar.
    |
    */

    .calendar-container {
        width: 100%;
        max-width: 100%;

        position: relative;

        isolation: isolate;

        z-index: 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Table Scroll Wrapper
    |--------------------------------------------------------------------------
    |
    | Scroll horizontal dan vertical dilakukan di dalam area tabel.
    |
    */

    .calendar-table-wrapper {
        width: 100%;
        max-width: 100%;

        /*
        | Tinggi maksimal tabel
        */
        max-height: 600px;

        /*
        | Scroll X + Y
        */
        overflow: auto;

        -webkit-overflow-scrolling: touch;

        border: 1px solid #dee2e6;

        position: relative;

        /*
        | Batasi stacking sticky hanya dalam wrapper ini
        */
        isolation: isolate;

        z-index: 0;

        background-color: #ffffff;
    }


    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    .calendar-table {
        width: 100%;

        /*
        | Memastikan semua kolom tetap memiliki ruang
        | dan tersedia horizontal scrollbar.
        */
        min-width: 2575px;

        table-layout: fixed !important;

        margin-bottom: 0;

        /*
        | Lebih stabil untuk sticky row/column
        */
        border-collapse: separate !important;
        border-spacing: 0;

        position: relative;

        z-index: 0;

        background-color: #ffffff;
    }


    /*
    |--------------------------------------------------------------------------
    | Column Width
    |--------------------------------------------------------------------------
    */

    /* Invoice */
    .calendar-table col.col-invoice {
        width: 160px;
    }

    /* Proforma Invoice */
    .calendar-table col.col-proforma {
        width: 160px;
    }

    /* Keterangan */
    .calendar-table col.col-keterangan {
        width: 440px;
    }

    /* Semua tanggal dan status */
    .calendar-table col.col-date {
        width: 125px;
    }


    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    |
    | Semua header freeze ketika tabel di-scroll ke bawah.
    |
    */

    .calendar-table thead th {
        position: sticky;

        top: 0;

        /*
        | Cukup tinggi terhadap body table,
        | tetapi tidak terlalu tinggi terhadap navbar.
        */
        z-index: 5;

        text-align: center;
        vertical-align: middle;

        font-size: 12px;
        font-weight: 700;
        line-height: 1.25;

        padding: 7px 5px;

        white-space: normal;

        background-color: #f8f9fa;
        color: #212529;

        border-top: 0;
        border-left: 0;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;

        box-sizing: border-box;

        /*
        | Pembatas visual saat scroll
        */
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.05);
    }


    /*
    |--------------------------------------------------------------------------
    | Body
    |--------------------------------------------------------------------------
    */

    .calendar-table tbody td {
        height: 45px;

        padding: 6px 5px;

        vertical-align: middle;
        text-align: center;

        position: relative;

        z-index: 0;

        font-size: 12px;
        line-height: 1.3;

        background-color: #ffffff;

        border-top: 0;
        border-left: 0;
        border-right: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;

        white-space: normal;
        word-break: break-word;

        box-sizing: border-box;
    }


    /*
    |--------------------------------------------------------------------------
    | Keterangan
    |--------------------------------------------------------------------------
    */

    .calendar-table tbody td.keterangan-cell {
        text-align: left;

        padding: 6px 10px;

        line-height: 1.4;

        white-space: normal;

        word-break: normal;
        overflow-wrap: break-word;
    }


    /*
    |--------------------------------------------------------------------------
    | Invoice Rowspan
    |--------------------------------------------------------------------------
    |
    | Invoice tetap vertical-align top.
    |
    */

    .calendar-table tbody td.invoice-cell {
        vertical-align: top !important;

        padding: 0 !important;

        text-align: center;

        font-size: 12px;

        white-space: normal;
        word-break: break-word;

        background-color: #ffffff;
    }


    /*
    |--------------------------------------------------------------------------
    | Invoice Content
    |--------------------------------------------------------------------------
    |
    | Tinggi 45px membuat invoice_no sejajar dengan
    | Proforma Invoice pertama.
    |
    */

    .invoice-cell-content {
        width: 100%;
        height: 45px;

        padding: 6px 5px;

        display: flex;

        align-items: center;
        justify-content: center;

        box-sizing: border-box;

        font-size: 12px;
        font-weight: 600;
        line-height: 1.3;

        text-align: center;

        white-space: normal;
        word-break: break-word;
    }


    /*
    |--------------------------------------------------------------------------
    | FREEZE COLUMN 1 : INVOICE
    |--------------------------------------------------------------------------
    |
    | Header:
    | - freeze atas
    | - freeze kiri
    |
    | Body:
    | - freeze kiri
    |
    */

    .calendar-table thead th.sticky-invoice {
        position: sticky;

        top: 0;
        left: 0;

        z-index: 8;

        background-color: #f8f9fa;
    }


    .calendar-table tbody td.sticky-invoice {
        position: sticky;

        left: 0;

        z-index: 3;

        background-color: #ffffff;
    }


    /*
    |--------------------------------------------------------------------------
    | FREEZE COLUMN 2 : PROFORMA INVOICE
    |--------------------------------------------------------------------------
    |
    | Invoice memiliki lebar 160px.
    | Maka Proforma dimulai dari left: 160px.
    |
    */

    .calendar-table thead th.sticky-proforma {
        position: sticky;

        top: 0;
        left: 160px;

        z-index: 8;

        background-color: #f8f9fa;

        border-right: 2px solid #adb5bd;

        /*
        | Shadow kanan untuk menandakan akhir frozen column
        */
        box-shadow:
            4px 0 6px rgba(0, 0, 0, 0.08),
            0 2px 3px rgba(0, 0, 0, 0.05);
    }


    .calendar-table tbody td.sticky-proforma {
        position: sticky;

        left: 160px;

        z-index: 3;

        background-color: #ffffff;

        border-right: 2px solid #adb5bd;

        box-shadow:
            4px 0 6px rgba(0, 0, 0, 0.08);
    }


    /*
    |--------------------------------------------------------------------------
    | Hover Row
    |--------------------------------------------------------------------------
    */

    .calendar-table tbody tr:hover td {
        background-color: #f8f9fa;
    }


    /*
    | Sticky column harus tetap memiliki background
    | saat row di-hover.
    */

    .calendar-table tbody tr:hover td.sticky-invoice,
    .calendar-table tbody tr:hover td.sticky-proforma {
        background-color: #f8f9fa;
    }


    /*
    |--------------------------------------------------------------------------
    | Badge
    |--------------------------------------------------------------------------
    */

    .calendar-table .badge {
        font-size: 12px !important;

        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Scrollbar
    |--------------------------------------------------------------------------
    */

    .calendar-table-wrapper::-webkit-scrollbar {
        width: 9px;
        height: 9px;
    }


    .calendar-table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
    }


    .calendar-table-wrapper::-webkit-scrollbar-thumb {
        background: #adb5bd;

        border-radius: 10px;
    }


    .calendar-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #6c757d;
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 768px) {

        .calendar-table-wrapper {
            max-height: 500px;
        }


        .calendar-table {
            min-width: 2575px;
        }


        .calendar-table thead th {
            font-size: 11px;

            padding: 6px 4px;
        }


        .calendar-table tbody td {
            font-size: 11px;

            padding: 5px 4px;
        }


        .calendar-table tbody td.keterangan-cell {
            padding: 5px 8px;
        }


        .calendar-table tbody td.invoice-cell {
            padding: 0 !important;
        }


        .invoice-cell-content {
            height: 45px;

            padding: 5px 4px;

            font-size: 11px;
        }


        .calendar-table .badge {
            font-size: 11px !important;
        }
    }
</style>



{{-- Calendar / Invoice Table --}}
<div class="calendar-container mb-4">


    {{-- Judul --}}
    <div class="text-center mb-3">

        <h4 class="fw-bold mb-0">

            @php
                $isYearAll = strtolower((string) $year) === 'all';
                $isMonthAll = strtolower((string) $month) === 'all';

                if ($isYearAll && $isMonthAll) {
                    // Year = All, Month = All
                    $periode = 'Semua Periode';
                } elseif (!$isYearAll && $isMonthAll) {
                    // Year dipilih, Month = All
                    $periode = 'Periode : ' . $year;
                } elseif ($isYearAll && !$isMonthAll) {
                    // Year = All, Month dipilih
                    $monthName = Carbon::create(2000, (int) $month, 1)->format('F');

                    $periode = 'Periode : Bulan ' . $monthName;
                } else {
                    // Year dan Month sama-sama dipilih
                    $periode = 'Periode : ' . Carbon::create((int) $year, (int) $month, 1)->format('F Y');
                }
            @endphp

            {{ $periode }}

        </h4>

    </div>



    {{-- Table Wrapper --}}
    <div class="calendar-table-wrapper">


        <table class="table calendar-table mb-0">


            {{--
            |--------------------------------------------------------------------------
            | Colgroup
            |--------------------------------------------------------------------------
            |
            | Lebar kolom ditentukan menggunakan colgroup.
            | Ini stabil walaupun Invoice menggunakan rowspan.
            |
            --}}

            <colgroup>

                {{-- 1. Invoice --}}
                <col class="col-invoice">

                {{-- 2. Vendor --}}
                <col class="col-keterangan">

                {{-- 3. Unit --}}
                <col class="col-date">

                {{-- 4. Divisi --}}
                <col class="col-date">

                {{-- 5. Tanggal Pembuatan PO --}}
                <col class="col-date">

                {{-- 6. Pengajuan Approval --}}
                <col class="col-date">

                {{-- 7. Tanggal Approved --}}
                <col class="col-date">

                {{-- 8. Approval Aging --}}
                <col class="col-date">

                {{-- 9. Keterangan --}}
                <col class="col-date">

            </colgroup>



            {{--
            |--------------------------------------------------------------------------
            | Header
            |--------------------------------------------------------------------------
            --}}

            <thead>

                <tr>


                    {{--
                    |--------------------------------------------------------------------------
                    | Invoice
                    |--------------------------------------------------------------------------
                    |
                    | Freeze atas + kiri.
                    |
                    --}}

                    <th class="sticky-invoice">

                        No. PO

                    </th>

                    <th>

                        Vendor

                    </th>

                    <th>

                        Unit

                    </th>

                    <th>

                        Divisi

                    </th>

                    <th>

                        Tanggal Pembuatan <br>
                        PO

                    </th>

                    <th>
                        Pengajuan <br>
                        Approval

                    </th>

                    <th>

                        Tanggal <br> Approved

                    </th>

                    <th>

                        Approval <br> Aging

                    </th>

                    <th>

                        Keterangan

                    </th>

                    <th>

                        Remarks

                    </th>

                </tr>

            </thead>

            <tbody>
                @php
                    $purchase_order = Purchase_order::query();

                    if ($year !== 'All') {
                        $purchase_order->whereYear('date', $year);
                    }

                    if ($month !== 'All') {
                        $purchase_order->whereMonth('date', $month);
                    }
                    $purchase_order = $purchase_order->whereIn('status', ['Approved', 'Approval', 'Received', 'Done']);
                    $purchase_order = $purchase_order->orderBy('date', 'desc')->get();

                @endphp
                @forelse ($purchase_order as $po)
                    <tr>

                        {{-- ============================================================ --}}
                        {{-- NO. PO - FREEZE --}}
                        {{-- ============================================================ --}}

                        <td class="sticky-invoice"
                            style="
                                font-weight: 600;
                                background-color: #ffffff;
                            ">
                            {{ $po->order_no }}
                        </td>


                        {{-- ============================================================ --}}
                        {{-- VENDOR --}}
                        {{-- ============================================================ --}}

                        <td class="keterangan-cell">
                            {{ $po->client_vendor->name ?? '-' }}
                        </td>


                        {{-- ============================================================ --}}
                        {{-- UNIT --}}
                        {{-- ============================================================ --}}

                        <td>
                            {{ $po->unit->vehicle_no ?? '-' }}
                        </td>


                        {{-- ============================================================ --}}
                        {{-- DIVISI --}}
                        {{-- ============================================================ --}}

                        <td>
                            {{ $po->department ?? '-' }}
                        </td>


                        {{-- ============================================================ --}}
                        {{-- TANGGAL PEMBUATAN PO --}}
                        {{-- ============================================================ --}}

                        <td>
                            {{ $po->date ? Carbon::parse($po->date)->format('d M Y') : '-' }}
                        </td>


                        {{-- ============================================================ --}}
                        {{-- PENGAJUAN APPROVAL --}}
                        {{-- ============================================================ --}}

                        <td>
                            {{-- Cari lewat approval process --}}
                            @php
                                $app_proc = Approval_process::with('approval_step')
                                    ->join(
                                        'approval_steps',
                                        'approval_processes.approval_step_id',
                                        '=',
                                        'approval_steps.id',
                                    )
                                    ->whereIn('approval_processes.approval_flow_id', $approval_flow_id)
                                    ->where('approval_processes.approvable_id', $po->id)
                                    ->orderBy('approval_steps.order', 'asc')
                                    ->select('approval_processes.*')
                                    ->first();
                                $tgl_pengajuan = $app_proc?->created_at ?? $po->created_at;
                            @endphp

                            {{ $app_proc?->created_at ? Carbon::parse($app_proc->created_at)->format('d M Y') : Carbon::parse($po->created_at)->format('d M Y') }}
                        </td>


                        {{-- ============================================================ --}}
                        {{-- TANGGAL APPROVED --}}
                        {{-- ============================================================ --}}

                        <td>
                            {{-- Cari lewat approval process --}}

                            @php
                                $app_proc = Approval_process::with('approval_step')
                                    ->join(
                                        'approval_steps',
                                        'approval_processes.approval_step_id',
                                        '=',
                                        'approval_steps.id',
                                    )
                                    ->whereIn('approval_processes.approval_flow_id', $approval_flow_id)
                                    ->where('approval_processes.approvable_id', $po->id)
                                    ->orderBy('approval_steps.order', 'desc')
                                    ->select('approval_processes.*')
                                    ->first();
                                $tgl_approve = $app_proc?->updated_at ?? $po->created_at;
                            @endphp

                            {{ $app_proc?->action === 'Approved' && $app_proc?->updated_at
                                ? Carbon::parse($app_proc->updated_at)->format('d M Y')
                                : Carbon::parse($po->created_at)->format('d M Y') }}
                        </td>


                        {{-- ============================================================ --}}
                        {{-- APPROVAL AGING --}}
                        {{-- ============================================================ --}}

                        <td>
                            {{-- Cari nya dari data approval process yang order pertama dibandingkan dengan order paling terakhir.
                            Statusnya yang 'Approval'
                            --}}
                            @php
                                $tanggal_awal = Carbon::parse($tgl_pengajuan);
                                $tanggal_akhir = Carbon::parse($tgl_approve);

                                $selisih_hari = round($tanggal_awal->diffInDays($tanggal_akhir));
                            @endphp

                            {{ $selisih_hari }} Hari
                        </td>


                        {{-- ============================================================ --}}
                        {{-- KETERANGAN --}}
                        {{-- ============================================================ --}}

                        <td>
                            {{-- Ini urgency --}}
                            {{ match ($po->urgency) {
                                'P5' => 'LP',
                                'P4' => 'MP',
                                'P3' => 'HP',
                                'P2' => 'U',
                                'P1' => 'TU',
                                default => '-',
                            } }}
                        </td>


                        {{-- ============================================================ --}}
                        {{-- REMARKS --}}
                        {{-- ============================================================ --}}

                        <td>
                            {{ $po->remarks ?? '-' }}
                        </td>

                    </tr>


                @empty

                    <tr>

                        <td colspan="10"
                            style="
                                text-align: center;
                                padding: 20px;
                            ">
                            Tidak ada data Purchase Order.
                        </td>

                    </tr>
                @endforelse

            </tbody>


        </table>


    </div>


</div>

<div class="d-md-flex d-grid align-items-center gap-1">
    <a href="{{ route('report.export', [
        't' => $t,
        'year' => $year,
        'month' => $month,
    ]) }}"
        target="_blank" type="button" class="btn btn-success">Export To Excel</a>

    <a href="{{ route('report.export_image', [
        't' => $t,
        'year' => $year,
        'month' => $month,
    ]) }}"
        target="_blank" type="button" class="btn btn-warning">Export To PNG</a>


    <a href="{{ route('report.print', [
        't' => $t,
        'year' => $year,
        'month' => $month,
    ]) }}"
        target="_blank" type="button" class="btn btn-primary ">Print</a>
</div>
