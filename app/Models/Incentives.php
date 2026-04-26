<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Incentives extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['incentive_no', 'client_id', 'transaction_date', 'description', 'amount', 'mode_of_payment', 'transaction_code'];

    public function Borrower()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public static function generateIncentiveNumber($transaction_date): string
    {
        $date = new DateTime($transaction_date);
        $date_format = $date->format('Y-m');

        $count = str_pad(Incentives::count() + 1, 5, '0', STR_PAD_LEFT);
        return "IN-{$date_format}-{$count}";
    }
}
