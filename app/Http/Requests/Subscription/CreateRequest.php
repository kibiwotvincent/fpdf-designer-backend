<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

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
            'title' => 'required|max:100|unique:subscription_plans',
            'price' => 'required|numeric',
            'description' => 'required',
            'items' => 'required|array',
            'duration' => 'required|numeric',
            'duration_type' => 'required|string',
            'stripe_name' => 'string|nullable',
            'stripe_price_id' => 'string|nullable',
        ];
    }
	
}
