<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class LoanType extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name','description'];

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
