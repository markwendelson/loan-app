<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseReport extends Model
{
    protected $fillable = [
        'user_id',
        'date_from',
        'date_to',
        'description',
        'categories_data',
        'amount',
        'data',
        'slug'
    ];
}
