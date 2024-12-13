<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeZones extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'time_zone';

    protected $fillable = [
        'zone_value',
        'zone_text',
    ];
}
