<?php

namespace App\Http\Requests\TrainingMonitoring;

use App\Rules\ClassDayVerifyRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ScheduleCreateRequest extends FormRequest
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
            'training_batch_id' => 'required|integer|exists:training_batches,id|unique:tms_training_batch_schedules,training_batch_id',
            'class_days' => ['required', 'string'],
            'class_time' => 'required|string|date_format:H:i',
            'class_duration' => 'required|integer|max:10',
        ];
    }

    public function messages()
    {
        return [
            'training_batch_id.unique' => 'Batch schedule already created',
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
