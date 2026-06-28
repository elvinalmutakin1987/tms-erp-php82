<?php

use App\Models\Contract;
use App\Models\Contract_fmf;
use App\Models\Contract_rate;
use App\Models\Maintenance;
use App\Models\Proforma_invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * cek ada ngeset approval gak?
 */
if (! function_exists('genProformaInvoice')) {
    function genProformaInvoice(Contract $contract, string $periode, string $periode_awal, string $periode_akhir): string
    {
        $grand_total = 0;

        /**
         * Kalo ngitung proforma invoicenya rental
         * dihitung jumlah breakdownnya
         * Jadi hitung work duration di table maintenance
         */
        if ($contract->type == 'Unit Rental') {
            $total_breakdown = Maintenance::whereBetween('date', [$periode_awal, $periode_akhir])
                ->selectRaw('ROUND(SUM(TIME_TO_SEC(work_duration)) / 3600, 2) as total_duration_decimal')
                ->value('total_duration_decimal');
        } else if ($contract->type == 'Fuel Truck Rental') {
            $contract_fmf = Contract_fmf::where('contract_id', '=', $contract->id)
                ->where('year', Carbon::parse($periode)->format('Y'))
                ->first();
            $fix_monthly_fee = $contract_fmf->fix_monthly_fee;
            $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
        } else if ($contract->type == 'LCT') {
        };
        return 0;
    }
}
