<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Approval_process extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function approval_flow(): BelongsTo
    {
        return $this->belongsTo(Approval_flow::class);
    }

    public function approval_step(): BelongsTo
    {
        return $this->belongsTo(Approval_step::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
