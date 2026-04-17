<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedPost extends Model
{
    protected $guarded = [];

    // This tells Laravel: "Go fetch whatever model is attached to this row"
    public function feedable()
    {
        return $this->morphTo();
    }
}
