<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Setting;

class WorkspaceResource extends JsonResource
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
		//get settings
		$settings = Setting::get()->mapWithKeys(function ($setting) {
			return [$setting['config'] => $setting['value']];
		});
		$setup['fonts'] = json_decode($settings['fonts']);
		$setup['page_sizes'] = json_decode($settings['page_sizes']);
		$setup['page_margins'] = json_decode($settings['page_margins']);
		
        return [
				'id' => $this->uuid,
				'name' => $this->name,
				'page_settings' => $this->page_settings,
				'draggables' => $this->draggables,
				'setup' => $setup,
				'text_defaults' => json_decode($settings['text_defaults'])
				];
    }
	
}
