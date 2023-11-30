<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDistrictRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|unique:districts,name',
            'division_id' => 'required|numeric'
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
            'name.required' => 'Please Enter Districts Name.',
            'name.unique' => 'Districts Name should be unique.',
            'name.regex' => 'The Districts Name field can only contain letters, numbers, hyphens, dots, spaces and parentheses.',
            'division_id.required' => 'Please Enter Division Name.'
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
