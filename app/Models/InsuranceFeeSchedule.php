<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class InsuranceFeeSchedule extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['min_amount','max_amount','monthly_fee','annual_fee','type','status'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where('id', 'LIKE', '%'.$search.'%')
                ->orWhere('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%');
        }
    }
}
