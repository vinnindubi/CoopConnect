<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupAchievement extends Model
{
    protected $fillable= ['title','date_achieved','description','image_path'];
    public function group(){
        return $this -> belongsTo(Group::class);
    }
}
