<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
	protected $authToken = null;
	
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
		
        $data = [
				'id' => $this->id,
				'name' => $this->name,
				'email' => $this->email,
				'roles' => $this->roles->map(function ($row) {
											return $row->name;
										})->all()
				];
				
		if($this->authToken != null) {
			$data['token'] = $authToken;
		}
				
		return $data;
    }
	
	/**
     * Append API auth token to the resource response
     *
     * @param  String $message
     * @return JSON
     */
	public function withToken($token) {
		$this->authToken = $token;
		return $this;
	}
}
