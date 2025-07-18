<?php

use App\Enums\LoanStatus;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\CountryCode;
use App\Models\JobTitle;
use App\Models\Province;
use App\Models\Client;
use App\Models\Loan;
use App\Models\Savings;

if(!function_exists('getProvinces')) {
    function getProvinces() {

        $result = Province::active()->get();

        return $result ?? [];
    }
}

if(!function_exists('getBranches')) {
    function getBranches() {

        $result = Branch::active()->get();

        return $result ?? [];
    }
}

if(!function_exists('getJobTitles')) {
    function getJobTitles() {

        $result = JobTitle::active()->get();

        return $result ?? [];
    }
}

if(!function_exists('getNationalities')) {
    function getNationalities() {

        $result = CountryCode::active()->orderBy('nationality')->get();

        return $result ?? [];
    }
}

if(!function_exists('getBanks')) {
    function getBanks() {

        $result = Bank::active()->orderBy('bank_code')->get();

        return $result ?? [];
    }
}

if(!function_exists('month_names')) {
    function month_names()
    {
        return collect(range(1, 12))->mapWithKeys(fn($i) =>
            [$i => \Carbon\Carbon::create()->month($i)->translatedFormat('F')]
        )->toArray();
    }
}

if(!function_exists('getClientBalance')) {
    function getClientBalance(Client $client)
    {
        $deposit = $client->savings
                ->where('transaction_type', 'deposit')->sum('amount');
        $withdraw = $client->savings
                ->where('transaction_type', 'withdraw')->sum('amount');
        $balance = $deposit - $withdraw;

        return number_format($balance,2);
    }
}

if(!function_exists('getLoanBalance')) {
    function getLoanBalance(Loan $loan)
    {
        $principal = $loan->principal;
        $payment = Savings::where('loan_id',$loan->id)->where('transaction_type','withdraw')->sum('amount');
        $balance = $principal - $payment;

        return $balance;
    }
}


if(!function_exists('markLoanAsClosed')) {
    function markLoanAsClosed(Loan $loan)
    {
        $loan->status = LoanStatus::CLOSED;
        $loan->save();
    }
}