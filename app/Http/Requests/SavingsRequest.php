<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class SavingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(Request $request): array
    {
        return [
            'amount' => 'required|numeric|gt:0',
            'attachment' => 'required_if:mode_of_change,authorized_person|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'transaction_code' => 'required_if:transaction_type,deposit'
        ];
    }

    public function messages(): array
    {
        return [

        ];
    }


    protected function prepareForValidation()
    {
        $this->merge([
            'amount'    => str_replace(',', '', $this->amount),
        ]);
    }
}
