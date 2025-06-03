<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Savings extends Model
{
    protected $fillable = ['transaction_date','client_id','loan_id','description','notes','amount','transaction_type','classifications',
                            'interest','interest_month','interest_year','insurance','insurance_month','insurance_year','principal'];

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
}
