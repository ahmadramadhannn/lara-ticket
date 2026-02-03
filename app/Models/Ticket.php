<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'booking_code',
        'qr_code',
        'seat_number',
        'passenger_name',
        'passenger_id_number',
        'price',
        'status',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->booking_code)) {
                $ticket->booking_code = self::generateBookingCode();
            }
        });
    }

    public static function generateBookingCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('booking_code', $code)->exists());

        return $code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function reschedules(): HasMany
    {
        return $this->hasMany(TicketReschedule::class);
    }

    public function payment(): MorphOne
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function isVerifiable(): bool
    {
        return $this->status === 'confirmed'
            && $this->schedule->departure_time->isToday()
            && $this->schedule->status === 'boarding';
    }

    public function isReschedulable(): bool
    {
        return $this->status === 'confirmed'
            && $this->schedule->departure_time->gt(now()->addHours(24));
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeUpcoming($query)
    {
        return $query->whereHas('schedule', function ($q) {
            $q->where('departure_time', '>', now());
        });
    }

    public function scopePast($query)
    {
        return $query->whereHas('schedule', function ($q) {
            $q->where('departure_time', '<', now());
        });
    }
}
