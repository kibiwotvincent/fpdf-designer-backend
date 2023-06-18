<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRolesRequest extends FormRequest
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
            'roles' => 'required|array',
        ];
    }
	
	/**
     * Change error message for roles.required attribute.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'roles.required' => 'Please select atleast one role'
        ];
    }
}
