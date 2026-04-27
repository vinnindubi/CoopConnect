<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Handle anonymous users safely
        $authorData = $this->author ? [
            'id' => $this->author->id,
            'name' => $this->author->name,
            'avatar' => $this->author->profile_photo_path ?? '', // Adjust to your actual column
            'isVerified' => $this->author->is_verified ?? false, // Adjust to your actual column
        ] : [
            'id' => null,
            'name' => 'Anonymous',
            'avatar' => '',
            'isVerified' => false,
        ];

        return [
            'id' => $this->id,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'category' => $this->category,
            'upvotes' => $this->upvotes,
            'comments' => $this->replies_count ?? 0, // Provided by withCount()
            'timeAgo' => $this->time_ago,
            'hot' => $this->is_hot,
            'author' => $authorData,
        ];
    }
}