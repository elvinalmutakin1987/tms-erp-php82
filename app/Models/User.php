<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use  Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */

    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'request_token',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function approval_step(): HasMany
    {
        return $this->hasMany(Approval_step::class);
    }

    public function p2h(): HasMany
    {
        return $this->hasMany(P2h::class);
    }

    public function p2h_checked(): HasMany
    {
        return $this->hasMany(P2h::class, 'checked_by', 'id');
    }

    public function mechanical_inspection(): HasMany
    {
        return $this->hasMany(Mechanical_inspection::class);
    }

    public function purchase_requisition(): HasMany
    {
        return $this->hasMany(Purchase_requisition::class);
    }

    public function request_quotation(): HasMany
    {
        return $this->hasMany(Request_quotation::class);
    }
}
