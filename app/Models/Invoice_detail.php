<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Invoice_detail extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
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

    public function service_item(): BelongsTo
    {
        return $this->belongsTo(Service_item::class);
    }
}
