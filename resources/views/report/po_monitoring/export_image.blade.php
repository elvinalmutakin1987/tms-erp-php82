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

    $titleStyle = 'text-align:center;vertical-align:middle;';
    $headerStyle =
        'text-align:center;vertical-align:middle;border:1px solid #000000;font-weight:bold;background-color:#f2f2f2;padding:5px;';
    $cellStyle = 'text-align:center;vertical-align:middle;border:1px solid #000000;padding:5px;';
    $textCellStyle = 'text-align:left;vertical-align:middle;border:1px solid #000000;padding:5px;';
@endphp

<table style="width:100%;border-collapse:separate;border-spacing:0;">
    {{-- Judul --}}
    <tr>
        <td colspan="11" style="{{ $titleStyle }}font-size:18px;">
            <b>PT. TUNAS MITRA SEJATI</b>
        </td>
    </tr>
    <tr>
        <td colspan="11" style="{{ $titleStyle }}font-size:18px;">
            <b>Purchase Order Monitoring</b>
        </td>
    </tr>
    <tr>
        <td colspan="11" style="{{ $titleStyle }}font-size:14px;">
            <b>{{ $periode }}</b>
        </td>
    </tr>
    <tr>
        <td colspan="11"></td>
    </tr>

    {{-- Header --}}
    <tr>
        <td style="{{ $headerStyle }}width:150px;"><b>No. PO</b></td>
        <td style="{{ $headerStyle }}width:300px;"><b>Vendor</b></td>
        <td style="{{ $headerStyle }}width:110px;"><b>Unit</b></td>
        <td style="{{ $headerStyle }}width:130px;"><b>Divisi</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Tanggal Pembuatan<br>PO</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Pengajuan<br>Approval</b></td>
        <td style="{{ $headerStyle }}width:125px;"><b>Tanggal<br>Approved</b></td>
        <td style="{{ $headerStyle }}width:120px;"><b>Approval<br>Aging</b></td>
        <td style="{{ $headerStyle }}width:110px;"><b>Keterangan</b></td>
        <td style="{{ $headerStyle }}width:300px;"><b>Remarks</b></td>
        <td style="{{ $headerStyle }}width:120px;"><b>Status Bayar</b></td>
    </tr>

    {{-- Data --}}
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

            [$agingBg, $agingColor] = match (true) {
                $selisih_hari < 3 => ['#198754', '#ffffff'],
                $selisih_hari == 3 => ['#ffc107', '#000000'],
                default => ['#dc3545', '#ffffff'],
            };

            [$urgencyLabel, $urgencyBg, $urgencyColor] = match ($po->urgency) {
                'P5' => ['LP', '#198754', '#ffffff'],
                'P4' => ['MP', '#0dcaf0', '#000000'],
                'P3' => ['HP', '#0d6efd', '#ffffff'],
                'P2' => ['U', '#ffc107', '#000000'],
                'P1' => ['TU', '#dc3545', '#ffffff'],
                default => ['-', '#6c757d', '#ffffff'],
            };

            $agingStyle = $cellStyle . 'font-weight:bold;background-color:' . $agingBg . ';color:' . $agingColor . ';';

            $urgencyStyle =
                $cellStyle . 'font-weight:bold;background-color:' . $urgencyBg . ';color:' . $urgencyColor . ';';
        @endphp

        <tr>
            <td style="{{ $cellStyle }}font-weight:bold;">{{ $po->order_no }}</td>
            <td style="{{ $textCellStyle }}">{{ $po->client_vendor->name ?? '-' }}</td>
            <td style="{{ $cellStyle }}">{{ $po->unit->vehicle_no ?? '-' }}</td>
            <td style="{{ $cellStyle }}">{{ $po->department ?? '-' }}</td>
            <td style="{{ $cellStyle }}">{{ $formatDate($po->date) }}</td>
            <td style="{{ $cellStyle }}">
                {{ $app_proc_awal?->created_at ? $formatDate($app_proc_awal->created_at) : $formatDate($po->created_at) }}
            </td>
            <td style="{{ $cellStyle }}">
                {{ $app_proc_akhir?->action === 'Approved' && $app_proc_akhir?->updated_at
                    ? $formatDate($app_proc_akhir->updated_at)
                    : $formatDate($po->created_at) }}
            </td>
            <td style="{{ $agingStyle }}">{{ $selisih_hari }} Hari</td>
            <td style="{{ $urgencyStyle }}">{{ $urgencyLabel }}</td>
            <td style="{{ $textCellStyle }}">{{ $po->remarks ?? '-' }}</td>
            <td style="{{ $cellStyle }}">{{ $po->payment_status ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="11" style="{{ $cellStyle }}padding:10px;">
                Tidak ada data Purchase Order.
            </td>
        </tr>
    @endforelse
</table>
