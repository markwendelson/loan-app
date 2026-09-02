<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientColorCode extends Model
{
    protected $table = 'client_color_codes';
    protected $fillable = [
        'user_id',
        'color_name',
        'color_description',
        'color_code',
        'status'
    ];
}
