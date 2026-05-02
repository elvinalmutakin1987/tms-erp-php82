<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Request_quotation extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;

    protected $guarded = [];

    public function purchase_requisition(): BelongsTo
    {
        return $this->belongsTo(Purchase_requisition::class);
    }

    public function client_vendor(): BelongsTo
    {
        return $this->belongsTo(Client_vendor::class);
    }
}
