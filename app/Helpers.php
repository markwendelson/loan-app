<?php

use App\Models\Bank;
use App\Models\Branch;
use App\Models\CountryCode;
use App\Models\JobTitle;
use App\Models\Province;

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