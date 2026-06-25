<?php

namespace Tests\Feature;

use App\Http\Controllers\SavingsController;
use App\Models\Loan;
use App\Models\LoanCharge;
use App\Models\LoanProduct;
use App\Models\LoanType;
use App\Models\Savings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class SavingsControllerTest extends TestCase
{
    use DatabaseTransactions;

    private function getOrCreateLoanProduct()
    {
        $loanProduct = LoanProduct::first();
        if (!$loanProduct) {
            $loanType = new LoanType();
            $loanType->name = 'loan-with-open-term';
            $loanType->save();

            $loanProduct = new LoanProduct();
            $loanProduct->name = 'Test Product';
            $loanProduct->loan_type_id = $loanType->id;
            $loanProduct->repayment_order = json_encode(['interest', 'principal']);
            $loanProduct->save();
        }
        return $loanProduct;
    }

    public function test_get_number_of_days_logic()
    {
        $loanProduct = $this->getOrCreateLoanProduct();

        // 1. Create a dummy Loan
        $loan = new Loan();
        $loan->loan_date = '2026-06-01';
        $loan->loan_no = 'LN-TEST-0001';
        $loan->borrower_id = 1; 
        $loan->disbursed_date = '2026-06-01';
        $loan->loan_product_id = $loanProduct->id;
        $loan->save();

        $loanCharge = new LoanCharge();
        $loanCharge->loan_id = $loan->id;
        $loanCharge->membership_fee = 0;
        $loanCharge->membership_fee_paid = 0;
        $loanCharge->save();

        $controller = new SavingsController();

        // Test 1: Fallback (No interest payments yet)
        // Should start from disbursement date
        $result = $controller->getNumberOfDays($loan, '2026-06-15');
        
        $this->assertEquals('06/01/2026', $result['start_date']);
        $this->assertEquals('06/15/2026', $result['end_date']);
        $this->assertEquals(14, $result['number_of_days']); // June 1 to June 15 is 14 days

        // Test 2: Transaction with interest = 0 (only principal paid)
        // Interest start date should NOT advance
        $savings1 = new Savings();
        $savings1->loan_id = $loan->id;
        $savings1->client_id = 1;
        $savings1->transaction_date = '2026-06-10';
        $savings1->transaction_type = 'withdraw';
        $savings1->interest = 0;
        $savings1->interest_start_date = '2026-06-01';
        $savings1->interest_end_date = '2026-06-10';
        $savings1->amount = 1000;
        $savings1->principal = 1000;
        $savings1->save();

        $resultAfterZeroInterest = $controller->getNumberOfDays($loan, '2026-06-15');
        // It should still start from disbursement date (06/01/2026) because interest paid was 0
        $this->assertEquals('06/01/2026', $resultAfterZeroInterest['start_date']);

        // Test 3: Transaction with interest > 0
        // Interest start date should advance to day after interest_end_date
        $savings2 = new Savings();
        $savings2->loan_id = $loan->id;
        $savings2->client_id = 1;
        $savings2->transaction_date = '2026-06-12';
        $savings2->transaction_type = 'withdraw';
        $savings2->interest = 150;
        $savings2->interest_start_date = '2026-06-01';
        $savings2->interest_end_date = '2026-06-12';
        $savings2->amount = 1150;
        $savings2->principal = 1000;
        $savings2->save();

        $resultAfterPaidInterest = $controller->getNumberOfDays($loan, '2026-06-15');
        // The day after savings2's interest_end_date (2026-06-12) is 2026-06-13
        $this->assertEquals('06/13/2026', $resultAfterPaidInterest['start_date']);
        $this->assertEquals('06/15/2026', $resultAfterPaidInterest['end_date']);
        $this->assertEquals(2, $resultAfterPaidInterest['number_of_days']); // June 13 to June 15 is 2 days
    }

    public function test_get_fees_with_matching_dates_returns_zero_interest()
    {
        $loanProduct = $this->getOrCreateLoanProduct();

        // Create a dummy Loan with 5% interest rate and 10000 principal balance
        $loan = new Loan();
        $loan->loan_date = '2026-06-01';
        $loan->loan_no = 'LN-TEST-0002';
        $loan->borrower_id = 1; 
        $loan->disbursed_date = '2026-06-01';
        $loan->interest_rate = 5;
        $loan->principal = 10000;
        $loan->loan_product_id = $loanProduct->id;
        $loan->save();

        $loanCharge = new LoanCharge();
        $loanCharge->loan_id = $loan->id;
        $loanCharge->membership_fee = 0;
        $loanCharge->membership_fee_paid = 0;
        $loanCharge->save();

        $controller = new SavingsController();

        // Check when interest period is the same as the start date (06/01/2026)
        $request = new Request();
        $request->merge([
            'id' => $loan->id,
            'interest_period' => '2026-06-01',
            'transaction_date' => '2026-06-01'
        ]);

        $response = $controller->getFees($request);
        $data = json_decode($response->getContent(), true);

        // Interest should be 0.00 and current_interest should be 0.00
        $this->assertEquals('0.00', $data['interest']);
        $this->assertEquals('0.00', $data['current_interest']);

        // Check when interest period is before the start date (e.g. 2026-05-25)
        $request2 = new Request();
        $request2->merge([
            'id' => $loan->id,
            'interest_period' => '2026-05-25',
            'transaction_date' => '2026-06-01'
        ]);

        $response2 = $controller->getFees($request2);
        $data2 = json_decode($response2->getContent(), true);

        // Interest should be 0.00 and current_interest should be 0.00
        $this->assertEquals('0.00', $data2['interest']);
        $this->assertEquals('0.00', $data2['current_interest']);
    }

    public function test_savings_store_validation_rejects_zero_amount()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('savings.store'), [
                'amount' => '0.00',
                'transaction_type' => 'withdraw',
                'transaction_date' => '2026-06-23',
            ]);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_savings_store_with_slip_amount_mismatch_due_to_type_strictness()
    {
        $user = User::factory()->create();
        
        // 1. Create client
        $client = new \App\Models\Client();
        $client->branch_id = 1;
        $client->status = 1;
        $client->save();

        // 2. Create client details
        $details = new \App\Models\ClientDetails();
        $details->client_id = $client->id;
        $details->firstname = 'John';
        $details->lastname = 'Doe';
        $details->save();

        // 3. Create a loan
        $loanProduct = $this->getOrCreateLoanProduct();
        $loan = new Loan();
        $loan->loan_date = '2026-06-01';
        $loan->loan_no = 'LN-TEST-0003';
        $loan->borrower_id = $client->id;
        $loan->disbursed_date = '2026-06-01';
        $loan->loan_product_id = $loanProduct->id;
        $loan->principal = 10000;
        $loan->save();

        $loanCharge = new LoanCharge();
        $loanCharge->loan_id = $loan->id;
        $loanCharge->membership_fee = 0;
        $loanCharge->membership_fee_paid = 0;
        $loanCharge->save();

        // 4. Create deposit savings of 56000 (so the client has enough balance)
        // One of the deposit savings has transaction_code = '123497', amount = 2000
        $deposit1 = new Savings();
        $deposit1->client_id = $client->id;
        $deposit1->transaction_no = 'DEP-001';
        $deposit1->transaction_date = '2026-06-10';
        $deposit1->transaction_type = 'deposit';
        $deposit1->amount = 54000;
        $deposit1->mode_of_payment = 'cash';
        $deposit1->save();

        $deposit2 = new Savings();
        $deposit2->client_id = $client->id;
        $deposit2->transaction_no = 'DEP-002';
        $deposit2->transaction_date = '2026-06-12';
        $deposit2->transaction_type = 'deposit';
        $deposit2->amount = 2000;
        $deposit2->transaction_code = '123497';
        $deposit2->mode_of_payment = 'atm_withdrawal';
        $deposit2->save();

        // Let's assert the client balance is indeed 56000
        $this->assertEquals('56,000.00', getClientBalance($client));

        // 5. Submit withdraw transaction for 2000, using the slip '123497'
        $response = $this->actingAs($user)
            ->post(route('savings.store'), [
                'client_id' => $client->id,
                'amount' => '2,000.00',
                'transaction_type' => 'withdraw',
                'transaction_date' => '2026-06-24',
                'mode_of_payment' => 'atm_withdrawal',
                'transaction_code_select' => ['123497'],
                'loan_id' => [$loan->id],
                'loan_amount' => [
                    $loan->id => '2000.00'
                ],
                'principal' => [
                    $loan->id => '2000.00'
                ]
            ]);

        // Expect it to succeed (status 200/success response)
        $response->assertJson([
            'status' => 'success'
        ]);
    }

    public function test_savings_store_with_early_settlement_including_special_loan()
    {
        $user = User::factory()->create();
        
        // 1. Create client
        $client = new \App\Models\Client();
        $client->branch_id = 1;
        $client->status = 1;
        $client->save();

        // 2. Create client details
        $details = new \App\Models\ClientDetails();
        $details->client_id = $client->id;
        $details->firstname = 'Jane';
        $details->lastname = 'Smith';
        $details->save();

        // 3. Create a regular loan
        $loanProduct = $this->getOrCreateLoanProduct();
        $regularLoan = new Loan();
        $regularLoan->loan_date = '2026-06-01';
        $regularLoan->loan_no = 'LN-REGULAR-123';
        $regularLoan->borrower_id = $client->id;
        $regularLoan->disbursed_date = '2026-06-01';
        $regularLoan->loan_product_id = $loanProduct->id;
        $regularLoan->principal = 10000;
        $regularLoan->save();

        $regularLoanCharge = new LoanCharge();
        $regularLoanCharge->loan_id = $regularLoan->id;
        $regularLoanCharge->membership_fee = 0;
        $regularLoanCharge->membership_fee_paid = 0;
        $regularLoanCharge->save();

        // 4. Create a special loan
        $specialLoanProduct = LoanProduct::where('name', 'Special Product')->first();
        if (!$specialLoanProduct) {
            $specialLoanProduct = new LoanProduct();
            $specialLoanProduct->name = 'Special Product';
            $specialLoanProduct->loan_type_id = $loanProduct->loan_type_id;
            $specialLoanProduct->repayment_order = json_encode(['interest', 'principal']);
            $specialLoanProduct->save();
        }

        $specialLoan = new Loan();
        $specialLoan->loan_date = '2026-06-05';
        $specialLoan->loan_no = 'LN-SPECIAL-456';
        $specialLoan->borrower_id = $client->id;
        $specialLoan->disbursed_date = '2026-06-05';
        $specialLoan->loan_product_id = $specialLoanProduct->id;
        $specialLoan->principal = 5000;
        $specialLoan->is_special = 1;
        $specialLoan->status = 1; // APPROVE
        $specialLoan->save();

        $specialLoanCharge = new LoanCharge();
        $specialLoanCharge->loan_id = $specialLoan->id;
        $specialLoanCharge->membership_fee = 0;
        $specialLoanCharge->membership_fee_paid = 0;
        $specialLoanCharge->save();

        // 5. Create deposit savings of 20000 (so the client has enough balance)
        $deposit = new Savings();
        $deposit->client_id = $client->id;
        $deposit->transaction_no = 'DEP-003';
        $deposit->transaction_date = '2026-06-10';
        $deposit->transaction_type = 'deposit';
        $deposit->amount = 20000;
        $deposit->mode_of_payment = 'cash';
        $deposit->save();

        // Let's assert the client balance is indeed 20000
        $this->assertEquals('20,000.00', getClientBalance($client));

        // 6. Submit withdraw transaction for early settlement of regular loan (10,000)
        // AND include special loan balance (5,000), total amount = 15,000
        $response = $this->actingAs($user)
            ->post(route('savings.store'), [
                'client_id' => $client->id,
                'amount' => '15,000.00',
                'transaction_type' => 'withdraw',
                'transaction_date' => '2026-06-24',
                'mode_of_payment' => 'cash',
                'loan_id' => [$regularLoan->id],
                'loan_amount' => [
                    $regularLoan->id => '15000.00'
                ],
                'interest' => [
                    $regularLoan->id => '0.00'
                ],
                'insurance' => [
                    $regularLoan->id => '0.00'
                ],
                'membership_fee' => [
                    $regularLoan->id => '0.00'
                ],
                'principal' => [
                    $regularLoan->id => '15000.00' // 10000 regular principal + 5000 special principal
                ],
                'is_early_settlement' => [
                    $regularLoan->id => '1'
                ],
                'include_special_loan_balance' => [
                    $regularLoan->id => '1'
                ]
            ]);

        // Expect it to succeed (status 200/success response)
        $response->assertJson([
            'status' => 'success'
        ]);

        // Check that regular loan balance is 0 and status is CLOSED (5)
        $regularLoan->refresh();
        $this->assertEquals(0, getLoanBalance($regularLoan));
        $this->assertEquals(5, $regularLoan->status); // CLOSED

        // Check that special loan balance is 0 and status is CLOSED (5)
        $specialLoan->refresh();
        $this->assertEquals(0, getLoanBalance($specialLoan));
        $this->assertEquals(5, $specialLoan->status); // CLOSED

        // Check that savings has two withdraw records
        $withdrawals = Savings::where('client_id', $client->id)->where('transaction_type', 'withdraw')->get();
        $this->assertCount(2, $withdrawals);

        // Check client balance is now 5,000 (20,000 - 15,000)
        $client->refresh();
        $this->assertEquals('5,000.00', getClientBalance($client));
    }
}

