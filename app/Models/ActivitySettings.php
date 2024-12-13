<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivitySettings extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'amount_per_score',
        'is_visitor_change_answer',
        'is_text_message',
        'ip_address',
    ];
}
