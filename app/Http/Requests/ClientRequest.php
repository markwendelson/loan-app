<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientRequest extends FormRequest
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
            'client_details.client_code' => [
                'unique:clients,client_code',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'client_details.client_code.unique' => 'Then client code ":input" field already exists.',
        ];
    }
}
