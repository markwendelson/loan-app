<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Savings extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable, SoftDeletes;


    protected $fillable = ['transaction_date','client_id','loan_id','description','notes','amount','transaction_type','classifications',
                            'interest','interest_start_date','interest_end_date','insurance','insurance_month','insurance_year','principal','principal_advance','due_change','mode_of_payment','membership_fee','transaction_code','change_mop','change_transaction_code',
                            'interest_deficit', 'principal_balance','payment_type','payment_type_reference'
                            ];

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where('savings_no', 'LIKE', '%'.$search.'%')
                ->orWhere('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%');
        }

        return $query;
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function attachments()
    {
        return $this->hasOne(Attachment::class, 'transaction_no', 'transaction_no');
    }

    public static function generateTransactionNumber(string $transaction_date, string $type): string
    {
        $date = new DateTime($transaction_date);
        $date_format = $date->format('Y-m');

        $count = str_pad(Savings::where('transaction_type',$type)->count() + 1, 5, '0', STR_PAD_LEFT);

        if($type == 'withdraw') {
            return "PAY-{$date_format}-{$count}";
        }

        if($type == 'deposit') {
            return "DEP-{$date_format}-{$count}";
        }

    }
}
