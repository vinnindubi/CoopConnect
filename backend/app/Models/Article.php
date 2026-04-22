<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
 
  protected $fillable=[
        'title',
        'content',
        'category',
        'excerpt',
        'image',
        'user_id'];

  public function comments(){
    return $this->hasMany(Comment::class);
  }
  public function author()
    {
        // Links to the user who wrote it
        return $this->belongsTo(User::class, 'user_id'); 
    }
    public function feedPost()
    {
        return $this->morphOne(FeedPost::class, 'feedable');
    }
    //  Add the Auto-Create logic
    protected static function booted()
    {
        static::created(function ($article) {
            // Automatically put it in the feed when created
            $article->feedPost()->create(); 
        });

        static::deleted(function ($article) {
            // Automatically remove it from the feed if the article is deleted
            if ($article->feedPost) {
                $article->feedPost()->delete();
            }
        });
    }
}
