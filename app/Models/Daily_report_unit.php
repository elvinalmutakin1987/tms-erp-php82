<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Daily_report_unit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function daily_report_detail(): BelongsTo
    {
        return $this->belongsTo(Daily_report_detail::class)->withDefault(['loading_at' => null]);
    }
}
