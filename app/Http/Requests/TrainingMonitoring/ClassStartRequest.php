<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClassStartRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'schedule_detail_id' => 'required|exists:tms_batch_schedule_details,id',
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'message' => $validator->messages(),
        ]));
    }
}
