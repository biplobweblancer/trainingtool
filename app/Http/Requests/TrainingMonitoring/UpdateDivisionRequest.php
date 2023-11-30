<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDivisionRequest extends FormRequest
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
        $division = request()->route('division');
        
        return [
            'name' => 'required|regex:/^[A-Za-z0-9\-.\(\) ]+$/|unique:divisions,name,' . $division->id,
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
            'name.required' => 'The division name field is required. ',
            'name.unique' => 'Division Name should be unique.',
            'name.regex' => 'The Division Name field can only contain letters, numbers, hyphens, dots, spaces and parentheses.',
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
