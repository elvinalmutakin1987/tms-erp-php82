@php
    use App\Models\Purchase_order;
    use App\Models\Approval_flow;
    use App\Models\Approval_process;
    use Carbon\Carbon;

    $approval_flow_id = Approval_flow::where('approvable_model', 'App\Models\Purchase_order')->pluck('id');

    $isYearAll = strtolower((string) $year) === 'all';
    $isMonthAll = strtolower((string) $month) === 'all';

    if ($isYearAll && $isMonthAll) {
        $periode = 'Semua Periode';
    } elseif (!$isYearAll && $isMonthAll) {
        $periode = 'Periode : ' . $year;
    } elseif ($isYearAll && !$isMonthAll) {
        $monthName = Carbon::create(2000, (int) $month, 1)->format('F');
        $periode = 'Periode : Bulan ' . $monthName;
    } else {
        $periode = 'Periode : ' . Carbon::create((int) $year, (int) $month, 1)->format('F Y');
    }

    $purchase_order = Purchase_order::query();

    if (!$isYearAll) {
        $purchase_order->whereYear('date', $year);
    }

    if (!$isMonthAll) {
        $purchase_order->whereMonth('date', $month);
    }

    $purchase_order = $purchase_order
        ->whereIn('status', ['Approved', 'Approval', 'Received', 'Done'])
        ->orderBy('date', 'desc')
        ->get();
@endphp

<style>
    .calendar-container {
        width: 100%;
        max-width: 100%;
        position: relative;
        isolation: isolate;
        z-index: 0;
    }

    .calendar-table-wrapper {
        width: 100%;
        max-width: 100%;
        max-height: 600px;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #dee2e6;
        position: relative;
        isolation: isolate;
        z-index: 0;
        background-color: #ffffff;
    }

    .calendar-table {
        width: 100%;
        min-width: 1800px;
        table-layout: fixed !important;
        margin-bottom: 0;
        border-collapse: separate !important;
        border-spacing: 0;
        position: relative;
        z-index: 0;
        background-color: #ffffff;
    }

    /* Column Width */
    .calendar-table col.col-po {
        width: 160px;
    }

    .calendar-table col.col-vendor {
        width: 320px;
    }

    .calendar-table col.col-normal {
        width: 125px;
    }

    .calendar-table col.col-remarks {
        width: 320px;
    }

    /* Header */
    .calendar-table thead th {
        position: sticky;
        top: 0;
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
        box-shadow: 0 2px 3px rgba(0, 0, 0, 0.05);
    }

    /* Body */
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

    .calendar-table tbody td.text-cell {
        text-align: left;
        padding: 6px 10px;
        line-height: 1.4;
        white-space: normal;
        word-break: normal;
        overflow-wrap: break-word;
    }

    /* Freeze No. PO */
    .calendar-table thead th.sticky-po {
        position: sticky;
        top: 0;
        left: 0;
        z-index: 8;
        background-color: #f8f9fa;
        border-right: 2px solid #adb5bd;
        box-shadow: 4px 0 6px rgba(0, 0, 0, 0.08), 0 2px 3px rgba(0, 0, 0, 0.05);
    }

    .calendar-table tbody td.sticky-po {
        position: sticky;
        left: 0;
        z-index: 3;
        background-color: #ffffff;
        border-right: 2px solid #adb5bd;
        box-shadow: 4px 0 6px rgba(0, 0, 0, 0.08);
        font-weight: 600;
    }

    .calendar-table tbody tr:hover td {
        background-color: #f8f9fa;
    }

    .calendar-table tbody tr:hover td.sticky-po {
        background-color: #f8f9fa;
    }

    .calendar-table .badge {
        font-size: 12px !important;
        white-space: nowrap;
    }

    /* Scrollbar */
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

    @media (max-width: 768px) {
        .calendar-table-wrapper {
            max-height: 500px;
        }

        .calendar-table {
            min-width: 1800px;
        }

        .calendar-table thead th {
            font-size: 11px;
            padding: 6px 4px;
        }

        .calendar-table tbody td {
            font-size: 11px;
            padding: 5px 4px;
        }

        .calendar-table tbody td.text-cell {
            padding: 5px 8px;
        }

        .calendar-table .badge {
            font-size: 11px !important;
        }
    }
</style>

<div class="calendar-container mb-4">
    <div class="text-center mb-3">
        <h4 class="fw-bold mb-0">{{ $periode }}</h4>
    </div>

    <div class="calendar-table-wrapper">
        <table class="table calendar-table mb-0">
            <colgroup>
                <col class="col-po">
                <col class="col-vendor">
                <col class="col-normal">
                <col class="col-normal">
                <col class="col-normal">
                <col class="col-normal">
                <col class="col-normal">
                <col class="col-normal">
                <col class="col-normal">
                <col class="col-remarks">
                <col class="col-normal">
            </colgroup>

            <thead>
                <tr>
                    <th class="sticky-po">No. PO</th>
                    <th>Vendor</th>
                    <th>Unit</th>
                    <th>Divisi</th>
                    <th>Tanggal Pembuatan<br>PO</th>
                    <th>Pengajuan<br>Approval</th>
                    <th>Tanggal<br>Approved</th>
                    <th>Approval<br>Aging</th>
                    <th>Keterangan</th>
                    <th>Remarks</th>
                    <th>Status Bayar</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($purchase_order as $po)
                    @php
                        $app_proc_awal = Approval_process::with('approval_step')
                            ->join('approval_steps', 'approval_processes.approval_step_id', '=', 'approval_steps.id')
                            ->whereIn('approval_processes.approval_flow_id', $approval_flow_id)
                            ->where('approval_processes.approvable_id', $po->id)
                            ->orderBy('approval_steps.order', 'asc')
                            ->select('approval_processes.*')
                            ->first();

                        $tgl_pengajuan = $app_proc_awal?->created_at ?? $po->created_at;

                        $app_proc_akhir = Approval_process::with('approval_step')
                            ->join('approval_steps', 'approval_processes.approval_step_id', '=', 'approval_steps.id')
                            ->whereIn('approval_processes.approval_flow_id', $approval_flow_id)
                            ->where('approval_processes.approvable_id', $po->id)
                            ->orderBy('approval_steps.order', 'desc')
                            ->select('approval_processes.*')
                            ->first();

                        $tgl_approve = $app_proc_akhir?->updated_at ?? $po->created_at;

                        $tanggal_awal = Carbon::parse($tgl_pengajuan)->startOfDay();
                        $tanggal_akhir = Carbon::parse($tgl_approve)->startOfDay();
                        $selisih_hari = (int) round($tanggal_awal->diffInDays($tanggal_akhir));
                    @endphp

                    <tr>
                        <td class="sticky-po">{{ $po->order_no }}</td>

                        <td class="text-cell">
                            {{ $po->client_vendor->name ?? '-' }}
                        </td>

                        <td>
                            {{ $po->unit->vehicle_no ?? '-' }}
                        </td>

                        <td>
                            {{ $po->department ?? '-' }}
                        </td>

                        <td>
                            {{ $po->date ? Carbon::parse($po->date)->format('d M Y') : '-' }}
                        </td>

                        <td>
                            {{ $app_proc_awal?->created_at
                                ? Carbon::parse($app_proc_awal->created_at)->format('d M Y')
                                : Carbon::parse($po->created_at)->format('d M Y') }}
                        </td>

                        <td>
                            {{ $app_proc_akhir?->action === 'Approved' && $app_proc_akhir?->updated_at
                                ? Carbon::parse($app_proc_akhir->updated_at)->format('d M Y')
                                : Carbon::parse($po->created_at)->format('d M Y') }}
                        </td>

                        <td>
                            {!! match (true) {
                                $selisih_hari < 3 => '<span class="badge bg-success">' . $selisih_hari . ' Hari</span>',
                                $selisih_hari == 3 => '<span class="badge bg-warning text-dark">' . $selisih_hari . ' Hari</span>',
                                $selisih_hari > 3 => '<span class="badge bg-danger">' . $selisih_hari . ' Hari</span>',
                            } !!}
                        </td>

                        <td>
                            {!! match ($po->urgency) {
                                'P5' => '<span class="badge bg-success">LP</span>',
                                'P4' => '<span class="badge bg-info">MP</span>',
                                'P3' => '<span class="badge bg-primary">HP</span>',
                                'P2' => '<span class="badge bg-warning text-dark">U</span>',
                                'P1' => '<span class="badge bg-danger">TU</span>',
                                default => '<span class="badge bg-secondary">-</span>',
                            } !!}
                        </td>

                        <td class="text-cell">
                            {{ $po->remarks ?? '-' }}
                        </td>

                        <td>
                            {{ $po->payment_status ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align:center; padding:20px;">
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
        target="_blank" type="button" class="btn btn-success">
        Export To Excel
    </a>

    <a href="{{ route('report.export_image', [
        't' => $t,
        'year' => $year,
        'month' => $month,
    ]) }}"
        target="_blank" type="button" class="btn btn-warning">
        Export To PNG
    </a>

    <a href="{{ route('report.print', [
        't' => $t,
        'year' => $year,
        'month' => $month,
    ]) }}"
        target="_blank" type="button" class="btn btn-primary">
        Print
    </a>
</div>
