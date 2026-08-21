<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'digio_document_id',
        'customer_name',
        'customer_mobile',
        'customer_email',
        'reference_id',
        'transaction_id',
        'workflow',
        'status',
        'kyc_details',
        'aadhaar_details',
        'kyc_completed_at',
        'kyc_expires_at',
        'raw_response',
        'callback_status',
        'callback_message',
    ];

    protected $casts = [
        'kyc_details'      => 'array',
        'aadhaar_details'  => 'array',
        'raw_response'     => 'array',
        'kyc_completed_at' => 'datetime',
        'kyc_expires_at'   => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Query Scopes ─────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'rejected', 'expired']);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('kyc_expires_at')
              ->orWhere('kyc_expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('kyc_expires_at', '<', now());
    }

    // ─── Accessor Helpers ─────────────────────────────────────────

    public function isApproved(): bool
    {
        return in_array(strtolower($this->status), ['approved', 'completed', 'success']);
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'initiated', 'approval_pending']);
    }

    public function isExpired(): bool
    {
        return $this->kyc_expires_at && $this->kyc_expires_at < now();
    }

    public function isActive(): bool
    {
        return $this->isApproved() && !$this->isExpired();
    }

    public function getAadhaarNumberAttribute()
    {
        return $this->aadhaar_details['masked_aadhaar']
            ?? $this->aadhaar_details['aadhaar_number']
            ?? null;
    }

    public function getFullNameAttribute()
    {
        return $this->aadhaar_details['name'] ?? $this->customer_name;
    }

    public function getDateOfBirthAttribute()
    {
        return $this->aadhaar_details['date_of_birth'] ?? null;
    }

    public function getGenderAttribute()
    {
        return $this->aadhaar_details['gender'] ?? null;
    }

    public function getAddressAttribute()
    {
        return $this->aadhaar_details['address'] ?? null;
    }

    public function getStatusWithColorAttribute(): array
    {
        $colors = [
            'approved'         => 'success',
            'completed'        => 'success',
            'pending'          => 'warning',
            'initiated'        => 'info',
            'approval_pending' => 'warning',
            'failed'           => 'danger',
            'rejected'         => 'danger',
            'expired'          => 'secondary',
        ];

        return [
            'text'  => ucfirst($this->status),
            'color' => $colors[$this->status] ?? 'secondary',
        ];
    }

    public function getAgeInDaysAttribute()
    {
        return $this->kyc_completed_at
            ? $this->kyc_completed_at->diffInDays(now())
            : null;
    }

    public function getDaysUntilExpiryAttribute()
    {
        return $this->kyc_expires_at
            ? now()->diffInDays($this->kyc_expires_at, false)
            : null;
    }

    // ─── Actions ──────────────────────────────────────────────────

    public function approve(array $kycDetails = [], array $aadhaarDetails = []): static
    {
        $this->update([
            'status'           => 'approved',
            'kyc_details'      => $kycDetails,
            'aadhaar_details'  => $aadhaarDetails,
            'kyc_completed_at' => now(),
        ]);

        return $this;
    }

    public function reject(string $reason = null): static
    {
        $this->update([
            'status'           => 'rejected',
            'kyc_completed_at' => now(),
            'callback_message' => $reason,
        ]);

        return $this;
    }

    public function markAsFailed(string $error = null): static
    {
        $this->update([
            'status'           => 'failed',
            'kyc_completed_at' => now(),
            'callback_message' => $error,
        ]);

        return $this;
    }

    // ─── Static Helpers ───────────────────────────────────────────

    public static function hasApprovedKyc(string $mobile): bool
    {
        return self::where('customer_mobile', $mobile)->approved()->active()->exists();
    }

    public static function getLatestApprovedKyc(string $mobile)
    {
        return self::where('customer_mobile', $mobile)
            ->approved()->active()
            ->latest('kyc_completed_at')
            ->first();
    }

    public static function findByDocumentId(string $documentId)
    {
        return self::where('digio_document_id', $documentId)->first();
    }

    public static function findByReferenceId(string $referenceId)
    {
        return self::where('reference_id', $referenceId)->first();
    }
}
