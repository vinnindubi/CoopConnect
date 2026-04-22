<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketplaceAdvert extends Model
{
    protected $table = 'marketplace_adverts';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
