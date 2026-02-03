<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'bus_operator_id',
        'user_status',
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

    // Relationships

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function busOperator(): BelongsTo
    {
        return $this->belongsTo(BusOperator::class);
    }

    // Role checks

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    // Backward compatibility
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isOperator();
    }

    public function isVerifier(): bool
    {
        return $this->isOperator();
    }

    // Status checks

    public function isActive(): bool
    {
        return $this->user_status === 'active';
    }

    public function isPending(): bool
    {
        return $this->user_status === 'pending';
    }

    public function isSuspended(): bool
    {
        return $this->user_status === 'suspended';
    }

    // Operator-specific checks

    public function hasApprovedOperator(): bool
    {
        return $this->isOperator() 
            && $this->busOperator 
            && $this->busOperator->isApproved();
    }
}
