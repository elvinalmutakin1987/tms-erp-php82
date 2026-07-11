<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Contract_rate extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class)->withDefault(['contract_no' => null]);
    }

    public function service_item(): BelongsTo
    {
        return $this->belongsTo(Service_item::class)->withDefault(['item_no' => null]);
    }

    public function proforma_invoice(): HasMany
    {
        return $this->hasMany(Proforma_invoice::class);
    }

    public function proforma_invoice_detail(): HasMany
    {
        return $this->hasMany(Proforma_invoice_detail::class);
    }
}
