<?php

namespace App\Observers;

use App\Models\Savings;

class PaymentObserver
{
    /**
     * Handle the Savings "created" event.
     */
    public function created(Savings $savings): void
    {
        //
    }

    /**
     * Handle the Savings "updated" event.
     */
    public function updated(Savings $savings): void
    {
        //
    }

    /**
     * Handle the Savings "deleted" event.
     */
    public function deleted(Savings $savings): void
    {
        //
    }

    /**
     * Handle the Savings "restored" event.
     */
    public function restored(Savings $savings): void
    {
        //
    }

    /**
     * Handle the Savings "force deleted" event.
     */
    public function forceDeleted(Savings $savings): void
    {
        //
    }

    public function saving(Savings $savings)
    {
        if (!$savings->interest) {
            $savings->interest_month = null;
            $savings->interest_year = null;
        }

        if (!$savings->insurance) {
            $savings->insurance_month = null;
            $savings->insurance_year = null;
        }
    }

}
