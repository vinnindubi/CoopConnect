<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable =['type','category','name','description','meeting_time','patron_name','proposal_document_path','mission','cover_image','meeting_venue','contact_email','slug'  ];
    public function members(){
        return $this ->belongsToMany(User::class,'group_user')
                    ->withPivot('role','title')
                    -> withTimestamps();
    }
    public function leaders(){
    return $this->members()->whereNotNull('group_user.title');
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
