<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Allow Laravel to save data to all columns
    protected $guarded = [];

    // The inverse relationship
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
