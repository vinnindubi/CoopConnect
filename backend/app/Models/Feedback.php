<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'rating',
        'details',
        'attachment_path',
        'is_anonymous',
        'allow_contact'
    ];
}
