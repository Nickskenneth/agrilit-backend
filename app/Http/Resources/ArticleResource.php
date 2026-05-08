<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'slug'          => $this->slug,
            'excerpt'       => $this->excerpt,
            'content'       => $this->content,
            'thumbnail_url' => $this->thumbnail_url, // accessor dari model
            'commodity'     => $this->commodity,
            'category'      => $this->category,
            'views'         => $this->views,
            'published_at'  => $this->published_at?->toISOString(),
            'updated_at'    => $this->updated_at->toISOString(),

            // Relasi author — hanya muncul jika sudah di-load (with('author'))
            'author' => $this->whenLoaded('author', fn() => [
                'id'   => $this->author->id,
                'name' => $this->author->name,
                'role' => $this->author->role,
            ]),
        ];
    }
}