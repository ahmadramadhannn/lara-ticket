<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amenities',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
        ];
    }

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }
}
