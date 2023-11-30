<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProviderRequest extends FormRequest
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
            "name.required" => trans('provider-list.name_required'),
            "name.regex" =>  trans('provider-list.name_regex'),
            "mobile.required" => trans('provider-list.mobile_required'),
            "mobile.regex" => trans('provider-list.mobile_regex'),
            "email.email" => trans('provider-list.email_email'),
            "address.regex" =>trans('provider-list.address_regex'),
            //"web_url.regex" => trans('provider-list.web_url_regex'),

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
        ],));
    }
}
