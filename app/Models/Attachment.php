<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'transaction_no',
        'file_name',
        'file_path',
    ];
}
