<?php

namespace App\Http\Requests\TrainingMonitoring;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSubCategoryRequest extends FormRequest
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
        $subcategory = request()->route('subCategory');
        
        return [
            'name' => 'required|regex:/^[A-Za-z0-9\-.\(\) ]+$/|unique:sub_categories,name,' . $subcategory->id,
            'category_id' => 'required|numeric'
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
            'name.required' => trans('sub-categorie-list.name_required'),
            'name.unique' =>  trans('sub-categorie-list.name_unique'),
            'name.regex' =>  trans('sub-categorie-list.name_regex'),
            'category_id.required' =>  trans('sub-categorie-list.category_id_required'),
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
