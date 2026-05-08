<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiseaseScanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'commodity'         => $this->commodity,
            'result_label'      => $this->result_label,
            'result_label_id'   => $this->result_label_id,
            'confidence_score'  => $this->confidence_score,
            'confidence_percent'=> $this->confidence_percent, // accessor: "93.1%"
            'image_url'         => $this->image_url,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'location_name'     => $this->location_name,
            'synced'            => (bool) $this->synced,
            'scanned_at'        => $this->scanned_at->toISOString(),
            'notes'             => $this->notes,

            'user' => $this->whenLoaded('user', fn() => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),
        ];
    }
}