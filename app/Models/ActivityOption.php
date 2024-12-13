<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'sub_activity_id',
        'sort_order',
        'is_correct',
        'score',
        'option',
        'option_image',
        'option_name',
        'answer_type',
        'select_count',
        'ip_address',
    ];
}
