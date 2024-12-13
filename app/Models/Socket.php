<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Socket extends Model
{
    use HasFactory;

     protected $fillable = [
        'type',
        'resource_id',
        'unique_id',
        'activity_id',
        'ip_address',
    ];
}
