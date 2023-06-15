<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;

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
            'permissions' => 'required|array',
        ];
    }
	
	/**
     * Change error message for permissions.required attribute.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'permissions.required' => 'Please select atleast one permission'
        ];
    }
}
