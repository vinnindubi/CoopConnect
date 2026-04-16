<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupEvent extends Model
{
    protected $fillable=[
        'title',
        'event_type',
        'event_date',
        'description'
    ];
    public function group(){
        return $this ->belongsTo(Group::class);
    }
}
