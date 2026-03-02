<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit_model extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function unit_brand(): BelongsTo
    {
        return $this->belongsTo(Unit_brand::class)->withDefault(['name' => null]);
    }

    public function unit(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
