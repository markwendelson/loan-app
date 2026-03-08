<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Incentives extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['client_id','transaction_date', 'description', 'amount'];


}
