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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class)->withDefault(['name' => null]);
    }

    public function daily_report(): BelongsTo
    {
        return $this->belongsTo(Daily_report::class)->withDefault(['report_no' => null]);
    }

    public function daily_report_unit(): HasMany
    {
        return $this->hasMany(Daily_report_unit::class);
    }
}
