<?php

namespace App\Http\Requests\TrainingMonitoring;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'fname' => 'required|regex:/^[A-Za-z .]+$/|max:255',
            'lname' => 'required|regex:/^[A-Za-z .]+$/|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|alpha_num|unique:users,username',
            'password' => 'min:8|required_with:confirm_password|same:confirm_password',
            'confirm_password' => 'min:8',
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
            'fname.required' => trans('register.fname_required'),
            'fname.regex' => trans('register.fname_regex'),
            'fname.max' => trans('register.fname_max'),
            'lname.required' => trans('register.lname_required'),
            'lname.regex' => trans('register.lname_regex'),
            'lname.max' => trans('register.lname_max'),
            'email.required' => trans('register.email_required'),
            'username.required' => trans('register.username_required'),
            'username.alpha_num' => trans('register.username_alpha_num'),
            'username.unique' => trans('register.username_unique'),
            'email.unique' => trans('register.email_unique'),
            'role.required' => trans('register.role_required'),
            'password.min' => trans('register.password_min'),
            'password.same' => trans('register.password_same'),
            'confirm_password.same' => trans('register.confirm_password_same'),
            'confirm_password.min' => trans('register.confirm_password_min'),
            'password.required_with' => trans('register.password_required_with'),
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => true,
            'msg' => trans('register.validation_error'),
            'message' => $validator->messages(),
        ]));
    }
}
