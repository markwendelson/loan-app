<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use App\Models\ClientDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use App\Enums\LoanStatus;

class Client extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['branch_id','client_code','status', 'created_by'];

    protected static function boot()
    {
        parent::boot();

        // Before the model is deleted
        static::deleting(function (Client $client) {
            // e.g. remove related files, log something...
            // Storage::disk('public')->deleteDirectory($client->path);
        });

        // After the model is deleted
        static::deleted(function (Client $client) {
            // e.g. clear cache, fire custom events...
            \Log::info("Client {$client->id} has been deleted.");
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFilter($query, $search)
    {
        if (!empty($search)) {
            $query->where('client_code', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('details', function ($q) use ($search) {
                      $q->where('email', 'LIKE', '%' . $search . '%')
                        ->orWhere('firstname', 'LIKE', '%' . $search . '%')
                        ->orWhere('middlename', 'LIKE', '%' . $search . '%')
                        ->orWhere('lastname', 'LIKE', '%' . $search . '%');
                  });
        }

        return $query;
    }

    public function details()
    {
        return $this->hasOne(ClientDetails::class, 'client_id');
    }

    public function coMakers()
    {
        return $this->belongsToMany(Client::class, 'client_co_makers', 'client_id', 'co_maker_id')
                    ->withPivot('id', 'loan_id');
    }

    public function isCoMakerFor()
    {
        return $this->belongsToMany(Client::class, 'client_co_makers', 'co_maker_id', 'client_id')
                    ->withPivot('id', 'loan_id');
    }

    public function savings()
    {
        return $this->hasMany(Savings::class);
    }

    public function jobs()
    {
        return $this->hasMany(ClientJobTitle::class);
    }

    public function documents()
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function bankAccount()
    {
        return $this->hasMany(ClientPayrollAccount::class);
    }

    public function getApproveLoans()
    {
       return Loan::with([
            'loanProduct:id,name,repayment_order,loan_type_id',
            'loanProduct.loanType:id,name'
       ])
        ->where('borrower_id', $this->id)
        ->where(function ($query) {
            $query->where('is_additional_loan', 0)
                  ->orWhere('is_special', 1);
        })
        ->where('status', LoanStatus::APPROVE)
        ->get();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getBalance()
    {
        return getClientBalance($this);
    }

}
