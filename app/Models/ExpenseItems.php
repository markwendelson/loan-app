<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItems extends Model
{
    protected $table = 'expense_items';
    protected $fillable = [
        'expense_id',
        'vendor_id',
        'description',
        'ref_no',
        'amount',
        'created_at',
        'updated_at',
    ];

}
