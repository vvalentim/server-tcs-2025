<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendMailRequest extends FormRequest
{
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
            'assunto' => 'required|max:255',
            'emailDestinatario' => 'required|email:rfc,dns',
            // 'emailDestinatario' => 'required|email:rfc,dns|exists:users,email',
            'corpo' => 'required|max:10000',
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
            'required' => 'O campo :attribute não pode estar vazio',
            'max' => 'O campo :attribute deve conter no máximo :max caracteres',
            'emailDestinatario.email' => 'O endereço de email do destinatário é inválido',
            // 'emailDestinatario.exists' => 'O endereço de email do destinatário não existe',
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
