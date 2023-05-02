<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
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
				'thumbnail' => url('storage/templates/'.$this->thumbnail),
				'page_settings' => $this->page_settings,
				'draggables' => $this->draggables,
				'owner_id' => $this->owner_id,
				];
    }
	
}
