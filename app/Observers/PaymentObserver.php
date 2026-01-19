<?php

namespace App\Observers;

use App\Models\Savings;
use DateTime;

class PaymentObserver
{
    /**
     * Handle the Savings "created" event.
     */
    public function created(Savings $savings): void
    {
        // check loan balance
        if ($savings->transaction_type == 'withdraw') {
            $loan = $savings->loan;
            if ($loan) {
                $balance = getLoanBalance($loan);
                if ($balance == 0) {
                    markLoanAsClosed($loan, $savings->transaction_date);
                }
            }
        }

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
        if ($savings->transaction_type == 'deposit') {
            $savings->interest_start_date = null;
            $savings->interest_end_date = null;
            $savings->insurance_month = null;
            $savings->insurance_year = null;
        }

        $savings->transaction_no = $this->generateReferenceNumber($savings->transaction_date);
    }

    public function generateReferenceNumber($payment_date): string
    {
        $date = new DateTime($payment_date);
        $date_format = $date->format('Y-m');

        $count = str_pad(Savings::count() + 1, 5, '0', STR_PAD_LEFT);
        return "REF-NO-{$date_format}-{$count}";
    }

}
