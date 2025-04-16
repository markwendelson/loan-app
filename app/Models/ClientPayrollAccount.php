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
        'is_active',
        'created_at',
        'updated_at'
    ];




}
