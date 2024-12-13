<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityResponse extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "activity_response";
    protected $fillable = [
        'activity_id',
        'sub_activity_id',
        'visitor_id',
        'option_id',
        'select_count',
        'response_from',
        'unique_name',
        'ip_address',
    ];
}
