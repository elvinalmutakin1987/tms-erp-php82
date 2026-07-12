<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit_target extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class)->withDefault(['contract_no' => null]);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withDefault(['vehicle_no' => null]);
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
