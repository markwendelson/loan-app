<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseAttachments extends Model
{
    protected $table = 'expense_attachments';
    protected $fillable = [
        'expense_id',
        'name',
        'created_at',
        'updated_at'
    ];
}
