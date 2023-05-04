<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
	/**
	 * Disable data wrapping.
	 *
	 * @var string
	 */
    public static $wrap = null;
		
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
				'id' => $this->uuid,
				'name' => $this->name,
				'thumbnail' => url('storage/documents/image_'.$this->thumbnail),
				'page_settings' => $this->page_settings,
				'draggables' => $this->draggables,
				];
    }
	
}
