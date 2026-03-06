<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Maintenance_detail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function maintenance_item(): BelongsTo
    {
        return $this->belongsTo(Maintenance_item::class)->withDefault(['name' => null]);
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class)->withDefault(['maintenance_no' => null]);
    }
}
