<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Terminal extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'address',
        'code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function originRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'origin_terminal_id');
    }

    public function destinationRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'destination_terminal_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name}, {$this->city->name}";
    }

    /**
     * All terminal admins assigned to this terminal.
     */
    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'terminal_user')
            ->withPivot(['assignment_type', 'can_manage_schedules', 'can_verify_tickets', 'can_confirm_arrivals'])
            ->withTimestamps();
    }

    /**
     * Only active terminal admins.
     */
    public function activeAdmins(): BelongsToMany
    {
        return $this->admins()->where('user_status', 'active');
    }

    /**
     * Admins who can manage schedules at this terminal.
     */
    public function scheduleManagers(): BelongsToMany
    {
        return $this->admins()->wherePivot('can_manage_schedules', true);
    }

    /**
     * Admins who can verify tickets at this terminal.
     */
    public function ticketVerifiers(): BelongsToMany
    {
        return $this->admins()->wherePivot('can_verify_tickets', true);
    }
}
