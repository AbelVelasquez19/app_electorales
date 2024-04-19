<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class SubMenuValidateAdd extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 422));
    }
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
            'role.url' => 'required',
            'role.name' => 'required',
            'role.description' => 'required|min:3',
            'role.order_role' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'role.url.required' => 'El campo url es obligatorio.',
            'role.name.required' => 'El campo nombre es obligatorio.',
            'role.description.required' => 'El campo descripcion es obligatorio.',
            'role.description.min' => 'La descripcion debe tener al menos 3 caracteres.',
            'role.order_role.required' => 'El campo orden es obligatorio',
        ];
    }
}
