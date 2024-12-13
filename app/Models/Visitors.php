<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visitors extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'unique_name',
        'ip_address',
        'browser_agent',
    ];
}
 