<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceProduct extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'user_id', 
        'title', 
        'price', 
        'category', 
        'condition', 
        'description', 
        'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
