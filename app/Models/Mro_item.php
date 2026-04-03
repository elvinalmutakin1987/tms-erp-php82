<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mro_item extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function mro_unit(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'mro_units');
    }

    public function purchase_requisition_detail(): HasMany
    {
        return $this->hasMany(purchase_requisition_detail::class);
    }
}
