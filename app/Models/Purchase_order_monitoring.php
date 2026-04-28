<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Purchase_order_monitoring extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    public function purchase_order(): BelongsTo
    {
        return $this->belongsTo(Purchase_order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['username' => null]);
    }
}
