<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    protected $fillable = [
        'client_id',
        'loan_id',
        'document_type_id',
        'file_path',
        'remarks',
    ];
}
