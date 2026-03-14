<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use OwenIt\Auditing\Contracts\Auditable;

class P2h extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($p2h) {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $p2h->p2h_no = running_number()
                ->type('p2h')
                ->formatter($presenter)
                ->generate();
        });
    }

    public function p2h_detail(): HasMany
    {
        return $this->hasMany(P2h_detail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name' => null]);
    }

    public function checked_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by', 'id')->withDefault(['name' => null]);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withDefault(['vehicle_no' => null]);
    }

    public function maintenance(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
