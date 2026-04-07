<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public function members(){
        return $this ->belongsToMany(User::class)
                    ->withPivot('role','title')
                    -> withTimestamps();
    }
    public function leaders(){
    return $this->members()->wherePivotNotNull('title');
    }
    public function posts(){
        return $this->hasMany(GroupPost::class)->latest();
        }
    public function events(){
    return $this->hasMany(GroupEvent::class)->orderBy('event_date', 'asc');
    }
    public function achievements(){
    return $this->hasMany(GroupAchievement::class)->latest();
    }
}
