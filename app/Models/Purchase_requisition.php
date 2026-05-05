<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Purchase_requisition extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($purchase_requisition) {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $purchase_requisition->requisition_no = running_number()
                ->type('pr')
                ->formatter($presenter)
                ->generate();
        });
    }

    public function purchase_requisition_detail(): HasMany
    {
        return $this->hasMany(Purchase_requisition_detail::class);
    }

    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class)->withDefault(['maintenance_no' => null]);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withDefault(['vehicle_no' => null]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['username' => null]);
    }

    public function request_quotation(): HasMany
    {
        return $this->hasMany(Request_quotation::class);
    }

    public function purchase_order(): HasMany
    {
        return $this->hasMany(Purchase_order::class);
    }
}
