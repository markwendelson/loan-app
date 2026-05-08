<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItems extends Model
{
    protected $table = 'expense_items';
    protected $fillable = [
        'expense_id',
        'category_id',
        'description',
        'qty',
        'unit_price',
        'sub_total',
        'created_at',
        'updated_at',
    ];

}
