<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['article_id', 'user_id', 'content'];

    // A comment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A comment belongs to an article
    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
