<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Savings extends Model
{
    protected $fillable = ['client_id','loan_id','description','notes','amount','transaction_type','classifications'];

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where('savings_no', 'LIKE', '%'.$search.'%')
                ->orWhere('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%');
        }

        return $query;
    }
}
