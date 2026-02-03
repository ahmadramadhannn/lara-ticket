<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_operator_id',
        'bus_class_id',
        'registration_number',
        'total_seats',
        'seat_layout',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'seat_layout' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function busOperator(): BelongsTo
    {
        return $this->belongsTo(BusOperator::class);
    }

    public function busClass(): BelongsTo
    {
        return $this->belongsTo(BusClass::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
