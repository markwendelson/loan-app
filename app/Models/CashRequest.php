<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRequest extends Model
{
    use HasFactory;

    protected $table = 'cash_requests';
    public $timestamps = false; // We will handle created_at manually or let DB handle it since there's no updated_at

    protected $fillable = [
        'employee_id',
        'branch_id',
        'amount',
        'created_at',
        'status',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'approved_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
