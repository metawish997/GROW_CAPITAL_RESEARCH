<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsignAgreement extends Model
{
    protected $fillable = [
        'user_id',
        'document_path',
        'ip_address',
        'signed_at',
        'digio_document_id',
        'esign_url',
        'status',
        'is_signed',
        'email_logs',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'email_logs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
