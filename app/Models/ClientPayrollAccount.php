<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPayrollAccount extends Model {
    use HasFactory;

    public $timestamps = true;

    protected $table = 'client_payroll_account';
    protected $fillable = [
        'id', 
        'bank_id', 
        'client_id', 
        'account_no', 
        'account_name', 
        'account_expiry',
        'is_active',
        'is_primary',
        'created_at',
        'updated_at'
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function payrollAccountBorrowings()
    {
        return $this->hasOne(ClientPayrollAccountBorrowing::class, 'payroll_account_id')
            ->where('status', 'borrowed');
    }
}
