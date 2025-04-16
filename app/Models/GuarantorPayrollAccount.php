<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuarantorPayrollAccount extends Model {
    use HasFactory;

    protected $table = 'guarantor_payroll_account';
    protected $fillable = [
        'bank_id',
        'client_id',
        'account_no',
        'account_name',
        'is_active',
        'created_at',
        'updated_at',
    ];

    
}
