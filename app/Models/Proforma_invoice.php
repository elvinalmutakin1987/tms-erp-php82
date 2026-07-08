<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Proforma_invoice extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function ($proforma_invoice) {
            if (!empty($proforma_invoice->proforma_no)) {
                return;
            }
            $kodeDokumen = 'P-INV';
            if (!empty($proforma_invoice->periode)) {
                $periode = Carbon::createFromFormat('Y-m', $proforma_invoice->periode);
            } elseif (!empty($proforma_invoice->periode_start)) {
                $periode = Carbon::parse($proforma_invoice->periode_start);
            } else {
                $periode = now();
            }
            $year = $periode->format('Y');
            $month = $periode->format('m');
            $proforma_invoice->proforma_no = running_number()
                ->type('pro-inv')
                ->formatter(new class($kodeDokumen, $year, $month) implements Presenter {
                    public function __construct(
                        private string $kodeDokumen,
                        private string $year,
                        private string $month
                    ) {}

                    public function format(string $type, int $number): string
                    {
                        return sprintf(
                            '%s/%s/%s-%03d',
                            $this->kodeDokumen,
                            $this->year,
                            $this->month,
                            $number
                        );
                    }
                })
                ->generate();
        });
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['username' => null]);
    }

    public function client_vendor(): BelongsTo
    {
        return $this->belongsTo(Client_vendor::class)->withDefault(['name' => null]);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class)->withDefault(['contract_no' => null]);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withDefault(['vehicle_no' => null]);
    }

    public function contract_fmf(): BelongsTo
    {
        return $this->belongsTo(Contract_fmf::class);
    }

    public function contract_rate(): BelongsTo
    {
        return $this->belongsTo(Contract_rate::class);
    }

    public function unit_target(): BelongsTo
    {
        return $this->belongsTo(Unit_target::class);
    }
}
