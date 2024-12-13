<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneralSettings extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'text_message_status',
        'invited_email_address',
        'invite_message',
        'smtp_mailer',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'twilio_secret_key',
        'twilio_token',
        'twilio_from_code',
        'twilio_from_number',
    ];
} 
