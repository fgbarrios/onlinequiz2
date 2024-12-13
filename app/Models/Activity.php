<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'title_type',
        'title',
        'title_image',
        'number_of_options_select',
        'total_number_of_select',
        'number_of_select_per_option',
        'is_had_score',
        'Option 1',
        'status',
        'option_text_type',
        'total_responses',
        'ip_address',
        'created_by',
    ];
}
