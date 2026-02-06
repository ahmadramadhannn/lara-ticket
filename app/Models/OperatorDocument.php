<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OperatorDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_operator_id',
        'document_type',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // Relationships

    public function busOperator(): BelongsTo
    {
        return $this->belongsTo(BusOperator::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Accessors

    public function getDocumentTypeNameAttribute(): string
    {
        return match ($this->document_type) {
            'business_license' => 'Business License',
            'business_permit' => 'Business Permit',
            'tax_id' => 'Tax ID Document',
            'other' => 'Other Document',
            default => ucfirst(str_replace('_', ' ', $this->document_type)),
        };
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return round($bytes / 1048576, 2) . ' MB';
        }
    }

    // Helper methods

    public function markAsVerified(int $userId, ?string $notes = null): bool
    {
        return $this->update([
            'is_verified' => true,
            'verified_by' => $userId,
            'verified_at' => now(),
            'notes' => $notes,
        ]);
    }
}
