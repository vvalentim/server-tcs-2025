<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|max:255',
            'email' => 'required|email:rfc,dns|unique:users',
            'senha' => 'required|min:8|max:20'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório',
            'max' => 'O campo :attribute deve conter no máximo :max caracteres',
            'min' => 'O campo :attribute deve conter no mínimo :min caracteres',
            'email.email' => 'O endereço de email é inválido',
            'email.unique' => 'O endereço de email já pertence a um usuário',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'mensagem' => 'Erro na requisição',
            'erro' => $validator->errors()->first()
        ], 400));
    }
}
