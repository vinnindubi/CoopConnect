<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    public function group(){
        return $this ->belongsTo(Group::class);
    }
    public function author(){
        return $this->belongsTo(User::class,'user_id');
    }
    
}
