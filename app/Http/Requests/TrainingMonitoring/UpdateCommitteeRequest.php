<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCommitteeRequest extends FormRequest
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
        $committee = request()->route('committee');

        $rules = [
            'committee_level' => 'required|string',
            'name' => 'required|string|unique:committees,name,' . $committee->id,
            'district_id' => 'nullable|numeric',
            'upazila_id' => 'nullable|numeric',
        ];
        if (request()->committee_level == 'Upazila') {
            $rules = [
                'committee_level' => 'required|string',
                'name' => 'required|string|unique:committees,name,' . $committee->id,
                'district_id' => 'required|numeric',
                'upazila_id' => 'required|numeric',
            ];
        }
        if (request()->committee_level == 'District') {
            $rules = [
                'committee_level' => 'required|string',
                'name' => 'required|string|unique:committees,name,' . $committee->id,
                'district_id' => 'required|numeric',
                'upazila_id' => 'nullable|numeric',
            ];
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        if (request()->committee_level == 'Upazila') {
            $message = [
                'committee_type.required' => 'Please select the committee level',
                'name.required' => 'Please Enter Committee Name.',
                'name.unique' => 'Committee Name should be unique.',
                'name.regex' => 'The Committee Name field can only contain letters, numbers, hyphens, dots, spaces and parentheses.',
                'district_id.required' => 'District id should be required value',
                'upazila_id.required' => 'Upazila id should be required value',
                'district_id.numeric' => 'District id should be numeric value',
                'upazila_id.numeric' => 'Upazila id should be numeric value',
            ];
        }
        if (request()->committee_level == 'District') {
            $message = [
                'committee_type.required' => 'Please select the committee level',
                'name.required' => 'Please Enter Committee Name.',
                'name.unique' => 'Committee Name should be unique.',
                'name.regex' => 'The Committee Name field can only contain letters, numbers, hyphens, dots, spaces and parentheses.',
                'district_id.required' => 'District id should be required value',
                'district_id.numeric' => 'District id should be numeric value',
                'upazila_id.numeric' => 'Upazila id should be numeric value',
            ];
        }

        return $message;
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
