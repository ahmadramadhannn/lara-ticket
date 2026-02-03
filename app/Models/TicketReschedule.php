<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReschedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'old_schedule_id',
        'new_schedule_id',
        'old_seat_number',
        'new_seat_number',
        'price_difference',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'price_difference' => 'decimal:2',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function oldSchedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'old_schedule_id');
    }

    public function newSchedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'new_schedule_id');
    }
}
