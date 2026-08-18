<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseBackup extends Model
{
    protected $table = 'database_backup';
    protected $fillable = [
        'file_name',
        'created_by'
    ];
}
