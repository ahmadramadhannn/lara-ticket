<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
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
        'terminal_id',
        'invited_by',
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

    // =========================================================================
    // Relationships
    // =========================================================================

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

    /**
     * Primary terminal for terminal_admin (quick access).
     */
    public function primaryTerminal(): BelongsTo
    {
        return $this->belongsTo(Terminal::class, 'terminal_id');
    }

    /**
     * All terminals assigned to this terminal_admin (many-to-many).
     */
    public function assignedTerminals(): BelongsToMany
    {
        return $this->belongsToMany(Terminal::class, 'terminal_user')
            ->withPivot(['assignment_type', 'can_manage_schedules', 'can_verify_tickets', 'can_confirm_arrivals'])
            ->withTimestamps();
    }

    /**
     * The company admin who invited this terminal admin.
     */
    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Terminal admins invited by this user.
     */
    public function invitedAdmins(): HasMany
    {
        return $this->hasMany(User::class, 'invited_by');
    }

    // =========================================================================
    // Role Checks
    // =========================================================================

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isCompanyAdmin(): bool
    {
        return $this->role === 'company_admin';
    }

    public function isTerminalAdmin(): bool
    {
        return $this->role === 'terminal_admin';
    }

    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    /**
     * Check if user is any type of operator (company or terminal level).
     * Backward compatibility method.
     */
    public function isOperator(): bool
    {
        return $this->isCompanyAdmin() || $this->isTerminalAdmin();
    }

    /**
     * Backward compatibility: Admin means super_admin or any operator role.
     */
    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || $this->isOperator();
    }

    /**
     * Backward compatibility: Verifiers are terminal admins or company admins.
     */
    public function isVerifier(): bool
    {
        return $this->isOperator();
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // =========================================================================
    // Status Checks
    // =========================================================================

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

    // =========================================================================
    // Company & Terminal Specific Checks
    // =========================================================================

    /**
     * Check if user's bus operator company is approved.
     * Works for both company_admin and terminal_admin.
     */
    public function hasApprovedOperator(): bool
    {
        return $this->busOperator && $this->busOperator->isApproved();
    }

    /**
     * Check if terminal_admin has at least one terminal assignment.
     */
    public function hasTerminalAssignment(): bool
    {
        return $this->assignedTerminals()->exists();
    }

    /**
     * Check if user can manage schedules at a specific terminal.
     */
    public function canManageTerminal(Terminal $terminal): bool
    {
        // Super admins can manage any terminal
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Company admins can manage all terminals for their company's routes
        if ($this->isCompanyAdmin() && $this->hasApprovedOperator()) {
            return true;
        }

        // Terminal admins must be explicitly assigned
        if ($this->isTerminalAdmin()) {
            return $this->assignedTerminals()
                ->where('terminals.id', $terminal->id)
                ->wherePivot('can_manage_schedules', true)
                ->exists();
        }

        return false;
    }

    /**
     * Check if user can verify tickets at a specific terminal.
     */
    public function canVerifyAtTerminal(Terminal $terminal): bool
    {
        if ($this->isSuperAdmin() || $this->isCompanyAdmin()) {
            return true;
        }

        if ($this->isTerminalAdmin()) {
            return $this->assignedTerminals()
                ->where('terminals.id', $terminal->id)
                ->wherePivot('can_verify_tickets', true)
                ->exists();
        }

        return false;
    }

    /**
     * Check if user can confirm arrivals at a destination terminal.
     */
    public function canConfirmArrivalsAt(Terminal $terminal): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isTerminalAdmin()) {
            return $this->assignedTerminals()
                ->where('terminals.id', $terminal->id)
                ->wherePivot('can_confirm_arrivals', true)
                ->exists();
        }

        return false;
    }

    // =========================================================================
    // Filament Panel Access
    // =========================================================================

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'super-admin' => $this->isSuperAdmin(),
            'company-admin' => $this->isCompanyAdmin() && $this->hasApprovedOperator(),
            'terminal-admin' => $this->isTerminalAdmin() && $this->hasApprovedOperator() && $this->hasTerminalAssignment(),
            // Legacy support: redirect old 'operator' panel access to company-admin
            'operator' => $this->isCompanyAdmin() && $this->hasApprovedOperator(),
            default => false,
        };
    }
}
