<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class LoanProduct extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name','description','loan_type_id','status'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopePopular($query)
    {
        return $query->take(4);
    }

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where('id', 'LIKE', '%'.$search.'%')
                ->orWhere('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%');
        }

        return $query;
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function prerequisite()
    {
        return $this->belongsToMany(
            LoanProduct::class,
            'loan_product_prerequisites',
            'loan_product_id',         // Foreign key on pivot table for this model
            'loan_product_id_required' // Foreign key on pivot table for the related model
        );
    }
}
