<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumPostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'content'     => $this->content,
            'commodity'   => $this->commodity,
            'image_url'   => $this->image_url,
            'status'      => $this->status,
            'is_answered' => (bool) $this->is_answered,
            'views'       => $this->views,
            'reply_count' => $this->reply_count, // accessor dari model
            'created_at'  => $this->created_at->toISOString(),

            'user' => $this->whenLoaded('user', fn() => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
                'role' => $this->user->role,
            ]),

            // Hanya muncul jika replies di-load (endpoint detail)
            'replies' => $this->whenLoaded('replies',
                fn() => ForumReplyResource::collection($this->replies)
            ),

            // Jawaban pakar saja (untuk list view — tampil di card)
            'expert_reply' => $this->whenLoaded('expertReply', fn() => [
                'id'      => $this->expertReply?->id,
                'content' => $this->expertReply?->content,
                'user'    => [
                    'name' => $this->expertReply?->user?->name,
                    'role' => $this->expertReply?->user?->role,
                ],
            ]),
        ];
    }
}