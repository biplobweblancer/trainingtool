<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'image_url' => 'sometimes|nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            "fname" => "required",
            "lname" => "required",
            'phone_number' => 'required',
            "father_name" => "required",
            "mother_name" => "required",
            "nid" => "required",
            "dob" => "required",
            "b_certificate" => "required",
            "district_id" => "required",
            "upazila_id" => "required",
            "address" => "required",
            "employment_status" => "required",
            "financial_status" => "required",
            "past_training" => "required",
            "past_course_name" => "nullable|required",
            "past_course_duration" => "nullable|required",
            "past_provider_id" => "nullable|required",
            "bank_id" => "required",
            "bkash_id" => "required",
            "category_id" => "required",
            "sub_category_id" => "sometimes|nullable",
            "hsc_certificate" => "sometimes|nullable|mimes:pdf,csv,xls,xlsx,doc,docx|max:2048",
            "ssc_certificate" => "sometimes|nullable|mimes:pdf,csv,xls,xlsx,doc,docx|max:2048",
            "signature" => "sometimes|nullable|mimes:pdf,csv,xls,xlsx,doc,docx|max:2048",
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
            //'fname.required' => 'The first name field is required. ',
            //'fname.regex' => 'The first Name field can only contain letters, numbers, hyphens, dots, spaces and parentheses.',
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
