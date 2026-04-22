<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'file_reference',
        'transaction_no',
        'file_name',
        'file_path',
    ];
}
