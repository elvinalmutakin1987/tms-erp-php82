<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit_brand extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function unit_model(): HasMany
    {
        return $this->hasMany(Unit_model::class);
    }

    public function unit(): HasMany
    {
        return $this->hasMany(Unit::class);
    }
}
