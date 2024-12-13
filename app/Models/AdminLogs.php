<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'remember_token',
        'browser_agent',
        'ip_address',
        'logged_at',
    ];
}
