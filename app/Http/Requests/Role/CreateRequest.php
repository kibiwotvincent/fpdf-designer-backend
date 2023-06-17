<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Spatie\Permission\Models\Role;

class CreateRequest extends FormRequest
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
     * Check if role name is already taken.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
		$name = $validator->getData()['name'];
		
        $validator->after(function ($validator) use ($name) {
				$roles = Role::where(['name' => $name, 'guard_name' => 'web'])->get();
				
				if($roles->isNotEmpty()) {
					$validator->errors()->add(
						'name', "The name has already been taken."
					);
				}
		});
    }
}
