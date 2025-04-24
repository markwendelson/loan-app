<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where('id', 'LIKE', '%'.$search.'%')
                ->orWhere('loan_no', 'LIKE', '%'.$search.'%')
                ->orWhere('borrower_id', 'LIKE', '%'.$search.'%');
        }
    }
}
