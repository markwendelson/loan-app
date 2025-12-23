<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Savings extends Model implements Auditable
{

    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $fillable = ['transaction_date','client_id','loan_id','description','notes','amount','transaction_type','classifications',
                            'interest','interest_start_date','interest_end_date','insurance','insurance_month','insurance_year','principal','due_change','mode_of_payment','membership_fee'];

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
}
