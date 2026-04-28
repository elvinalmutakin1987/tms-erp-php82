<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;


class Purchase_order extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($purchase_order) {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $purchase_order->requisition_no = running_number()
                ->type('po')
                ->formatter($presenter)
                ->generate();
        });
    }

    public function purchase_order_detail(): HasMany
    {
        return $this->hasMany(Purchase_order_detail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['username' => null]);
    }

    public function purchase_requisition(): BelongsTo
    {
        return $this->belongsTo(Purchase_requisition::class)->withDefault(['requisition_no' => null]);
    }
}
