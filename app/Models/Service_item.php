<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service_item extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class)->withDefault(['name' => null]);
    }
}
