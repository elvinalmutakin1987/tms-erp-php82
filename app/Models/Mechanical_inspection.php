<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;

class Mechanical_inspection extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($mechanical_inspection) {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $mechanical_inspection->inspection_no = running_number()
                ->type('insp')
                ->formatter($presenter)
                ->generate();
        });
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withDefault(['vehicle_no' => null]);
    }

    public function mechanical_inspection_detail(): HasMany
    {
        return $this->hasMany(Mechanical_inspection_detail::class);
    }

    public function maintenance(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
