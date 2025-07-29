<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class LoanRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        return [
            'loan_product_id' => 'required',
            'loan_date' => 'required|date',
            'applied_amount' => 'required',
            'net_proceeds' => 'numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'loan_product_id.required' => 'Please select loan product.',
            'loan_date.required'       => 'Please provide the loan date.',
            'loan_date.date'           => 'The loan date must be a valid date.',
            'applied_amount.required'  => 'Please enter the amount.',
        ];
    }


    protected function prepareForValidation()
    {
        $this->merge([
            'applied_amount'    => str_replace(',', '', $this->applied_amount),
            'principal'         => str_replace(',', '', $this->applied_amount),
            'advance_interest'  => str_replace(',', '', $this->advance_interest),
            'service_fee'       => str_replace(',', '', $this->service_fee),
            'insurance_fee'     => str_replace(',', '', $this->insurance_fee),
            'notary_fee'        => str_replace(',', '', $this->notary_fee),
            'loan_no'           => \App\Models\Loan::generateLoanNumber($this->loan_date),
            'is_additional_loan'    => $this->is_additional_loan ?? 0,
        ]);
    }
}
