<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;

class Purchase_order_payment extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($purchase_order_payment) {
            $presenter = new DatePrefixPresenter('Y/m', '/');
            $purchase_order_payment->payment_no = running_number()
                ->type('po-payment')
                ->formatter($presenter)
                ->generate();
        });
    }


    public function purchase_order(): BelongsTo
    {
        return $this->belongsTo(Purchase_order::class);
    }
}
