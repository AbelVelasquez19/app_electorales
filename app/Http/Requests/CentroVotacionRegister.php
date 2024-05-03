<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class CentroVotacionRegister extends FormRequest
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
            'centroVotacion.nombre' => 'required',
            'centroVotacion.direccion' => 'required',
            'centroVotacion.provincia_id' => 'required',
            'centroVotacion.distrito_id' => 'required',
            'centroVotacion.departamento_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'centroVotacion.nombre.required' => 'El campo nombre es requerido.',
            'centroVotacion.direccion.required' => 'El campo dirección es requerido.',
            'centroVotacion.provincia_id.required' => 'El campo provincia es requerido.',
            'centroVotacion.distrito_id.required' => 'El campo distrito es requerido.',
            'centroVotacion.departamento_id.required' => 'El campo corigimiento es requerido.',
        ];
    }
}
