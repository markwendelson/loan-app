<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Client extends Model implements Auditable
{
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['branch_id','client_code','status', 'created_by'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function details()
    {
        return $this->hasOne(ClientDetails::class, 'client_id');
    }

    public function coMakers()
    {
        return $this->belongsToMany(Client::class, 'client_co_makers', 'client_id', 'co_maker_id');
    }

    public function isCoMakerFor()
    {
        return $this->belongsToMany(Client::class, 'client_co_makers', 'co_maker_id', 'client_id');
    }
}
