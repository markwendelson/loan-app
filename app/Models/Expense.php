<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'branch_id',
        'category_id',
        'payment_method',
        'amount',
        'description',
        'expense_date',
        'slug',
        'created_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function expenseItems()
    {
        return $this->hasMany(ExpenseItems::class)
        ->select(
            'expense_items.*',
            'vendors.name as vendor_name',
            'vendors.tin as vendor_tin',
        )
        ->leftJoin('vendors', 'vendors.id', '=', 'expense_items.vendor_id');
    }

    public function expenseAttachments()
    {
        return $this->hasMany(ExpenseAttachments::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
