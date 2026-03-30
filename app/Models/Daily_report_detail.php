<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Daily_report_detail extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function daily_report(): BelongsTo
    {
        return $this->belongsTo(Daily_report::class)->withDefault(['report_no' => null]);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withDefault(['vehicle_no' => null]);
    }
}
