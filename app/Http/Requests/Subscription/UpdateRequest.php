<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Subscription;

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
            'title' => 'required|max:100',
            'price' => 'required|numeric',
            'description' => 'required',
            'items' => 'required|array',
            'duration' => 'required|numeric',
            'duration_type' => 'required|string',
        ];
    }
    
    /**
     * Check if subscription title is already taken.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
	 * @throws Illuminate\Validation\ValidationException;
     */
    public function withValidator(Validator $validator)
    {
		$title = $validator->getData()['title'];
		$uuID = $this->uuid;
		
        $validator->after(function ($validator) use ($uuID, $title) {
				$titles = Subscription::where('title', $title)->where('uuid', '!=', $uuID)->get();
				
				if($titles->isNotEmpty()) {
					$validator->errors()->add(
						'title', "The title has already been taken."
					);
				}
		});
    }
	
}
