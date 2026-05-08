<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumReplyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'content'          => $this->content,
            'image_url'        => $this->image_url,
            'is_expert_answer' => (bool) $this->is_expert_answer,
            'upvotes'          => $this->upvotes,
            'created_at'       => $this->created_at->toISOString(),

            'user' => $this->whenLoaded('user', fn() => [
                'id'        => $this->user->id,
                'name'      => $this->user->name,
                'role'      => $this->user->role,
                'avatar_url' => $this->user->avatar
                                ? asset('storage/' . $this->user->avatar)
                                : null,
            ]),
        ];
    }
}