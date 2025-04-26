<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Savings extends Model
{
    public function scopeFilterSavingsNo($query, $search)
    {
        if (!empty($search)) {
            $query->where('savings_no', 'LIKE', '%'.$search.'%')
                ->orWhere('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%');
        }
    }

    public function scopeFilterName($query, $search)
    {
        if (!empty($search)) {
            $query->where('savings_no', 'LIKE', '%'.$search.'%')
                ->orWhere('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%');
        }
    }
}
