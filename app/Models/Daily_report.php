<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Daily_report extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($daily_report) {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $daily_report->report_no = running_number()
                ->type('rep')
                ->formatter($presenter)
                ->generate();
        });
    }

    public function daily_report_detail(): HasMany
    {
        return $this->hasMany(Daily_report_detail::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withDefault(['vehicle_no' => null]);
    }
}
