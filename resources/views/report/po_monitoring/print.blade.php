@php
    use App\Models\Purchase_order;
    use App\Models\Approval_flow;
    use App\Models\Approval_process;
    use Carbon\Carbon;

    $approval_flow_id = Approval_flow::where('approvable_model', 'App\\Models\\Purchase_order')->pluck('id');

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

    $formatDate = function ($value) {
        return $value ? Carbon::parse($value)->format('d M Y') : '-';
    };
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Purchase Order Monitoring</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12pt;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            color: #000;
            background-color: #fff;
        }

        .print-shell {
            padding: 8pt;
        }

        .report-title {
            text-align: center;
            margin: 0 0 9px;
            padding: 0;
        }

        .company-name {
            font-size: 17px;
            font-weight: bold;
            line-height: 1.15;
            margin-bottom: 3px;
        }

        .report-name {
            font-size: 15px;
            font-weight: bold;
            line-height: 1.15;
            margin-bottom: 3px;
        }

        .report-period {
            font-size: 12px;
            font-weight: bold;
            line-height: 1.15;
        }

        .report-table {
            width: 100%;
            table-layout: fixed;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
            padding: 0;
        }

        .report-table thead {
            display: table-header-group;
        }

        .report-table th {
            border: 1px solid #000;
            background-color: #f2f2f2;
            color: #000;
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
            font-weight: bold;
            line-height: 1.15;
            padding: 4px 3px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .report-table td {
            border: 1px solid #000;
            background-color: #fff;
            color: #000;
            text-align: center;
            vertical-align: middle;
            font-size: 8px;
            line-height: 1.2;
            padding: 4px 3px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .report-table tr {
            page-break-inside: avoid;
        }

        .po-cell {
            font-weight: bold;
        }

        .text-cell {
            text-align: left !important;
            padding-left: 5px !important;
            padding-right: 5px !important;
            line-height: 1.25 !important;
        }

        .col-po {
            width: 10%;
        }

        .col-vendor {
            width: 18%;
        }

        .col-unit {
            width: 7%;
        }

        .col-divisi {
            width: 8%;
        }

        .col-date {
            width: 9%;
        }

        .col-aging {
            width: 8%;
        }

        .col-keterangan {
            width: 7%;
        }

        .col-remarks {
            width: 10%;
        }

        .col-status {
            width: 5%;
        }

        .aging-success,
        .aging-warning,
        .aging-danger,
        .urgency-lp,
        .urgency-mp,
        .urgency-hp,
        .urgency-u,
        .urgency-tu,
        .urgency-default {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }

        .aging-success,
        .urgency-lp {
            background-color: #198754 !important;
            color: #fff !important;
        }

        .aging-warning,
        .urgency-u {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .aging-danger,
        .urgency-tu {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .urgency-mp {
            background-color: #0dcaf0 !important;
            color: #000 !important;
        }

        .urgency-hp {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        .urgency-default {
            background-color: #6c757d !important;
            color: #fff !important;
        }
    </style>
</head>

<body>
    <div class="print-shell">
        <div class="report-title">
            <div class="company-name">PT. TUNAS MITRA SEJATI</div>
            <div class="report-name">Purchase Order Monitoring</div>
            <div class="report-period">{{ $periode }}</div>
        </div>

        <table class="report-table">
            <colgroup>
                <col class="col-po">
                <col class="col-vendor">
                <col class="col-unit">
                <col class="col-divisi">
                <col class="col-date">
                <col class="col-date">
                <col class="col-date">
                <col class="col-aging">
                <col class="col-keterangan">
                <col class="col-remarks">
                <col class="col-status">
            </colgroup>

            <thead>
                <tr>
                    <th>No. PO</th>
                    <th>Vendor</th>
                    <th>Unit</th>
                    <th>Divisi</th>
                    <th>Tanggal Pembuatan<br>PO</th>
                    <th>Pengajuan<br>Approval</th>
                    <th>Tanggal<br>Approved</th>
                    <th>Approval<br>Aging</th>
                    <th>Keterangan</th>
                    <th>Remarks</th>
                    <th>Status<br>Bayar</th>
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

                        $agingClass = match (true) {
                            $selisih_hari < 3 => 'aging-success',
                            $selisih_hari == 3 => 'aging-warning',
                            default => 'aging-danger',
                        };

                        [$urgencyLabel, $urgencyClass] = match ($po->urgency) {
                            'P5' => ['LP', 'urgency-lp'],
                            'P4' => ['MP', 'urgency-mp'],
                            'P3' => ['HP', 'urgency-hp'],
                            'P2' => ['U', 'urgency-u'],
                            'P1' => ['TU', 'urgency-tu'],
                            default => ['-', 'urgency-default'],
                        };
                    @endphp

                    <tr>
                        <td class="po-cell">{{ $po->order_no }}</td>
                        <td class="text-cell">{{ $po->client_vendor->name ?? '-' }}</td>
                        <td>{{ $po->unit->vehicle_no ?? '-' }}</td>
                        <td>{{ $po->department ?? '-' }}</td>
                        <td>{{ $formatDate($po->date) }}</td>
                        <td>
                            {{ $app_proc_awal?->created_at ? $formatDate($app_proc_awal->created_at) : $formatDate($po->created_at) }}
                        </td>
                        <td>
                            {{ $app_proc_akhir?->action === 'Approved' && $app_proc_akhir?->updated_at
                                ? $formatDate($app_proc_akhir->updated_at)
                                : $formatDate($po->created_at) }}
                        </td>
                        <td class="{{ $agingClass }}">{{ $selisih_hari }} Hari</td>
                        <td class="{{ $urgencyClass }}">{{ $urgencyLabel }}</td>
                        <td class="text-cell">{{ $po->remarks ?? '-' }}</td>
                        <td>{{ $po->payment_status ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align:center;padding:10px;">
                            Tidak ada data Purchase Order.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
