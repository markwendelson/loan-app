<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'branch_id',
        'vendor_name',
        'ref_no',
        'payment_method',
        'tax_rate',
        'sub_total',
        'amount',
        'tax',
        'description',
        'expense_date',
        'slug',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
