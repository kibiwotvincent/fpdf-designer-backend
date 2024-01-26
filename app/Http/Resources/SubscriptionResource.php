<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		return [
				'id' => $this->uuid,
				'title' => $this->title,
				'description' => $this->description,
                'price' => number_format($this->price, 2),
                'duration' => $this->duration,
                'duration_type' => $this->duration_type,
                'items' => $this->items,
                'deleted' => $this->trashed()
        ];
    }
	
}
