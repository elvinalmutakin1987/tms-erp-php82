<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withDefault(['name' => null]);
    }

    public function unit_brand(): BelongsTo
    {
        return $this->belongsTo(Unit_brand::class)->withDefault(['name' => null]);
    }

    public function unit_model(): BelongsTo
    {
        return $this->belongsTo(Unit_model::class)->withDefault(['desc' => null]);
    }

    public function unit_rate(): HasMany
    {
        return $this->hasMany(Unit_rate::class);
    }

    public function mro_unit(): HasMany
    {
        return $this->hasMany(Mro_unit::class);
    }

    public function mechanical_inspection(): HasMany
    {
        return $this->hasMany(Mechanical_inspection::class);
    }

    public function p2h(): HasMany
    {
        return $this->hasMany(P2h::class);
    }

    public function maintenance(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function purchase_requisition(): HasMany
    {
        return $this->hasMany(Purchase_requisition::class);
    }
}
