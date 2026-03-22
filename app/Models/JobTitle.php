<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class JobTitle extends Model  implements Auditable
{

    use \OwenIt\Auditing\Auditable;

    protected $table = 'job_title';
    protected $fillable = ['title', 'description', 'status'];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

}
