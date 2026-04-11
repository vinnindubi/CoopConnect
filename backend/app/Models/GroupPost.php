<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    protected $fillable= ['user_id','group_id','content','likes_count'];
    public function group(){
        return $this ->belongsTo(Group::class, 'group_post');
    }
    public function author(){
        return $this->belongsTo(User::class,'user_id');
    }
    
}
