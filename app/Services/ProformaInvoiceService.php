<?php

namespace App\Services;


use App\Models\Contract;
use App\Models\Contract_fmf;
use App\Models\Contract_rate;
use App\Models\Maintenance;
use App\Models\Proforma_invoice;
use App\Models\Unit_target;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProformaInvoiceService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    function genProformaInvoice(Contract $contract, string $year, string $month): array
    {
        $contract_fmf = Contract_fmf::where('contract_id', '=', $contract->id)
            ->where('year', $year)
            ->first();
        $fix_monthly_fee = $contract_fmf->value ?? 0;
        $contract_rate = Contract_rate::where('contract_id', $contract->id)->get();
        $unit_target = Unit_target::where('contract_id', $contract->id)->get();
        return [
            'contract' => $contract,
            'year' => $year,
            'month' => $month,
            'contract_fmf' => $contract_fmf,
            'fix_monthly_fee' => $fix_monthly_fee,
            'contract_rate' => $contract_rate,
            'unit_target' => $unit_target
        ];
    }

    function saveProforvaInvoice(array $data): void {}

    function checkProformaInvoice(Contract $contract, string $year, string $month): bool
    {
        $periode = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        return Proforma_invoice::where('contract_id', $contract->id)
            ->where('periode', $periode)
            ->exists();
    }

    function getProformaInvoice(Contract $contract, string $year, string $month): Collection
    {
        $periode = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
        return Proforma_invoice::where('contract_id', $contract->id)
            ->where('periode', $periode)
            ->first();
    }

    function genInvoiceFromProforma(Proforma_invoice $proforma_invoice): void {}
}
