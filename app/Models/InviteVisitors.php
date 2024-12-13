<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InviteVisitors extends Model
{
    use HasFactory;
    protected $fillable = [
        'emails',
        'message',
        'ip_address',
    ];
}
