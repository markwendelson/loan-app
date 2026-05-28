<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanDisbursementDetails extends Model
{
    protected $table='loan_disbursement_details';
    protected $fillable = [
        'loan_id',
        'processing_branch',
        'amount',
        'disbursed_by',
        'disburse_date',
        'notes',
        'created_at',
        'updated_at'
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
