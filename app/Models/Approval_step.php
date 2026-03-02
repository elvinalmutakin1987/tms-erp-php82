<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval_step extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function approval_flow(): BelongsTo
    {
        return $this->belongsTo(Approval_flow::class)->withDefault(['name' => null]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name' => null]);
    }
}
