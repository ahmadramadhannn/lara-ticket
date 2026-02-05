<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'bus_id',
        'bus_operator_id',
        'departure_time',
        'arrival_time',
        'base_price',
        'available_seats',
        'status',
        'arrival_status',
        'confirmed_by',
        'confirmed_at',
        'confirmation_notes',
    ];

    protected function casts(): array
    {
        return [
            'departure_time' => 'datetime',
            'arrival_time' => 'datetime',
            'base_price' => 'float',
            'confirmed_at' => 'datetime',
        ];
    }

    // Arrival confirmation methods
    public function confirmArrival(User $user, ?string $notes = null): void
    {
        $this->update([
            'arrival_status' => 'confirmed',
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
            'confirmation_notes' => $notes,
        ]);
    }

    public function rejectArrival(User $user, ?string $notes = null): void
    {
        $this->update([
            'arrival_status' => 'rejected',
            'confirmed_by' => $user->id,
            'confirmed_at' => now(),
            'confirmation_notes' => $notes,
        ]);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function busOperator(): BelongsTo
    {
        return $this->belongsTo(BusOperator::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->base_price, 0, ',', '.');
    }

    public function getDurationAttribute(): int
    {
        return $this->departure_time->diffInMinutes($this->arrival_time);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->where('departure_time', '>', now());
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('departure_time', $date);
    }

    public function scopeFromTerminal($query, $terminalId)
    {
        return $query->whereHas('route', function ($q) use ($terminalId) {
            $q->where('origin_terminal_id', $terminalId);
        });
    }

    public function scopeToTerminal($query, $terminalId)
    {
        return $query->whereHas('route', function ($q) use ($terminalId) {
            $q->where('destination_terminal_id', $terminalId);
        });
    }

    public function scopeByOperator($query, $operatorId)
    {
        return $query->where('bus_operator_id', $operatorId);
    }

    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('base_price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('base_price', '<=', $maxPrice);
        }

        return $query;
    }

    public function scopeByBusClass($query, array $classIds)
    {
        return $query->whereHas('bus', function ($q) use ($classIds) {
            $q->whereIn('bus_class_id', $classIds);
        });
    }
}
