<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Spatie\Permission\Models\Permission;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:100',
        ];
    }
	
	/**
     * Check if permission name is already taken.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
		$name = $validator->getData()['name'];
		$permissionID = $this->id;
		
        $validator->after(function ($validator) use ($permissionID, $name) {
				$permissions = Permission::where(['name' => $name, 'guard_name' => 'web'])->get()->except([$permissionID]);
				
				if($permissions->isNotEmpty()) {
					$validator->errors()->add(
						'name', "The name has already been taken."
					);
				}
		});
    }
}
