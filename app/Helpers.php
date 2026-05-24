<?php

use App\Enums\LoanStatus;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\BranchUser;
use App\Models\CountryCode;
use App\Models\JobTitle;
use App\Models\Province;
use App\Models\Client;
use App\Models\ClientMembershipFee;
use App\Models\Loan;
use App\Models\Savings;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

if (!function_exists('getProvinces')) {
    function getProvinces()
    {

        $result = Province::active()->get();

        return $result ?? [];
    }
}

if (!function_exists('getBranches')) {
    function getBranches()
    {

        $result = Branch::active()->get();

        return $result ?? [];
    }
}

if (!function_exists('getJobTitles')) {
    function getJobTitles()
    {

        $result = JobTitle::active()->get();

        return $result ?? [];
    }
}

if (!function_exists('getNationalities')) {
    function getNationalities()
    {

        $result = CountryCode::active()->orderBy('nationality')->get();

        return $result ?? [];
    }
}

if (!function_exists('getBanks')) {
    function getBanks()
    {

        $result = Bank::active()->orderBy('bank_code')->get();

        return $result ?? [];
    }
}

if (!function_exists('month_names')) {
    function month_names()
    {
        return collect(range(1, 12))->mapWithKeys(
            fn($i) =>
            [$i => \Carbon\Carbon::create()->month($i)->translatedFormat('F')]
        )->toArray();
    }
}

if (!function_exists('getClientBalance')) {
    function getClientBalance(Client $client)
    {
        $deposit = $client->savings
            ->where('transaction_type', 'deposit')->sum('amount');

        // deductions
        $withdraw = $client->savings
            ->where('transaction_type', 'withdraw')->sum('amount');
        // $interest = $client->savings
        //     ->where('transaction_type', 'withdraw')->sum('interest');
        // $insurance = $client->savings
        //     ->where('transaction_type', 'withdraw')->sum('insurance');
        // $principal = $client->savings
        //     ->where('transaction_type', 'withdraw')->sum('principal');
        // $membership_fee = $client->savings
        //     ->where('transaction_type', 'withdraw')->sum('membership_fee');
        // $due_change = $client->savings
        //     ->where('transaction_type', 'withdraw')->sum('due_change');

        // $deductions = $interest + $insurance + $principal + $membership_fee + $due_change;

        $balance = $deposit - $withdraw;

        return number_format($balance, 2);
    }
}

if (!function_exists('getLoanBalance')) {
    function getLoanBalance(Loan $loan)
    {
        $principal = $loan->final_principal;
        $payment = Savings::where('loan_id', $loan->id)->where('transaction_type', 'withdraw')->sum('principal');
        $balance = $principal - $payment;

        Log::info('loan_id:' . $loan->id);
        Log::info('principal:' . $principal);
        Log::info('payment:' . $payment);
        Log::info('balance:' . $balance);
        return $balance;
    }
}


if (!function_exists('markLoanAsClosed')) {
    function markLoanAsClosed(Loan $loan, $closed_date)
    {
        $loan->status = LoanStatus::CLOSED;
        $loan->closed_date = $closed_date;
        $loan->save();

        DB::enableQueryLog();
        Loan::where([
            'is_additional_loan' => 1,
            'reference_loan_no' => $loan->loan_no,
        ])->update([
                    'status' => LoanStatus::CLOSED,
                    'closed_date' => $closed_date,
                ]);
        Log::info(DB::getQueryLog());
    }
}


if (!function_exists('getSpecialLoanBalance')) {
    function getSpecialLoanBalance(int $borrower_id)
    {
        $special_loans = Loan::specialLoan($borrower_id)->get();
        $total_special_loan_principal = 0;
        $total_special_loan_payment = 0;
        $special_loan_balance = 0;

        foreach ($special_loans as $special_loan) {
            $total_special_loan_principal += $special_loan->principal;
            $special_loan_payments = $special_loan?->payments->where('transaction_type', 'withdraw');
            $total_special_loan_payment += $special_loan_payments->sum('principal');
        }

        $special_loan_balance = $total_special_loan_principal - $total_special_loan_payment;

        return $special_loan_balance;
    }
}

if (!function_exists('getMembershipFeeBalance')) {
    function getMembershipFeeBalance(Loan $loan)
    {
        $membership_fee = $loan->loanCharges->membership_fee; // Target Fee
        $membership_fee_initial_payment = $loan->loanCharges->membership_fee_paid; // Initial Payment
        $payment = Savings::where('loan_id', $loan->id)->where('transaction_type', 'withdraw')->sum('membership_fee');
        $balance = $membership_fee - ($membership_fee_initial_payment + $payment);

        return number_format($balance ?? 0, 2);
    }
}

if (!function_exists('getClientTotalLoanBalance')) {
    function getClientTotalLoanBalance(Client $client)
    {
        $loan_ids =  $client->getApproveLoans()->pluck('id');
        $amount = 0;
        foreach ($loan_ids as $id) {
            $amount += getLoanBalance(Loan::find($id));
        }
        return $amount;
    }
}

if (!function_exists('getUserRoles')) {
    function getUserRoles(User $user, ?int $branchId)
    {
        $branchId ??= $user->current_branch_id;

        $roleQuery = DB::table('model_has_roles')
                    ->where('model_id', $user->id)
                    ->where('branch_id', $branchId)
                    ->pluck('role_id');

        $roles = Role::withoutGlobalScopes()
            ->whereIn('id',$roleQuery)->pluck('name')
            ->toArray();

        return implode(",",$roles);
    }
}

if (!function_exists('getUserBranch')) {
    function getUserBranch(User $user)
    {
        $branches = BranchUser::with('branch')->where('user_id', $user->id)->get(['user_id','branch_id']);

        return $branches;
    }
}