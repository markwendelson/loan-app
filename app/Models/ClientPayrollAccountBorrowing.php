<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPayrollAccountBorrowing extends Model
{
    protected $table = 'client_payroll_account_borrowing';
    protected $fillable = [
        'payroll_account_id',
        'original_branch_id',
        'borrowing_branch_id',
        'borrowed_at',
        'borrowed_by',
        'expected_return_at',
        'returned_at',
        'returned_by',
        'status',
        'borrower_remarks',
        'return_remarks',
    ];

    public function clientPayrollAccount()
    {
        return $this->belongsTo(ClientPayrollAccount::class. 'payroll_account_id');
    }
}
