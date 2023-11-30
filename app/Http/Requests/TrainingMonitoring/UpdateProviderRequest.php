<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProviderRequest extends FormRequest
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
            "name" => "required|regex:/^[A-Za-z .]+$/",
            "mobile" => "required|regex:/^\+8801[0-9]{9}$/",
            "email" => "nullable|email",
            "address" => "nullable|regex:/^[-0-9A-Za-z.,:#*\/ ]+$/",
            //"web_url" => "nullable|regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "name.required" => "The name field is required.",
            "name.regex" => "The name field should only contain letters, spaces, and dots.",
            "mobile.required" => "The mobile field is required.",
            "mobile.regex" => "The mobile field should be a valid Bangladeshi mobile number in the format +8801XXXXXXXXXX.",
            "email.email" => "Please enter a valid email address.",
            "address.regex" => "The address field should only contain letters, numbers, hyphens, commas, colons, slashes, and spaces.",
            //"web_url.regex" => "Please enter a valid web URL.",
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return json array
     */
    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => $validator->messages(),
        ]));
    }
}
