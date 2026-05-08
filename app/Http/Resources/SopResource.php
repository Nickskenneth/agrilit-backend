<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'commodity'        => $this->commodity,
            'description'      => $this->description,
            'duration_days'    => $this->duration_days,
            'thumbnail_url'    => $this->thumbnail_url,
            'monthly_calendar' => $this->monthly_calendar, // sudah auto cast array
            'inputs_needed'    => $this->inputs_needed,
            'updated_at'       => $this->updated_at->toISOString(),

            'author' => $this->whenLoaded('author', fn() => [
                'id'   => $this->author->id,
                'name' => $this->author->name,
            ]),
        ];
    }
}