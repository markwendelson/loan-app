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
        $rules = [
            'loan_product_id' => 'required',
            'loan_date' => 'required|date',
            'applied_amount' => 'required',
            'net_proceeds' => 'numeric|min:0',
            'client_comaker' => 'required_unless:loan_type,mortgage-loan',
        ];

        $isAdditional = filter_var($request->is_additional_loan ?? false, FILTER_VALIDATE_BOOLEAN);
        $isSpecial = false;
        if ($request->loan_product_id) {
            $product = \App\Models\LoanProduct::with('prerequisite')->find($request->loan_product_id);
            if ($product && $product->prerequisite->isNotEmpty()) {
                $isSpecial = true;
            }
        }
        $hasPriorLoans = false;
        if ($request->borrower_id) {
            $hasPriorLoans = \App\Models\Loan::where('borrower_id', $request->borrower_id)->exists();
        }

        if (!$isAdditional && !$isSpecial && !$hasPriorLoans) {
            $rules['client_documents'] = 'required';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'loan_product_id.required' => 'Please select loan product.',
            'loan_date.required'       => 'Please provide the loan date.',
            'loan_date.date'           => 'The loan date must be a valid date.',
            'applied_amount.required'  => 'Please enter the amount.',
            'client_comaker.required'  => 'Please add co-marker.',
            'client_documents.required'  => 'Please add required documents.',
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
            'membership_fee'    => str_replace(',', '', $this->membership_fee),
            'principal_advance_monthly' => str_replace(',', '', $this->principal_advance_monthly) == "" ? 0 : str_replace(',', '', $this->principal_advance_monthly),
            'principal_advance_duration' => $this->principal_advance_duration == "" ? 0 : (int)$this->principal_advance_duration,
            'principal_advance'    => str_replace(',', '', $this->principal_advance) == "" ? 0 : str_replace(',', '', $this->principal_advance),
            'paid_interest_advance'    => str_replace(',', '', $this->paid_interest_advance),
            'loan_no'           => \App\Models\Loan::generateLoanNumber($this->loan_date),
            'is_additional_loan'    => $this->is_additional_loan ?? 0,
        ]);
    }
}
