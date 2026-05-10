<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Purchase_order_payment extends Model
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];


    public function purchase_order(): BelongsTo
    {
        return $this->belongsTo(Purchase_order::class);
    }
}
