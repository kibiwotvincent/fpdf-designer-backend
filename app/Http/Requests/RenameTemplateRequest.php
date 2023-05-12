<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Template;
use Illuminate\Validation\ValidationException;

class RenameTemplateRequest extends FormRequest
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
            'name' => 'required',
        ];
    }
	
	/**
     * Check if template name is already taken.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
	 * @throws Illuminate\Validation\ValidationException;
     */
    public function withValidator(Validator $validator)
    {
		$name = $validator->getData()['name'];
		$uuID = $this->uuid;
		
        $validator->after(function ($validator) use ($uuID, $name) {
				$names = Template::where('name', $name)->where('uuid', '!=', $uuID)->get();
				
				if($names->isNotEmpty()) {
					$validator->errors()->add(
						'name', "The name has already been taken."
					);
				}
		});
    }
	
}
