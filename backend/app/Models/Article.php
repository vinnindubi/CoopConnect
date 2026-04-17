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
}
