<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttendanceTakeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'trainees' => 'required|array|min:1',
            'trainees.*' => 'required|exists:tms_class_attendances,ProfileId',
            'batch_schedule_detail_id' => 'required|exists:tms_class_attendances,batch_schedule_detail_id',
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
