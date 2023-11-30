<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorePermissionRequest extends FormRequest
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
            'name' => 'required|unique:tms_permissions,name',
            'route_name' => 'required|unique:tms_permissions,route_name',
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
            'name.required' => 'Permission name should be required',
            'name.unique' => 'Permission name should be unique',
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
