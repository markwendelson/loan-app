<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientColorCode extends Model
{
    protected $table = 'client_color_codes';
    protected $fillable = [
<<<<<<< HEAD
        'client_id',
=======
>>>>>>> b17a79587ed752373c3cbdcef644e8e4b62207ab
        'color_name',
        'color_description',
        'color_code',
        'status'
    ];
}
