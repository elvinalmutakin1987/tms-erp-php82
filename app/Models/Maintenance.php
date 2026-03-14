<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Maintenance extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($maintenance) {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $maintenance->maintenance_no = running_number()
                ->type('main')
                ->formatter($presenter)
                ->generate();
        });
    }

    public function maintenance_detail(): HasMany
    {
        return $this->hasMany(Maintenance_detail::class);
    }

    public function client_vendor(): BelongsTo
    {
        return $this->belongsTo(Client_vendor::class)->withDefault(['name' => null]);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withDefault(['vehicle_no' => null]);
    }

    public function p2h(): BelongsTo
    {
        return $this->belongsTo(P2h::class)->withDefault(['p2h_no' => null]);
    }

    public function mechanical_inspection(): BelongsTo
    {
        return $this->belongsTo(Mechanical_inspection::class)->withDefault(['inspection_no' => null]);
    }
}
