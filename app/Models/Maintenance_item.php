<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Maintenance_item extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function maintenance_detail(): HasMany
    {
        return $this->hasMany(Maintenance_detail::class);
    }

    public function purchase_requisition_detail(): HasMany
    {
        return $this->hasMany(purchase_requisition_detail::class);
    }
}
