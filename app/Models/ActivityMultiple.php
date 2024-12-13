<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityMultiple extends Model
{
    use HasFactory;
    protected $fillable = [
        'activity_id',
        'question',
        'sort_order',
        'ip_address',
    ];
}
