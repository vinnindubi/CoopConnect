<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ForumPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'excerpt', 'category', 'upvotes', 'is_hot', 'tags'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_hot' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class);
    }

    // This dynamically generates the "2 hours ago" format your Vue expects
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}