<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Yajra\DataTables\Html\Editor\Fields\BelongsTo;

class Mechanical_inspection extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($mechanical_inspection) {
            $mechanical_inspection->inpection_no = running_number()
                ->type('inspection')
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
}
