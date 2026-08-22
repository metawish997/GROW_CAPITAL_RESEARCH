<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'password',
        'role',
        'otp',
        'otp_expires_at',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'dob',
        'gender',
        'marital_status',
        'pan_card',
        'pan_card_name',
        'father_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at'    => 'datetime',
            'password'          => 'hashed',
            'dob'               => 'date',
        ];
    }

    /**
     * Check if user is admin or staff.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'staff']);
    }

    /**
     * Check if user is super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the latest KYC verification for the user.
     */
    public function kyc()
    {
        return $this->hasOne(KycVerification::class)->latest();
    }

    /**
     * Get the e-sign agreement for the user.
     */
    public function esignAgreement()
    {
        return $this->hasOne(EsignAgreement::class);
    }
}
