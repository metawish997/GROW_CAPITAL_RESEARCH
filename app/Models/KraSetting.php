<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class KraSetting extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'ndml_user_id',
        'ndml_password',
        'ndml_bp_id',
        'ndml_passkey',
        'ndml_encryption_key',
        'ndml_uat_mode',
        'sftp_host',
        'sftp_port',
        'sftp_username',
        'sftp_password',
        'auto_upload_on_approval',
    ];
 
    protected $casts = [
        'ndml_uat_mode' => 'boolean',
        'auto_upload_on_approval' => 'boolean',
        'sftp_port' => 'integer',
    ];
 
    /**
     * Get the single active settings row or return a new empty instance.
     */
    public static function getSettings(): self
    {
        return self::first() ?? new self([
            'ndml_uat_mode' => true,
            'sftp_port' => 22,
            'auto_upload_on_approval' => false
        ]);
    }
}
