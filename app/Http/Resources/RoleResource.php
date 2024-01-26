<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Permission;

class RoleResource extends JsonResource
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
				'id' => $this->id,
				'name' => $this->name,
				'permissions' => $this->permissions->map(function ($row) {
											return $row->name;
										})->all(),
                'all_permissions' => Permission::get()->map(function ($row) { 
                                            return $row->name;
                                        })->all()
				];
    }
	
}
