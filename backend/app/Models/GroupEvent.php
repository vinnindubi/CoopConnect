<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupEvent extends Model
{
    protected $guarded = [];
    protected $fillable=[
        'title',
        'event_type',
        'event_date',
        'description',
        'location',
        'price',
        'organizer',
        'is_featured',
        'image'
    ];
    public function group(){
        return $this ->belongsTo(Group::class);
    }
    // This allows an Event to automatically have a FeedPost
    public function feedPost()
    {
        return $this->morphOne(FeedPost::class, 'feedable');
    }
    protected static function booted()
    {
        // This listens for any time a new Event is saved to the database
        static::created(function ($event) {
            // Automatically generate the Feed Ledger entry!
            $event->feedPost()->create();
        });
        
        //  Automatically delete the feed post if the event is deleted!
        static::deleted(function ($event) {
            if ($event->feedPost) {
                $event->feedPost->delete();
            }
        });
    }
}
