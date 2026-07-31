<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use CleaniqueCoders\RunningNumber\Presenters\DatePrefixPresenter;
use CleaniqueCoders\RunningNumber\Contracts\Presenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Invoice_proforma_invoice extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    // use SoftDeletes;

    protected $guarded = [];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class)->withDefault(['invoice_no' => null]);
    }

    public function proforma_invoice(): BelongsTo
    {
        return $this->belongsTo(Proforma_invoice::class)->withDefault(['proforma_no' => null]);
    }
}
