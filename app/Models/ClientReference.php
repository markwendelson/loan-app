<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientReference extends Model {
    use HasFactory;

    protected $table = 'client_references';
    protected $fillable = [
        'client_id',
        'fullname',
        'contact_info',
        'occupation',
        'created_at',
        'updated_at',
    ];
}
